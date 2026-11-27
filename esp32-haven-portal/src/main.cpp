/**
 * ============================================================================
 * HAVEN PORTAL - FIRMWARE ESP32 + RFID RC522 MULTI-MODE READER
 * RW 013 Kelurahan Suryatmajan, Danurejan, Kota Yogyakarta
 * ============================================================================
 * Mendukung 4 Mode Operasi Otomatis dari Cloud Backend:
 * 1. Portal Gerbang Masuk (Buka Palang Otomatis)
 * 2. Absensi Ronda Malam (Presensi Petugas Siskamling)
 * 3. Identifikasi Bank Sampah (Cek Saldo & Transaksi Nasabah)
 * 4. Registrasi Kartu Baru (Pairing UID ke Akun Warga Terpilih)
 *
 * Konfigurasi Jaringan: WiFiManager (Hotspot AP Mandiri: Haven-Portal-RW013)
 * Target Server: https://havenportal.mehape.com/api/iot/portal/tap
 * ============================================================================
 */

#include <Arduino.h>
#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <WiFiManager.h>
#include <ArduinoJson.h>

// ---------- PIN DEFINITIONS (ESP32 DOIT DEVKIT V1) ----------
#define SS_PIN          5    // SDA / SS pada RC522 -> GPIO 5
#define RST_PIN         22   // RST pada RC522 -> GPIO 22
#define SCK_PIN         18   // SCK pada RC522 -> GPIO 18
#define MISO_PIN        19   // MISO pada RC522 -> GPIO 19
#define MOSI_PIN        23   // MOSI pada RC522 -> GPIO 23

#define LED_INDICATOR   2    // Onboard Blue LED -> GPIO 2
#define RELAY_GATE_PIN  15   // Relay Pembuka Palang Gerbang -> GPIO 15 (Active LOW)
#define BUZZER_PIN      4    // Buzzer Feedback -> GPIO 4 (Opsional)

// ---------- CLOUD SERVER & API SETTINGS ----------
const char* API_URL       = "https://havenportal.mehape.com/api/iot/portal/tap";
const char* API_KEY       = "haven-rw013-gate-key-2026";
const char* DEVICE_ID     = "GATE-01";

// Wi-Fi AP Fallback Settings jika WiFi rumah/pos ronda belum terhubung
const char* AP_SSID       = "Haven-Portal-RW013";
const char* AP_PASSWORD   = "havenrw013";

// ---------- INISIALISASI OBJEK ----------
MFRC522 rfid(SS_PIN, RST_PIN);
unsigned long lastTapTime = 0;
const unsigned long DEBOUNCE_DELAY_MS = 2000; // Jeda 2 detik anti-double tap

// Prototipe Fungsi
void beepSuccess();
void beepDenied();
void triggerGateRelay();
void sendTapToServer(const String& uidString);

void setup() {
    Serial.begin(115200);
    delay(500);
    Serial.println("\n\n========================================================");
    Serial.println("  HAVEN PORTAL - ESP32 RFID GATE SYSTEM RW 013");
    Serial.println("========================================================");

    // Inisialisasi Output Pin
    pinMode(LED_INDICATOR, OUTPUT);
    pinMode(RELAY_GATE_PIN, OUTPUT);
    pinMode(BUZZER_PIN, OUTPUT);

    // Default state: LED mati, Relay terbuka (Gate tertutup / Active LOW default HIGH)
    digitalWrite(LED_INDICATOR, LOW);
    digitalWrite(RELAY_GATE_PIN, HIGH);
    digitalWrite(BUZZER_PIN, LOW);

    // Inisialisasi Bus SPI & RFID Reader RC522
    SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, SS_PIN);
    rfid.PCD_Init();
    delay(100);

    Serial.print("MFRC522 Self-Test: ");
    byte v = rfid.PCD_ReadRegister(rfid.VersionReg);
    Serial.print("Firmware Version 0x");
    Serial.println(v, HEX);
    if (v == 0x00 || v == 0xFF) {
        Serial.println("[WARNING] RFID RC522 tidak terdeteksi! Periksa kabel wiring SPI.");
    } else {
        Serial.println("[OK] RFID RC522 Siap Membaca Kartu.");
    }

    // Inisialisasi Jaringan WiFiManager (Captive Portal)
    Serial.println("\n[WIFI] Menghubungkan ke Wi-Fi tersimpan...");
    WiFiManager wm;
    wm.setConfigPortalTimeout(180); // 3 menit waktu tunggu jika AP aktif

    // Kedipkan LED cepat tanda proses koneksi Wi-Fi
    digitalWrite(LED_INDICATOR, HIGH);
    bool wifiConnected = wm.autoConnect(AP_SSID, AP_PASSWORD);
    digitalWrite(LED_INDICATOR, LOW);

    if (!wifiConnected) {
        Serial.println("[WIFI] Gagal terhubung ke Wi-Fi & timeout AP habis. Merestart ESP32...");
        delay(2000);
        ESP.restart();
    }

    Serial.println("[WIFI] BERHASIL TERHUBUNG!");
    Serial.print("  SSID       : "); Serial.println(WiFi.SSID());
    Serial.print("  IP Address : "); Serial.println(WiFi.localIP());
    Serial.print("  Target API : "); Serial.println(API_URL);

    // 2x Beep tanda sistem siap siaga
    beepSuccess();
    delay(150);
    beepSuccess();

    Serial.println("\n[READY] Dekatkan kartu RFID warga pada sensor...\n");
}

