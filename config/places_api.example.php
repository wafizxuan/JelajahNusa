<?php
/**
 * JelajahNusa - Konfigurasi Google Places API (New)
 * ─────────────────────────────────────────────────────────────────
 * INSTRUKSI SETUP:
 *
 * 1. Salin file ini: cp config/places_api.example.php config/places_api.php
 * 2. Isi GOOGLE_PLACES_API_KEY dengan API key dari Google Cloud Console.
 * 3. JANGAN commit config/places_api.php ke repository (sudah ada di .gitignore).
 *
 * Cara mendapatkan API key:
 *   a. Buka https://console.cloud.google.com/
 *   b. Buat project baru atau pilih yang sudah ada.
 *   c. Aktifkan "Places API (New)" di Library.
 *   d. Buat API Key di Credentials.
 *   e. Batasi key: Application restrictions → HTTP referrers → domain Anda.
 *   f. Batasi key: API restrictions → Places API (New).
 *   g. Pastikan billing diaktifkan (Places API memerlukan billing).
 *
 * Kuota estimasi:
 *   - Text Search (New): $17 per 1.000 request
 *   - Place Details (New): $17 per 1.000 request
 *   - $200 kredit gratis per bulan dari Google (berlaku untuk akun baru).
 *
 * Kebijakan caching Google Places:
 *   - Data Places (rating, ulasan) TIDAK boleh di-cache lebih dari 30 hari.
 *   - Place ID BOLEH disimpan permanen di database.
 *   - Atribusi Google Maps WAJIB ditampilkan.
 */

// API Key Google Places (New) — GANTI dengan key Anda
define('GOOGLE_PLACES_API_KEY', 'YOUR_GOOGLE_PLACES_API_KEY_HERE');

// Base URL Places API (New) — jangan diubah
define('GOOGLE_PLACES_BASE_URL', 'https://places.googleapis.com/v1');

// Aktifkan/matikan integrasi Places API
// Jika false, halaman tetap tampil normal tanpa data rating dari Google
define('GOOGLE_PLACES_ENABLED', false); // Set ke true setelah API key tersedia

// Timeout HTTP request ke Google API (detik)
define('GOOGLE_PLACES_TIMEOUT', 8);
