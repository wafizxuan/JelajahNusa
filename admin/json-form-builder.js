/**
 * JelajahNusa Admin — JSON Form Builder v2
 *
 * Mengganti 6 textarea JSON di form tambah/edit destinasi dengan panel
 * visual interaktif. Admin tidak perlu menulis atau memahami JSON.
 *
 * Mekanisme:
 * 1. Setiap textarea target disembunyikan (display:none).
 * 2. Panel visual disisipkan tepat setelahnya.
 * 3. Setiap perubahan input → serialize ke JSON → isi textarea tersembunyi.
 * 4. Saat form submit → satu serialize terakhir memastikan data ter-update.
 * 5. Jika textarea sudah berisi JSON yang valid saat halaman dimuat
 *    (mode edit), data itu dipakai untuk mengisi field visual.
 *
 * Fallback keamanan:
 * - Jika textarea kosong saat submit, backend PHP mempertahankan nilai lama
 *   via logika `$_POST['field'] ?: $existingValue` (lihat catatan di PHP).
 * - Struktur JSON yang dihasilkan selalu konsisten dengan yang sudah ada
 *   di database — tidak ada field yang ditambah/dihapus.
 */

(function () {
  'use strict';

  /* =========================================================================
     UTILITAS DASAR
  ========================================================================= */

  /** Buat elemen DOM dengan atribut shorthand */
  function el(tag, attrs) {
    const e = document.createElement(tag);
    if (!attrs) return e;
    Object.entries(attrs).forEach(([k, v]) => {
      if (k === 'class')     e.className   = v;
      else if (k === 'html') e.innerHTML   = v;
      else if (k === 'text') e.textContent = v;
      else                   e.setAttribute(k, v);
    });
    return e;
  }

  /** Parse JSON dengan aman, kembalikan fallback jika gagal */
  function safeJson(str, fallback) {
    if (!str || str.trim() === '') return fallback;
    try {
      const p = JSON.parse(str);
      return (p !== null && p !== undefined) ? p : fallback;
    } catch (_) {
      return fallback;
    }
  }

  /** Bungkus child dalam kolom Bootstrap */
  function col(xs, md, child) {
    const c = el('div', { class: `col-${xs} col-${md}` });
    c.appendChild(child);
    return c;
  }

  /** Buat wrapper label + input/textarea */
  function field(labelText, fieldName, value, placeholder, isRequired, type) {
    const wrap = el('div', { class: 'mb-2' });

    const lbl = el('label', {
      class: 'form-label small fw-semibold text-dark mb-1',
      text:  labelText + (isRequired ? ' *' : '')
    });
    wrap.appendChild(lbl);

    if (type === 'textarea') {
      const ta = el('textarea', {
        class:            'form-control form-control-sm jfb-input',
        'data-field':     fieldName,
        placeholder:      placeholder || '',
        rows:             '2',
      });
      ta.textContent = value || '';
      wrap.appendChild(ta);
    } else {
      const inp = el('input', {
        type:         type || 'text',
        class:        'form-control form-control-sm jfb-input',
        'data-field': fieldName,
        placeholder:  placeholder || '',
        value:        value || '',
      });
      wrap.appendChild(inp);
    }
    return wrap;
  }

  /** Buat header item-card dengan badge nomor + tombol hapus */
  function itemCard(index, onRemove) {
    const card = el('div', {
      class:            'jfb-item-card rounded-3 border p-3 mb-3',
      'data-item-index': String(index),
    });

    const hdr  = el('div', { class: 'd-flex justify-content-between align-items-center mb-2' });
    const badge = el('span', { class: 'jfb-item-badge badge rounded-pill', text: `Item ${index + 1}` });
    const del   = el('button', {
      type:  'button',
      class: 'btn btn-outline-danger btn-sm rounded-pill px-2 py-0',
      html:  '<i class="bi bi-trash3"></i> Hapus',
    });
    del.addEventListener('click', onRemove);
    hdr.appendChild(badge);
    hdr.appendChild(del);
    card.appendChild(hdr);
    return card;
  }

  /** Tombol "+ Tambah ..." */
  function addBtn(label, onClick) {
    const btn = el('button', {
      type:  'button',
      class: 'btn btn-outline-forest btn-sm rounded-pill px-3 mt-2',
      html:  `<i class="bi bi-plus-circle me-1"></i>${label}`,
    });
    btn.addEventListener('click', onClick);
    return btn;
  }

  /**
   * Buat panel visual dan sembunyikan textarea asli.
   * Kembalikan { panel, list } di mana list = container kartu item.
   */
  function createPanel(textarea, title, icon) {
    textarea.style.display = 'none';
    textarea.setAttribute('aria-hidden', 'true');

    const panel = el('div', { class: 'jfb-panel rounded-4 border mb-2 p-3' });
    const header = el('div', { class: 'jfb-panel-header d-flex align-items-center gap-2 mb-3' });
    header.innerHTML = `<i class="bi ${icon} text-success fs-5"></i><span class="fw-bold small text-dark">${title}</span>`;
    panel.appendChild(header);

    const list = el('div', { class: 'jfb-list' });
    panel.appendChild(list);

    // Sisipkan panel tepat setelah textarea
    textarea.parentNode.insertBefore(panel, textarea.nextSibling);
    return { panel, list };
  }

  /** Perbarui badge nomor item setelah hapus */
  function reindex(list) {
    list.querySelectorAll('.jfb-item-card').forEach((c, i) => {
      c.dataset.itemIndex = i;
      const b = c.querySelector('.jfb-item-badge');
      if (b) b.textContent = `Item ${i + 1}`;
    });
  }

  /** Daftar semua fungsi serialize yang aktif, dipanggil saat submit */
  const _syncFns = [];

  /** Jalankan semua sync sebelum submit */
  function syncAll() {
    _syncFns.forEach(fn => { try { fn(); } catch (_) {} });
  }

  /* =========================================================================
     1. DAYA TARIK UTAMA
        Struktur: [{ judul, desc, icon }, ...]
  ========================================================================= */
  function buildDayaTarik(textarea) {
    const data          = safeJson(textarea.value, []);
    const { panel, list } = createPanel(textarea, 'Daya Tarik Utama', 'bi-gem');
    const initial       = Array.isArray(data) && data.length ? data : [{}];

    function serialize() {
      textarea.value = JSON.stringify(
        Array.from(list.querySelectorAll('.jfb-item-card')).map(c => ({
          judul: c.querySelector('[data-field="judul"]')?.value.trim() || '',
          desc:  c.querySelector('[data-field="desc"]')?.value.trim()  || '',
          icon:  c.querySelector('[data-field="icon"]')?.value.trim()  || 'bi-water',
        }))
      );
    }

    function addItem(d) {
      const idx  = list.querySelectorAll('.jfb-item-card').length;
      const card = itemCard(idx, () => { card.remove(); reindex(list); serialize(); });
      const row  = el('div', { class: 'row g-2' });

      row.appendChild(col(12, 'md-5', field('Judul Daya Tarik', 'judul', d.judul, 'Contoh: Panorama Keindahan Alam', true)));
      row.appendChild(col(12, 'md-5', field('Deskripsi', 'desc', d.desc, 'Kalimat penjelas singkat', false, 'textarea')));

      // Ikon dengan preview
      const iconWrap = el('div', { class: 'col-12 col-md-2 mb-2' });
      iconWrap.appendChild(el('label', { class: 'form-label small fw-semibold text-dark mb-1', text: 'Ikon' }));
      const iconRow    = el('div', { class: 'd-flex gap-1 align-items-center' });
      const iconInp    = el('input', { type: 'text', class: 'form-control form-control-sm jfb-input', 'data-field': 'icon', value: d.icon || 'bi-water', placeholder: 'bi-water' });
      const iconPreview = el('i',   { class: `bi ${d.icon || 'bi-water'} text-success fs-5` });
      iconInp.addEventListener('input', () => { iconPreview.className = `bi ${iconInp.value} text-success fs-5`; });
      iconRow.appendChild(iconInp);
      iconRow.appendChild(iconPreview);
      iconWrap.appendChild(iconRow);
      iconWrap.appendChild(el('small', { class: 'text-muted d-block', text: 'icons.getbootstrap.com' }));
      row.appendChild(iconWrap);

      card.appendChild(row);
      list.appendChild(card);
      serialize();
    }

    initial.forEach(d => addItem(d));
    list.addEventListener('input', serialize);
    panel.appendChild(addBtn('Tambah Daya Tarik', () => addItem({})));
    _syncFns.push(serialize);
  }

  /* =========================================================================
     2. TEMPAT WISATA POPULER
        Struktur: [{ id, nama, lokasi, foto, tag, deskripsi }, ...]
  ========================================================================= */
  function buildPopularPlaces(textarea) {
    const data          = safeJson(textarea.value, []);
    const { panel, list } = createPanel(textarea, 'Tempat Wisata Populer', 'bi-pin-map-fill');
    const initial       = Array.isArray(data) && data.length ? data : [{}];

    function serialize() {
      textarea.value = JSON.stringify(
        Array.from(list.querySelectorAll('.jfb-item-card')).map(c => {
          const nama = c.querySelector('[data-field="nama"]')?.value.trim() || '';
          const id   = c.querySelector('[data-field="id"]')?.value.trim()
                       || nama.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
                       || ('place-' + Date.now());
          return {
            id,
            nama,
            lokasi:    c.querySelector('[data-field="lokasi"]')?.value.trim()    || '',
            foto:      c.querySelector('[data-field="foto"]')?.value.trim()      || '',
            tag:       c.querySelector('[data-field="tag"]')?.value.trim()       || '',
            deskripsi: c.querySelector('[data-field="deskripsi"]')?.value.trim() || '',
          };
        })
      );
    }

    function addItem(d) {
      const idx  = list.querySelectorAll('.jfb-item-card').length;
      const card = itemCard(idx, () => { card.remove(); reindex(list); serialize(); });
      const row  = el('div', { class: 'row g-2' });

      row.appendChild(col(12, 'md-4', field('Nama Tempat', 'nama', d.nama, 'Contoh: Spot Utama & Gardu Pandang', true)));
      row.appendChild(col(12, 'md-4', field('Lokasi / Area', 'lokasi', d.lokasi, 'Contoh: Kawasan Pesisir', true)));
      row.appendChild(col(12, 'md-4', field('Label / Tag', 'tag', d.tag, 'Contoh: Ikon Wisata')));
      row.appendChild(col(12, 'md-8', field('URL Foto (800px)', 'foto', d.foto, 'https://images.unsplash.com/...')));
      row.appendChild(col(12, 'md-4', field('ID Slug (otomatis)', 'id', d.id, 'spot-utama')));
      row.appendChild(col(12, 'md-12', field('Deskripsi', 'deskripsi', d.deskripsi, 'Kalimat deskriptif tempat ini', false, 'textarea')));

      // Auto-fill ID dari nama
      const namaInp = card.querySelector('[data-field="nama"]');
      const idInp   = card.querySelector('[data-field="id"]');
      if (namaInp && idInp) {
        namaInp.addEventListener('input', () => {
          if (!idInp._manual) {
            idInp.value = namaInp.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
          }
        });
        idInp.addEventListener('input', () => { idInp._manual = true; });
      }

      card.appendChild(row);
      list.appendChild(card);
      serialize();
    }

    initial.forEach(d => addItem(d));
    list.addEventListener('input', serialize);
    panel.appendChild(addBtn('Tambah Tempat Wisata', () => addItem({})));
    _syncFns.push(serialize);
  }

  /* =========================================================================
     3. REKOMENDASI PENGINAPAN
        Struktur: [{ id, nama, lokasi, jenis, harga, periode, foto, fasilitas }, ...]
  ========================================================================= */
  function buildRecommendedStays(textarea) {
    const data          = safeJson(textarea.value, []);
    const { panel, list } = createPanel(textarea, 'Rekomendasi Penginapan', 'bi-house-heart-fill');
    const initial       = Array.isArray(data) && data.length ? data : [{}];

    function serialize() {
      textarea.value = JSON.stringify(
        Array.from(list.querySelectorAll('.jfb-item-card')).map(c => {
          const nama = c.querySelector('[data-field="nama"]')?.value.trim() || '';
          return {
            id:        nama.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || ('stay-' + Date.now()),
            nama,
            lokasi:    c.querySelector('[data-field="lokasi"]')?.value.trim()    || '',
            jenis:     c.querySelector('[data-field="jenis"]')?.value.trim()     || '',
            harga:     c.querySelector('[data-field="harga"]')?.value.trim()     || '',
            periode:   c.querySelector('[data-field="periode"]')?.value.trim()   || '/ malam',
            foto:      c.querySelector('[data-field="foto"]')?.value.trim()      || '',
            fasilitas: c.querySelector('[data-field="fasilitas"]')?.value.trim() || '',
          };
        })
      );
    }

    function addItem(d) {
      const idx  = list.querySelectorAll('.jfb-item-card').length;
      const card = itemCard(idx, () => { card.remove(); reindex(list); serialize(); });
      const row  = el('div', { class: 'row g-2' });

      row.appendChild(col(12, 'md-5', field('Nama Penginapan', 'nama', d.nama, 'Contoh: Nusantara Eco Resort', true)));
      row.appendChild(col(12, 'md-4', field('Lokasi / Area', 'lokasi', d.lokasi, 'Contoh: Kawasan Pesisir', true)));
      row.appendChild(col(12, 'md-3', field('Jenis Akomodasi', 'jenis', d.jenis, 'Contoh: Eco Luxury Resort', true)));
      row.appendChild(col(12, 'md-3', field('Harga per Malam', 'harga', d.harga, 'Contoh: Rp 1.850.000')));
      row.appendChild(col(12, 'md-2', field('Periode', 'periode', d.periode || '/ malam', '/ malam')));
      row.appendChild(col(12, 'md-7', field('URL Foto (800px)', 'foto', d.foto, 'https://images.unsplash.com/...')));
      row.appendChild(col(12, 'md-12', field('Fasilitas (pisahkan •)', 'fasilitas', d.fasilitas, 'Contoh: Infinity View • Restoran Lokal')));

      card.appendChild(row);
      list.appendChild(card);
      serialize();
    }

    initial.forEach(d => addItem(d));
    list.addEventListener('input', serialize);
    panel.appendChild(addBtn('Tambah Penginapan', () => addItem({})));
    _syncFns.push(serialize);
  }

  /* =========================================================================
     4. KULINER KHAS AUTENTIK
        Struktur: [{ id, nama, jenis, lokasi, harga, foto, deskripsi }, ...]
  ========================================================================= */
  function buildLocalCulinary(textarea) {
    const data          = safeJson(textarea.value, []);
    const { panel, list } = createPanel(textarea, 'Kuliner Khas Autentik', 'bi-cup-hot-fill');
    const initial       = Array.isArray(data) && data.length ? data : [{}];

    function serialize() {
      textarea.value = JSON.stringify(
        Array.from(list.querySelectorAll('.jfb-item-card')).map((c, i) => ({
          id:        'kuliner-khas-' + (i + 1),
          nama:      c.querySelector('[data-field="nama"]')?.value.trim()      || '',
          jenis:     c.querySelector('[data-field="jenis"]')?.value.trim()     || '',
          lokasi:    c.querySelector('[data-field="lokasi"]')?.value.trim()    || '',
          harga:     c.querySelector('[data-field="harga"]')?.value.trim()     || '',
          foto:      c.querySelector('[data-field="foto"]')?.value.trim()      || '',
          deskripsi: c.querySelector('[data-field="deskripsi"]')?.value.trim() || '',
        }))
      );
    }

    function addItem(d) {
      const idx  = list.querySelectorAll('.jfb-item-card').length;
      const card = itemCard(idx, () => { card.remove(); reindex(list); serialize(); });
      const row  = el('div', { class: 'row g-2' });

      row.appendChild(col(12, 'md-4', field('Nama Kuliner', 'nama', d.nama, 'Contoh: Olahan Rempah Tradisional', true)));
      row.appendChild(col(12, 'md-4', field('Jenis / Kategori', 'jenis', d.jenis, 'Contoh: Makanan Pokok Tradisional', true)));
      row.appendChild(col(12, 'md-4', field('Lokasi Warung', 'lokasi', d.lokasi, 'Contoh: Sentra Kuliner Lokal')));
      row.appendChild(col(12, 'md-4', field('Kisaran Harga', 'harga', d.harga, 'Contoh: Rp 35.000 - Rp 65.000')));
      row.appendChild(col(12, 'md-8', field('URL Foto (800px)', 'foto', d.foto, 'https://images.unsplash.com/...')));
      row.appendChild(col(12, 'md-12', field('Deskripsi Kuliner', 'deskripsi', d.deskripsi, 'Kalimat deskriptif cita rasa & asal usul', false, 'textarea')));

      card.appendChild(row);
      list.appendChild(card);
      serialize();
    }

    initial.forEach(d => addItem(d));
    list.addEventListener('input', serialize);
    panel.appendChild(addBtn('Tambah Kuliner', () => addItem({})));
    _syncFns.push(serialize);
  }

  /* =========================================================================
     5. AKTIVITAS & PENGALAMAN (things_to_do)
        Struktur: [{ icon, judul, lokasi, durasi, deskripsi }, ...]
  ========================================================================= */
  function buildThingsToDo(textarea) {
    const data          = safeJson(textarea.value, []);
    const { panel, list } = createPanel(textarea, 'Aktivitas & Pengalaman Wisata', 'bi-compass-fill');
    const initial       = Array.isArray(data) && data.length ? data : [{}];

    function serialize() {
      textarea.value = JSON.stringify(
        Array.from(list.querySelectorAll('.jfb-item-card')).map(c => ({
          icon:      c.querySelector('[data-field="icon"]')?.value.trim()      || 'bi-compass',
          judul:     c.querySelector('[data-field="judul"]')?.value.trim()     || '',
          lokasi:    c.querySelector('[data-field="lokasi"]')?.value.trim()    || '',
          durasi:    c.querySelector('[data-field="durasi"]')?.value.trim()    || '',
          deskripsi: c.querySelector('[data-field="deskripsi"]')?.value.trim() || '',
        }))
      );
    }

    function addItem(d) {
      const idx  = list.querySelectorAll('.jfb-item-card').length;
      const card = itemCard(idx, () => { card.remove(); reindex(list); serialize(); });
      const row  = el('div', { class: 'row g-2' });

      row.appendChild(col(12, 'md-5', field('Judul Aktivitas', 'judul', d.judul, 'Contoh: Eksplorasi Jalur Panorama', true)));
      row.appendChild(col(12, 'md-4', field('Lokasi', 'lokasi', d.lokasi, 'Contoh: Area Spot Utama')));
      row.appendChild(col(12, 'md-3', field('Estimasi Durasi', 'durasi', d.durasi, 'Contoh: 2 - 3 Jam')));

      // Ikon dengan preview
      const iconWrap = el('div', { class: 'col-12 col-md-3 mb-2' });
      iconWrap.appendChild(el('label', { class: 'form-label small fw-semibold text-dark mb-1', text: 'Ikon Bootstrap' }));
      const iconRow    = el('div', { class: 'd-flex gap-1 align-items-center' });
      const iconInp    = el('input', { type: 'text', class: 'form-control form-control-sm jfb-input', 'data-field': 'icon', value: d.icon || 'bi-compass', placeholder: 'bi-compass' });
      const iconPrev   = el('i',   { class: `bi ${d.icon || 'bi-compass'} text-success fs-5` });
      iconInp.addEventListener('input', () => { iconPrev.className = `bi ${iconInp.value} text-success fs-5`; });
      iconRow.appendChild(iconInp);
      iconRow.appendChild(iconPrev);
      iconWrap.appendChild(iconRow);
      row.appendChild(iconWrap);

      row.appendChild(col(12, 'md-9', field('Deskripsi Aktivitas', 'deskripsi', d.deskripsi, 'Apa yang bisa dinikmati pengunjung', false, 'textarea')));

      card.appendChild(row);
      list.appendChild(card);
      serialize();
    }

    initial.forEach(d => addItem(d));
    list.addEventListener('input', serialize);
    panel.appendChild(addBtn('Tambah Aktivitas', () => addItem({})));
    _syncFns.push(serialize);
  }

  /* =========================================================================
     6. PANDUAN PERJALANAN / TRAVEL INFO
        Struktur: OBJECT dengan 4 key tetap:
          { biaya, transportasi, tips, musim }
          Masing-masing: { title, icon, items: string[] }

        PENTING: Struktur ini adalah object (bukan array).
        4 seksi adalah tetap — admin hanya mengedit isi items[].
  ========================================================================= */
  function buildTravelInfo(textarea) {
    textarea.style.display = 'none';
    textarea.setAttribute('aria-hidden', 'true');

    const rawData = safeJson(textarea.value, {});

    // 4 seksi tetap — key tidak boleh berubah karena backend dan detail-destinasi.php bergantung pada ini
    const SECTIONS = [
      { key: 'biaya',        label: 'Estimasi Biaya Harian',   defaultIcon: 'bi-cash-coin',     iconClass: 'text-success', ph: 'Contoh: Backpacker / Hemat: Rp 350.000 - Rp 600.000 / hari' },
      { key: 'transportasi', label: 'Pilihan Transportasi',    defaultIcon: 'bi-car-front',     iconClass: 'text-primary', ph: 'Contoh: Sewa Motor: Rp 75.000 - Rp 120.000 / hari' },
      { key: 'tips',         label: 'Tips Perjalanan Penting', defaultIcon: 'bi-shield-check',  iconClass: 'text-warning', ph: 'Contoh: Siapkan uang tunai yang cukup' },
      { key: 'musim',        label: 'Waktu Kunjungan Terbaik', defaultIcon: 'bi-calendar-check', iconClass: 'text-info',   ph: 'Contoh: Musim Cerah: Cuaca bersahabat untuk wisata luar ruangan' },
    ];

    const panel = el('div', { class: 'jfb-panel rounded-4 border mb-2 p-3' });
    const hdr   = el('div', { class: 'jfb-panel-header d-flex align-items-center gap-2 mb-3' });
    hdr.innerHTML = '<i class="bi bi-map-fill text-success fs-5"></i><span class="fw-bold small text-dark">Panduan Perjalanan — 4 Seksi (Biaya · Transportasi · Tips · Musim)</span>';
    panel.appendChild(hdr);

    const refs = {}; // { key: { titleInp, iconInp, itemsCont } }

    SECTIONS.forEach(sec => {
      const secData  = (rawData && rawData[sec.key]) ? rawData[sec.key] : {};
      const secItems = Array.isArray(secData.items) ? secData.items : [];

      const secCard = el('div', { class: 'jfb-ti-section rounded-3 border p-3 mb-3' });

      // Label seksi dengan ikon
      const secHdr = el('div', { class: 'd-flex align-items-center gap-2 mb-2' });
      secHdr.innerHTML = `<i class="bi ${sec.defaultIcon} ${sec.iconClass} fs-5"></i><strong class="small text-dark">${sec.label}</strong>`;
      secCard.appendChild(secHdr);

      // Input judul & ikon seksi (admin bisa ubah teks tampilan)
      const metaRow  = el('div', { class: 'row g-2 mb-2' });
      const titleInp = el('input', {
        type:        'text',
        class:       'form-control form-control-sm',
        value:       secData.title || sec.label,
        placeholder: 'Judul seksi',
      });
      const iconInp = el('input', {
        type:        'text',
        class:       'form-control form-control-sm',
        value:       secData.icon || sec.defaultIcon,
        placeholder: sec.defaultIcon,
      });
      const colT = el('div', { class: 'col-8' });
      const colI = el('div', { class: 'col-4' });
      colT.appendChild(el('label', { class: 'form-label small text-muted mb-1', text: 'Judul Seksi' }));
      colT.appendChild(titleInp);
      colI.appendChild(el('label', { class: 'form-label small text-muted mb-1', text: 'Ikon' }));
      colI.appendChild(iconInp);
      metaRow.appendChild(colT);
      metaRow.appendChild(colI);
      secCard.appendChild(metaRow);

      // Container baris-baris item
      const itemsCont = el('div', { class: 'jfb-ti-items' });
      secCard.appendChild(itemsCont);

      function addTiRow(text) {
        const row   = el('div', { class: 'input-group input-group-sm mb-1' });
        const inp   = el('input', { type: 'text', class: 'form-control jfb-ti-input', value: text || '', placeholder: sec.ph });
        const delB  = el('button', { type: 'button', class: 'btn btn-outline-danger', html: '<i class="bi bi-x"></i>' });
        delB.addEventListener('click', () => { row.remove(); serializeTi(); });
        row.appendChild(inp);
        row.appendChild(delB);
        itemsCont.appendChild(row);
      }

      // Render item yang sudah ada
      if (secItems.length > 0) {
        secItems.forEach(t => addTiRow(t));
      } else {
        addTiRow(''); // minimal 1 baris kosong
      }

      const addRowBtn = el('button', {
        type:  'button',
        class: 'btn btn-outline-secondary btn-sm rounded-pill px-2 mt-1 mb-1',
        html:  '<i class="bi bi-plus me-1"></i>Tambah Baris',
      });
      addRowBtn.addEventListener('click', () => addTiRow(''));
      secCard.appendChild(addRowBtn);
      panel.appendChild(secCard);

      refs[sec.key] = { titleInp, iconInp, itemsCont };
    });

    textarea.parentNode.insertBefore(panel, textarea.nextSibling);

    function serializeTi() {
      const result = {};
      SECTIONS.forEach(sec => {
        const r = refs[sec.key];
        result[sec.key] = {
          title: r.titleInp.value.trim() || sec.label,
          icon:  r.iconInp.value.trim()  || sec.defaultIcon,
          items: Array.from(r.itemsCont.querySelectorAll('.jfb-ti-input'))
                      .map(i => i.value.trim())
                      .filter(v => v !== ''),
        };
      });
      textarea.value = JSON.stringify(result);
    }

    panel.addEventListener('input', serializeTi);
    serializeTi(); // initial sync
    _syncFns.push(serializeTi);
  }

  /* =========================================================================
     CSS INLINE
  ========================================================================= */
  function injectCss() {
    if (document.getElementById('jfb-styles')) return;
    const s = document.createElement('style');
    s.id = 'jfb-styles';
    s.textContent = `
      .jfb-panel {
        background: #fafaf8;
        border-color: rgba(52,91,73,0.15) !important;
      }
      .jfb-panel-header {
        border-bottom: 1px solid rgba(52,91,73,0.1);
        padding-bottom: 8px;
        margin-bottom: 12px;
      }
      .jfb-item-card {
        background: #ffffff;
        border-color: rgba(52,91,73,0.12) !important;
        transition: box-shadow .15s;
      }
      .jfb-item-card:hover { box-shadow: 0 2px 12px rgba(52,91,73,0.08); }
      .jfb-item-badge {
        font-size: .72rem;
        background-color: rgba(52,91,73,0.12) !important;
        color: #345B49 !important;
      }
      .jfb-ti-section {
        background: #ffffff;
        border-color: rgba(52,91,73,0.1) !important;
      }
      .btn-outline-forest {
        color: #345B49 !important;
        border-color: #345B49 !important;
      }
      .btn-outline-forest:hover {
        background-color: #345B49 !important;
        color: #ffffff !important;
      }
    `;
    document.head.appendChild(s);
  }

  /* =========================================================================
     INISIALISASI
  ========================================================================= */
  function init() {
    injectCss();

    const dtEl = document.getElementById('daya_tarik');
    if (dtEl) buildDayaTarik(dtEl);

    const ppEl = document.getElementById('popular_places');
    if (ppEl) buildPopularPlaces(ppEl);

    const rsEl = document.getElementById('recommended_stays');
    if (rsEl) buildRecommendedStays(rsEl);

    const lcEl = document.getElementById('local_culinary');
    if (lcEl) buildLocalCulinary(lcEl);

    const ttEl = document.getElementById('things_to_do');
    if (ttEl) buildThingsToDo(ttEl);

    const tiEl = document.getElementById('travel_info');
    if (tiEl) buildTravelInfo(tiEl);

    // Satu event listener 'submit' per form — sync semua textarea tersembunyi
    document.querySelectorAll('form[method="POST"]').forEach(form => {
      // Gunakan capture phase (true) agar berjalan sebelum validasi browser
      form.addEventListener('submit', syncAll, true);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
