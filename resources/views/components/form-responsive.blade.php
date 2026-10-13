<section id="contact" class="max-w-2xl mx-auto py-6 sm:py-10">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-8 lg:p-10 shadow-lg space-y-6">
        
        <div class="text-center space-y-2">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Konsultasi Implementasi Enterprise</h2>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">Hubungi tim kami untuk aktivasi portal multi-cabang atau integrasi API sistem payroll.</p>
        </div>

        <form onsubmit="event.preventDefault(); alert('Terima kasih! Permintaan Anda telah diterima.');" class="space-y-4 sm:space-y-5">
            <!-- 2-Column Responsive Grid on sm: -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label for="fullname" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="fullname" 
                           name="fullname" 
                           required 
                           placeholder="Contoh: Pratama Wijaya"
                           class="w-full min-h-[48px] px-3.5 py-3 text-base sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <!-- Email Perusahaan -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                        Email Kerja <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           placeholder="nama@perusahaan.com"
                           class="w-full min-h-[48px] px-3.5 py-3 text-base sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Perkiraan Tim -->
            <div class="space-y-1.5">
                <label for="team_size" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Jumlah Karyawan / Pengguna Aktif
                </label>
                <select id="team_size" 
                        name="team_size" 
                        class="w-full min-h-[48px] px-3.5 py-3 text-base sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="1-50">1 - 50 Karyawan (Starter Tim)</option>
                    <option value="51-200">51 - 200 Karyawan (Professional Branch)</option>
                    <option value="201-1000">201 - 1,000 Karyawan (Enterprise Multi-Site)</option>
                    <option value="1000+">&gt; 1,000 Karyawan (Corporate Custom)</option>
                </select>
            </div>

            <!-- Catatan Kebutuhan -->
            <div class="space-y-1.5">
                <label for="notes" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Kebutuhan Integrasi atau Pertanyaan Khusus
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="4" 
                          placeholder="Misal: integrasi mesin absensi fisik, sistem shift rolling, atau ekspor laporan khusus..."
                          class="w-full px-3.5 py-3 text-base sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-y"></textarea>
            </div>

            <!-- Tombol Kirim: Min Touch Height 48px, Full Width di Mobile -->
            <button type="submit" 
                    class="touch-target w-full min-h-[48px] px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-sm sm:text-base rounded-xl shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center gap-2">
                <span>Kirim Permintaan Konsultasi</span>
                <i class="ti ti-send text-lg"></i>
            </button>
        </form>

    </div>
</section>
