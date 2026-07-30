<div align="center">

# 🏆 Yomuda Championship

**Automated Esports Tournament Management System**

Platform manajemen turnamen esports modern dengan sistem pendaftaran otomatis, pembayaran terintegrasi, dan dashboard administrasi yang powerful.

<br>

![Laravel](https://img.shields.io/badge/Laravel-F9322C?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-FFD700?style=for-the-badge&logo=javascript&logoColor=black)
![Payment Gateway](https://img.shields.io/badge/Payment-Gateway-success?style=for-the-badge)

<br>

🌐 **Official Website**  
https://yomudachamps.com

</div>

---

# 🎯 Tentang Yomuda Championship

**Yomuda Championship** adalah platform manajemen turnamen esports yang dirancang untuk mengotomatisasi seluruh proses penyelenggaraan kompetisi, mulai dari pendaftaran tim, pembayaran, validasi slot, hingga pengelolaan data peserta.

Sistem ini dibuat untuk memberikan pengalaman registrasi yang cepat, aman, dan profesional bagi peserta sekaligus memberikan kontrol penuh kepada administrator dalam mengelola jalannya turnamen.

Dibangun menggunakan Laravel dengan integrasi payment gateway modern, Yomuda Championship menghadirkan sistem yang scalable, reliable, dan siap digunakan untuk berbagai skala turnamen esports.

---

# ✨ Fitur

## 🏆 Tournament Registration

- Pendaftaran tim secara online
- Form registrasi otomatis
- Validasi data peserta
- Sistem manajemen slot real-time
- Status registrasi otomatis
- Countdown pembayaran

---

## 💳 Multi Payment Gateway

Yomuda Championship mendukung beberapa payment gateway untuk memberikan fleksibilitas pembayaran kepada peserta.

### 🔹 Tripay Gateway

- QRIS
- E-Wallet
- Virtual Account
- Otomatisasi verifikasi pembayaran
- Webhook transaction callback

### 🔹 iPaymu Gateway

- Digital payment processing
- Transaction monitoring
- Payment status synchronization

### 🔹 DIPS Gateway

- Alternative payment processing
- Automated transaction handling
- Payment verification system

Seluruh transaksi diproses melalui sistem webhook sehingga status pembayaran dapat diperbarui secara otomatis dan akurat.

---

## 🛡️ Security & Transaction Handling

### Race Condition Protection

Sistem menggunakan validasi berlapis untuk memastikan:

- Tidak terjadi double registration
- Slot tidak melebihi kapasitas
- Transaksi tidak terduplikasi
- Status pembayaran tetap konsisten

### Payment Verification

- Webhook validation
- Transaction status checking
- Automatic registration activation
- Secure payment flow

---

## 📱 WhatsApp Integration

Mempermudah komunikasi antara panitia dan peserta:

- Generate format nomor peserta otomatis
- Link WhatsApp langsung
- Informasi registrasi tim
- Notifikasi status pembayaran

---

## 👨‍💼 Administration Dashboard

Dashboard admin memberikan kontrol penuh terhadap:

- Manajemen turnamen
- Pengaturan biaya pendaftaran
- Pengelolaan slot
- Monitoring peserta
- Monitoring transaksi
- Statistik pendapatan
- Konfigurasi periode pendaftaran

---

## 📧 Smart Notification

Sistem notifikasi otomatis:

- Konfirmasi pembayaran berhasil
- Update status registrasi
- Informasi transaksi
- Laporan aktivitas sistem

---

# ⚡ Highlights

- ⚡ Automated Tournament Registration
- 💳 Multi Payment Gateway Support
- 🔒 Secure Transaction Processing
- 🏟️ Real-time Slot Management
- 📊 Complete Admin Dashboard
- 📱 WhatsApp Integration
- 🚀 Fast & Responsive Interface
- 🏗️ Scalable Backend Architecture

---

# 🏗️ Architecture

Yomuda Championship menggunakan arsitektur backend Laravel dengan integrasi API eksternal untuk payment gateway.

Alur utama sistem:

```text
                 User
                  │
                  ▼
        Tournament Registration
                  │
                  ▼
          Payment Selection
                  │
        ┌─────────┼─────────┐
        │         │         │
     Tripay    iPaymu    DIPS
        │         │         │
        └─────────┼─────────┘
                  │
                  ▼
          Webhook Verification
                  │
                  ▼
        Transaction Processing
                  │
                  ▼
          Slot Activation
                  │
                  ▼
          Admin Dashboard
