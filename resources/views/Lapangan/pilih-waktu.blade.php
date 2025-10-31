<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pilih Waktu - Raketin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#E9F0FB]">
    @include('layouts.navbar')

    <!-- Breadcrumb -->
    <div class="container mx-auto px-6 py-6 text-sm text-gray-500">
        <a href="/" class="hover:text-blue-600">Home</a> /
        <a href="{{ route('lapangan.index') }}" class="hover:text-blue-600">Lapangan</a> /
        <a href="{{ route('lapangan.detail', $lapangan->id) }}">Detail Lapangan</a> /
        <span class="text-gray-800 font-medium">Pilih Waktu</span>
    </div>

    <div class="container mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Pilih Waktu</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Kalender & Slot -->
            <div class="bg-white p-6 rounded-xl shadow-md md:col-span-2">
                <!-- Navigasi Bulan (disable karena tanggal dikunci) -->
                <div class="flex justify-between items-center mb-4">
                    <button id="prevMonth" class="p-2 text-gray-400 cursor-not-allowed" disabled>&lt;</button>
                    <h3 id="monthYear" class="text-lg font-semibold text-gray-800"></h3>
                    <button id="nextMonth" class="p-2 text-gray-400 cursor-not-allowed" disabled>&gt;</button>
                </div>

                <!-- Nama Hari -->
                <div class="grid grid-cols-7 gap-2 text-center text-sm text-gray-600 font-medium mb-2">
                    <div>M</div>
                    <div>S</div>
                    <div>S</div>
                    <div>R</div>
                    <div>K</div>
                    <div>J</div>
                    <div>S</div>
                </div>

                <!-- Kalender -->
                <div id="calendarDays" class="grid grid-cols-7 gap-2 text-center text-sm mb-6"></div>

                <!-- Slot Waktu -->
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Pilih Jam Bermain</h3>
                <div id="slotsContainer" class="grid grid-cols-3 gap-2 text-center text-sm"></div>
            </div>

            <!-- Summary -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
                <div class="text-gray-600 text-sm mb-2">
                    <p><strong>Lapangan:</strong> {{ $lapangan->nama }}</p>
                    <p><strong>Tanggal:</strong> <span id="selectedDate">-</span></p>
                    <p><strong>Jam:</strong> <span id="selectedTime">-</span></p>
                    <p><strong>Durasi:</strong> <span id="selectedDuration">0 jam</span></p>
                </div>

                <p class="text-right text-lg font-bold text-blue-600 mt-4">
                    Rp <span id="totalPrice">0</span>
                </p>

                <button
                    class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                    Continue to Payment
                </button>
            </div>

        </div>
    </div>

    <script>
        // Ambil tanggal dari backend
        const tanggalDipilih = "{{ $tanggalDipilih }}"; // dari Laravel
        const selectedDate = new Date(tanggalDipilih);
        const hargaPerJam = {{ $lapangan->harga_per_jam }};

        const monthYearEl = document.getElementById("monthYear");
        const calendarDaysEl = document.getElementById("calendarDays");
        const slotsContainer = document.getElementById("slotsContainer");
        const selectedDateEl = document.getElementById("selectedDate");
        const selectedTimeEl = document.getElementById("selectedTime");
        const selectedDurationEl = document.getElementById("selectedDuration");
        const totalPriceEl = document.getElementById("totalPrice");

        let selectedSlots = [];

        const slotTimes = [
            "08:00", "09:00", "10:00", "11:00",
            "12:00", "13:00", "14:00", "15:00",
            "16:00", "17:00", "18:00", "19:00",
            "20:00", "21:00", "22:00"
        ];

        function renderCalendar() {
            const date = new Date(tanggalDipilih);
            const year = date.getFullYear();
            const month = date.getMonth();
            const selectedDay = date.getDate();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDay = firstDay.getDay();

            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            monthYearEl.textContent = `${monthNames[month]} ${year}`;

            calendarDaysEl.innerHTML = "";

            // Spasi sebelum tanggal pertama
            for (let i = 0; i < (startDay === 0 ? 6 : startDay - 1); i++) {
                calendarDaysEl.innerHTML += `<div></div>`;
            }

            // Render tanggal bulan ini
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const btn = document.createElement("button");
                btn.textContent = day;
                btn.className = "p-2 rounded-lg hover:bg-gray-100 transition text-gray-700";
                const fullDate = new Date(year, month, day);
                const dateString = fullDate.toISOString().split("T")[0];

                if (dateString === tanggalDipilih) {
                    btn.classList.add("bg-blue-600", "text-white", "font-semibold");
                } else {
                    btn.classList.add("text-gray-400", "cursor-not-allowed", "opacity-60");
                    btn.disabled = true;
                }

                calendarDaysEl.appendChild(btn);
            }

            selectedDateEl.textContent = date.toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric"
            });

            renderSlots();
        }

        function renderSlots() {
            slotsContainer.innerHTML = "";
            slotTimes.forEach(time => {
                const btn = document.createElement("button");
                btn.textContent = time;
                btn.className = "p-2 rounded-lg border border-gray-300 hover:bg-blue-50 transition";

                btn.addEventListener("click", () => {
                    const index = selectedSlots.indexOf(time);
                    if (index > -1) {
                        // Hapus dari pilihan
                        selectedSlots.splice(index, 1);
                        btn.classList.remove("bg-blue-600", "text-white", "font-semibold");
                    } else {
                        // Tambahkan ke pilihan
                        selectedSlots.push(time);
                        selectedSlots.sort();
                        btn.classList.add("bg-blue-600", "text-white", "font-semibold");
                    }

                    updateSummary();
                });

                slotsContainer.appendChild(btn);
            });
        }

        function updateSummary() {
            if (selectedSlots.length > 0) {
                const start = selectedSlots[0];
                const end = slotTimes[slotTimes.indexOf(selectedSlots[selectedSlots.length - 1]) + 1];
                selectedTimeEl.textContent = end ? `${start} - ${end}` : `${start} - selesai`;
            } else {
                selectedTimeEl.textContent = "-";
            }

            selectedDurationEl.textContent = `${selectedSlots.length} jam`;
            totalPriceEl.textContent = (selectedSlots.length * hargaPerJam).toLocaleString("id-ID");
        }

        renderCalendar();
    </script>
</body>

</html>
