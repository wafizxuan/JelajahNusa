/**
 * JelajahNusa Admin — Google Place Picker
 *
 * Menambahkan panel pencarian & pencocokan Google Place ID
 * di form tambah dan edit destinasi.
 *
 * Semua request ke Google API dilakukan via server-side proxy
 * (auth/places_proxy.php) sehingga API key TIDAK terekspos ke browser.
 */

(function () {
  'use strict';

  const API_PATH = '../auth/places_proxy.php';

  // ─── Deteksi mode (tambah vs edit) ─────────────────────────────────────────
  const isEditPage = window.location.pathname.includes('destinasi_edit');

  // Ambil dest_id dari URL query string (hanya mode edit)
  function getDestId() {
    return new URLSearchParams(window.location.search).get('id') || '';
  }

  // ─── HTML panel ────────────────────────────────────────────────────────────
  function buildPanel(currentPlaceId) {
    const panel = document.createElement('div');
    panel.id        = 'gmp-panel';
    panel.className = 'content-card';
    panel.style.borderLeft = '4px solid #345B49';

    panel.innerHTML = `
      <div class="section-header-pill" style="background:rgba(52,91,73,0.08);color:#345B49;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 488 512" fill="currentColor" aria-hidden="true"><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>
        Pencocokan Google Maps Place
      </div>

      <p class="text-muted small mb-3">
        Cari dan pilih tempat yang sesuai di Google Maps. Rating dan ulasan akan diambil otomatis dari Google.
        Admin <strong>tidak bisa</strong> mengubah rating secara manual — hanya Google Maps yang menjadi sumber data.
      </p>

      <!-- Status Place ID saat ini -->
      <div id="gmp-current-status" class="alert ${currentPlaceId ? 'alert-success' : 'alert-secondary'} py-2 px-3 small mb-3 border-0 rounded-3">
        ${currentPlaceId
          ? `<i class="bi bi-check-circle-fill me-1"></i>Terhubung: <code>${currentPlaceId}</code>
             <button type="button" id="gmp-clear-btn" class="btn btn-sm btn-link text-danger p-0 ms-2">Hapus asosiasi</button>`
          : `<i class="bi bi-info-circle me-1"></i>Belum terhubung dengan Google Maps.`}
      </div>

      <!-- Form pencarian -->
      <div class="row g-2 mb-3">
        <div class="col">
          <input type="text" id="gmp-search-input" class="form-control"
                 placeholder="Contoh: Raja Ampat Papua, Gunung Bromo Jawa Timur..."
                 value="">
        </div>
        <div class="col-auto">
          <button type="button" id="gmp-search-btn" class="btn btn-forest">
            <i class="bi bi-search me-1"></i>Cari di Google Maps
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div id="gmp-loading" class="d-none text-center py-2">
        <span class="spinner-border spinner-border-sm text-success me-2"></span>Mencari tempat...
      </div>

      <!-- Error -->
      <div id="gmp-error" class="alert alert-danger py-2 px-3 small d-none border-0 rounded-3"></div>

      <!-- Disabled notice -->
      <div id="gmp-disabled-notice" class="alert alert-warning py-2 px-3 small d-none border-0 rounded-3">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Google Places API belum dikonfigurasi. Isi <code>config/places_api.php</code> dan aktifkan <code>GOOGLE_PLACES_ENABLED = true</code>.
        Pencocokan Place ID tetap bisa disimpan, tapi pencarian tidak tersedia.
      </div>

      <!-- Hasil pencarian -->
      <div id="gmp-results" class="d-none">
        <p class="small fw-semibold text-dark mb-2">Pilih tempat yang sesuai:</p>
        <div id="gmp-results-list"></div>
        <p class="text-muted small mt-2">
          <i class="bi bi-info-circle me-1"></i>
          Jika tidak ada yang cocok, coba kata kunci yang lebih spesifik (tambahkan nama kota atau provinsi).
        </p>
      </div>

      <!-- Tempat terpilih -->
      <div id="gmp-selected" class="d-none">
        <div class="alert alert-success py-2 px-3 border-0 rounded-3 d-flex justify-content-between align-items-start">
          <div>
            <strong class="d-block" id="gmp-selected-name"></strong>
            <span class="small text-muted" id="gmp-selected-address"></span><br>
            <span class="small"><i class="bi bi-star-fill text-warning me-1"></i><span id="gmp-selected-rating">—</span></span>
            <code class="small ms-3 text-muted" id="gmp-selected-id"></code>
          </div>
          <div class="d-flex flex-column gap-1 ms-2">
            <button type="button" id="gmp-save-btn" class="btn btn-success btn-sm rounded-pill px-3">
              <i class="bi bi-check2 me-1"></i>Simpan
            </button>
            <button type="button" id="gmp-cancel-btn" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
              Batal
            </button>
          </div>
        </div>
      </div>
    `;

    return panel;
  }

  // ─── Tampilkan kartu hasil pencarian ───────────────────────────────────────
  function renderResults(places, onSelect) {
    const list = document.getElementById('gmp-results-list');
    list.innerHTML = '';

    if (!places.length) {
      list.innerHTML = '<p class="text-muted small">Tidak ada tempat ditemukan. Coba kata kunci yang lebih spesifik.</p>';
      return;
    }

    places.forEach(place => {
      const card = document.createElement('div');
      card.className = 'border rounded-3 p-2 mb-2 bg-white d-flex justify-content-between align-items-center';
      card.innerHTML = `
        <div>
          <div class="fw-semibold small text-dark">${place.name}</div>
          <div class="text-muted" style="font-size:.78rem;">${place.address || ''}</div>
          ${place.rating ? `<span style="font-size:.75rem;"><i class="bi bi-star-fill text-warning me-1"></i>${place.rating} (${(place.user_ratings || 0).toLocaleString('id-ID')} ulasan)</span>` : ''}
        </div>
        <button type="button" class="btn btn-outline-forest btn-sm rounded-pill px-2 flex-shrink-0 ms-2">Pilih</button>
      `;
      card.querySelector('button').addEventListener('click', () => onSelect(place));
      list.appendChild(card);
    });
  }

  // ─── Main init ──────────────────────────────────────────────────────────────
  function init() {
    // Cari form destinasi di halaman
    const mainForm = document.querySelector('form[method="POST"]');
    if (!mainForm) return;

    // Ambil current place_id untuk mode edit
    let currentPlaceId = '';
    const hiddenField = mainForm.querySelector('input[name="google_place_id"]');
    if (hiddenField) currentPlaceId = hiddenField.value || '';

    // Sisipkan panel sebelum submit bar (setelah content-card terakhir sebelum tombol)
    const submitBar = mainForm.querySelector('.d-flex.justify-content-between.align-items-center.pb-5');
    if (!submitBar) return;

    const panel = buildPanel(currentPlaceId);
    mainForm.insertBefore(panel, submitBar);

    // ── State ────────────────────────────────────────────────────────────────
    let selectedPlace  = null;
    const destId       = getDestId();

    // ── Cek apakah API tersedia ───────────────────────────────────────────────
    fetch(API_PATH + '?action=search&q=test', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .catch(() => null)
      .then(d => {
        if (d && d.disabled) {
          document.getElementById('gmp-disabled-notice').classList.remove('d-none');
        }
      });

    // ── Tombol hapus asosiasi ─────────────────────────────────────────────────
    const clearBtn = document.getElementById('gmp-clear-btn');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        if (!confirm('Hapus asosiasi Google Maps dari destinasi ini?')) return;
        if (!destId) {
          // Mode tambah: cukup kosongkan hidden field
          updateHidden('');
          document.getElementById('gmp-current-status').className = 'alert alert-secondary py-2 px-3 small mb-3 border-0 rounded-3';
          document.getElementById('gmp-current-status').innerHTML = '<i class="bi bi-info-circle me-1"></i>Belum terhubung dengan Google Maps.';
          return;
        }
        savePlaceId('', destId, (ok) => {
          if (ok) {
            document.getElementById('gmp-current-status').className = 'alert alert-secondary py-2 px-3 small mb-3 border-0 rounded-3';
            document.getElementById('gmp-current-status').innerHTML = '<i class="bi bi-info-circle me-1"></i>Belum terhubung dengan Google Maps.';
            updateHidden('');
          }
        });
      });
    }

    // ── Tombol cari ───────────────────────────────────────────────────────────
    const searchBtn   = document.getElementById('gmp-search-btn');
    const searchInput = document.getElementById('gmp-search-input');
    const loadingEl   = document.getElementById('gmp-loading');
    const errorEl     = document.getElementById('gmp-error');
    const resultsEl   = document.getElementById('gmp-results');
    const selectedEl  = document.getElementById('gmp-selected');

    function doSearch() {
      const q = searchInput.value.trim();
      if (!q || q.length < 3) {
        errorEl.textContent = 'Masukkan minimal 3 karakter untuk pencarian.';
        errorEl.classList.remove('d-none');
        return;
      }
      errorEl.classList.add('d-none');
      resultsEl.classList.add('d-none');
      selectedEl.classList.add('d-none');
      loadingEl.classList.remove('d-none');

      fetch(`${API_PATH}?action=search&q=${encodeURIComponent(q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .catch(() => null)
        .then(data => {
          loadingEl.classList.add('d-none');
          if (!data || !data.success) {
            errorEl.textContent = data?.message || 'Gagal mengambil hasil pencarian.';
            errorEl.classList.remove('d-none');
            return;
          }
          renderResults(data.places || [], onPlaceSelect);
          resultsEl.classList.remove('d-none');
        });
    }

    searchBtn.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

    // ── Pilih tempat dari hasil ────────────────────────────────────────────────
    function onPlaceSelect(place) {
      selectedPlace = place;
      resultsEl.classList.add('d-none');
      document.getElementById('gmp-selected-name').textContent    = place.name;
      document.getElementById('gmp-selected-address').textContent = place.address || '';
      document.getElementById('gmp-selected-rating').textContent  = place.rating ? `${place.rating} (${(place.user_ratings || 0).toLocaleString('id-ID')} ulasan)` : 'Belum ada rating';
      document.getElementById('gmp-selected-id').textContent      = place.place_id;
      selectedEl.classList.remove('d-none');
    }

    // ── Simpan pilihan ─────────────────────────────────────────────────────────
    document.getElementById('gmp-save-btn').addEventListener('click', () => {
      if (!selectedPlace) return;
      const pid = selectedPlace.place_id;

      if (destId) {
        // Mode edit: simpan langsung via AJAX
        savePlaceId(pid, destId, (ok) => {
          if (ok) {
            updateHidden(pid);
            document.getElementById('gmp-current-status').className = 'alert alert-success py-2 px-3 small mb-3 border-0 rounded-3';
            document.getElementById('gmp-current-status').innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>Terhubung: <code>${pid}</code>`;
            selectedEl.classList.add('d-none');
            showNotif('✅ Place ID berhasil disimpan!');
          }
        });
      } else {
        // Mode tambah: simpan ke hidden field, akan di-insert bersama form
        updateHidden(pid);
        document.getElementById('gmp-current-status').className = 'alert alert-success py-2 px-3 small mb-3 border-0 rounded-3';
        document.getElementById('gmp-current-status').innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>Terpilih: <code>${pid}</code>`;
        selectedEl.classList.add('d-none');
        showNotif('✅ Tempat dipilih. Place ID akan disimpan saat form dikirim.');
      }
    });

    document.getElementById('gmp-cancel-btn').addEventListener('click', () => {
      selectedPlace = null;
      selectedEl.classList.add('d-none');
    });
  }

  // ─── Helper: update hidden input google_place_id di form ───────────────────
  function updateHidden(placeId) {
    const mainForm = document.querySelector('form[method="POST"]');
    if (!mainForm) return;
    let inp = mainForm.querySelector('input[name="google_place_id"]');
    if (!inp) {
      inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'google_place_id';
      mainForm.appendChild(inp);
    }
    inp.value = placeId;
  }

  // ─── Helper: simpan place_id via AJAX ───────────────────────────────────────
  function savePlaceId(placeId, destId, callback) {
    const fd = new FormData();
    fd.append('action',   'save_place_id');
    fd.append('dest_id',  destId);
    fd.append('place_id', placeId);

    fetch(API_PATH, {
      method: 'POST',
      body:   fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then(r => r.json())
      .catch(() => null)
      .then(d => {
        if (d && d.success) {
          callback(true);
        } else {
          alert(d?.message || 'Gagal menyimpan Place ID.');
          callback(false);
        }
      });
  }

  // ─── Helper: toast notifikasi ───────────────────────────────────────────────
  function showNotif(msg) {
    let box = document.querySelector('.nature-toast-box');
    if (!box) {
      box = document.createElement('div');
      box.className = 'nature-toast-box';
      document.body.appendChild(box);
    }
    const t = document.createElement('div');
    t.className = 'nature-toast';
    t.innerHTML = `<i class="bi bi-check-circle-fill text-success me-2"></i><span>${msg}</span>`;
    box.appendChild(t);
    setTimeout(() => {
      t.style.opacity   = '0';
      t.style.transform = 'translateY(10px)';
      setTimeout(() => t.remove(), 400);
    }, 3000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