void loop() {
    // 1. Cek Koneksi Wi-Fi, rekoneksi otomatis jika terputus
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[WIFI] Koneksi terputus. Mencoba rekoneksi...");
        digitalWrite(LED_INDICATOR, LOW);
        WiFi.reconnect();
        delay(1000);
        return;
    }

    // 2. Cek apakah ada kartu RFID baru mendekat
    if (!rfid.PICC_IsNewCardPresent()) {
        return;
    }

    // 3. Baca serial data UID kartu
    if (!rfid.PICC_ReadCardSerial()) {
        return;
    }

    // 4. Cegah pembacaan ganda dalam waktu singkat (Debounce)
    if (millis() - lastTapTime < DEBOUNCE_DELAY_MS) {
        rfid.PICC_HaltA();
        rfid.PCD_StopCrypto1();
        return;
    }
    lastTapTime = millis();

    // 5. Konversi UID Hex ke String huruf kapital bersih tanpa spasi
    String uidString = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        if (rfid.uid.uidByte[i] < 0x10) uidString += "0";
        uidString += String(rfid.uid.uidByte[i], HEX);
    }
    uidString.toUpperCase();

    Serial.print("\n>>> KARTU TERDETEKSI! UID: ");
    Serial.println(uidString);

    // 6. Indikator LED Menyala saat mengirim ke Server
    digitalWrite(LED_INDICATOR, HIGH);

    // 7. Kirim Data Tap ke Cloud Backend Haven Portal
    sendTapToServer(uidString);

    // 8. Hentikan pembacaan kartu saat ini
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();
    digitalWrite(LED_INDICATOR, LOW);
}

void sendTapToServer(const String& uidString) {
    WiFiClientSecure client;
    client.setInsecure(); // Mengabaikan verifikasi SSL root cert untuk efisiensi RAM ESP32

    HTTPClient http;
    if (!http.begin(client, API_URL)) {
        Serial.println("[HTTP] Gagal menginisialisasi koneksi HTTPS.");
        beepDenied();
        return;
    }

    // Set Header Permintaan
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-API-KEY", API_KEY);
    http.addHeader("User-Agent", "Haven-ESP32-Gate/1.0");

    // Susun JSON Payload
    JsonDocument docReq;
    docReq["uid"] = uidString;
    docReq["device_id"] = DEVICE_ID;
    String requestBody;
    serializeJson(docReq, requestBody);

    Serial.print("[HTTP] Mengirim POST ke ");
    Serial.print(API_URL);
    Serial.print(" dengan data: ");
    Serial.println(requestBody);

    int httpCode = http.POST(requestBody);
    Serial.print("[HTTP] Kode Response Server: ");
    Serial.println(httpCode);

    if (httpCode > 0) {
        String responsePayload = http.getString();
        Serial.print("[HTTP] Response Body: ");
        Serial.println(responsePayload);

        JsonDocument docRes;
        DeserializationError error = deserializeJson(docRes, responsePayload);

        if (!error) {
            bool access = docRes["access"] | false;
            const char* mode = docRes["mode"] | "portal";
            const char* status = docRes["status"] | "";
            const char* name = docRes["name"] | "Tamu";
            const char* message = docRes["message"] | "";

            if (access) {
                Serial.println("==================================================");
                Serial.printf("  [AKSES DITERIMA] Mode: %s\n", mode);
                Serial.printf("  Nama: %s\n", name);
                Serial.printf("  Pesan: %s\n", message);
                Serial.println("==================================================");

                // Indikator Berhasil
                beepSuccess();

                // Jika mode portal: Buka palang gerbang fisik melalui relay
                if (strcmp(mode, "portal") == 0) {
                    triggerGateRelay();
                }
            } else {
                Serial.println("==================================================");
                Serial.printf("  [AKSES DITOLAK] %s\n", message);
                Serial.println("==================================================");
                beepDenied();
            }
        } else {
            Serial.print("[JSON] Gagal mengurai response JSON: ");
            Serial.println(error.c_str());
            beepDenied();
        }
    } else {
        Serial.printf("[HTTP] Gagal terhubung ke server! Error: %s\n", http.errorToString(httpCode).c_str());
        beepDenied();
    }

    http.end();
}

/**
 * Memicu Relay Palang Gerbang Terbuka selama 3 detik lalu kembali tertutup
 */
void triggerGateRelay() {
    Serial.println("[RELAY] Membuka Palang Gerbang Masuk RW 013 (3 Detik)...");
    digitalWrite(RELAY_GATE_PIN, LOW);   // Aktifkan Relay (Active LOW)
    digitalWrite(LED_INDICATOR, HIGH);
    delay(3000);                         // Tahan palang terbuka 3 detik
    digitalWrite(RELAY_GATE_PIN, HIGH);  // Matikan Relay (Palang menutup kembali)
    digitalWrite(LED_INDICATOR, LOW);
    Serial.println("[RELAY] Palang Gerbang Selesai Tertutup.");
}

/**
 * Nada Beep Sukses (1x nada tinggi)
 */
void beepSuccess() {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(120);
    digitalWrite(BUZZER_PIN, LOW);
}

/**
 * Nada Beep Ditolak (2x nada pendek peringatan)
 */
void beepDenied() {
    for (int i = 0; i < 2; i++) {
        digitalWrite(BUZZER_PIN, HIGH);
        delay(80);
        digitalWrite(BUZZER_PIN, LOW);
        delay(60);
    }
}
