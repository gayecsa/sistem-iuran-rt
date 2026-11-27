# Panduan Perangkat Keras: ESP32 + RFID-RC522 Haven Portal RW 013

Sistem gerbang pintar (Smart Gate) & pembaca kartu RFID terpadu untuk rukun warga RW 013 Suryatmajan Danurejan Kota Yogyakarta, terhubung langsung ke web [havenportal.mehape.com](https://havenportal.mehape.com).

---

## 1. Skema Wiring & Pinout ESP32

Gunakan mikrokontroler **ESP32 DOIT DevKit V1** (30 Pin atau 36 Pin) dan modul RFID **MFRC522** (SPI Mode).

| Pin RFID-RC522 | Pin ESP32 (GPIO) | Catatan Penting |
| :--- | :--- | :--- |
| **VCC (3.3V)** | **3V3** | **WAJIB 3.3V!** Jangan hubungkan ke 5V (dapat merusak RC522). |
| **RST** | **GPIO 22** | Reset Pin |
| **GND** | **GND** | Ground bersama |
| **MISO** | **GPIO 19** | SPI MISO |
| **MOSI** | **GPIO 23** | SPI MOSI |
| **SCK** | **GPIO 18** | SPI Clock |
| **SDA / SS** | **GPIO 5** | SPI Chip Select |
| **IRQ** | *Tidak Dihubungkan* | Dibiarkan kosong |

### Komponen Tambahan (Output Indikator & Aktuator):
| Komponen | Pin ESP32 | Keterangan |
| :--- | :--- | :--- |
| **Relay Palang Gerbang (IN)** | **GPIO 15** | Mengendalikan motor barrier gate / solenoid door lock (Active LOW). |
| **Buzzer Aktif (Positif)** | **GPIO 4** | Umpan balik bunyi saat tap kartu (Negatif ke GND). |
| **Onboard Blue LED** | **GPIO 2** | Indikator transmisi data & status standby. |

---

## 2. Cara Kerja 4 Mode Multi-Fungsi (1 Kartu untuk Semua Layanan)

Reader ESP32 ini dapat diubah modenya secara instan langsung dari Web Dashboard Pengurus (`/ronda?tab=portal`):

1. **Mode 1 — Portal Gerbang Masuk (`portal`)**:
   - Warga menempelkan kartu di gerbang masuk.
   - Server memvalidasi akun warga aktif -> Relay membuka palang gerbang selama 3 detik.
   - Suara beep sukses 1x + log tercatat otomatis di web.
2. **Mode 2 — Absensi Ronda Malam (`ronda`)**:
   - Petugas ronda menempelkan kartu di pos siskamling.
   - Server mencatat kehadiran ronda malam & log audit keamanan.
3. **Mode 3 — Identifikasi Bank Sampah (`bank_sampah`)**:
   - Warga menempelkan kartu saat menyetorkan sampah di TPS/Bank Sampah.
   - Layar admin otomatis mendeteksi nama warga, nomor RT, serta saldo tabungan sampah saat ini.
4. **Mode 4 — Registrasi Kartu Baru (`register`)**:
   - Pengurus memilih nama warga di web, lalu mengaktifkan mode scan.
   - Warga menempelkan kartu RFID fisik baru pada reader ESP32.
   - Kartu langsung terpasang ke akun warga tersebut dan ESP32 otomatis kembali ke mode portal.

---

## 3. Konfigurasi Jaringan (WiFiManager Captive Portal)

Firmware ini telah dilengkapi pustaka **WiFiManager**. Anda **tidak perlu meng-hardcode nama Wi-Fi dan password** ke dalam kode program:

1. Nyalakan ESP32 untuk pertama kali.
2. Buka Wi-Fi di HP Anda, cari hotspot bernama:
   - **SSID**: `Haven-Portal-RW013`
   - **Password**: `havenrw013`
3. Sambungkan ke hotspot tersebut. Layar HP akan otomatis membuka halaman Captive Portal (atau buka browser ke `192.168.4.1`).
4. Klik **Configure WiFi**, pilih SSID Wi-Fi pos satpam / gerbang Anda, masukkan password, lalu klik **Save**.
5. ESP32 akan otomatis tersambung ke internet dan menyimpan konfigurasi tersebut di memori non-volatile EEPROM.

---

## 4. Cara Compile & Flash Firmware via PlatformIO

1. Buka folder `esp32-haven-portal` di **Visual Studio Code** yang telah terpasang ekstensi **PlatformIO IDE**.
2. Hubungkan board ESP32 ke komputer menggunakan kabel data Micro USB berkualitas baik.
3. Buka file `src/main.cpp`, pastikan `API_URL` dan `API_KEY` sesuai:
   ```cpp
   const char* API_URL = "https://havenportal.mehape.com/api/iot/portal/tap";
   const char* API_KEY = "haven-rw013-gate-key-2026";
   ```
4. Klik tombol **Build (Centang)** di bilah bawah PlatformIO untuk meng-compile program.
5. Klik tombol **Upload (Panah Kanan)** untuk mengunggah firmware ke ESP32.
6. Buka **Serial Monitor (Baud rate: 115200)** untuk melihat log aktivitas dan pengetesan tap kartu.
