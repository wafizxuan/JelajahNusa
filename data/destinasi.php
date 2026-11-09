<?php
/**
 * JelajahNusa - Data Komprehensif 6 Destinasi Unggulan
 * Konsep: Nature-Inspired, Aesthetic, Clean
 */

$all_destinasi = [
    // =========================================================================
    // 1. RAJA AMPAT (ID 1)
    // =========================================================================
    1 => [
        'id'           => 1,
        'slug'         => 'raja-ampat',
        'nama'         => 'Raja Ampat',
        'provinsi'     => 'Papua Barat Daya',
        'wilayah'      => 'Kepulauan Raja Ampat, Papua Barat Daya',
        'kategori'     => 'Wisata Bahari & Alam',
        'rating'       => 4.9,
        'total_ulasan' => 3420,
        'hero_image'   => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Mahakarya Zamrud Khatulistiwa dengan Keanekaragaman Hayati Bawah Laut Terkaya di Dunia',
        'deskripsi'    => 'Raja Ampat adalah surga bahari impian di ujung timur Indonesia. Terdiri dari ribuan pulau karang karst yang tersebar di atas samudra toska sejernih kristal, perairan ini menjadi rumah bagi 75% spesies karang dunia. Dari panorama puncak Pianemo yang magis hingga kekayaan biota bawah laut tak tertandingi, Raja Ampat menawarkan petualangan alam murni yang menggetarkan jiwa.',
        'about_heading'=> 'Keajaiban Karst Zamrud Khatulistiwa',
        'daya_tarik'   => [
            [
                'judul' => 'Gugusan Pulau Karst Dunia',
                'desc'  => 'Pulau karang hijau zamrud yang menjulang di atas laut toska di gugusan Pianemo dan Wayag.',
                'icon'  => 'bi-water'
            ],
            [
                'judul' => 'Surga Biodiversitas Bawah Laut',
                'desc'  => 'Habitat bagi ribuan spesies ikan tropis, terumbu karang purba, pari manta, dan penyu laut.',
                'icon'  => 'bi-shield-check'
            ],
            [
                'judul' => 'Kearifan Lokal Suku Maritim',
                'desc'  => 'Keramahan masyarakat desa wisata pesisir Arborek dan Yenbuba yang menjaga kelestarian laut.',
                'icon'  => 'bi-bank2'
            ],
            [
                'judul' => 'Keindahan Pasir Timbul Alami',
                'desc'  => 'Hamparan pasir putih lembut di tengah laut biru yang muncul eksotis saat pasang surut.',
                'icon'  => 'bi-stars'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'Oktober - April (Kondisi Laut Tenang)',
            'cuaca' => 'Angin teduh, jarak pandang bawah laut mencapai 30 meter, sempurna untuk menyelam.',
            'tips'  => 'Bulan November hingga Februari adalah momen emas untuk bertemu kawanan pari manta di Manta Sandy.'
        ],
        'popular_places' => [
            [
                'id'        => 'pianemo',
                'nama'      => 'Pianemo Lagoon',
                'lokasi'    => 'Kepulauan Fam, Raja Ampat',
                'foto'      => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pulau Karst',
                'deskripsi' => 'Gugusan bukit karst menyerupai miniatur Wayag dengan tangga kayu terawat menuju gardu pandang.'
            ],
            [
                'id'        => 'wayag',
                'nama'      => 'Kepulauan Wayag',
                'lokasi'    => 'Raja Ampat Utara',
                'foto'      => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Ikon Dunia',
                'deskripsi' => 'Lanskap paling ikonik Raja Ampat dengan puncak tebing karang terjal yang menantang dan memukau.'
            ],
            [
                'id'        => 'arborek',
                'nama'      => 'Desa Wisata Arborek',
                'lokasi'    => 'Selat Dampier, Raja Ampat',
                'foto'      => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Desa Budaya',
                'deskripsi' => 'Desa pelopor konservasi laut dengan tradisi anyaman topi pari dan dermaga penuh ikan warna-warni.'
            ],
            [
                'id'        => 'pasir-timbul',
                'nama'      => 'Pasir Timbul Mansuar',
                'lokasi'    => 'Pulau Mansuar, Raja Ampat',
                'foto'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Fenomena Alam',
                'deskripsi' => 'Pulau pasir putih sehalus tepung di tengah laut lepas yang hanya timbul selama beberapa jam sehari.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'misool-resort',
                'nama'      => 'Misool Eco Resort',
                'lokasi'    => 'Kepulauan Misool Selatan',
                'jenis'     => 'Resor Ekologi Pulau Privat',
                'harga'     => 'Rp 6.500.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Pondok di Atas Air • Pusat Selam • Kawasan Konservasi Laut Privat'
            ],
            [
                'id'        => 'raja-ampat-dive-lodge',
                'nama'      => 'Raja Ampat Dive Lodge',
                'lokasi'    => 'Pulau Mansuar, Raja Ampat',
                'jenis'     => 'Resor Selam Tepi Pantai',
                'harga'     => 'Rp 2.800.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Dermaga Pribadi • Teras Senja • Restoran Hidangan Laut'
            ],
            [
                'id'        => 'arborek-homestay',
                'nama'      => 'Lalosi Arborek Eco Homestay',
                'lokasi'    => 'Desa Arborek, Selat Dampier',
                'jenis'     => 'Rumah Inap Tradisional di Atas Air',
                'harga'     => 'Rp 650.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Bungalow di Atas Air • Makan 3x Sehari • Pemandu Lokal'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'papeda-kuah-kuning',
                'nama'      => 'Papeda & Ikan Kuah Kuning',
                'jenis'     => 'Kuliner Pokok Tradisional Papua',
                'lokasi'    => 'Waisai & Selat Dampier',
                'harga'     => 'Rp 45.000 - Rp 85.000',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Sagu kenyal lembut disajikan dengan sup ikan kakap merah bumbu kunyit asam segar beraroma daun kemangi.'
            ],
            [
                'id'        => 'ikan-bakar-colo',
                'nama'      => 'Ikan Laut Bakar Sambal Colo-Colo',
                'jenis'     => 'Hidangan Pesisir Segar',
                'lokasi'    => 'Sentra Kuliner Pantai Waisai',
                'harga'     => 'Rp 50.000 - Rp 95.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Ikan karang hasil pancingan nelayan lokal dibakar arang dan disiram sambal colo-colo tomat hijau pedas manis.'
            ],
            [
                'id'        => 'kue-lontar-papua',
                'nama'      => 'Kue Lontar Khas Papua',
                'jenis'     => 'Camilan Manis Tradisional',
                'lokasi'    => 'Pasar Kota Sorong & Waisai',
                'harga'     => 'Rp 20.000 - Rp 45.000',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Pai susu khas Papua dengan kulit renyah gurih dan isian vla telur kental lembut manis bercita rasa vanila.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-water',
                'judul'     => 'Menyelam Kelas Dunia di Selat Dampier',
                'lokasi'    => 'Cape Kri & Pulau Mioskon',
                'durasi'    => 'Tur 1 Hari',
                'deskripsi' => 'Menyelam di titik dengan rekor spesies ikan terbanyak di dunia serta terumbu karang hidup aneka warna.'
            ],
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Jelajah Panorama ke Puncak Pianemo',
                'lokasi'    => 'Gardu Pandang Pianemo, Kepulauan Fam',
                'durasi'    => '2 - 3 Jam',
                'deskripsi' => 'Mendaki tangga kayu menuju gardu pandang teratas untuk menikmati bentang gugusan pulau karang zamrud.'
            ],
            [
                'icon'      => 'bi-sun',
                'judul'     => 'Eksplorasi Pasir Timbul & Selam Permukaan di Pulau Mansuar',
                'lokasi'    => 'Pasir Timbul Mansuar',
                'durasi'    => 'Setengah Hari',
                'deskripsi' => 'Berjalan santai di atas pasir putih timbul di tengah samudra toska dan berenang bersama biota laut dangkal.'
            ],
            [
                'icon'      => 'bi-stars',
                'judul'     => 'Mengamati Burung Cenderawasih Merah Menari',
                'lokasi'    => 'Hutan Desa Sawinggrai, Gam',
                'durasi'    => 'Subuh (05:30 - 08:00)',
                'deskripsi' => 'Menyusuri hutan kanopi tropis pagi hari untuk menyaksikan atraksi tarian sakral burung surga endemik Papua.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat / Rumah Inap: Rp 750.000 - Rp 1.400.000 / hari (rumah inap lokal, makan terpadu, sewa perahu bersama)',
                    'Menengah / Resor Selam: Rp 2.500.000 - Rp 4.500.000 / hari (resor pantai, sewa kapal cepat, kegiatan menyelam harian)',
                    'Mewah / Kapal Wisata Inap: Rp 6.000.000+ / hari (kapal pinisi eksklusif untuk berlayar dan menginap, tur privat ke pulau terpencil)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Kapal Cepat Feri Sorong - Waisai: Rp 125.000 - Rp 250.000 / perjalanan (durasi tempuh sekitar 2 jam)',
                    'Sewa Kapal Cepat Antar Pulau: Rp 2.500.000 - Rp 6.000.000 / hari (sangat disarankan patungan rombongan)',
                    'Ojek Lokal Waisai: Rp 20.000 - Rp 50.000 per perjalanan di sekitar pusat pulau Waigeo'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Siapkan Kartu Masuk Kawasan Konservasi Laut (PIN Tara) sebelum memulai tur wisata bahari',
                    'Bawa uang tunai rupiah secukupnya dari Sorong karena mesin ATM di kepulauan sangat terbatas',
                    'Gunakan tabir surya yang aman bagi terumbu karang demi menjaga ekosistem laut'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Tenang (Oktober - April): Laut tenang, jarak pandang jernih, waktu paling prima untuk menyelam',
                    'Musim Angin Timur (Juni - September): Angin dan ombak laut lebih tinggi, cocok untuk wisata darat pulau Waigeo',
                    'Bulan Terbaik: November dan Desember menawarkan perairan super tenang dan cuaca tropis bersahabat'
                ]
            ]
        ]
    ],

    // =========================================================================
    // 2. PULAU BALI (ID 2) - PRESERVED 100% ORIGINAL
    // =========================================================================
    2 => [
        'id'           => 2,
        'slug'         => 'bali',
        'nama'         => 'Bali, Indonesia',
        'provinsi'     => 'Provinsi Bali',
        'wilayah'      => 'Kepulauan Sunda Kecil, Indonesia',
        'kategori'     => 'Budaya, Alam & Bahari',
        'rating'       => 4.9,
        'total_ulasan' => 4820,
        'hero_image'   => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Pulau Seribu Pura dengan Harmoni Alam, Tradisi Sakral, dan Pesona Pesisir Tropis Dunia',
        'deskripsi'    => 'Bali adalah pulau magis di Indonesia yang terkenal di seluruh dunia berkat keindahan bentang alamnya yang memukau—dari hamparan terasering sawah bertingkat di Ubud, jajaran pura laut yang sakral, hingga pesisir pantai selancar berpasir putih. Lebih dari sekadar pemandangan, jiwa Bali terpancar dari keramahan masyarakatnya serta denyut tradisi seni dan upacara adat Hindu Dharma yang lestari.',
        'about_heading'=> 'Pesona Abadi Zamrud Dewata',
        'daya_tarik'   => [
            [
                'judul' => 'Kekayaan Budaya & Pura Luhur',
                'desc'  => 'Pura-pura megah di tebing laut dan lereng gunung, seperti Pura Uluwatu, Tanah Lot, dan Besakih, menyajikan harmoni spiritual yang mengagumkan.',
                'icon'  => 'bi-bank2'
            ],
            [
                'judul' => 'Bentang Alam Hijau & Terasering Ubud',
                'desc'  => 'Lembah sungai Ayung dan terasering sawah Tegalalang di Ubud menawarkan ketenangan asri serta sistem irigasi Subak warisan dunia UNESCO.',
                'icon'  => 'bi-tree-fill'
            ],
            [
                'judul' => 'Surga Bahari & Olahraga Selancar',
                'desc'  => 'Mulai dari ombak selancar kelas dunia di pesisir Uluwatu hingga ketenangan menyelam bersama ikan pari manta di Nusa Penida.',
                'icon'  => 'bi-water'
            ],
            [
                'judul' => 'Kesenian & Kuliner Autentik',
                'desc'  => 'Seni tari Kecak dan Barong yang dramatis, kerajinan ukir perak celuk, serta racikan rempah bumbu base genep khas pulau dewata.',
                'icon'  => 'bi-stars'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'April - Oktober (Musim Kemarau)',
            'cuaca' => 'Cerah dengan hembusan angin sejuk, kelembapan sedang, sangat ideal untuk aktivitas pantai, pendakian saat fajar, dan eksplorasi pura.',
            'tips'  => 'Bulan Mei, Juni, dan September merupakan waktu terbaik dengan cuaca prima dan keramaian wisatawan yang lebih tenang dibanding musim puncak liburan.'
        ],
        'popular_places' => [
            [
                'id'        => 'ubud',
                'nama'      => 'Ubud',
                'lokasi'    => 'Gianyar, Bali',
                'foto'      => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pusat Budaya & Seni',
                'deskripsi' => 'Pusat kesenian Bali berhawa sejuk dengan hamparan terasering sawah hijau Tegalalang dan hutan monyet sakral.'
            ],
            [
                'id'        => 'tanah-lot',
                'nama'      => 'Pura Tanah Lot',
                'lokasi'    => 'Tabanan, Bali',
                'foto'      => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pura Ikonik',
                'deskripsi' => 'Pura laut legendaris di atas bongkahan batu karang besar dengan panorama matahari terbenam spektakuler.'
            ],
            [
                'id'        => 'uluwatu',
                'nama'      => 'Pura Luhur Uluwatu',
                'lokasi'    => 'Kuta Selatan, Badung',
                'foto'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Tebing & Senja',
                'deskripsi' => 'Pura megah di puncak tebing karang terjal setinggi 70 meter dengan pertunjukan Tari Kecak magis saat senja.'
            ],
            [
                'id'        => 'nusa-penida',
                'nama'      => 'Nusa Penida',
                'lokasi'    => 'Klungkung, Bali',
                'foto'      => 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Wisata Bahari',
                'deskripsi' => 'Pulau eksotis dengan tebing ikonik Kelingking Beach berbentuk T-Rex dan lokasi untuk menyelam bersama ikan pari manta.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'bambu-indah',
                'nama'      => 'Bambu Indah Eco Luxury Resort',
                'lokasi'    => 'Sayan, Ubud - Bali',
                'jenis'     => 'Resor & Vila Ekologis Mewah',
                'harga'     => 'Rp 2.850.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Kolam Alami • Pemandangan Lembah • Arsitektur Bambu Alami'
            ],
            [
                'id'        => 'seminyak-resort',
                'nama'      => 'The Seminyak Beach Resort & Spa',
                'lokasi'    => 'Pantai Seminyak, Badung - Bali',
                'jenis'     => 'Resor Mewah Tepi Pantai',
                'harga'     => 'Rp 3.200.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Kolam Tepi Pantai Tanpa Batas • Dek Senja • Spa Kelas Dunia'
            ],
            [
                'id'        => 'munduk-plantation',
                'nama'      => 'Munduk Moding Nature Resort',
                'lokasi'    => 'Munduk, Buleleng - Bali',
                'jenis'     => 'Penginapan & Vila Ekologis Pegunungan',
                'harga'     => 'Rp 1.950.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Kolam Tanpa Batas di Atas Awan • Kebun Kopi Organik • Udara Sejuk'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'bebek-betutu',
                'nama'      => 'Bebek Betutu & Sambal Matah',
                'jenis'     => 'Kuliner Tradisional Khas Bali',
                'lokasi'    => 'Gianyar & Ubud, Bali',
                'harga'     => 'Rp 45.000 - Rp 110.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Olahan bebek empuk yang dibalut racikan bumbu base genep dan dimasak dengan sekam daun pisang selama berjam-jam hingga rempahnya meresap sempurna.'
            ],
            [
                'id'        => 'sop-ikan-sanur',
                'nama'      => 'Sop Ikan Laut Segar & Nasi Campur',
                'jenis'     => 'Kuliner Legendaris Pesisir',
                'lokasi'    => 'Pantai Sanur, Denpasar',
                'harga'     => 'Rp 55.000 / porsi',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Sop ikan kuah bening asam pedas dengan potongan belimbing wuluh segar, disajikan bersama ikan laut goreng krispi dan sambal terasi khas.'
            ],
            [
                'id'        => 'sate-lilit',
                'nama'      => 'Sate Lilit Ikan & Lawar Bali',
                'jenis'     => 'Makanan Tradisional Autentik',
                'lokasi'    => 'Pasar Senggol Gianyar & Klungkung',
                'harga'     => 'Rp 25.000 - Rp 50.000',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Daging ikan cincang berpadu kelapa parut dan rempah wangi yang dililitkan pada batang serai segar, dibakar di atas arang batok kelapa.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-water',
                'judul'     => 'Selam Permukaan & Menyelam Bersama Pari Manta',
                'lokasi'    => 'Manta Point & Crystal Bay, Nusa Penida',
                'durasi'    => 'Tur 1 Hari',
                'deskripsi' => 'Pengalaman magis berenang di air laut sejernih kristal bersama kawanan ikan pari manta raksasa dan terumbu karang tropis terlindungi.'
            ],
            [
                'icon'      => 'bi-stars',
                'judul'     => 'Menyaksikan Tari Kecak Saat Senja di Tebing Uluwatu',
                'lokasi'    => 'Panggung Terbuka Pura Uluwatu',
                'durasi'    => 'Sore (17:30 - 19:00)',
                'deskripsi' => 'Pementasan seni tari sakral kolosal berlatar senja Samudra Hindia yang melantunkan kisah epik Ramayana dengan paduan suara vokal dinamis.'
            ],
            [
                'icon'      => 'bi-sun',
                'judul'     => 'Pendakian Menyambut Matahari Terbit di Puncak Gunung Batur',
                'lokasi'    => 'Kintamani, Bangli',
                'durasi'    => 'Dini Hari (03:00 - 08:30)',
                'deskripsi' => 'Pendakian fajar di kaldera vulkanik purba untuk menikmati panorama matahari terbit spektakuler di atas lautan awan dan Danau Batur.'
            ],
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Arung Jeram Menyusuri Lembah Sungai Ayung',
                'lokasi'    => 'Sungai Ayung, Ubud',
                'durasi'    => 'Setengah Hari',
                'deskripsi' => 'Petualangan seru mendayung perahu karet melintasi jeram alami dengan pemandangan relief pahatan batu tebing dan hutan rimba yang asri.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat: Rp 350.000 - Rp 600.000 / hari (rumah inap, kuliner lokal, sewa motor)',
                    'Menengah / Keluarga: Rp 900.000 - Rp 1.800.000 / hari (hotel bintang 3-4, sewa mobil)',
                    'Mewah / Resor: Rp 2.500.000+ / hari (vila pribadi, santap mewah, tur privat eksklusif)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Sewa Sepeda Motor: Rp 70.000 - Rp 120.000 / hari (sangat efisien untuk pasangan dan wisatawan solo)',
                    'Sewa Mobil + Sopir Lokal: Rp 500.000 - Rp 750.000 / hari (sangat nyaman untuk wisata keluarga)',
                    'Taksi & Transportasi Online: Tersedia luas di area Denpasar, Kuta, Seminyak, Canggu, dan Ubud'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Kenakan pakaian sopan (sarung dan selendang) saat memasuki area suci pura peribadatan',
                    'Hormati sesajen (canang sari) yang diletakkan di trotoar atau depan pintu masuk',
                    'Siapkan uang tunai kecil untuk tiket masuk retribusi wisata, donasi pura, dan parkir'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Cerah (April - Oktober): Terbaik untuk aktivitas pantai, selam permukaan, dan jalan-jalan luar ruangan',
                    'Musim Hujan (November - Maret): Suasana lebih tenang, sawah sangat hijau, dan harga akomodasi lebih hemat',
                    'Bulan Terbaik: Mei, Juni, dan September menawarkan cuaca paling cerah tanpa kepadatan tinggi'
                ]
            ]
        ]
    ],

    // =========================================================================
    // 3. GUNUNG BROMO (ID 3)
    // =========================================================================
    3 => [
        'id'           => 3,
        'slug'         => 'bromo',
        'nama'         => 'Gunung Bromo',
        'provinsi'     => 'Jawa Timur',
        'wilayah'      => 'Taman Nasional Bromo Tengger Semeru, Jawa Timur',
        'kategori'     => 'Wisata Pegunungan & Petualangan',
        'rating'       => 4.9,
        'total_ulasan' => 3890,
        'hero_image'   => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Keagungan Kaldera Purba Tengger dengan Lautan Pasir dan Matahari Terbit Mistis',
        'deskripsi'    => 'Gunung Bromo berdiri megah di jantung Taman Nasional Bromo Tengger Semeru dengan pesona kaldera vulkanik purba yang tiada duanya di dunia. Diselimuti kabut pagi keemasan, hamparan kaldera lautan pasir berbisik, serta kesakralan tradisi Yadnya Kasada suku Tengger, Bromo menawarkan petualangan alam pegunungan yang dramatis sekaligus menenangkan jiwa.',
        'about_heading'=> 'Keajaiban Kaldera dan Savana Tengger',
        'daya_tarik'   => [
            [
                'judul' => 'Matahari Terbit Keemasan di Penanjakan',
                'desc'  => 'Panorama matahari terbit spektakuler di atas lautan kabut awan dengan siluet Gunung Semeru.',
                'icon'  => 'bi-brightness-high'
            ],
            [
                'judul' => 'Kaldera Lautan Pasir Berbisik',
                'desc'  => 'Hamparan pasir vulkanik seluas lebih dari 5.000 hektar dengan suara desau angin alami yang magis.',
                'icon'  => 'bi-wind'
            ],
            [
                'judul' => 'Kawah Vulkanik Aktif Bromo',
                'desc'  => 'Gemuruh kawah aktif yang dapat disaksikan langsung dari bibir kawah melalui tangga beton kokoh.',
                'icon'  => 'bi-activity'
            ],
            [
                'judul' => 'Kearifan Adat Suku Tengger',
                'desc'  => 'Harmoni kehidupan spiritual masyarakat Hindu Tengger dan kesakralan upacara Yadnya Kasada.',
                'icon'  => 'bi-bank2'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'Juni - Oktober (Musim Kemarau Pegunungan)',
            'cuaca' => 'Langit cerah bersih dengan kabut lembah dramatis, suhu dingin berkisar 3°C - 18°C.',
            'tips'  => 'Bawa jaket tebal windproof, sarung tangan, kupluk, dan masker penahan debu vulkanik.'
        ],
        'popular_places' => [
            [
                'id'        => 'penanjakan',
                'nama'      => 'Penanjakan 1',
                'lokasi'    => 'TNBTS, Pasuruan - Probolinggo',
                'foto'      => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Matahari Terbit Keemasan',
                'deskripsi' => 'Titik pandang tertinggi kaldera untuk menikmati panorama matahari terbit berlatar Bromo dan Semeru.'
            ],
            [
                'id'        => 'pasir-berbisik',
                'nama'      => 'Pasir Berbisik',
                'lokasi'    => 'Kaldera Bromo, Jawa Timur',
                'foto'      => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Lautan Pasir',
                'deskripsi' => 'Hamparan pasir vulkanik yang luas dengan desau angin khas, lokasi syuting film dan foto legendaris.'
            ],
            [
                'id'        => 'pura-poten',
                'nama'      => 'Pura Luhur Poten',
                'lokasi'    => 'Dasar Kaldera Bromo',
                'foto'      => 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Situs Budaya',
                'deskripsi' => 'Pura suci sakral tempat peribadatan masyarakat Hindu Tengger di tengah hamparan lautan pasir.'
            ],
            [
                'id'        => 'bukit-teletubbies',
                'nama'      => 'Bukit Teletubbies & Savana',
                'lokasi'    => 'Lembah Jemplang, TNBTS',
                'foto'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Savana Hijau',
                'deskripsi' => 'Perbukitan hijau bergelombang yang asri dengan padang ilalang dan pemandangan lembah memikat.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'plataran-bromo',
                'nama'      => 'Plataran Bromo Resort & Venue',
                'lokasi'    => 'Tosari, Pasuruan - Jawa Timur',
                'jenis'     => 'Resor Mewah Pegunungan',
                'harga'     => 'Rp 3.100.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Jacuzzi Air Hangat Privat • Teras Matahari Terbit • Perkebunan Apel Organik'
            ],
            [
                'id'        => 'jiwa-jawa-bromo',
                'nama'      => 'Jiwa Jawa Resort Bromo',
                'lokasi'    => 'Sukapura, Probolinggo',
                'jenis'     => 'Resor Ekologis Pegunungan & Galeri Seni',
                'harga'     => 'Rp 1.750.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Galeri Foto Alam • Kafe Lava Hangat • Pemandangan Lembah'
            ],
            [
                'id'        => 'bromo-camp-house',
                'nama'      => 'Bromo Camp House & Lodge',
                'lokasi'    => 'Cemorolawang, Ngadisari',
                'jenis'     => 'Kemah Mewah & Kabin Pegunungan',
                'harga'     => 'Rp 650.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Api Unggun Malam • Pemanas Air • Titik Awal Jejak Kawah'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'nasi-aron-tengger',
                'nama'      => 'Nasi Aron Jagung & Sambal Pokak',
                'jenis'     => 'Kuliner Pokok Khas Tengger',
                'lokasi'    => 'Desa Ngadisari & Wonokitri',
                'harga'     => 'Rp 25.000 - Rp 45.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Nasi olahan jagung putih pipil khas dataran tinggi Tengger disajikan dengan tempe bacem dan sambal pokak pedas.'
            ],
            [
                'id'        => 'rawon-hangat-bromo',
                'nama'      => 'Rawon Daging Hangat Rempah Kluwek',
                'jenis'     => 'Sup Daging Tradisional Jawa Timur',
                'lokasi'    => 'Warung Lereng Bromo & Sukapura',
                'harga'     => 'Rp 35.000 - Rp 60.000',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Sup daging sapi kuah hitam pekat rempah kluwek yang disajikan panas mengepul lengkap dengan tauge pendek dan telur asin.'
            ],
            [
                'id'        => 'bakso-malang-wedang',
                'nama'      => 'Bakso Malang & Wedang Pokak Hangat',
                'jenis'     => 'Sajian Penghangat Udara Dingin',
                'lokasi'    => 'Titik Pandang Cemorolawang',
                'harga'     => 'Rp 20.000 - Rp 35.000',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Bakso kuah kaldu sapi hangat bertabur pangsit krispi ditemani seduhan jahe, sereh, pandan, dan kayu manis khas Bromo.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-car-front',
                'judul'     => 'Tur Jeep Hardtop 4x4 Menyusuri Kaldera Fajar',
                'lokasi'    => 'Penanjakan - Lautan Pasir - Savana',
                'durasi'    => 'Dini Hari (02:30 - 09:30)',
                'deskripsi' => 'Sensasi melintasi lautan pasir berdebu di kegelapan fajar dengan armada ikonik mobil jip legendaris.'
            ],
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Mendaki 250 Anak Tangga Menuju Bibir Kawah Aktif',
                'lokasi'    => 'Kawah Vulkanik Gunung Bromo',
                'durasi'    => '1.5 - 2 Jam',
                'deskripsi' => 'Berjalan meniti tangga beton aman menuju puncak kawah untuk menyaksikan gemuruh magma vulkanik dari dekat.'
            ],
            [
                'icon'      => 'bi-stars',
                'judul'     => 'Menunggang Kuda Melintasi Lautan Pasir Berbisik',
                'lokasi'    => 'Dasar Kaldera Pasir Berbisik',
                'durasi'    => '1 Jam',
                'deskripsi' => 'Pengalaman klasik dipandu warga Tengger menunggang kuda sejuk melintasi panorama pasir berlatar tebing purba.'
            ],
            [
                'icon'      => 'bi-moon-stars',
                'judul'     => 'Fotografi Gugusan Bintang Bima Sakti di Bukit Kingkong',
                'lokasi'    => 'Gardu Pandang Bukit Kingkong, TNBTS',
                'durasi'    => 'Malam Hari (22:00 - 02:00)',
                'deskripsi' => 'Mengabadikan gemerlap jutaan bintang galaksi Bima Sakti di langit dataran tinggi Bromo yang bebas polusi cahaya.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat: Rp 350.000 - Rp 650.000 / hari (rumah inap Cemorolawang, sewa jip bersama, warung lokal)',
                    'Menengah / Keluarga: Rp 950.000 - Rp 1.800.000 / hari (resor alam, jip privat ke 4 lokasi, kuliner hangat)',
                    'Mewah / Eksklusif: Rp 2.800.000+ / hari (resor mewah bintang 5, tur privat menyambut matahari terbit, fotografer)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Sewa Jip Hardtop 4x4 (Wajib Kaldera): Rp 600.000 - Rp 850.000 / armada (kapasitas 5-6 orang)',
                    'Sewa Mobil Malang/Surabaya - Bromo: Rp 500.000 - Rp 700.000 / hari',
                    'Sewa Kuda Kaldera Pasir: Rp 150.000 - Rp 250.000 pulang pergi menuju anak tangga kawah'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Pesan tiket masuk TNBTS secara daring lebih awal karena kuota pengunjung harian terbatas',
                    'Bawa uang tunai rupiah secukupnya karena ATM hanya tersedia di Sukapura dan Tosari bagian bawah',
                    'Kenakan pakaian berlapis dan bawa masker agar nyaman saat berada di lautan pasir'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Kemarau (Juni - Oktober): Waktu terbaik menikmati matahari terbit tanpa terhalang awan hujan',
                    'Musim Upacara Kasada (Sesuai Kalender Tengger): Momen budaya spiritual paling sakral dan mengesankan',
                    'Musim Hujan (Desember - Maret): Kawasan savana sangat hijau subur dan pemandangan perbukitan lebih segar'
                ]
            ]
        ]
    ],

    // =========================================================================
    // 4. LABUAN BAJO (ID 4)
    // =========================================================================
    4 => [
        'id'           => 4,
        'slug'         => 'labuan-bajo',
        'nama'         => 'Labuan Bajo',
        'provinsi'     => 'Nusa Tenggara Timur',
        'wilayah'      => 'Manggarai Barat, Flores, Nusa Tenggara Timur',
        'kategori'     => 'Wisata Alam, Satwa & Bahari',
        'rating'       => 4.9,
        'total_ulasan' => 4150,
        'hero_image'   => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Gerbang Eksotisme Pulau Komodo dengan Savana Dramatis dan Perairan Karang Dunia',
        'deskripsi'    => 'Labuan Bajo adalah pintu gerbang menuju keajaiban Taman Nasional Komodo di Flores barat. Dikenal dengan perpaduan lanskap savana bukit Pulau Padar, pantai pasir merah muda Pink Beach, habitat purba komodo, serta perairan jernih bertaraf dunia, Labuan Bajo menghadirkan pengalaman berlayar dengan kapal pinisi yang spektakuler dan tak terlupakan.',
        'about_heading'=> 'Pesona Savana Dramatis dan Habitat Naga Purba',
        'daya_tarik'   => [
            [
                'judul' => 'Habitat Asli Komodo',
                'desc'  => 'Satu-satunya habitat kadal purba terbesar di muka bumi di Pulau Komodo dan Rinca yang dilindungi UNESCO.',
                'icon'  => 'bi-shield-check'
            ],
            [
                'judul' => 'Puncak Tiga Teluk Pulau Padar',
                'desc'  => 'Pendakian menuju gardu pandang bukit savana ikonik dengan panorama tiga teluk berpasir warna berbeda.',
                'icon'  => 'bi-compass'
            ],
            [
                'judul' => 'Keajaiban Pasir Pink Beach',
                'desc'  => 'Pantai pasir merah muda langka hasil serpihan karang merah foraminifera berpadu laut toska bening.',
                'icon'  => 'bi-heart'
            ],
            [
                'judul' => 'Pelayaran Kapal Pinisi Mewah',
                'desc'  => 'Pengalaman berlayar dan menginap di kapal yang melintasi kepulauan tropis dengan fasilitas setara hotel berbintang di atas laut.',
                'icon'  => 'bi-water'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'April - November (Cuaca Cerah & Laut Tenang)',
            'cuaca' => 'Langit biru cerah, savana berubah keemasan eksotis di pertengahan tahun, laut sangat tenang untuk berlayar.',
            'tips'  => 'Bulan September - November adalah waktu terbaik melihat komodo aktif dan berenang bersama pari manta di Manta Point.'
        ],
        'popular_places' => [
            [
                'id'        => 'pulau-padar',
                'nama'      => 'Pulau Padar',
                'lokasi'    => 'Taman Nasional Komodo, NTT',
                'foto'      => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Savana Ikonik',
                'deskripsi' => 'Pulau tak berpenghuni dengan perbukitan savana dramatis dan gardu pandang tiga teluk yang tersohor di dunia.'
            ],
            [
                'id'        => 'pink-beach',
                'nama'      => 'Pink Beach',
                'lokasi'    => 'Pulau Komodo, NTT',
                'foto'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pantai Unik',
                'deskripsi' => 'Salah satu dari sedikit pantai pasir merah muda alami di dunia dengan terumbu karang dangkal yang sehat.'
            ],
            [
                'id'        => 'pulau-komodo',
                'nama'      => 'Pulau Komodo & Loh Liang',
                'lokasi'    => 'Taman Nasional Komodo, NTT',
                'foto'      => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Suaka Margasatwa',
                'deskripsi' => 'Jalur penjelajahan hutan savana bersama jagawana berpengalaman untuk melihat kehidupan satwa langka komodo.'
            ],
            [
                'id'        => 'pulau-kalong',
                'nama'      => 'Pulau Kalong Saat Senja',
                'lokasi'    => 'Kawasan Perairan Bajo, NTT',
                'foto'      => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Senja Spektakuler',
                'deskripsi' => 'Kawasan hutan bakau tempat ribuan kelelawar raksasa terbang melintasi langit senja lembayung keemasan.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'ayana-komodo',
                'nama'      => 'AYANA Komodo Waecicu Beach',
                'lokasi'    => 'Pantai Waecicu, Labuan Bajo',
                'jenis'     => 'Resor Mewah Bintang 5 di Tepi Pantai',
                'harga'     => 'Rp 3.800.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Dermaga Pribadi • Bar Atap untuk Menikmati Senja • Akses Pantai Privat'
            ],
            [
                'id'        => 'sudamala-komodo',
                'nama'      => 'Sudamala Resort Komodo',
                'lokasi'    => 'Pantai Pede, Labuan Bajo',
                'jenis'     => 'Resor & Vila Pesisir Mewah',
                'harga'     => 'Rp 2.450.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Kolam Tepi Laut • Spa Tradisional • Restoran Hidangan Laut Tropis'
            ],
            [
                'id'        => 'bajo-nature-lodge',
                'nama'      => 'Eco Tree O’tel Labuan Bajo',
                'lokasi'    => 'Jl. Soekarno Hatta, Kampung Ujung',
                'jenis'     => 'Hotel Ekologis Berkonsep Butik',
                'harga'     => 'Rp 750.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Pemandangan Dermaga Kapal • Restoran di Atap Gedung • Dekat Sentra Kuliner'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'ikan-kuah-asam-bajo',
                'nama'      => 'Ikan Kuah Asam Segar Flores',
                'jenis'     => 'Kuliner Sup Pesisir Tradisional',
                'lokasi'    => 'Pasar Hidangan Laut Kampung Ujung',
                'harga'     => 'Rp 50.000 - Rp 90.000',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Ikan kerapu segar dimasak dengan kuah asam belimbing wuluh, kemangi, dan rempah kunyit harum tanpa santan.'
            ],
            [
                'id'        => 'kolo-nasi-bambu',
                'nama'      => 'Kolo (Nasi Bakar Bambu Manggarai)',
                'jenis'     => 'Makanan Adat Khas Manggarai',
                'lokasi'    => 'Pusat Kuliner Lokal Labuan Bajo',
                'harga'     => 'Rp 30.000 - Rp 55.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Beras ketan dan rempah tradisional dimasak di dalam bilah bambu muda lalu dibakar di atas bara api hingga gurih wangi.'
            ],
            [
                'id'        => 'sei-sapi-flores',
                'nama'      => 'Se’i Daging Asap Sambal Lu’at',
                'jenis'     => 'Olahan Daging Asap Autentik NTT',
                'lokasi'    => 'Jl. Pantai Pede & Pusat Kota',
                'harga'     => 'Rp 40.000 - Rp 75.000',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Daging empuk yang diasap perlahan dengan kayu kosambi harum, disajikan dengan daun singkong tumis dan sambal lu’at segar.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-water',
                'judul'     => 'Berlayar dan Menginap di Kapal Pinisi Mengelilingi Taman Nasional',
                'lokasi'    => 'Perairan Komodo & Pulau Sekitar',
                'durasi'    => '3 Hari 2 Malam',
                'deskripsi' => 'Tidur di atas kabin kapal pinisi megah sembari berpindah dari pulau ke pulau dengan pemandangan bintang.'
            ],
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Pendakian ke Puncak Savana Pulau Padar Saat Matahari Terbit',
                'lokasi'    => 'Puncak Gardu Pandang Pulau Padar',
                'durasi'    => 'Pagi Hari (05:30 - 08:00)',
                'deskripsi' => 'Mendaki tangga bukit savana untuk menyambut matahari terbit berlatar lekukan teluk legendaris kepulauan Flores.'
            ],
            [
                'icon'      => 'bi-shield-check',
                'judul'     => 'Menjumpai Satwa Komodo di Jalur Penjelajahan Loh Liang',
                'lokasi'    => 'Pulau Komodo, Taman Nasional',
                'durasi'    => '2 - 3 Jam',
                'deskripsi' => 'Berjalan menyusuri hutan musim bersama pemandu alam untuk mengamati perilaku alami komodo di alam bebas.'
            ],
            [
                'icon'      => 'bi-sun',
                'judul'     => 'Selam Permukaan Bersama Kawanan Pari Manta di Manta Point',
                'lokasi'    => 'Manta Point (Karang Makassar)',
                'durasi'    => 'Setengah Hari',
                'deskripsi' => 'Berenang berdampingan bersama pari manta raksasa yang melayang anggun di perairan jernih berarus tenang.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat: Rp 550.000 - Rp 950.000 / hari (hostel kota, paket wisata bersama dengan kapal cepat, kuliner pasar malam)',
                    'Menengah / Keluarga: Rp 1.500.000 - Rp 3.200.000 / hari (hotel tepi pantai, berbagi kapal pinisi berpendingin udara)',
                    'Mewah / Sewa Kapal Privat: Rp 5.500.000+ / hari (sewa kapal pesiar pinisi privat, kamar suite resor bintang 5, juru masak pribadi)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Kapal Wisata / Pinisi: Rp 1.800.000 - Rp 4.500.000 / orang untuk paket perjalanan bersama 3 hari 2 malam',
                    'Sewa Kapal Cepat 1 Hari (6 Destinasi): Rp 1.200.000 - Rp 1.500.000 / orang',
                    'Sewa Motor di Kota Bajo: Rp 75.000 - Rp 100.000 / hari untuk jalan-jalan ke Gua Rangko & Bukit Silvia'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Selalu patuhi instruksi jagawana saat berada di area pengamatan komodo dan jangan membuat gerakan tiba-tiba',
                    'Gunakan sepatu pendakian dengan daya cengkeram baik saat menaiki tangga terjal berbatu di bukit Pulau Padar',
                    'Bawa botol minum isi ulang (tumbler) untuk mengurangi sampah plastik sekali pakai di kawasan taman nasional'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Savana Hijau (Desember - April): Savana tampak hijau segar, musim buah mangga, perairan lebih sepi wisatawan',
                    'Musim Savana Emas (Mei - Oktober): Cuaca sangat cerah, pemandangan savana cokelat keemasan yang dramatis',
                    'Musim Pari Manta (Agustus - Oktober): Waktu puncak kawanan pari manta berkumpul di perairan hangat Flores'
                ]
            ]
        ]
    ],

    // =========================================================================
    // 5. DANAU TOBA (ID 5)
    // =========================================================================
    5 => [
        'id'           => 5,
        'slug'         => 'danau-toba',
        'nama'         => 'Danau Toba',
        'provinsi'     => 'Sumatera Utara',
        'wilayah'      => 'Kawasan Kaldera Toba, Sumatera Utara',
        'kategori'     => 'Wisata Alam & Budaya',
        'rating'       => 4.7,
        'total_ulasan' => 3120,
        'hero_image'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Kemegahan Danau Vulkanik Terbesar di Asia Tenggara dan Warisan Luhur Batak di Pulau Samosir',
        'deskripsi'    => 'Danau Toba adalah kaldera vulkanik raksasa warisan letusan supervolcano purba yang kini menjadi danau vulkanik terbesar di Asia Tenggara dan UNESCO Global Geopark. Dikelilingi tebing-tebing hijau pinus yang menjulang dan perairan tenang berkabut sejuk, Danau Toba menyimpan keanggunan budaya Batak di Pulau Samosir serta harmoni alam nusantara yang meneduhkan jiwa.',
        'about_heading'=> 'Keajaiban Kaldera Raksasa dan Pusaka Batak',
        'daya_tarik'   => [
            [
                'judul' => 'Kaldera Supervolcano Dunia',
                'desc'  => 'Danau kaldera vulkanik raksasa dengan kedalaman lebih dari 500 meter diapit perbukitan asri pinus.',
                'icon'  => 'bi-water'
            ],
            [
                'judul' => 'Warisan Luhur Adat Batak',
                'desc'  => 'Rumah adat Bolon bersejarah, makam raja-raja kuno Sidabutar, dan tradisi tenun kain Ulos di Samosir.',
                'icon'  => 'bi-bank2'
            ],
            [
                'judul' => 'Perbukitan Hijau Savana Holbung',
                'desc'  => 'Lanskap perbukitan hijau bertingkat menjorok ke perairan danau dengan panorama 360 derajat.',
                'icon'  => 'bi-tree-fill'
            ],
            [
                'judul' => 'Gemuruh Air Terjun Sipiso-piso',
                'desc'  => 'Air terjun megah setinggi 120 meter yang jatuh menghujam ngarai hijau menuju muara Danau Toba.',
                'icon'  => 'bi-droplet-fill'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'Mei - September (Musim Kering Sejuk)',
            'cuaca' => 'Suhu sejuk berkisar 17°C - 24°C dengan angin danau segar dan pemandangan perairan bebas kabut tebal.',
            'tips'  => 'Bulan Juni dan Juli sangat cocok untuk berkendara santai mengelilingi pesisir Pulau Samosir.'
        ],
        'popular_places' => [
            [
                'id'        => 'samosir',
                'nama'      => 'Pulau Samosir',
                'lokasi'    => 'Danau Toba, Sumatera Utara',
                'foto'      => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pusat Budaya',
                'deskripsi' => 'Pulau vulkanik di tengah Danau Toba yang menjadi jantung kebudayaan Batak dan pesona perairan sejuk.'
            ],
            [
                'id'        => 'bukit-holbung',
                'nama'      => 'Bukit Holbung',
                'lokasi'    => 'Janji Martahan, Samosir',
                'foto'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Savana Danau',
                'deskripsi' => 'Perbukitan hijau fotogenik yang menyajikan lanskap terluas Danau Toba dan pemandangan matahari senja.'
            ],
            [
                'id'        => 'tomok',
                'nama'      => 'Desa Wisata Tomok',
                'lokasi'    => 'Simanindo, Pulau Samosir',
                'foto'      => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Desa Adat Batak',
                'deskripsi' => 'Kawasan desa budaya dengan deretan Rumah Bolon otentik, makam batu Raja Sidabutar, dan pasar suvenir Ulos.'
            ],
            [
                'id'        => 'sipiso-piso',
                'nama'      => 'Air Terjun Sipiso-piso',
                'lokasi'    => 'Tongging, Danau Toba Utara',
                'foto'      => 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Air Terjun Raksasa',
                'deskripsi' => 'Salah satu air terjun tertinggi di Indonesia yang mengalir deras dari sungai bawah tanah menuju kaldera Toba.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'taman-simalem',
                'nama'      => 'Taman Simalem Resort',
                'lokasi'    => 'Merek, Karo - Danau Toba',
                'jenis'     => 'Resor Mewah Ekologis Dataran Tinggi',
                'harga'     => 'Rp 2.100.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Gardu Pandang Kaldera Privat • Kebun Teh Organik • Lapangan Golf'
            ],
            [
                'id'        => 'tiara-bunga',
                'nama'      => 'Tiara Bunga Hotel & Villa',
                'lokasi'    => 'Balige, Toba Samosir',
                'jenis'     => 'Vila Tepi Danau & Resor Pantai',
                'harga'     => 'Rp 1.450.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Akses Perahu Privat • Pasir Putih Tepi Danau • Restoran Terapung'
            ],
            [
                'id'        => 'zoes-paradise',
                'nama'      => 'Zoe’s Paradise Waterfront Hotel',
                'lokasi'    => 'Tuktuk Siadong, Samosir',
                'jenis'     => 'Penginapan Alam Berkonsep Butik',
                'harga'     => 'Rp 650.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Balkon Tepi Danau • Kolam Alami • Restoran Masakan Rumahan'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'arsik-ikan-mas',
                'nama'      => 'Ikan Mas Arsik Bumbu Andaliman',
                'jenis'     => 'Hidangan Tradisional Luhur Batak',
                'lokasi'    => 'Tuktuk Siadong & Balige',
                'harga'     => 'Rp 45.000 - Rp 80.000',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Ikan mas segar dimasak bumbu kuning rempah andaliman khas Batak, asam gelugur, kincung, dan lokio yang harum getir.'
            ],
            [
                'id'        => 'mie-gomak-toba',
                'nama'      => 'Mie Gomak Kuah Santan Hangat',
                'jenis'     => 'Spaghetti Khas Tanah Batak',
                'lokasi'    => 'Warung Tradisional Parapat & Pangururan',
                'harga'     => 'Rp 15.000 - Rp 30.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Mie lidi tebal kenyal disiram kuah santan rempah andaliman gurih pedas hangat, sangat nikmat disantap di udara dingin danau.'
            ],
            [
                'id'        => 'kopi-lintong-toba',
                'nama'      => 'Seduhan Kopi Arabika Lintong & Tipang',
                'jenis'     => 'Kopi Asal Tunggal Kaldera',
                'lokasi'    => 'Kedai Kopi Pesisir Danau Toba',
                'harga'     => 'Rp 20.000 - Rp 35.000',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Kopi arabika dataran tinggi Toba yang diproses basah dengan cita rasa rempah hangat, cokelat hitam, dan keasaman seimbang.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Bersepeda Santai Menyusuri Semenanjung Tuktuk',
                'lokasi'    => 'Tuktuk Siadong, Pulau Samosir',
                'durasi'    => 'Setengah Hari',
                'deskripsi' => 'Mengayuh sepeda santai di jalanan beraspal teduh pinggir danau sembari menikmati arsitektur rumah khas Batak.'
            ],
            [
                'icon'      => 'bi-stars',
                'judul'     => 'Menyaksikan Tarian Magis Patung Boneka Sigale-gale',
                'lokasi'    => 'Desa Simanindo, Samosir',
                'durasi'    => 'Pagi Hari (10:30 - 11:30)',
                'deskripsi' => 'Atraksi pertunjukan seni boneka kayu tradisional Batak yang menari mengikuti lantunan tabuhan gondang sabangunan.'
            ],
            [
                'icon'      => 'bi-tree-fill',
                'judul'     => 'Menjelajahi Perbukitan Hijau Holbung Bukit Cinta',
                'lokasi'    => 'Bukit Holbung, Samosir',
                'durasi'    => '2 - 3 Jam',
                'deskripsi' => 'Mendaki padang savana berundak dengan tiupan angin sejuk kaldera dan panorama kapal melintas di bawah bukit.'
            ],
            [
                'icon'      => 'bi-water',
                'judul'     => 'Menyeberang Kaldera dengan Kapal Feri Tradisional',
                'lokasi'    => 'Dermaga Parapat - Tomok Samosir',
                'durasi'    => '45 Menit',
                'deskripsi' => 'Menikmati ketenangan permukaan danau vulkanik luas dengan latar tebing kaldera menjulang dari atas dek kapal.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat: Rp 280.000 - Rp 500.000 / hari (rumah inap Tuktuk, sewa motor, kuliner lokal)',
                    'Menengah / Keluarga: Rp 750.000 - Rp 1.500.000 / hari (resor tepi danau, sewa mobil keliling Samosir)',
                    'Mewah / Dataran Tinggi: Rp 2.200.000+ / hari (resor bintang 5 di kaldera atas, tur privat bersama pemandu budaya Batak)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Kapal Penumpang Parapat - Tuktuk: Rp 15.000 - Rp 25.000 / orang (diantar langsung ke dermaga hotel)',
                    'Sewa Motor di Samosir: Rp 80.000 - Rp 120.000 / hari (pilihan paling ideal menjelajahi pulau)',
                    'Sewa Mobil Medan/Silangit - Toba: Rp 450.000 - Rp 700.000 / hari'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Pilihlah penerbangan langsung menuju Bandara Internasional Silangit (DTB) untuk akses tercepat (30 menit ke danau)',
                    'Kenakan pakaian hangat karena suhu malam hari di sekitar danau dapat turun hingga 16 derajat Celsius',
                    'Ucapkan salam santun "Horas" saat menyapa masyarakat setempat sebagai bentuk keramahan khas Batak'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Kering Sejuk (Mei - September): Hari-hari cerah, ombak danau sangat tenang, cocok untuk wisata air',
                    'Musim Festival (Oktober - Desember): Penuh perayaan budaya Batak, pertunjukan seni gondang, dan musik rakyat',
                    'Bulan Terbaik: Juni dan Juli menawarkan pemandangan bukit paling jernih'
                ]
            ]
        ]
    ],

    // =========================================================================
    // 6. YOGYAKARTA & CANDI (ID 6)
    // =========================================================================
    6 => [
        'id'           => 6,
        'slug'         => 'yogyakarta',
        'nama'         => 'Yogyakarta & Candi',
        'provinsi'     => 'D.I. Yogyakarta',
        'wilayah'      => 'D.I. Yogyakarta & Jawa Tengah',
        'kategori'     => 'Wisata Budaya, Sejarah & Seni',
        'rating'       => 4.8,
        'total_ulasan' => 4680,
        'hero_image'   => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=1920&q=80',
        'tagline'      => 'Jantung Peradaban Luhur Jawa dengan Mahakarya Candi Dunia dan Kehangatan Budaya Kota Seni',
        'deskripsi'    => 'Yogyakarta adalah episentrum seni dan kebudayaan Jawa yang memancarkan pesona sejarah agung. Rumah bagi mahakarya arsitektur candi warisan dunia UNESCO seperti Candi Prambanan dan Borobudur, kemegahan Keraton Ngayogyakarta, serta kehangatan seniman dan kuliner legendaris Malioboro, Jogja senantiasa menyambut setiap pengelana dengan keramahan yang begitu membekas di hati.',
        'about_heading'=> 'Harmoni Mahakarya Candi dan Peradaban Keraton',
        'daya_tarik'   => [
            [
                'judul' => 'Mahakarya Candi Dunia UNESCO',
                'desc'  => 'Kemegahan arsitektur Candi Prambanan yang menjulang anggun dan keagungan relief Candi Borobudur.',
                'icon'  => 'bi-bank2'
            ],
            [
                'judul' => 'Keluhuran Keraton & Budaya Jawa',
                'desc'  => 'Pusat tradisi Kesultanan Ngayogyakarta Hadiningrat dengan gamelan, tari keraton, dan filosofi luhur.',
                'icon'  => 'bi-stars'
            ],
            [
                'judul' => 'Romantisme Kota & Malioboro',
                'desc'  => 'Kawasan pedestrian legendaris dengan deretan seniman musik jalanan, delman andong, dan kuliner malam.',
                'icon'  => 'bi-geo-alt'
            ],
            [
                'judul' => 'Pusat Kerajinan Batik & Perak',
                'desc'  => 'Warisan seni adiluhung membatik tulis tradisional serta kerajinan ukiran perak filigree di Kotagede.',
                'icon'  => 'bi-brush'
            ]
        ],
        'waktu_terbaik' => [
            'musim' => 'Juni - Oktober (Musim Kemarau Sejuk)',
            'cuaca' => 'Cerah bersih dan nyaman, waktu paling ideal menjelajahi kompleks pelataran candi dan jalanan kota tua.',
            'tips'  => 'Kunjungi Candi Prambanan sore hari untuk menikmati senja keemasan lalu saksikan Sendratari Ramayana malam harinya.'
        ],
        'popular_places' => [
            [
                'id'        => 'prambanan',
                'nama'      => 'Candi Prambanan',
                'lokasi'    => 'Bokoharjo, Sleman, Yogyakarta',
                'foto'      => 'https://images.unsplash.com/photo-1584810359583-96fc3448beaa?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Candi Hindu Terbesar',
                'deskripsi' => 'Kompleks candi Hindu terindah di Asia Tenggara dengan stupa candi Siwa setinggi 47 meter yang menjulang megah.'
            ],
            [
                'id'        => 'borobudur',
                'nama'      => 'Candi Borobudur',
                'lokasi'    => 'Magelang, Jawa Tengah',
                'foto'      => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Warisan Dunia UNESCO',
                'deskripsi' => 'Monumen Buddha terbesar di dunia berhiaskan 2.672 panel relief dan 504 arca Buddha yang mengagumkan.'
            ],
            [
                'id'        => 'keraton',
                'nama'      => 'Keraton Yogyakarta',
                'lokasi'    => 'Kraton, Kota Yogyakarta',
                'foto'      => 'https://images.unsplash.com/photo-1590736969955-71cc94901144?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Pusat Budaya Jawa',
                'deskripsi' => 'Istana resmi Kesultanan Ngayogyakarta Hadiningrat yang masih lestari dengan museum pusaka dan pagelaran tari.'
            ],
            [
                'id'        => 'malioboro',
                'nama'      => 'Jalan Malioboro',
                'lokasi'    => 'Jantung Kota Yogyakarta',
                'foto'      => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=800&q=80',
                'tag'       => 'Ikon Romansa Kota',
                'deskripsi' => 'Jalur pedestrian legendaris pusat belanja oleh-oleh, pertunjukan musik angklung, andong, dan kuliner lesehan.'
            ]
        ],
        'recommended_stays' => [
            [
                'id'        => 'the-phoenix',
                'nama'      => 'The Phoenix Hotel Yogyakarta (MGallery)',
                'lokasi'    => 'Jl. Jend. Sudirman, Kota Yogyakarta',
                'jenis'     => 'Hotel Mewah Bergaya Kolonial Bersejarah',
                'harga'     => 'Rp 1.450.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Arsitektur Kolonial 1918 • Kolam Renang Klasik • Restoran Masakan Keraton'
            ],
            [
                'id'        => 'plataran-borobudur',
                'nama'      => 'Plataran Heritage Borobudur Hotel',
                'lokasi'    => 'Borobudur, Magelang',
                'jenis'     => 'Resor & Spa Mewah Bernuansa Warisan Budaya',
                'harga'     => 'Rp 2.600.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Pemandangan Candi & Perbukitan Menoreh • Spa Tradisional Jawa • Kolam Tropis'
            ],
            [
                'id'        => 'domah-yogya',
                'nama'      => 'D’Omah Yogya Eco Resort',
                'lokasi'    => 'Sewon, Bantul, Yogyakarta',
                'jenis'     => 'Vila Ekologis Tradisional Jawa',
                'harga'     => 'Rp 850.000',
                'periode'   => '/ malam',
                'foto'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                'fasilitas' => 'Rumah Joglo Kayu Jati • Kolam Renang Lotus • Suasana Pedesaan Tenang'
            ]
        ],
        'local_culinary' => [
            [
                'id'        => 'gudeg-yu-djum',
                'nama'      => 'Gudeg Asli Yu Djum Wijilan',
                'jenis'     => 'Kuliner Legendaris Yogyakarta',
                'lokasi'    => 'Sentra Gudeg Wijilan, Kraton',
                'harga'     => 'Rp 30.000 - Rp 65.000',
                'foto'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Nangka muda manis gurih yang dimasak kayu bakar selama berjam-jam, disajikan dengan ayam kampung, telur bebek, dan krecek pedas.'
            ],
            [
                'id'        => 'sate-klatak-pak-pong',
                'nama'      => 'Sate Klatak Jeruji Besi & Kopi Joss',
                'jenis'     => 'Sate Kambing Khas Imogiri',
                'lokasi'    => 'Imogiri Timur, Bantul',
                'harga'     => 'Rp 35.000 - Rp 50.000',
                'foto'      => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Daging kambing muda empuk yang ditusuk jeruji sepeda lalu dibakar garam merica, disajikan dengan kuah gulai gurih gurih sedap.'
            ],
            [
                'id'        => 'bakpia-kukus-tugu',
                'nama'      => 'Bakpia Pathok & Bakpia Kukus Tugu',
                'jenis'     => 'Oleh-Oleh Khas Kota Budaya',
                'lokasi'    => 'Kawasan Pathok & Malioboro',
                'harga'     => 'Rp 35.000 - Rp 55.000 / kotak',
                'foto'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
                'deskripsi' => 'Kue manis bertekstur lembut dengan varian isian kacang hijau lezat, keju gurih, dan cokelat lumer yang dipanggang segar.'
            ]
        ],
        'things_to_do' => [
            [
                'icon'      => 'bi-stars',
                'judul'     => 'Menyaksikan Sendratari Ramayana Prambanan',
                'lokasi'    => 'Panggung Terbuka Candi Prambanan',
                'durasi'    => 'Malam Hari (19:30 - 21:30)',
                'deskripsi' => 'Pertunjukan kolosal tarian Jawa berlatar kemegahan siluet Candi Prambanan bermandikan cahaya lampu spektakuler.'
            ],
            [
                'icon'      => 'bi-sun',
                'judul'     => 'Tur Matahari Terbit Menikmati Siluet Candi Borobudur',
                'lokasi'    => 'Punthuk Setumbu, Magelang',
                'durasi'    => 'Subuh (04:30 - 07:00)',
                'deskripsi' => 'Menyaksikan matahari terbit perlahan menyinari kabut lembah dengan kemunculan puncak stupa candi Borobudur.'
            ],
            [
                'icon'      => 'bi-brush',
                'judul'     => 'Workshop Membatik Tulis Tradisional di Kampung Batik',
                'lokasi'    => 'Kampung Batik Giriloyo & Kotagede',
                'durasi'    => '2 - 3 Jam',
                'deskripsi' => 'Belajar menorehkan canting dan malam panas di atas selembar kain mori dipandu langsung pembatik maestro lokal.'
            ],
            [
                'icon'      => 'bi-compass',
                'judul'     => 'Wisata Malam Menyusuri Titik Nol Kilometer & Malioboro',
                'lokasi'    => 'Kawasan Titik Nol & Benteng Vredeburg',
                'durasi'    => 'Malam Hari (19:00 - 22:00)',
                'deskripsi' => 'Berjalan santai di trotoar luas menikmati bangunan cagar budaya kolonial, kuliner angkringan, dan alunan musisi jalanan.'
            ]
        ],
        'travel_info' => [
            'biaya' => [
                'title' => 'Estimasi Biaya Harian',
                'icon'  => 'bi-cash-coin',
                'items' => [
                    'Wisatawan Hemat: Rp 200.000 - Rp 400.000 / hari (rumah inap Prawirotaman, sewa motor, angkringan dan gudeg)',
                    'Menengah / Keluarga: Rp 650.000 - Rp 1.400.000 / hari (hotel bintang 3-4, sewa mobil, tur candi)',
                    'Mewah / Warisan Budaya: Rp 2.200.000+ / hari (resor mewah dekat Borobudur, tur privat eksklusif ke candi, santap mewah di keraton)'
                ]
            ],
            'transportasi' => [
                'title' => 'Pilihan Transportasi',
                'icon'  => 'bi-car-front',
                'items' => [
                    'Trans Jogja & KRL Jogja-Solo: Rp 3.600 - Rp 8.000 / perjalanan (sangat ramah kantong dan menjangkau pusat wisata)',
                    'Sewa Motor Kota: Rp 70.000 - Rp 100.000 / hari (sangat lincah untuk jalan-jalan dalam dan luar kota)',
                    'Sewa Mobil + Sopir: Rp 450.000 - Rp 650.000 / hari (cocok untuk rute jauh ke Candi Borobudur & Tebing Breksi)'
                ]
            ],
            'tips' => [
                'title' => 'Tips Perjalanan Penting',
                'icon'  => 'bi-shield-check',
                'items' => [
                    'Pesan tiket naik struktur Candi Borobudur jauh-jauh hari secara online karena slot per jam dibatasi kuota',
                    'Kenakan pakaian sopan berkerah dan hindari celana pendek saat mengunjungi kompleks Keraton Yogyakarta',
                    'Selalu tanyakan harga terlebih dahulu sebelum memesan hidangan di warung tenda atau menaiki andong wisata'
                ]
            ],
            'musim' => [
                'title' => 'Waktu Kunjungan Terbaik',
                'icon'  => 'bi-calendar-check',
                'items' => [
                    'Musim Kemarau (Juni - Oktober): Waktu paling nyaman untuk tur keliling candi luar ruangan tanpa terkendala hujan',
                    'Musim Perayaan Sekaten (Maulid Nabi): Momen budaya meriah dengan pasar malam rakyat dan grebeg keraton yang sakral',
                    'Bulan Terbaik: Juli dan Agustus bertepatan dengan pagelaran Sendratari Ramayana di panggung terbuka'
                ]
            ]
        ]
    ]
];
