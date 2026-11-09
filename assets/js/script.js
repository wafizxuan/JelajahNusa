/**
 * JelajahNusa - Nature-Inspired Travel Guide & Planner
 * Frontend JavaScript Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const navbar = document.querySelector('.custom-navbar');
  const backToTopBtn = document.getElementById('backToTopBtn');
  const heroSearchForm = document.getElementById('heroSearchForm');
  const searchInput = document.getElementById('heroSearchInput');
  const categorySelect = document.getElementById('heroCategorySelect');
  const destinationCards = document.querySelectorAll('.destination-card-col');
  const noDestinationsNotice = document.getElementById('noDestinationsNotice');

  // ========================================================================
  // 1. Sticky Navbar on Scroll
  // ========================================================================
  const handleScroll = () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
      if (backToTopBtn) backToTopBtn.classList.add('active');
    } else {
      navbar.classList.remove('scrolled');
      if (backToTopBtn) backToTopBtn.classList.remove('active');
    }
  };

  window.addEventListener('scroll', handleScroll);
  handleScroll();

  // ========================================================================
  // 2. Back To Top
  // ========================================================================
  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // ========================================================================
  // 3. Active Nav Link on Scroll
  // ========================================================================
  const sections = document.querySelectorAll('header[id], section[id]');
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

  const highlightNavLink = () => {
    const scrollY = window.pageYOffset;

    sections.forEach((current) => {
      const sectionHeight = current.offsetHeight;
      const sectionTop = current.offsetTop - 130;
      const sectionId = current.getAttribute('id');

      if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
        navLinks.forEach((link) => {
          link.classList.remove('active');
          if (link.getAttribute('href') === `#${sectionId}`) {
            link.classList.add('active');
          }
        });
      }
    });
  };

  window.addEventListener('scroll', highlightNavLink);

  // Close mobile navbar on link click
  const navCollapse = document.getElementById('navbarContent');
  navLinks.forEach((link) => {
    link.addEventListener('click', () => {
      if (navCollapse && navCollapse.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
        if (bsCollapse) bsCollapse.hide();
      }
    });
  });

  // ========================================================================
  // 4. Destination Search & Filter
  // ========================================================================
  const filterDestinations = (keyword = '', category = '') => {
    const cleanKeyword = keyword.trim().toLowerCase();
    const cleanCategory = category.trim().toLowerCase();
    let visibleCount = 0;

    destinationCards.forEach((card) => {
      const title = card.getAttribute('data-title')?.toLowerCase() || '';
      const location = card.getAttribute('data-location')?.toLowerCase() || '';
      const cardCategory = card.getAttribute('data-category')?.toLowerCase() || '';
      const tags = card.getAttribute('data-tags')?.toLowerCase() || '';

      const matchesKeyword =
        !cleanKeyword ||
        title.includes(cleanKeyword) ||
        location.includes(cleanKeyword) ||
        tags.includes(cleanKeyword);

      const matchesCategory =
        !cleanCategory ||
        cleanCategory === 'semua' ||
        cardCategory === cleanCategory ||
        cardCategory.includes(cleanCategory);

      if (matchesKeyword && matchesCategory) {
        card.style.display = 'block';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (noDestinationsNotice) {
      if (visibleCount === 0) {
        noDestinationsNotice.classList.remove('d-none');
      } else {
        noDestinationsNotice.classList.add('d-none');
      }
    }
  };

  // Search form submit
  if (heroSearchForm) {
    heroSearchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const keyword = searchInput ? searchInput.value : '';
      const category = categorySelect ? categorySelect.value : '';

      filterDestinations(keyword, category);

      const destSection = document.getElementById('destinasi');
      if (destSection) {
        destSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  // Live filter on search input
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const keyword = searchInput.value;
      const category = categorySelect ? categorySelect.value : '';
      filterDestinations(keyword, category);
    });
  }

  // Category select change
  if (categorySelect) {
    categorySelect.addEventListener('change', () => {
      const keyword = searchInput ? searchInput.value : '';
      const category = categorySelect.value;
      filterDestinations(keyword, category);
    });
  }

  // Reset filter button
  const resetDestBtn = document.getElementById('resetDestFilterBtn');
  if (resetDestBtn) {
    resetDestBtn.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      if (categorySelect) categorySelect.value = '';
      filterDestinations('', '');
    });
  }

  // Quick tag links
  const quickTags = document.querySelectorAll('.hero-quick-tag');
  quickTags.forEach((tag) => {
    tag.addEventListener('click', (e) => {
      e.preventDefault();
      const searchVal = tag.getAttribute('data-search') || tag.innerText.trim();
      if (searchInput) searchInput.value = searchVal;
      if (categorySelect) categorySelect.value = '';
      filterDestinations(searchVal, '');

      const destSection = document.getElementById('destinasi');
      if (destSection) {
        destSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // "Lihat Semua Destinasi" button action
  const viewAllDestBtn = document.getElementById('viewAllDestBtn');
  if (viewAllDestBtn) {
    viewAllDestBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (searchInput) searchInput.value = '';
      if (categorySelect) categorySelect.value = '';
      filterDestinations('', '');
      
      const destSection = document.getElementById('destinasi');
      if (destSection) {
        destSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  }

  // Category cards click (Triggers filter by category)
  const categoryCards = document.querySelectorAll('.category-card-trigger');
  categoryCards.forEach((card) => {
    card.addEventListener('click', (e) => {
      e.preventDefault();
      const categoryId = card.getAttribute('data-category-id') || '';
      if (categorySelect) {
        categorySelect.value = categoryId;
      }
      if (searchInput) {
        searchInput.value = '';
      }
      filterDestinations('', categoryId);

      const destSection = document.getElementById('destinasi');
      if (destSection) {
        destSection.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // ========================================================================
  // 5. Rekomendasi Tabs (Stay, Culinary, Experiences)
  // ========================================================================
  const recTabs = document.querySelectorAll('.rekomendasi-tab-btn');
  const recCards = document.querySelectorAll('.recommendation-card-col');

  recTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      recTabs.forEach((t) => t.classList.remove('active'));
      tab.classList.add('active');

      const targetCategory = tab.getAttribute('data-category');

      recCards.forEach((card) => {
        const cardType = card.getAttribute('data-type');
        if (targetCategory === 'all' || cardType === targetCategory) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ========================================================================
  // 6. Interactive Travel Planner Modal
  // ========================================================================
  const plannerForm = document.getElementById('plannerBuilderForm');
  if (plannerForm) {
    plannerForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const destination = document.getElementById('planDestSelect').value;
      const days = document.getElementById('planDaysInput').value;
      const travelerType = document.getElementById('planTypeSelect').value;

      // Update mockup text live in the Travel Planner section
      const mockupTitle = document.getElementById('mockupTripTitle');
      const mockupMeta = document.getElementById('mockupTripMeta');

      if (mockupTitle) {
        mockupTitle.textContent = `Rencana Liburan ${days} Hari: Eksplorasi ${destination}`;
      }
      if (mockupMeta) {
        mockupMeta.textContent = `${days} Hari • Kategori: ${formatTravelerCategory(travelerType)} • Rekomendasi Terpadu`;
      }

      // Close modal
      const modalEl = document.getElementById('plannerModal');
      if (modalEl) {
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();
      }

      // Smooth scroll to Travel Planner section
      const plannerSection = document.getElementById('planner');
      if (plannerSection) {
        plannerSection.scrollIntoView({ behavior: 'smooth' });
      }

      showToast(`Jadwal perjalanan ke ${destination} selama ${days} hari untuk kategori ${formatTravelerCategory(travelerType)} berhasil diperbarui!`);
    });
  }

  // ========================================================================
  // 7. Destination Detail Modal
  // ========================================================================
  const detailModal = document.getElementById('destinationDetailModal');
  if (detailModal) {
    detailModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      if (!button) return;

      const title = button.getAttribute('data-title');
      const location = button.getAttribute('data-location');
      const category = button.getAttribute('data-category');
      const rating = button.getAttribute('data-rating');
      const image = button.getAttribute('data-image');
      const description = button.getAttribute('data-description');
      const highlights = button.getAttribute('data-highlights');

      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalLocation').textContent = location;
      document.getElementById('modalCategory').textContent = category;
      document.getElementById('modalRating').textContent = rating;
      document.getElementById('modalImage').src = image;
      document.getElementById('modalDescription').textContent = description;

      const highlightsContainer = document.getElementById('modalHighlights');
      highlightsContainer.innerHTML = '';
      if (highlights) {
        const tagList = highlights.split(',');
        tagList.forEach((tag) => {
          const badge = document.createElement('span');
          badge.className = 'badge bg-light text-success border me-1 mb-1 p-2 font-weight-normal';
          badge.textContent = `✓ ${tag.trim()}`;
          highlightsContainer.appendChild(badge);
        });
      }
    });
  }

  // ========================================================================
  // 8. Fitur Pengguna: Favorit Destinasi (Database-backed, Auth-gated)
  // ========================================================================

  /**
   * Sistem Favorit berbasis database dengan proteksi session PHP.
   *
   * - Jika belum login: klik tombol hati memunculkan notifikasi + link login.
   * - Jika sudah login: toggle add/remove via fetch ke auth/favorites.php.
   * - Status auth dibaca dari meta tag yang dirender PHP (tidak dari JS).
   * - ID destinasi favorit dibaca dari elemen <script type="application/json">
   *   yang dirender server saat halaman dimuat, sehingga state awal akurat.
   */

  // ─── Deteksi status login dari meta yang dicetak PHP ─────────────────────
  const isLoggedInMeta = document.querySelector('meta[name="jn-auth-status"]');
  const JN_LOGGED_IN   = isLoggedInMeta ? isLoggedInMeta.getAttribute('content') === 'true' : false;

  // ─── Deteksi path agar URL API relatif selalu benar ──────────────────────
  const isInsidePages = window.location.pathname.toLowerCase().includes('/pages/');
  const FAV_API_URL   = isInsidePages ? '../auth/favorites.php' : 'auth/favorites.php';

  // ─── Set ID favorit dari data yang dirender server ────────────────────────
  // Halaman index.php mengambil via API; halaman favorit.php sudah ada di DOM.
  let jnFavIds = new Set();

  const loadFavIdsFromServer = async () => {
    if (!JN_LOGGED_IN) return;
    try {
      const res  = await fetch(FAV_API_URL + '?action=ids', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json().catch(() => null);
      if (data && data.success && Array.isArray(data.ids)) {
        jnFavIds = new Set(data.ids.map(String));
        updateFavButtonStates();
      }
    } catch (_) { /* network error — tombol tetap dalam state default */ }
  };

  // Halaman favorit.php menyertakan data ID via inline JSON
  const favIdsScript = document.getElementById('jn-fav-ids-data');
  if (favIdsScript) {
    try {
      const ids = JSON.parse(favIdsScript.textContent);
      if (Array.isArray(ids)) jnFavIds = new Set(ids.map(String));
    } catch (_) {}
  }

  // ─── Update badge counter di navbar ──────────────────────────────────────
  const updateFavBadge = (count) => {
    const badge = document.getElementById('navFavCount');
    if (badge) badge.textContent = count;
  };

  // ─── Update state visual tombol hati berdasarkan jnFavIds ────────────────
  const updateFavButtonStates = () => {
    document.querySelectorAll('.btn-card-fav').forEach((btn) => {
      const id = btn.getAttribute('data-id') || btn.getAttribute('data-dest-id');
      if (!id) return;
      const isFav   = jnFavIds.has(String(id));
      const icon     = btn.querySelector('i');
      const textSpan = btn.querySelector('.detail-fav-text');

      btn.classList.toggle('active', isFav);
      if (icon) icon.className = isFav ? 'bi bi-heart-fill text-danger' : 'bi bi-heart';
      if (textSpan) textSpan.textContent = isFav ? 'Tersimpan di Favorit' : 'Simpan Favorit';
    });
  };

  // ─── Render list modal favorit (untuk #favoritesModal di index.php) ───────
  const renderFavModal = async () => {
    const container  = document.getElementById('favoritesListContainer');
    const emptyState = document.getElementById('favoritesEmptyState');
    if (!container || !emptyState) return;

    if (!JN_LOGGED_IN) {
      // Guest: tampilkan state "perlu login"
      emptyState.classList.remove('d-none');
      emptyState.innerHTML = `
        <i class="bi bi-lock text-muted fs-1 d-block mb-3"></i>
        <h6 class="fw-bold text-dark">Masuk untuk Melihat Favorit</h6>
        <p class="text-muted small mb-3">Simpan dan lihat daftar destinasi favorit Anda dengan masuk ke akun JelajahNusa.</p>
        <button type="button" class="btn btn-forest btn-sm rounded-pill px-4"
          data-bs-dismiss="modal"
          data-bs-toggle="modal"
          data-bs-target="#authModal"
          id="favModalLoginBtn">
          <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
        </button>`;
      container.innerHTML = '';
      return;
    }

    // User sudah login — ambil list dari API
    container.innerHTML = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm text-success me-2"></span>Memuat favorit...</div>';
    emptyState.classList.add('d-none');

    try {
      const res  = await fetch(FAV_API_URL + '?action=list', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json().catch(() => null);

      container.innerHTML = '';

      if (!data || !data.success || data.favorites.length === 0) {
        emptyState.classList.remove('d-none');
        emptyState.innerHTML = `
          <i class="bi bi-heart text-muted fs-1 d-block mb-3"></i>
          <h6 class="fw-bold text-dark">Belum Ada Favorit</h6>
          <p class="text-muted small">Klik ikon hati pada kartu destinasi untuk menyimpannya.</p>
          <a href="#destinasi" class="btn btn-outline-forest btn-sm mt-2" data-bs-dismiss="modal">Jelajahi Destinasi</a>`;
        updateFavBadge(0);
        return;
      }

      const favs = data.favorites;
      jnFavIds   = new Set(favs.map(f => String(f.id)));
      updateFavBadge(favs.length);
      updateFavButtonStates();

      favs.forEach((item) => {
        const detailLink = isInsidePages
          ? `detail-destinasi.php?id=${encodeURIComponent(item.id)}`
          : `pages/detail-destinasi.php?id=${encodeURIComponent(item.id)}`;
        const itemEl = document.createElement('div');
        itemEl.className = 'favorite-item-card';
        itemEl.innerHTML = `
          <img src="${item.foto}" alt="${item.nama}" class="favorite-item-img">
          <div class="flex-grow-1 overflow-hidden pe-2">
            <a href="${detailLink}" class="fw-bold text-dark text-decoration-none d-block text-truncate mb-0">${item.nama}</a>
            <span class="text-muted small"><i class="bi bi-geo-alt me-1 text-success"></i>${item.lokasi}</span>
          </div>
          <div class="d-flex align-items-center gap-1 flex-shrink-0">
            <a href="${detailLink}" class="btn btn-sm btn-outline-forest rounded-pill px-2 py-1" style="font-size:0.76rem;" title="Lihat Destinasi">Lihat</a>
            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-modal-remove-fav"
              data-dest-id="${item.id}" data-nama="${item.nama}" title="Hapus dari favorit" aria-label="Hapus">
              <i class="bi bi-trash3"></i>
            </button>
          </div>`;
        container.appendChild(itemEl);
      });

      // Event hapus dari modal
      container.querySelectorAll('.btn-modal-remove-fav').forEach((rmBtn) => {
        rmBtn.addEventListener('click', async (e) => {
          e.preventDefault();
          const destId = rmBtn.getAttribute('data-dest-id');
          const nama   = rmBtn.getAttribute('data-nama');
          await removeFavorite(destId, nama, rmBtn);
          renderFavModal(); // Re-render modal setelah hapus
        });
      });
    } catch (_) {
      container.innerHTML = '<p class="text-danger text-center small py-3">Gagal memuat favorit. Coba lagi.</p>';
    }
  };

  // ─── Inti toggle favorit (add / remove) ──────────────────────────────────
  const addFavorite = async (destId, nama, btn) => {
    btn.disabled = true;
    try {
      const fd = new FormData();
      fd.append('action', 'add');
      fd.append('dest_id', destId);
      const res  = await fetch(FAV_API_URL, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json().catch(() => null);

      if (data && data.success) {
        jnFavIds.add(String(destId));
        updateFavButtonStates();
        updateFavBadge(data.total ?? jnFavIds.size);
        showToast(`❤️ ${nama} ditambahkan ke destinasi favoritmu!`);
      } else {
        showToast(data?.message || 'Gagal menambahkan favorit.');
      }
    } catch (_) {
      showToast('Gagal terhubung ke server. Periksa koneksi internet Anda.');
    } finally {
      btn.disabled = false;
    }
  };

  const removeFavorite = async (destId, nama, btn) => {
    if (btn) btn.disabled = true;
    try {
      const fd = new FormData();
      fd.append('action', 'remove');
      fd.append('dest_id', destId);
      const res  = await fetch(FAV_API_URL, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json().catch(() => null);

      if (data && data.success) {
        jnFavIds.delete(String(destId));
        updateFavButtonStates();
        updateFavBadge(data.total ?? jnFavIds.size);
        showToast(`${nama} dihapus dari destinasi favorit.`);

        // Hapus kartu dari halaman favorit.php tanpa reload
        const col = document.getElementById(`fav-col-${destId}`);
        if (col) {
          col.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          col.style.opacity    = '0';
          col.style.transform  = 'scale(0.96)';
          setTimeout(() => {
            col.remove();
            // Perbarui counter & empty state
            const remaining = document.querySelectorAll('.fav-card-col').length;
            const counter   = document.getElementById('pageFavoritesCount');
            if (counter) counter.textContent = `${remaining} Destinasi Disimpan`;
            const clearBtn = document.getElementById('clearAllFavoritesBtn');
            if (clearBtn) clearBtn.classList.toggle('d-none', remaining === 0);
            if (remaining === 0) {
              const container = document.getElementById('pageFavoritesContainer');
              if (container) {
                container.innerHTML = `
                  <div class="col-12">
                    <div class="about-card-box mx-auto text-center" style="max-width:540px;padding:48px 32px;">
                      <div class="brand-leaf-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.8rem;background-color:var(--c-beige);color:var(--c-forest);">
                        <i class="bi bi-heart"></i>
                      </div>
                      <h3 class="fw-bold text-dark mb-2">Belum Ada Destinasi Favorit</h3>
                      <p class="text-muted mb-4">Kamu belum menandai tempat wisata impianmu. Jelajahi keindahan nusantara dan klik ikon hati pada kartu wisata untuk menyimpannya di sini.</p>
                      <a href="../index.php#destinasi" class="btn btn-forest px-4 py-2">
                        <i class="bi bi-compass me-1"></i> Mulai Jelajahi Wisata
                      </a>
                    </div>
                  </div>`;
              }
            }
          }, 320);
        }
      } else {
        showToast(data?.message || 'Gagal menghapus favorit.');
      }
    } catch (_) {
      showToast('Gagal terhubung ke server. Periksa koneksi internet Anda.');
    } finally {
      if (btn) btn.disabled = false;
    }
  };

  // ─── Handler utama tombol hati (.btn-card-fav) ────────────────────────────
  document.querySelectorAll('.btn-card-fav').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();

      // Skip jika tombol ini adalah tombol hapus dari halaman favorit (ditangani di bawah)
      if (btn.classList.contains('btn-fav-remove')) return;

      const destId = btn.getAttribute('data-id') || btn.getAttribute('data-dest-id');
      const nama   = btn.getAttribute('data-nama') || btn.getAttribute('data-title') || 'Destinasi ini';

      if (!destId) return;

      // ── GUARD: Belum login ────────────────────────────────────────────────
      if (!JN_LOGGED_IN) {
        // Tampilkan notifikasi inline di area terdekat dengan tombol
        const existingNote = document.getElementById('fav-login-note');
        if (existingNote) existingNote.remove();

        const note = document.createElement('div');
        note.id        = 'fav-login-note';
        note.className = 'fav-login-required-toast';
        note.innerHTML = `
          <i class="bi bi-lock-fill me-2"></i>
          <span>Silakan <strong>masuk</strong> terlebih dahulu untuk menyimpan destinasi favorit.</span>
          <button type="button"
            class="btn btn-forest btn-sm rounded-pill ms-2 px-3"
            data-bs-toggle="modal"
            data-bs-target="#authModal"
            onclick="document.getElementById('fav-login-note')?.remove();">
            Masuk
          </button>
          <button type="button" class="btn-close btn-close-sm ms-2"
            aria-label="Tutup"
            onclick="document.getElementById('fav-login-note')?.remove();"
            style="font-size:0.65rem;"></button>`;

        document.body.appendChild(note);

        // Auto-hilang setelah 5 detik
        setTimeout(() => {
          if (note.isConnected) {
            note.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            note.style.opacity    = '0';
            note.style.transform  = 'translateY(10px)';
            setTimeout(() => note.remove(), 400);
          }
        }, 5000);

        return; // Jangan lanjutkan
      }

      // ── User sudah login: toggle ──────────────────────────────────────────
      const isFav = jnFavIds.has(String(destId));
      if (isFav) {
        await removeFavorite(destId, nama, btn);
      } else {
        await addFavorite(destId, nama, btn);
      }
    });
  });

  // ─── Handler tombol hapus di halaman favorit.php ──────────────────────────
  document.querySelectorAll('.btn-fav-remove').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();
      const destId = btn.getAttribute('data-dest-id');
      const nama   = btn.getAttribute('data-nama') || 'Destinasi ini';
      await removeFavorite(destId, nama, btn);
    });
  });

  // ─── Tombol Kosongkan Semua (di halaman favorit.php) ─────────────────────
  const clearAllBtn = document.getElementById('clearAllFavoritesBtn');
  if (clearAllBtn) {
    clearAllBtn.addEventListener('click', async () => {
      if (!JN_LOGGED_IN) return;
      if (!confirm('Yakin ingin menghapus semua destinasi dari daftar favorit?')) return;

      clearAllBtn.disabled = true;
      try {
        const fd = new FormData();
        fd.append('action', 'clear');
        const res  = await fetch(FAV_API_URL, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json().catch(() => null);

        if (data && data.success) {
          jnFavIds.clear();
          updateFavButtonStates();
          updateFavBadge(0);
          showToast('Seluruh daftar favorit telah dikosongkan.');
          // Reload halaman favorit setelah hapus semua
          if (window.location.pathname.toLowerCase().includes('favorit')) {
            setTimeout(() => window.location.reload(), 800);
          }
        } else {
          showToast(data?.message || 'Gagal mengosongkan daftar favorit.');
        }
      } catch (_) {
        showToast('Gagal terhubung ke server.');
      } finally {
        clearAllBtn.disabled = false;
      }
    });
  }

  // ─── Modal Favorit: muat data saat dibuka ────────────────────────────────
  const favoritesModalEl = document.getElementById('favoritesModal');
  if (favoritesModalEl) {
    favoritesModalEl.addEventListener('show.bs.modal', () => {
      renderFavModal();
    });
  }

  // ─── Inisialisasi awal ────────────────────────────────────────────────────
  // Muat ID favorit dari server agar state tombol hati akurat sejak awal
  if (JN_LOGGED_IN && !favIdsScript) {
    // Hanya fetch jika bukan di halaman favorit.php (yang sudah ada inline data)
    loadFavIdsFromServer();
  } else {
    // State tombol hati langsung diperbarui dari data inline (halaman favorit.php)
    updateFavButtonStates();
  }

  // ========================================================================
  // 9. Toast Notification Helper
  // ========================================================================
  function showToast(message) {
    let container = document.querySelector('.nature-toast-box');
    if (!container) {
      container = document.createElement('div');
      container.className = 'nature-toast-box';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'nature-toast';
    toast.innerHTML = `<i class="bi bi-info-circle-fill text-success"></i><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 400);
    }, 3200);
  }

  // ========================================================================
  // 10. Auth Modal (Login / Register Switching)
  // ========================================================================
  const tabLogin = document.getElementById('tabLoginBtn');
  const tabRegister = document.getElementById('tabRegisterBtn');
  const formLogin = document.getElementById('loginForm');
  const formRegister = document.getElementById('registerForm');
  const authModalTitle = document.getElementById('authModalTitle');

  const navLoginBtn = document.getElementById('navLoginBtn');
  const navRegisterBtn = document.getElementById('navRegisterBtn');

  // Helper alert autentikasi modal
  const showAuthAlert = (message, isSuccess = false) => {
    const alertEl = document.getElementById('authAlert');
    if (alertEl) {
      alertEl.textContent = message;
      alertEl.className = isSuccess
        ? 'alert alert-success py-2 px-3 small rounded-3 mb-3'
        : 'alert alert-danger py-2 px-3 small rounded-3 mb-3';
      alertEl.classList.remove('d-none');
    } else {
      showToast(message);
    }
  };

  const clearAuthAlert = () => {
    const alertEl = document.getElementById('authAlert');
    if (alertEl) {
      alertEl.textContent = '';
      alertEl.classList.add('d-none');
    }
  };

  const authModalEl = document.getElementById('authModal');
  if (authModalEl) {
    authModalEl.addEventListener('show.bs.modal', () => {
      const alertEl = document.getElementById('authAlert');
      if (alertEl && !alertEl.classList.contains('alert-info')) {
        clearAuthAlert();
      }
    });
  }

  // Helper sanitasi URL pengalihan (Mencegah Open Redirect)
  const getSanitizedRedirectUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const redirectParam = urlParams.get('redirect');
    const redirectInput = document.querySelector('input[name="redirect"]');
    const rawUrl = (redirectInput && redirectInput.value) ? redirectInput.value : redirectParam;

    if (!rawUrl) return null;

    const trimmed = rawUrl.trim();
    // Tolak URL eksternal, skema berbahaya, atau protocol-relative
    if (
      trimmed.startsWith('http://') ||
      trimmed.startsWith('https://') ||
      trimmed.startsWith('//') ||
      trimmed.startsWith('\\') ||
      trimmed.toLowerCase().startsWith('javascript:') ||
      trimmed.toLowerCase().startsWith('data:')
    ) {
      return null;
    }

    // Hanya izinkan target rute internal JelajahNusa yang valid
    const allowedPrefixes = ['pages/favorit.php', 'pages/rencana-perjalanan.php', 'pages/detail-destinasi.php', 'index.php'];
    const isAllowed = allowedPrefixes.some(prefix => trimmed === prefix || trimmed.startsWith(prefix + '?') || trimmed.startsWith('./' + prefix));

    if (!isAllowed) {
      return null;
    }

    // Sesuaikan path jika user berada di subfolder /pages/ vs di root
    const isInPagesFolder = window.location.pathname.includes('/pages/');
    if (isInPagesFolder) {
      if (trimmed.startsWith('pages/')) {
        return trimmed.replace('pages/', '');
      }
      return '../' + trimmed;
    } else {
      return trimmed;
    }
  };

  // Deteksi otomatis jika diarahkan ke login dari halaman terproteksi
  const checkAuthRedirect = () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auth') === 'login') {
      const authModalEl = document.getElementById('authModal');
      if (authModalEl) {
        const bsModal = bootstrap.Modal.getOrCreateInstance(authModalEl);
        bsModal.show();
        const redirectVal = urlParams.get('redirect') || '';
        let pageName = 'halaman yang Anda tuju';
        if (redirectVal.includes('favorit')) pageName = 'Destinasi Favorit';
        else if (redirectVal.includes('rencana-perjalanan') || redirectVal.includes('planner')) pageName = 'Rencana Perjalanan';

        const alertEl = document.getElementById('authAlert');
        if (alertEl) {
          alertEl.className = 'alert alert-info py-2 px-3 small rounded-3 mb-3';
          alertEl.innerHTML = `<i class="bi bi-info-circle-fill me-2"></i>Silakan masuk atau daftar terlebih dahulu untuk mengakses <strong>${pageName}</strong>.`;
          alertEl.classList.remove('d-none');
        }
      }
    }
  };
  checkAuthRedirect();

  if (tabLogin && tabRegister) {
    tabLogin.addEventListener('click', () => {
      clearAuthAlert();
      tabLogin.className = 'btn btn-forest flex-fill rounded-pill py-1 small fw-bold';
      tabRegister.className = 'btn btn-link text-muted text-decoration-none flex-fill rounded-pill py-1 small fw-bold';
      formLogin.classList.remove('d-none');
      formRegister.classList.add('d-none');
      if (authModalTitle) authModalTitle.innerHTML = 'Masuk ke <span translate="no" class="notranslate">JelajahNusa</span>';
    });

    tabRegister.addEventListener('click', () => {
      clearAuthAlert();
      tabRegister.className = 'btn btn-forest flex-fill rounded-pill py-1 small fw-bold';
      tabLogin.className = 'btn btn-link text-muted text-decoration-none flex-fill rounded-pill py-1 small fw-bold';
      formRegister.classList.remove('d-none');
      formLogin.classList.add('d-none');
      if (authModalTitle) authModalTitle.innerHTML = 'Daftar Akun <span translate="no" class="notranslate">JelajahNusa</span>';
    });
  }

  // Trigger correct tab from navbar buttons
  if (navLoginBtn && tabLogin) {
    navLoginBtn.addEventListener('click', () => {
      clearAuthAlert();
      tabLogin.click();
    });
  }

  if (navRegisterBtn && tabRegister) {
    navRegisterBtn.addEventListener('click', () => {
      clearAuthAlert();
      tabRegister.click();
    });
  }

  const switchRegisterLink = document.getElementById('switchToRegister');
  if (switchRegisterLink && tabRegister) {
    switchRegisterLink.addEventListener('click', (e) => {
      e.preventDefault();
      clearAuthAlert();
      tabRegister.click();
    });
  }

  const switchLoginLink = document.getElementById('switchToLogin');
  if (switchLoginLink && tabLogin) {
    switchLoginLink.addEventListener('click', (e) => {
      e.preventDefault();
      clearAuthAlert();
      tabLogin.click();
    });
  }

  // Integrasi Autentikasi AJAX: Login
  if (formLogin) {
    formLogin.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearAuthAlert();

      const submitBtn = formLogin.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
      }

      const isSubpage = window.location.pathname.includes('/pages/');
      const actionUrl = formLogin.getAttribute('action') || (isSubpage ? '../auth/login.php' : 'auth/login.php');
      const formData = new FormData(formLogin);

      try {
        const response = await fetch(actionUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await response.json().catch(() => null);

        if (response.ok && data && data.success) {
          showAuthAlert(data.message || 'Masuk berhasil! Memperbarui sesi...', true);
          showToast(data.message || 'Masuk berhasil!');
          formLogin.reset();
          setTimeout(() => {
            const modalEl = document.getElementById('authModal');
            if (modalEl) {
              const bsModal = bootstrap.Modal.getInstance(modalEl);
              if (bsModal) bsModal.hide();
            }
            // Admin → dashboard admin; User → selalu ke Beranda
            const role = data.user && data.user.role ? data.user.role : 'user';
            if (role === 'admin') {
              const isSubpageLogin = window.location.pathname.toLowerCase().includes('/pages/');
              window.location.href = isSubpageLogin ? '../admin/' : 'admin/';
            } else {
              const isSubpageLogin = window.location.pathname.toLowerCase().includes('/pages/');
              window.location.href = isSubpageLogin ? '../index.php' : 'index.php';
            }
          }, 800);
        } else {
          const errorMsg = (data && data.message) ? data.message : 'Gagal masuk. Periksa kembali alamat email dan kata sandi Anda.';
          showAuthAlert(errorMsg, false);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        showAuthAlert('Terjadi gangguan jaringan atau server. Silakan coba lagi.', false);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }

  // Integrasi Autentikasi AJAX: Register
  if (formRegister) {
    formRegister.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearAuthAlert();

      const submitBtn = formRegister.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mendaftarkan...';
      }

      const isSubpage = window.location.pathname.includes('/pages/');
      const actionUrl = formRegister.getAttribute('action') || (isSubpage ? '../auth/register.php' : 'auth/register.php');
      const formData = new FormData(formRegister);

      try {
        const response = await fetch(actionUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await response.json().catch(() => null);

        if (response.ok && data && data.success) {
          showAuthAlert(data.message || 'Pendaftaran berhasil! Mengalihkan...', true);
          showToast(data.message || 'Pendaftaran berhasil!');
          formRegister.reset();
          setTimeout(() => {
            const modalEl = document.getElementById('authModal');
            if (modalEl) {
              const bsModal = bootstrap.Modal.getInstance(modalEl);
              if (bsModal) bsModal.hide();
            }
            // Register hanya untuk user biasa → selalu ke Beranda
            const isSubpageRegister = window.location.pathname.toLowerCase().includes('/pages/');
            window.location.href = isSubpageRegister ? '../index.php' : 'index.php';
          }, 800);
        } else {
          const errorMsg = (data && data.message) ? data.message : 'Pendaftaran gagal. Silakan periksa kembali formulir Anda.';
          showAuthAlert(errorMsg, false);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        showAuthAlert('Terjadi gangguan jaringan atau server. Silakan coba lagi.', false);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }

  // Dummy form handling for feedback (misalnya newsletter)
  const handleDummyForm = (formId, msg) => {
    const form = document.getElementById(formId);
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        showToast(msg);
        const modalEl = document.getElementById('authModal');
        if (modalEl) {
          const bsModal = bootstrap.Modal.getInstance(modalEl);
          if (bsModal) bsModal.hide();
        }
      });
    }
  };

  handleDummyForm('newsletterForm', 'Terima kasih telah berlangganan info wisata JelajahNusa!');

  // Membuka modal Buat Rencana Perjalanan
const btnCreateTrip = document.getElementById('btnCreateTrip');
const createTripModal = document.getElementById('createTripModal');

if (btnCreateTrip && createTripModal) {
  btnCreateTrip.addEventListener('click', () => {
    bootstrap.Modal.getOrCreateInstance(createTripModal).show();
  });
}

// ==========================================
// 11. FITUR RENCANA PERJALANAN — DATABASE API
// ==========================================

/**
 * Travel Planner kini berbasis database MySQL melalui auth/trips.php.
 * localStorage (TRIP_STORAGE_KEY) dipertahankan sebagai referensi historis;
 * data lama tidak dihapus tetapi tidak lagi digunakan untuk render.
 *
 * Endpoint: auth/trips.php
 *   GET  ?action=list            → daftar trip user
 *   GET  ?action=get&trip_id=N   → detail trip + items
 *   POST action=create           → buat trip baru (+ item opsional)
 *   POST action=delete_trip      → hapus trip
 *   POST action=add_item         → tambah item ke trip
 *   POST action=delete_item      → hapus item
 */

const TRIPS_API = (() => {
  const p = window.location.pathname.toLowerCase();
  if (p.includes('/pages/')) return '../auth/trips.php';
  return 'auth/trips.php';
})();

// ── State ─────────────────────────────────────────────────────────────────────
let dbTrips        = [];
let activeDbTripId = null;
let selectedDbDay  = 'all';

// ── Elemen DOM (tersedia di rencana-perjalanan.php) ───────────────────────────
const tripSelect            = document.getElementById('plannerTripSelect');
const tripSummary           = document.getElementById('tripSummaryContainer');
const emptyStatePlanner     = document.getElementById('plannerEmptyState');
const dayFilterTabs         = document.getElementById('dayFilterTabs');
const dayItineraryContainer = document.getElementById('dayItineraryContainer');
const createTripForm        = document.getElementById('createTripForm');
const itemForm              = document.getElementById('addTripItemForm');
const itemDaySelect         = document.getElementById('itemDaySelect');

// ── Helpers ───────────────────────────────────────────────────────────────────
const escHtml = (s) => {
  const d = document.createElement('div');
  d.textContent = s ?? '';
  return d.innerHTML;
};

const formatDateId = (dateStr) => {
  if (!dateStr) return '—';
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

/**
 * Kembalikan string tanggal hari ini dalam format YYYY-MM-DD
 * berdasarkan waktu lokal browser (sesuai Asia/Jakarta di perangkat Indonesia).
 * Digunakan sebagai nilai `min` pada semua input tanggal trip.
 */
const getTodayLocal = () => {
  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, '0');
  const d = String(now.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

/**
 * Validasi tanggal tidak lampau (frontend).
 * Mengembalikan pesan error atau null jika valid.
 */
const validateDateNotPast = (dateStr, fieldLabel = 'Tanggal') => {
  if (!dateStr) return `${fieldLabel} wajib diisi.`;
  if (dateStr < getTodayLocal()) return `${fieldLabel} tidak boleh sebelum hari ini.`;
  return null;
};

function getActiveDbTrip() {
  return dbTrips.find(t => String(t.id) === String(activeDbTripId)) || null;
}

function formatTravelerCategory(category) {
  const labels = {
    'Pasangan & Keluarga': 'Pasangan & Keluarga',
    'Solo Traveler': 'Wisatawan Solo (Mandiri)',
    'Teman & Grup': 'Teman & Kelompok',
  };
  return labels[category] || category;
}

// ── API calls ─────────────────────────────────────────────────────────────────
async function apiGet(params) {
  const url = TRIPS_API + '?' + new URLSearchParams(params).toString();
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  return res.json().catch(() => null);
}

async function apiPost(body) {
  const fd = new FormData();
  Object.entries(body).forEach(([k, v]) => fd.append(k, v ?? ''));
  const res = await fetch(TRIPS_API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  return res.json().catch(() => null);
}

// ── Load trips dari server ────────────────────────────────────────────────────
async function loadDbTrips() {
  try {
    const data = await apiGet({ action: 'list' });
    if (data && data.success) {
      dbTrips = data.trips || [];
      if (!activeDbTripId && dbTrips.length > 0) activeDbTripId = dbTrips[0].id;
    } else {
      dbTrips = [];
    }
  } catch (_) { dbTrips = []; }
}

// ── Render dropdown pemilih trip ──────────────────────────────────────────────
function renderTripSelect() {
  if (!tripSelect) return;
  tripSelect.innerHTML = '';
  dbTrips.forEach(t => {
    const opt = document.createElement('option');
    opt.value = t.id;
    opt.textContent = t.name;
    tripSelect.appendChild(opt);
  });
  if (activeDbTripId) tripSelect.value = activeDbTripId;
}

// ── Render summary card ───────────────────────────────────────────────────────
function renderTripSummary() {
  if (!tripSummary) return;
  const trip = getActiveDbTrip();
  if (!trip) { tripSummary.innerHTML = ''; return; }
  tripSummary.innerHTML = `
    <div class="trip-summary-card mb-4">
      <h3>${escHtml(trip.name)}</h3>
      <p><i class="bi bi-geo-alt"></i> ${escHtml(trip.destination)}</p>
      <p><i class="bi bi-calendar"></i> ${formatDateId(trip.start_date)} - ${formatDateId(trip.end_date)}</p>
      <p><i class="bi bi-people"></i> ${escHtml(formatTravelerCategory(trip.category))}</p>
      <p>${escHtml(String(trip.days))} Hari Perjalanan &nbsp;·&nbsp; ${escHtml(String(trip.item_count ?? 0))} Aktivitas</p>
    </div>`;
}

// ── Render tabs filter hari ───────────────────────────────────────────────────
function renderDbDayTabs() {
  const trip = getActiveDbTrip();
  if (!trip || !dayFilterTabs) return;
  dayFilterTabs.innerHTML = `
    <button type="button" class="btn ${selectedDbDay === 'all' ? 'btn-forest' : 'btn-outline-forest'} btn-sm rounded-pill me-2 mb-2" data-day="all">Semua Hari</button>
    ${Array.from({ length: trip.days }, (_, i) => `
      <button type="button" class="btn ${String(selectedDbDay) === String(i+1) ? 'btn-forest' : 'btn-outline-forest'} btn-sm rounded-pill me-2 mb-2" data-day="${i+1}">Hari ${i+1}</button>
    `).join('')}`;
  dayFilterTabs.querySelectorAll('[data-day]').forEach(btn => {
    btn.addEventListener('click', () => {
      selectedDbDay = btn.dataset.day;
      renderDbDayTabs();
      renderDbItinerary();
    });
  });
}

// ── Render pilihan hari di modal tambah item ──────────────────────────────────
function renderDbDayOptions() {
  const trip = getActiveDbTrip();
  if (!itemDaySelect) return;
  itemDaySelect.innerHTML = '';
  if (!trip) return;
  for (let d = 1; d <= trip.days; d++) {
    const opt = document.createElement('option');
    opt.value = d;
    opt.textContent = `Hari ${d}`;
    itemDaySelect.appendChild(opt);
  }
}

// ── Render itinerary per hari ─────────────────────────────────────────────────
async function renderDbItinerary() {
  const trip = getActiveDbTrip();
  if (!trip || !dayItineraryContainer) return;

  dayItineraryContainer.innerHTML = '<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-success"></span></div>';

  const data = await apiGet({ action: 'get', trip_id: trip.id });
  if (!data || !data.success) {
    dayItineraryContainer.innerHTML = '<p class="text-danger small text-center py-3">Gagal memuat jadwal perjalanan.</p>';
    return;
  }

  const allItems = data.trip.items || [];
  const days = selectedDbDay === 'all'
    ? Array.from({ length: trip.days }, (_, i) => i + 1)
    : [Number(selectedDbDay)];

  dayItineraryContainer.innerHTML = days.map(day => {
    const dayItems = allItems.filter(item => Number(item.day_number) === day);
    return `
      <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Hari ${day}</h5>
          <span class="badge bg-success-subtle text-success">${dayItems.length} Aktivitas</span>
        </div>
        ${dayItems.length === 0
          ? '<p class="text-muted mb-0">Belum ada aktivitas pada hari ini.</p>'
          : dayItems.map(item => `
            <div class="border rounded-3 p-3 mb-2">
              <div class="d-flex justify-content-between gap-2">
                <div>
                  <span class="badge bg-light text-success mb-2">${escHtml(item.category)}</span>
                  <h6 class="fw-bold mb-1">${escHtml(item.name)}</h6>
                  ${item.location   ? `<p class="text-muted small mb-1"><i class="bi bi-geo-alt"></i> ${escHtml(item.location)}</p>` : ''}
                  ${item.time_visit ? `<p class="text-muted small mb-1"><i class="bi bi-clock"></i> ${escHtml(item.time_visit)}</p>` : ''}
                  ${item.note       ? `<p class="small mb-0">${escHtml(item.note)}</p>` : ''}
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm align-self-start flex-shrink-0"
                        data-delete-db-item="${item.id}" title="Hapus kegiatan ini">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>`).join('')
        }
      </div>`;
  }).join('');

  // Handler hapus item
  dayItineraryContainer.querySelectorAll('[data-delete-db-item]').forEach(btn => {
    btn.addEventListener('click', async () => {
      if (!confirm('Yakin ingin menghapus kegiatan ini?')) return;
      btn.disabled = true;
      const r = await apiPost({ action: 'delete_item', item_id: btn.dataset.deleteDbItem });
      if (r && r.success) {
        showToast('Kegiatan berhasil dihapus.');
        await refreshPlannerPage();
      } else {
        showToast(r?.message || 'Gagal menghapus kegiatan.');
        btn.disabled = false;
      }
    });
  });
}

// ── Refresh seluruh tampilan planner ─────────────────────────────────────────
async function refreshPlannerPage() {
  await loadDbTrips();
  if (dbTrips.length === 0) {
    if (emptyStatePlanner) emptyStatePlanner.classList.remove('d-none');
    if (tripSummary)       tripSummary.innerHTML = '';
    if (dayFilterTabs)     dayFilterTabs.innerHTML = '';
    if (dayItineraryContainer) dayItineraryContainer.innerHTML = '';
    renderTripSelect();
    return;
  }
  if (emptyStatePlanner) emptyStatePlanner.classList.add('d-none');
  renderTripSelect();
  renderTripSummary();
  renderDbDayOptions();
  renderDbDayTabs();
  await renderDbItinerary();
}

// ── Inisialisasi planner ──────────────────────────────────────────────────────
if (tripSelect || emptyStatePlanner) {
  (async () => { await refreshPlannerPage(); })();
}

// ── Set atribut min=today pada semua input tanggal trip ───────────────────────
/**
 * Dipanggil sekali saat DOM siap dan setiap kali modal dibuka,
 * agar nilai min selalu mencerminkan hari ini (bukan hari saat build).
 */
function setTripDateInputsMin() {
  const today = getTodayLocal();
  // Input di modal createTripModal (rencana-perjalanan.php)
  const sdInput = document.getElementById('tripStartDateInput');
  const edInput = document.getElementById('tripEndDateInput');
  if (sdInput) sdInput.min = today;
  if (edInput) edInput.min = today;

  // Input di modal addToTripModal (detail-destinasi.php)
  const sdNew = document.getElementById('newTripStart');
  const edNew = document.getElementById('newTripEnd');
  if (sdNew) sdNew.min = today;
  if (edNew) edNew.min = today;
}

// Jalankan sekali saat halaman dimuat
setTripDateInputsMin();

// Pastikan min diperbarui setiap modal createTripModal dibuka
const createTripModalEl = document.getElementById('createTripModal');
if (createTripModalEl) {
  createTripModalEl.addEventListener('show.bs.modal', () => {
    setTripDateInputsMin();
  });
}

// Pastikan min diperbarui setiap modal addToTripModal dibuka
// (addToTripModalEl sudah dideklarasikan di section 12 di bawah,
//  kita tambahkan listener kedua yang aman)
document.addEventListener('show.bs.modal', (e) => {
  if (e.target && e.target.id === 'addToTripModal') {
    setTripDateInputsMin();
  }
});

// ── Sinkronisasi tanggal akhir mengikuti tanggal awal ────────────────────────
// Jika user memilih tanggal berangkat, min tanggal pulang ikut berubah.
function bindEndDateMinToStart(startId, endId) {
  const startEl = document.getElementById(startId);
  const endEl   = document.getElementById(endId);
  if (!startEl || !endEl) return;
  startEl.addEventListener('change', () => {
    if (startEl.value) {
      endEl.min = startEl.value;
      // Jika tanggal pulang sudah dipilih tapi lebih awal dari start, reset
      if (endEl.value && endEl.value < startEl.value) {
        endEl.value = startEl.value;
      }
    }
  });
}

bindEndDateMinToStart('tripStartDateInput', 'tripEndDateInput');  // Travel Planner
bindEndDateMinToStart('newTripStart', 'newTripEnd');               // Modal detail-destinasi

// ── Ganti trip aktif ──────────────────────────────────────────────────────────
if (tripSelect) {
  tripSelect.addEventListener('change', async () => {
    activeDbTripId = tripSelect.value;
    selectedDbDay = 'all';
    renderTripSummary();
    renderDbDayOptions();
    renderDbDayTabs();
    await renderDbItinerary();
  });
}

// ── Buat trip baru (form di rencana-perjalanan.php) ───────────────────────────
if (createTripForm) {
  createTripForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name      = document.getElementById('tripNameInput')?.value.trim()  || '';
    const dest      = document.getElementById('tripDestSelect')?.value         || '';
    const startDate = document.getElementById('tripStartDateInput')?.value     || '';
    const endDate   = document.getElementById('tripEndDateInput')?.value       || '';
    const category  = document.getElementById('tripCategorySelect')?.value     || '';
    const notes     = document.getElementById('tripNotesInput')?.value.trim()  || '';

    if (!name || !dest || !startDate || !endDate || !category) { alert('Mohon lengkapi semua data.'); return; }
    const errStart = validateDateNotPast(startDate, 'Tanggal berangkat');
    if (errStart) { alert(errStart); return; }
    const errEnd = validateDateNotPast(endDate, 'Tanggal pulang');
    if (errEnd) { alert(errEnd); return; }
    if (endDate < startDate) { alert('Tanggal pulang tidak boleh sebelum tanggal berangkat.'); return; }

    const submitBtn = createTripForm.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'; }

    const data = await apiPost({ action: 'create', name, destination: dest, start_date: startDate, end_date: endDate, category, notes });

    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan & Buat Jadwal Harian'; }

    if (data && data.success) {
      activeDbTripId = data.trip_id;
      selectedDbDay = 'all';
      const modalEl = document.getElementById('createTripModal');
      if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
      createTripForm.reset();
      await refreshPlannerPage();
      showToast('Rencana perjalanan berhasil dibuat!');
    } else {
      alert(data?.message || 'Gagal membuat rencana perjalanan.');
    }
  });
}

// ── Tambah item (form di rencana-perjalanan.php) ──────────────────────────────
if (itemForm) {
  itemForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const trip = getActiveDbTrip();
    if (!trip) { alert('Buat atau pilih rencana perjalanan terlebih dahulu.'); return; }

    const name     = document.getElementById('itemNameInput')?.value.trim()    || '';
    const cat      = document.getElementById('itemCategorySelect')?.value       || 'Destinasi';
    const day      = Number(itemDaySelect?.value || 1);
    const location = document.getElementById('itemLocationInput')?.value.trim() || '';
    const time     = document.getElementById('itemTimeInput')?.value.trim()     || '';
    const photo    = document.getElementById('itemPhotoInput')?.value.trim()    || '';
    const note     = document.getElementById('itemNoteInput')?.value.trim()     || '';

    if (!name || !cat || !day) { alert('Nama kegiatan, kategori, dan hari wajib diisi.'); return; }

    const submitBtn = itemForm.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'; }

    const data = await apiPost({ action: 'add_item', trip_id: trip.id, day_number: day, category: cat, name, location, time_visit: time, photo_url: photo, note });

    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Tambahkan ke Jadwal Hari Ini'; }

    if (data && data.success) {
      const modalEl = document.getElementById('addTripItemModal');
      if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
      itemForm.reset();
      await renderDbItinerary();
      await loadDbTrips();
      renderTripSummary();
      showToast('Kegiatan berhasil ditambahkan!');
    } else {
      alert(data?.message || 'Gagal menambahkan kegiatan.');
    }
  });
}

// ── plannerBuilderForm di detail-destinasi.php (lama, tetap berjalan) ─────────
const plannerBuilderForm = document.getElementById('plannerBuilderForm');
if (plannerBuilderForm) {
  plannerBuilderForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const destination = document.getElementById('planDestSelect')?.value  || '';
    const days        = document.getElementById('planDaysInput')?.value    || '4';
    const travelerType = document.getElementById('planTypeSelect')?.value  || '';
    const mockupTitle = document.getElementById('mockupTripTitle');
    const mockupMeta  = document.getElementById('mockupTripMeta');
    if (mockupTitle) mockupTitle.textContent = `Rencana Liburan ${days} Hari: Eksplorasi ${destination}`;
    if (mockupMeta)  mockupMeta.textContent  = `${days} Hari • Kategori: ${formatTravelerCategory(travelerType)} • Rekomendasi Terpadu`;
    const modalEl = document.getElementById('plannerModal');
    if (modalEl) bootstrap.Modal.getInstance(modalEl)?.hide();
    const plannerSection = document.getElementById('planner');
    if (plannerSection) plannerSection.scrollIntoView({ behavior: 'smooth' });
    showToast(`Jadwal perjalanan ke ${destination} selama ${days} hari berhasil diperbarui!`);
  });
}

// ── Hapus trip ────────────────────────────────────────────────────────────────
const btnDeleteTrip = document.getElementById('btnDeleteCurrentTrip');
if (btnDeleteTrip) {
  btnDeleteTrip.addEventListener('click', async () => {
    const trip = getActiveDbTrip();
    if (!trip) { alert('Belum ada rencana perjalanan yang dipilih.'); return; }
    if (!confirm(`Yakin ingin menghapus rencana "${trip.name}" beserta seluruh kegiatannya?`)) return;

    btnDeleteTrip.disabled = true;
    const data = await apiPost({ action: 'delete_trip', trip_id: trip.id });
    btnDeleteTrip.disabled = false;

    if (data && data.success) {
      activeDbTripId = null;
      selectedDbDay = 'all';
      await refreshPlannerPage();
      showToast('Rencana perjalanan berhasil dihapus!');
    } else {
      alert(data?.message || 'Gagal menghapus rencana perjalanan.');
    }
  });
}

// ── Cetak jadwal perjalanan ───────────────────────────────────────────────────
const btnPrintTrip = document.getElementById('btnPrintTripBtn');
if (btnPrintTrip) {
  btnPrintTrip.addEventListener('click', async () => {
    const trip = getActiveDbTrip();
    if (!trip) { alert('Buat atau pilih rencana perjalanan terlebih dahulu!'); return; }

    btnPrintTrip.disabled = true;
    const data = await apiGet({ action: 'get', trip_id: trip.id });
    btnPrintTrip.disabled = false;

    if (!data || !data.success) { alert('Gagal mengambil data jadwal perjalanan.'); return; }

    const fullTrip = data.trip;
    const allItems = fullTrip.items || [];
    const printWindow = window.open('', '_blank');
    if (!printWindow) { alert('Izinkan jendela pop-up di peramban untuk mencetak jadwal perjalanan.'); return; }

    let itineraryHTML = '';
    for (let day = 1; day <= fullTrip.days; day++) {
      const items = allItems.filter(item => Number(item.day_number) === day);
      itineraryHTML += `<section class="day-section"><h2>Hari ${day}</h2>
        ${items.length
          ? items.map(item => `<div class="activity"><strong>${escHtml(item.name)}</strong>
              <p>Kategori: ${escHtml(item.category)}</p>
              ${item.location   ? `<p>Lokasi: ${escHtml(item.location)}</p>` : ''}
              ${item.time_visit ? `<p>Waktu: ${escHtml(item.time_visit)}</p>` : ''}
              ${item.note       ? `<p>Catatan: ${escHtml(item.note)}</p>` : ''}
            </div>`).join('')
          : '<p>Belum ada kegiatan untuk hari ini.</p>'
        }</section>`;
    }
    printWindow.document.write(`<!DOCTYPE html><html lang="id"><head>
      <meta charset="UTF-8"><title>Jadwal Perjalanan - ${escHtml(fullTrip.name)}</title>
      <style>body{font-family:Arial,sans-serif;padding:30px;color:#29352D}h1{color:#345B49}
      h2{border-bottom:2px solid #A8B99A;padding-bottom:8px}
      .activity{padding:12px;margin-bottom:12px;border:1px solid #ddd;border-radius:8px}
      .activity p{margin:6px 0}@media print{body{padding:0}}</style>
    </head><body>
      <h1>JelajahNusa</h1><h2>${escHtml(fullTrip.name)}</h2>
      <p>Destinasi: ${escHtml(fullTrip.destination)}</p>
      <p>Tanggal: ${escHtml(fullTrip.start_date)} - ${escHtml(fullTrip.end_date)}</p>
      <p>Durasi: ${fullTrip.days} Hari</p>
      ${itineraryHTML}
      <script>window.onload=function(){window.print()};<\/script>
    </body></html>`);
    printWindow.document.close();
  });
}

// ── Tombol tambah destinasi di toolbar planner ────────────────────────────────
const btnAddTripItem = document.getElementById('btnAddTripItemBtn');
if (btnAddTripItem) {
  btnAddTripItem.addEventListener('click', () => {
    const trip = getActiveDbTrip();
    if (!trip) { alert('Buat atau pilih rencana perjalanan terlebih dahulu.'); return; }
    renderDbDayOptions();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('addTripItemModal')).show();
  });
}

// ==========================================
// 12. MODAL #addToTripModal (detail-destinasi.php)
// ==========================================

/**
 * Handler modal "Tambahkan ke Rencana" di halaman detail destinasi.
 * Tombol .btn-add-to-trip-trigger membuka modal ini.
 * Tab A: tambah ke trip yang sudah ada.
 * Tab B: buat trip baru sekaligus tambahkan item ke Hari 1.
 */

let pendingItem  = { name: '', location: '', category: 'Destinasi', photo: '' };
let modalDbTrips = [];

const TRIPS_API_PAGES = '../auth/trips.php';

async function tripsApiGet(params) {
  const url = TRIPS_API_PAGES + '?' + new URLSearchParams(params).toString();
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  return res.json().catch(() => null);
}
async function tripsApiPost(body) {
  const fd = new FormData();
  Object.entries(body).forEach(([k, v]) => fd.append(k, v ?? ''));
  const res = await fetch(TRIPS_API_PAGES, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  return res.json().catch(() => null);
}

function showAddToTripAlert(msg, isSuccess = false) {
  const el = document.getElementById('addToTripAlert');
  if (!el) return;
  el.textContent = msg;
  el.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'} py-2 px-3 small rounded-3 mb-3`;
  el.classList.remove('d-none');
}
function clearAddToTripAlert() {
  const el = document.getElementById('addToTripAlert');
  if (el) { el.textContent = ''; el.classList.add('d-none'); }
}

function populateExistingTripForm(trips) {
  const sel     = document.getElementById('existingTripSelect');
  const daysSel = document.getElementById('existingDaySelect');
  if (!sel || !daysSel) return;

  sel.innerHTML = '';
  trips.forEach(t => {
    const opt = document.createElement('option');
    opt.value = t.id;
    opt.textContent = t.name;
    opt.dataset.days = t.days;
    sel.appendChild(opt);
  });

  const updateDays = () => {
    const chosen = sel.options[sel.selectedIndex];
    const maxDays = parseInt(chosen?.dataset.days || 1, 10);
    daysSel.innerHTML = '';
    for (let d = 1; d <= maxDays; d++) {
      const o = document.createElement('option');
      o.value = d; o.textContent = `Hari ${d}`;
      daysSel.appendChild(o);
    }
    const info = document.getElementById('existingTripInfo');
    if (info) {
      const trip = modalDbTrips.find(t => String(t.id) === String(chosen?.value));
      if (trip) info.textContent = `${trip.destination} · ${trip.days} hari`;
    }
  };
  sel.addEventListener('change', updateDays);
  updateDays();
}

async function loadAddToTripModal() {
  clearAddToTripAlert();
  const loadEl   = document.getElementById('existingTripsLoading');
  const guestEl  = document.getElementById('existingTripsGuest');
  const emptyEl  = document.getElementById('existingTripsEmpty');
  const formEl   = document.getElementById('addToExistingTripForm');
  const guestNew = document.getElementById('newTripGuest');
  const newForm  = document.getElementById('createNewTripForm');

  if (loadEl)  loadEl.classList.remove('d-none');
  if (guestEl) guestEl.classList.add('d-none');
  if (emptyEl) emptyEl.classList.add('d-none');
  if (formEl)  formEl.classList.add('d-none');

  const data = await tripsApiGet({ action: 'list' });

  if (loadEl) loadEl.classList.add('d-none');

  if (!data || data.auth === false) {
    if (guestEl)  guestEl.classList.remove('d-none');
    if (guestNew) guestNew.classList.remove('d-none');
    if (newForm)  newForm.classList.add('d-none');
    return;
  }
  if (!data.success) { showAddToTripAlert('Gagal memuat daftar rencana.'); return; }

  if (guestNew) guestNew.classList.add('d-none');
  if (newForm)  newForm.classList.remove('d-none');

  modalDbTrips = data.trips || [];

  if (modalDbTrips.length === 0) {
    if (emptyEl) emptyEl.classList.remove('d-none');
    return;
  }
  if (formEl) formEl.classList.remove('d-none');
  populateExistingTripForm(modalDbTrips);
}

// Tangkap klik tombol .btn-add-to-trip-trigger
document.querySelectorAll('.btn-add-to-trip-trigger').forEach(btn => {
  btn.addEventListener('click', () => {
    pendingItem = {
      name:     btn.getAttribute('data-nama')     || '',
      location: btn.getAttribute('data-lokasi')   || '',
      category: btn.getAttribute('data-kategori') || 'Destinasi',
      photo:    btn.getAttribute('data-foto')     || '',
    };

    const nameEl     = document.getElementById('addToTripItemName');
    const metaEl     = document.getElementById('addToTripItemMeta');
    const photoEl    = document.getElementById('addToTripItemPhoto');
    const subtitleEl = document.getElementById('addToTripItemSubtitle');

    if (nameEl)     nameEl.textContent = pendingItem.name || '—';
    if (metaEl)     metaEl.textContent = `${pendingItem.category} · ${pendingItem.location}`;
    if (photoEl)    { photoEl.src = pendingItem.photo; photoEl.style.display = pendingItem.photo ? '' : 'none'; }
    if (subtitleEl) subtitleEl.textContent = `${pendingItem.category}: ${pendingItem.name}`;

    const tabExisting = document.getElementById('tab-existing-btn');
    if (tabExisting) tabExisting.click();
  });
});

// Muat data saat modal dibuka
const addToTripModalEl = document.getElementById('addToTripModal');
if (addToTripModalEl) {
  addToTripModalEl.addEventListener('show.bs.modal', () => {
    loadAddToTripModal();
  });
}

// Switch ke tab baru dari empty state
const switchToNewTab = document.getElementById('switchToNewTripTab');
if (switchToNewTab) {
  switchToNewTab.addEventListener('click', () => {
    document.getElementById('tab-new-btn')?.click();
  });
}

// Form: tambah ke trip yang sudah ada
const addToExistingForm = document.getElementById('addToExistingTripForm');
if (addToExistingForm) {
  addToExistingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAddToTripAlert();

    const tripId    = document.getElementById('existingTripSelect')?.value;
    const dayNum    = document.getElementById('existingDaySelect')?.value || 1;
    const time      = document.getElementById('existingTimeInput')?.value.trim()  || '';
    const note      = document.getElementById('existingNoteInput')?.value.trim()  || '';
    const submitBtn = document.getElementById('addToExistingTripBtn');

    if (!tripId) { showAddToTripAlert('Pilih rencana perjalanan terlebih dahulu.'); return; }

    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'; }

    const data = await tripsApiPost({
      action:     'add_item',
      trip_id:    tripId,
      day_number: dayNum,
      category:   pendingItem.category,
      name:       pendingItem.name,
      location:   pendingItem.location,
      time_visit: time,
      photo_url:  pendingItem.photo,
      note,
    });

    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Tambahkan ke Rencana Ini'; }

    if (data && data.success) {
      showAddToTripAlert(`✅ ${pendingItem.name} berhasil ditambahkan ke rencana perjalanan!`, true);
      addToExistingForm.reset();
      showToast(`🗺️ ${pendingItem.name} ditambahkan ke rencana perjalanan!`);
      setTimeout(() => { bootstrap.Modal.getInstance(addToTripModalEl)?.hide(); }, 1400);
    } else {
      showAddToTripAlert(data?.message || 'Gagal menambahkan kegiatan ke rencana perjalanan.');
    }
  });
}

// Form: buat trip baru + tambah item sekaligus
const createNewTripForm = document.getElementById('createNewTripForm');
if (createNewTripForm) {
  createNewTripForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAddToTripAlert();

    const name  = document.getElementById('newTripName')?.value.trim()  || '';
    const dest  = document.getElementById('newTripDest')?.value.trim()  || '';
    const sd    = document.getElementById('newTripStart')?.value        || '';
    const ed    = document.getElementById('newTripEnd')?.value          || '';
    const cat   = document.getElementById('newTripCategory')?.value     || '';
    const notes = document.getElementById('newTripNotes')?.value.trim() || '';

    if (!name || !dest || !sd || !ed) {
      showAddToTripAlert('Mohon lengkapi nama, destinasi, dan tanggal perjalanan.');
      return;
    }
    const errSd = validateDateNotPast(sd, 'Tanggal berangkat');
    if (errSd) { showAddToTripAlert(errSd); return; }
    const errEd = validateDateNotPast(ed, 'Tanggal pulang');
    if (errEd) { showAddToTripAlert(errEd); return; }
    if (ed < sd) {
      showAddToTripAlert('Tanggal pulang tidak boleh sebelum tanggal berangkat.');
      return;
    }

    const submitBtn = document.getElementById('createNewTripBtn');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Membuat Rencana...'; }

    const data = await tripsApiPost({
      action:        'create',
      name,
      destination:   dest,
      start_date:    sd,
      end_date:      ed,
      category:      cat,
      notes,
      item_name:     pendingItem.name,
      item_category: pendingItem.category,
      item_location: pendingItem.location,
      item_photo:    pendingItem.photo,
      item_day:      1,
    });

    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Buat Rencana & Tambahkan Kegiatan'; }

    if (data && data.success) {
      showAddToTripAlert(`✅ Rencana baru berhasil dibuat dan ${pendingItem.name} ditambahkan ke Hari 1!`, true);
      createNewTripForm.reset();
      showToast(`🗺️ Rencana baru dan ${pendingItem.name} berhasil disimpan!`);
      setTimeout(() => { bootstrap.Modal.getInstance(addToTripModalEl)?.hide(); }, 1600);
    } else {
      showAddToTripAlert(data?.message || 'Gagal membuat rencana baru.');
    }
  });
}

});
