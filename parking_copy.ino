// ====== NodeMCU (ESP8266) Ultrasonik -> MySQL via PHP ======
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

/// ----------- WIFI CONFIG -----------
const char* WIFI_SSID = "--";
const char* WIFI_PASS = "---";

/// ----------- SERVER CONFIG -----------
/// Ganti IP/LAN kamu, contoh: "http://192.168.1.10/parking/insert.php"
const char* SERVER_INSERT_URL = "http://----/parking/insert.php";

/// ----------- PINOUT -----------
const int PIN_TRIG   = D1;
const int PIN_ECHO   = D2;  // Wajib via pembagi tegangan!
const int LED1       = D6;
const int LED2       = D7;
const int LED3       = D8;
const int BUZZER     = D5;  // aktif HIGH
const int PIN_TV = D4;   // kontrol LED/relay TV
unsigned long lastCheckTV = 0;
const unsigned long checkTVInterval = 5000; // cek tiap 5 detik

unsigned long lastSendMs = 0;
const unsigned long sendIntervalMs = 3000; // kirim ke server tiap 3 detik

// Buzzer non-blocking
unsigned long lastBeepToggleMs = 0;
bool buzzerState = false;
unsigned long buzzerIntervalMs = 0; // 0 = off

// ---------- UTIL ----------
float readDistanceCM() {
  // trigger 10us
  digitalWrite(PIN_TRIG, LOW); delayMicroseconds(2);
  digitalWrite(PIN_TRIG, HIGH); delayMicroseconds(10);
  digitalWrite(PIN_TRIG, LOW);

  // timeout 30ms (30000us)
  unsigned long duration = pulseIn(PIN_ECHO, HIGH, 30000UL);
  if (duration == 0) return -1.0;  // gagal
  return duration / 58.0;          // cm
}

String getStatusFromDistance(float d) {
  if (d >= 0 && d <= 10.0)    return "PENUH";    // 3 LED
  if (d >= 15.0 && d <= 20.0) return "WASPADA";  // 2 LED
  if (d > 20.0)               return "KOSONG";   // 1 LED
  return "TIDAK_VALID";                         // 10–15 cm atau gagal
}

void setIndicators(float d) {
  // reset dulu
  digitalWrite(LED1, LOW);
  digitalWrite(LED2, LOW);
  digitalWrite(LED3, LOW);

  if (d >= 0 && d <= 10.0) {
    // 3 LED ON, buzzer 100 ms (cepat)
    digitalWrite(LED1, HIGH);
    digitalWrite(LED2, HIGH);
    digitalWrite(LED3, HIGH);
    buzzerIntervalMs = 100;
  } else if (d >= 15.0 && d <= 20.0) {
    // 2 LED ON (LED2 + LED3), buzzer 500 ms (sedang)
    digitalWrite(LED2, HIGH);
    digitalWrite(LED3, HIGH);
    buzzerIntervalMs = 500;
  } else if (d > 20.0) {
    // 1 LED ON (LED1), buzzer 1000 ms (1 detik)
    digitalWrite(LED1, HIGH);
    buzzerIntervalMs = 1000;
  } else {
    // 10–15 cm atau pembacaan tidak valid -> semuanya mati
    buzzerIntervalMs = 0;
  }
}

void handleBuzzer() {
  if (buzzerIntervalMs == 0) {
    digitalWrite(BUZZER, LOW);
    buzzerState = false;
    return;
  }
  unsigned long now = millis();
  if (now - lastBeepToggleMs >= buzzerIntervalMs) {
    buzzerState = !buzzerState;
    digitalWrite(BUZZER, buzzerState ? HIGH : LOW);
    lastBeepToggleMs = now;
  }
}

void sendToServer(float distance, const String& status) {
  if (WiFi.status() != WL_CONNECTED || strlen(SERVER_INSERT_URL) == 0) return;

  WiFiClient client;
  HTTPClient http;
  if (http.begin(client, SERVER_INSERT_URL)) {
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String body = "status=" + status + "&jarak=" + String(distance, 2);
    int code = http.POST(body);
    // Serial.printf("POST -> %d\n", code);
    http.end();
  }
}

void checkTVStatus() {
  if (WiFi.status() != WL_CONNECTED) return;
  WiFiClient client;
  HTTPClient http;
  if (http.begin(client, "http://10.145.32.181/parking/tv_status.php")) {
    int code = http.GET();
    if (code == 200) {
      String payload = http.getString();
      if (payload.indexOf("ON") >= 0) {
        digitalWrite(PIN_TV, HIGH); // nyalakan LED/relay
      } else {
        digitalWrite(PIN_TV, LOW);  // matikan
      }
    }
    http.end();
  }
}


void setup() {
  Serial.begin(115200);

  pinMode(PIN_TRIG, OUTPUT);
  pinMode(PIN_ECHO, INPUT);

  pinMode(LED1, OUTPUT);
  pinMode(LED2, OUTPUT);
  pinMode(LED3, OUTPUT);
  pinMode(BUZZER, OUTPUT);
  digitalWrite(BUZZER, LOW);
  pinMode(PIN_TV, OUTPUT);
digitalWrite(PIN_TV, LOW);

  // WiFi (boleh dilewati jika tak kirim data)
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  Serial.print("Connecting WiFi");
  unsigned long t0 = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - t0 < 15000) {
    Serial.print(".");
    delay(500);
  }
  Serial.println();
  if (WiFi.status() == WL_CONNECTED) {
    Serial.print("WiFi IP: "); Serial.println(WiFi.localIP());
  } else {
    Serial.println("WiFi not connected (lanjutkan lokal saja).");
  }
}

void loop() {
  float d = readDistanceCM();
  if (millis() - lastCheckTV >= checkTVInterval) {
  lastCheckTV = millis();
  checkTVStatus();
}

  if (d > 0 && d < 400) {
    setIndicators(d);
    handleBuzzer();

    String status = getStatusFromDistance(d);

    // kirim ke server tiap 3 detik jika status valid
    unsigned long now = millis();
    if (status != "TIDAK_VALID" && (now - lastSendMs >= sendIntervalMs)) {
      lastSendMs = now;
      Serial.printf("Distance: %.2f cm, Status: %s\n", d, status.c_str());
      sendToServer(d, status);
    }
  } else {
    // baca gagal -> semua mati
    setIndicators(-1);
    handleBuzzer();
  }
}