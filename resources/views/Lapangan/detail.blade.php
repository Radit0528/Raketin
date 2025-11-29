<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Lapangan - Raketin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#E9F0FB]">
    @include('layouts.navbar')

    <!-- Breadcrumb -->
    <div class="container mx-auto px-6 py-6 text-sm text-gray-500">
        <a href="/" class="hover:text-blue-600">Home</a> /
        <a href="{{ route('lapangan.index') }}" class="hover:text-blue-600">Lapangan</a> /
        <span class="text-gray-800 font-medium">{{ $lapangan->nama }}</span>
    </div>

    <!-- Detail Lapangan -->
    <section class="container mx-auto px-6 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Gambar & Deskripsi -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
                <div class="grid grid-cols-3 gap-2 mb-6">
                    <div class="col-span-3">
                        <img src="{{ asset('storage/lapangan_images/' . basename($lapangan->gambar)) }}"
                            alt="Lapangan {{ $lapangan->nama }}"
                            class="w-full h-64 rounded-xl object-cover bg-gray-200"
                            onerror="this.onerror=null;this.src='https://placehold.co/600x400/CCCCCC/333333?text=Gambar+Lapangan';">
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $lapangan->nama }}</h1>
                <p class="text-gray-600 leading-relaxed mb-6">
                    {{ $lapangan->deskripsi ?? 'Lapangan ini menawarkan permukaan permainan profesional dengan pencahayaan dan ventilasi yang sangat baik.' }}
                </p>

                <h3 class="text-lg font-semibold text-gray-800 mb-3">Fasilitas</h3>

                @php
                    $icons = [
                        'Toilet' => 'fa-toilet',
                        'Loker' => 'fa-lock',
                        'Area Tunggu' => 'fa-couch',
                        'Parkir' => 'fa-car',
                        'Mushola' => 'fa-mosque',
                        'Kantin' => 'fa-utensils',
                        'Wifi' => 'fa-wifi',
                        'Kursi Penonton' => 'fa-chair',
                    ];

                    $fasilitasArray = !empty($lapangan->fasilitas)
                        ? array_map('trim', explode(',', $lapangan->fasilitas))
                        : [];
                @endphp

                <div class="flex flex-wrap gap-3 mb-6">
                    @foreach ($fasilitasArray as $fa)
                        <div class="inline-flex items-center gap-2 bg-blue-50 px-3 py-1.5 rounded-lg text-blue-700 font-medium">
                            <i class="fa-solid {{ $icons[$fa] ?? 'fa-circle-check' }} text-l"></i>
                            <span class="text-l">{{ $fa }}</span>
                        </div>
                    @endforeach
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-3">Lokasi</h3>
                {{-- Contoh embed map, bisa disimpan di DB juga kalau mau --}}
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                    <iframe
                        src="https://www.google.com/maps?q={{ urlencode($lapangan->lokasi) }}&output=embed"
                        width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
        </div>

            <!-- Sidebar Harga & Kalender -->
            <div class="bg-white shadow-md rounded-xl p-6 h-fit">
                <h3 class="text-sm text-gray-500 mb-1">Harga</h3>
                <p class="text-2xl font-bold text-blue-600 mb-4">
                    Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                    <span class="text-gray-500 text-base font-normal">/ jam</span>
                </p>

                <h3 class="text-sm text-gray-500 mb-2">Ketersediaan</h3>

                <div class="border rounded-lg p-4 bg-gray-50">
                    <!-- Header Bulan -->
                    <div class="flex justify-between items-center mb-4">
                        <button id="prevMonth"
                            class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold">
                            <i class="fa-solid fa-chevron-left"></i> <span>Prev</span>
                        </button>

                        <p id="monthYear" class="text-gray-700 font-semibold text-lg"></p>

                        <button id="nextMonth"
                            class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold">
                            <span>Next</span> <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Nama Hari -->
                    <div class="grid grid-cols-7 gap-1 text-center text-sm text-gray-600 font-medium mb-2">
                        <span>M</span>
                        <span>S</span>
                        <span>S</span>
                        <span>R</span>
                        <span>K</span>
                        <span>J</span>
                        <span>S</span>
                    </div>

                    <!-- Tanggal -->
                    <div id="calendar" class="grid grid-cols-7 gap-1 text-center text-sm text-gray-700"></div>
                </div>

                <a id="pesanBtn"
                    href="#"
                    class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Pesan Sekarang
                </a>


            </div>
        </div>
    </section>

    <script>
        const calendarEl = document.getElementById("calendar");
        const monthYearEl = document.getElementById("monthYear");
        const prevBtn = document.getElementById("prevMonth");
        const nextBtn = document.getElementById("nextMonth");
        const pesanBtn = document.getElementById("pesanBtn");

        let currentDate = new Date();
        let selectedDate = null; // simpan tanggal yang diklik user

        function renderCalendar(date) {
            calendarEl.innerHTML = "";

            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Hari kosong di awal bulan
            for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
                const empty = document.createElement("div");
                calendarEl.appendChild(empty);
            }

            // Generate hari
            for (let day = 1; day <= daysInMonth; day++) {
                const btn = document.createElement("button");
                btn.textContent = day;
                btn.className = "p-2 rounded-lg hover:bg-blue-100 transition";
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                btn.dataset.date = dateString;

                // Highlight tanggal hari ini
                const today = new Date();
                if (
                    day === today.getDate() &&
                    month === today.getMonth() &&
                    year === today.getFullYear()
                ) {
                    btn.classList.add("bg-blue-600", "text-white", "font-semibold");
                }

                // Klik tanggal
                btn.addEventListener("click", () => {
                    document.querySelectorAll("#calendar button").forEach(b => b.classList.remove("bg-blue-600", "text-white"));
                    btn.classList.add("bg-blue-600", "text-white");
                    selectedDate = dateString;
                    console.log("Tanggal dipilih:", selectedDate);
                });

                calendarEl.appendChild(btn);
            }

            const monthName = date.toLocaleString("id-ID", {
                month: "long"
            });
            monthYearEl.textContent = `${monthName} ${year}`;
        }

        // Navigasi bulan
        prevBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        nextBtn.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        // Klik tombol Pesan Sekarang
        pesanBtn.addEventListener("click", (e) => {
            e.preventDefault(); // cegah reload halaman langsung

            // ✅ Gunakan template literal untuk nilai dari Blade
            const lapanganId = "{{ $lapangan->id }}";
            const tanggal = selectedDate || new Date().toISOString().split('T')[0]; // default hari ini
            const url = `/lapangan/${lapanganId}/pilih-waktu?tanggal=${tanggal}`;

            window.location.href = url;
        });

        renderCalendar(currentDate);
    </script>

</body>

</html>