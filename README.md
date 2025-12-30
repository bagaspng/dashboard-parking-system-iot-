# 🚗 Dashboard Parking System IoT

> Sistem dashboard monitoring dan manajemen parkir berbasis IoT dengan teknologi sensor real-time

[![PHP](https://img.shields.io/badge/PHP-70.1%25-777BB4?style=flat-square&logo=php&logoColor=white)](https://github.com/bagaspng/dashboard-parking-system-iot-)
[![C++](https://img.shields.io/badge/C++-29.9%25-00599C?style=flat-square&logo=c%2B%2B&logoColor=white)](https://github.com/bagaspng/dashboard-parking-system-iot-)
[![IoT](https://img.shields.io/badge/IoT-Enabled-success?style=flat-square&logo=internetofthings&logoColor=white)](https://github.com/bagaspng/dashboard-parking-system-iot-)


## 📋 Deskripsi

Dashboard Parking System IoT adalah solusi pintar untuk monitoring dan manajemen area parkir secara real-time. Sistem ini mengintegrasikan sensor IoT (menggunakan C++) dengan dashboard web (PHP) untuk memberikan visibilitas penuh terhadap ketersediaan parkir, statistik penggunaan, dan manajemen data kendaraan.

## ✨ Fitur Utama

### 🎯 **Monitoring Real-Time**
- 📊 **Live Dashboard** - Monitoring status parkir secara real-time
- 🅿️ **Slot Availability** - Menampilkan ketersediaan tempat parkir
- 📈 **Usage Analytics** - Statistik penggunaan parkir harian/bulanan
- 🚨 **Alert System** - Notifikasi ketika parkir penuh/kosong

### 🛠️ **IoT Integration**
- 🔌 **Sensor Network** - Integrasi dengan sensor ultrasonic/infrared
- 📡 **Data Transmission** - Komunikasi wireless ESP32/Arduino
- 🔄 **Auto Sync** - Sinkronisasi data otomatis ke database
- 🌐 **Remote Control** - Kontrol sistem dari jarak jauh

### 💻 **Web Dashboard**
- 👤 **User Management** - Sistem login admin/operator
- 📋 **Parking History** - Riwayat kendaraan masuk/keluar
- 📊 **Reports** - Generate laporan penggunaan parkir
- ⚙️ **System Config** - Konfigurasi sensor dan pengaturan

### 📱 **Responsive Design**
- 📱 **Mobile Friendly** - Akses dashboard dari smartphone
- 💻 **Desktop Optimized** - Interface optimal untuk monitoring center
- 🎨 **Modern UI/UX** - Antarmuka yang intuitif dan professional

## 🏗️ Arsitektur Sistem

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   IoT Sensors   │────│  Microcontroller │────│   Web Server    │
│  (Ultrasonic,   │    │   (ESP32/Arduino)│    │     (PHP)       │
│   IR, Camera)   │    │                  │    │                 │
└─────────────────┘    └──────────────────┘    └─────────────────┘
         │                        │                        │
         └── Detect Vehicle ──────┼── Send Data ───────────┤
                                  │                        │
                            ┌──────────────────┐    ┌─────────────────┐
                            │    Database      │────│   Dashboard     │
                            │    (MySQL)       │    │   (HTML/CSS/JS) │
                            └──────────────────┘    └─────────────────┘
```

## 🛠️ Teknologi

| Komponen | Teknologi | Persentase | Deskripsi |
|----------|-----------|------------|-----------|
| **Backend** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) | 70.1% | Web server, API, database management |
| **IoT Firmware** | ![C++](https://img.shields.io/badge/C++-00599C?style=flat-square&logo=c%2B%2B&logoColor=white) | 29.9% | Microcontroller programming, sensor control |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | - | Data storage dan management |
| **Frontend** | ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | - | Dashboard interface |

## 🔧 Hardware Requirements

### 📟 **Microcontroller**
- **ESP32** / Arduino Uno R3
- **WiFi Module** (ESP8266) - untuk komunikasi wireless
- **Power Supply** 5V/3.3V

### 🔍 **Sensors**
- **Ultrasonic Sensor** (HC-SR04) - deteksi jarak kendaraan
- **IR Sensor** - deteksi keberadaan objek
- **Camera Module** (opsional) - untuk ANPR
- **LED Indicators** - status visual

### 💻 **Server Requirements**
- **Web Server** (Apache/Nginx)
- **PHP** 7.4+ dengan extensions:
  - mysqli/PDO
  - curl
  - json
- **MySQL** 5.7+

## 📦 Instalasi

### 🌐 **1. Setup Web Dashboard**

```bash
# Clone repository
git clone https://github.com/bagaspng/dashboard-parking-system-iot-.git
cd dashboard-parking-system-iot-

# Setup database
mysql -u root -p < database/parking_system.sql

# Configure database connection
cp config/database.example.php config/database.php
nano config/database.php
```

**Database Configuration:**
```php
<?php
$config = [
    'host' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'parking_system'
];
?>
```

### 🔧 **2. Setup IoT Device**

```cpp
// Configure WiFi credentials di sketch Arduino
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
const char* server = "http://your-server. com/api/";

// Upload sketch ke ESP32/Arduino
// Pastikan library yang diperlukan sudah terinstall: 
// - WiFi. h
// - HTTPClient.h  
// - ArduinoJson.h
```

### ⚙️ **3. Web Server Setup**

```bash
# Apache Virtual Host
sudo nano /etc/apache2/sites-available/parking-dashboard.conf

# Nginx Configuration  
sudo nano /etc/nginx/sites-available/parking-dashboard

# Enable site dan restart
sudo a2ensite parking-dashboard
sudo systemctl reload apache2
```

## 🎮 Penggunaan

### 👨‍💻 **Admin Dashboard**

1. **Login ke sistem** - `http://your-domain. com/admin`
2. **Monitor real-time** - Lihat status parkir live
3. **Manage users** - Tambah/edit operator
4. **View reports** - Generate laporan penggunaan
5. **System settings** - Konfigurasi sensor dan alert

### 📊 **Monitoring Features**

```
🔴 Occupied Slots:  45/100
🟢 Available Slots: 55/100  
📈 Today's Traffic: 234 vehicles
⏱️ Average Duration: 2.5 hours
💰 Revenue:  Rp 1,250,000
```

### 📱 **API Endpoints**

```bash
# Get parking status
GET /api/parking/status

# Update slot status (dari IoT device)
POST /api/parking/update
{
    "slot_id": 1,
    "status": "occupied",
    "timestamp": "2024-01-15 10:30:00"
}

# Get historical data
GET /api/parking/history? date=2024-01-15
```

## 📁 Struktur Project

```
dashboard-parking-system-iot-/
│
├── 📂 web/                     # PHP Web Dashboard
│   ├── 📂 admin/              # Admin panel
│   ├── 📂 api/                # REST API endpoints  
│   ├── 📂 assets/             # CSS, JS, images
│   ├── 📂 config/             # Database configuration
│   └── 📄 index.php           # Main dashboard page
│
├── 📂 firmware/               # C++ Arduino/ESP32 code
│   ├── 📄 parking_sensor. ino  # Main sensor sketch
│   ├── 📄 wifi_config.h       # WiFi configuration
│   └── 📂 libraries/          # Required libraries
│
├── 📂 database/               # Database schemas
│   ├── 📄 parking_system.sql  # Database structure
│   └── 📄 sample_data.sql     # Sample data
│
├── 📂 docs/                   # Documentation
│   ├── 📄 API_Documentation.md
│   ├── 📄 Hardware_Setup.md
│   └── 📄 Deployment_Guide.md
│
└── 📄 README.md               # This file
```

## 🔌 Hardware Setup

### 🔗 **Wiring Diagram**

```
ESP32/Arduino Uno          Ultrasonic Sensor (HC-SR04)
├── VCC (3.3V/5V) ────────── VCC
├── GND ──────────────────── GND  
├── Digital Pin 2 ────────── Trig
└── Digital Pin 3 ────────── Echo

LED Indicators: 
├── Digital Pin 13 ───────── Green LED (Available)
└── Digital Pin 12 ───────── Red LED (Occupied)
```

### 📝 **Sensor Configuration**

```cpp
// Sensor pins definition
#define TRIG_PIN 2
#define ECHO_PIN 3
#define GREEN_LED 13
#define RED_LED 12

// Detection settings
#define DISTANCE_THRESHOLD 10  // cm
#define DETECTION_DELAY 2000   // ms
```

## 📊 Database Schema

### 🗃️ **Main Tables**

```sql
-- Parking slots
CREATE TABLE parking_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_number VARCHAR(10) UNIQUE,
    status ENUM('available', 'occupied'),
    sensor_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Parking history  
CREATE TABLE parking_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_id INT,
    vehicle_plate VARCHAR(20),
    entry_time TIMESTAMP,
    exit_time TIMESTAMP NULL,
    duration INT DEFAULT 0,
    FOREIGN KEY (slot_id) REFERENCES parking_slots(id)
);

-- System users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'operator'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🎯 Features Detail

### 🔄 **Real-time Data Flow**

1. **Sensor Detection** → Ultrasonic mengukur jarak
2. **Data Processing** → ESP32 process dan validasi data  
3. **WiFi Transmission** → Kirim data ke web server
4. **Database Update** → PHP API update status slot
5. **Dashboard Refresh** → Real-time update via AJAX
6. **Alert System** → Trigger notifikasi jika diperlukan

### 📈 **Analytics Dashboard**

```php
// Contoh implementasi analytics
class ParkingAnalytics {
    public function getDailyStats($date) {
        return [
            'total_vehicles' => 234,
            'peak_hour' => '14:00-15:00',
            'occupancy_rate' => 78.5,
            'revenue' => 1250000
        ];
    }
}
```

## 🚀 Deployment

### 🌐 **Production Setup**

```bash
# Setup SSL Certificate
sudo certbot --apache -d your-domain.com

# Configure firewall
sudo ufw allow 80
sudo ufw allow 443  
sudo ufw allow 22

# Setup automated backup
crontab -e
0 2 * * * mysqldump parking_system > backup_$(date +\%Y\%m\%d).sql
```

## 👨‍💻 Author

**Bagas Pangestu** ([@bagaspng](https://github.com/bagaspng))

---

<div align="center">

**🚗 Smart Parking for Smart Cities 🌆**

[![GitHub stars](https://img.shields.io/github/stars/bagaspng/dashboard-parking-system-iot-?style=social)](https://github.com/bagaspng/dashboard-parking-system-iot-/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/bagaspng/dashboard-parking-system-iot-?style=social)](https://github.com/bagaspng/dashboard-parking-system-iot-/network/members)

**Made with ❤️ in Indonesia**

</div>
