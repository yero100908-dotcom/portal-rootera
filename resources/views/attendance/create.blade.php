<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Presensi Wajah & Validasi Geofencing</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Presensi kamera real-time dengan validasi radius GPS lokasi kantor (Maks 100m)</p>
            </div>
            <a href="{{ route('attendance.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                &larr; Rekap Absensi
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="faceAttendanceApp()">
        
        <!-- GPS Geofencing Status Indicator Bar -->
        <div class="border rounded-xl p-4 sm:p-5 transition duration-300 shadow-sm"
             :class="{
                 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700 dark:text-emerald-300': geoStatus === 'allowed',
                 'bg-rose-500/10 border-rose-500/30 text-rose-700 dark:text-rose-300': geoStatus === 'denied' || geoStatus === 'out_of_bounds',
                 'bg-amber-500/10 border-amber-500/30 text-amber-700 dark:text-amber-300': geoStatus === 'loading'
             }">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg shrink-0 shadow-sm"
                         :class="{
                             'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30': geoStatus === 'allowed',
                             'bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30': geoStatus === 'denied' || geoStatus === 'out_of_bounds',
                             'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30': geoStatus === 'loading'
                         }">
                        <i class="ti" :class="geoStatus === 'allowed' ? 'ti-map-pin-check' : (geoStatus === 'loading' ? 'ti-loader animate-spin' : 'ti-map-pin-x')"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider" x-text="geoTitle"></h4>
                        <p class="text-xs mt-0.5 font-medium" x-text="geoSubtitle"></p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0 w-full sm:w-auto">
                    @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                        <button @click="setMyLocationAsOffice()" :disabled="!userLat || settingLocation" 
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:opacity-50 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                            <i class="ti ti-map-pin text-sm"></i>
                            <span x-text="settingLocation ? 'Menyimpan...' : 'Atur Lokasi Ini Sebagai Kantor'"></span>
                        </button>
                    @endif

                    <button @click="requestGeolocation()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Admin Geofence Options Bar -->
            @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800/60 flex flex-wrap items-center justify-between text-[11px] text-slate-600 dark:text-slate-400 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-slate-700 dark:text-slate-300">Titik Kantor Saat Ini: <b class="text-slate-900 dark:text-white" x-text="officeLat + ', ' + officeLng"></b> (Radius: <b class="text-indigo-600 dark:text-indigo-400" x-text="maxRadius + 'm'"></b>)</span>
                    </div>

                    <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300 font-medium">
                        <input type="checkbox" x-model="bypassGeofence" class="rounded bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span>Bypass Geofence (Mode Dev/Override Admin)</span>
                    </label>
                </div>
            @endif
        </div>

        <!-- Main Camera Container -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 sm:p-6 shadow-sm relative space-y-5 transition-colors duration-200">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" :class="(geoStatus === 'allowed' || bypassGeofence) ? 'bg-emerald-500 animate-ping' : 'bg-rose-500'"></span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Live Camera Stream</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Posisikan wajah Anda di tengah kotak pemindai</p>
                </div>

                @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                    <div class="w-full sm:w-auto">
                        <select x-model="selectedUserId" class="bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 w-full focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Mode Otomatis (Face Recognition) --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }} ({{ strtoupper($usr->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Video Stream Container -->
            <div class="relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border border-slate-800 flex items-center justify-center">
                <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="snapshotCanvas" class="hidden"></canvas>

                <!-- Geofencing Lock Overlay if out of radius and not bypassed -->
                <div x-show="geoStatus !== 'allowed' && !bypassGeofence" class="absolute inset-0 bg-slate-950/90 z-30 flex flex-col items-center justify-center p-6 text-center space-y-3" x-cloak>
                    <div class="w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                        <i class="ti ti-lock text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Presensi Kamera Dikunci</h4>
                        <p class="text-xs text-slate-400 max-w-md mt-1" x-text="geoSubtitle"></p>
                    </div>
                    
                    <div class="flex items-center gap-2 pt-1">
                        @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                            <button @click="setMyLocationAsOffice()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                <i class="ti ti-map-pin me-1"></i> Atur Lokasi Ini Sebagai Kantor
                            </button>
                        @endif
                        <button @click="requestGeolocation()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 transition">
                            <i class="ti ti-refresh me-1"></i> Refresh GPS
                        </button>
                    </div>
                </div>

                <!-- Scanner Reticle Box -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-56 border border-dashed border-indigo-400/80 rounded-2xl relative flex items-center justify-center">
                        <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-indigo-400"></div>
                        <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-indigo-400"></div>
                        <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-indigo-400"></div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-indigo-400"></div>
                        
                        <span class="text-[11px] font-semibold text-indigo-300 bg-slate-950/80 px-3 py-1 rounded-full border border-indigo-500/30">
                            <i class="ti ti-scan me-1"></i> Deteksi Wajah
                        </span>
                    </div>
                </div>

                <!-- Snapshot Overlay -->
                <div x-show="capturedImage" class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-4" x-cloak>
                    <img :src="capturedImage" class="w-56 h-40 object-cover rounded-lg border border-indigo-500 mb-3 shadow-md">
                    <p class="text-xs text-indigo-300 font-semibold">Snapshot Berhasil Diambil!</p>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <button @click="submitAttendance('masuk')" :disabled="loading || (geoStatus !== 'allowed' && !bypassGeofence)" 
                        :class="(geoStatus === 'allowed' || bypassGeofence) ? 'bg-emerald-600 hover:bg-emerald-500 cursor-pointer' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed opacity-60'"
                        class="w-full sm:w-auto px-6 py-2.5 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                    <i class="ti ti-login text-base"></i> Check-In (Masuk)
                </button>

                <button @click="submitAttendance('pulang')" :disabled="loading || (geoStatus !== 'allowed' && !bypassGeofence)"
                        :class="(geoStatus === 'allowed' || bypassGeofence) ? 'bg-rose-600 hover:bg-rose-500 cursor-pointer' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed opacity-60'"
                        class="w-full sm:w-auto px-6 py-2.5 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                    <i class="ti ti-logout text-base"></i> Check-Out (Pulang)
                </button>
            </div>

            <div class="text-center pt-2">
                <p x-text="statusText" class="text-xs text-slate-500 dark:text-slate-400 font-medium"></p>
            </div>

        </div>
    </div>

    <script>
        function faceAttendanceApp() {
            return {
                videoElement: null,
                canvasElement: null,
                capturedImage: null,
                selectedUserId: '',
                loading: false,
                settingLocation: false,
                bypassGeofence: false,
                statusText: 'Kamera aktif. Posisikan wajah dan pilih Check-In atau Check-Out.',

                // Office Location Config
                officeLat: {{ $officeLat }},
                officeLng: {{ $officeLng }},
                maxRadius: {{ $maxRadius }},

                // User Location State
                userLat: null,
                userLng: null,
                currentDistance: null,
                geoStatus: 'loading', // 'loading', 'allowed', 'out_of_bounds', 'denied'
                geoTitle: 'Mengecek Lokasi GPS...',
                geoSubtitle: 'Mohon berikan izin akses lokasi perangkatan untuk memverifikasi radius kantor.',

                getSwalColors() {
                    const isDark = document.documentElement.classList.contains('dark');
                    return {
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#0f172a'
                    };
                },

                init() {
                    this.videoElement = document.getElementById('webcam');
                    this.canvasElement = document.getElementById('snapshotCanvas');
                    this.startCamera();
                    this.requestGeolocation();
                },

                async startCamera() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 1280, height: 720, facingMode: 'user' }
                        });
                        this.videoElement.srcObject = stream;
                    } catch (err) {
                        this.statusText = 'Gagal mengakses kamera browser. Mohon izinkan akses webcam.';
                    }
                },

                calculateHaversineDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return Math.round(R * c * 100) / 100;
                },

                requestGeolocation() {
                    this.geoStatus = 'loading';
                    this.geoTitle = 'Mengecek Lokasi GPS...';
                    this.geoSubtitle = 'Menghubungkan ke sensor lokasi perangkat...';

                    if (!navigator.geolocation) {
                        this.geoStatus = 'denied';
                        this.geoTitle = 'GPS Tidak Didukung';
                        this.geoSubtitle = 'Browser Anda tidak mendukung Geolocation API.';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.userLat = position.coords.latitude;
                            this.userLng = position.coords.longitude;
                            this.evaluateDistance();
                        },
                        (error) => {
                            this.geoStatus = 'denied';
                            this.geoTitle = 'Akses Lokasi Ditolak';
                            this.geoSubtitle = 'Mohon aktifkan GPS & izinkan akses lokasi pada browser untuk membuka kunci presensi.';
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },

                evaluateDistance() {
                    this.currentDistance = this.calculateHaversineDistance(
                        this.userLat, this.userLng,
                        this.officeLat, this.officeLng
                    );

                    if (this.currentDistance <= this.maxRadius) {
                        this.geoStatus = 'allowed';
                        this.geoTitle = 'Lokasi Terverifikasi (Dalam Radius)';
                        this.geoSubtitle = `Jarak Anda: ${this.currentDistance}m dari lokasi kantor. Presensi diizinkan!`;
                    } else {
                        this.geoStatus = 'out_of_bounds';
                        this.geoTitle = 'Di Luar Radius Kantor';
                        this.geoSubtitle = `Jarak Anda: ${this.currentDistance}m dari lokasi kantor (Batas maksimum: ${this.maxRadius}m). Presensi dikunci.`;
                    }
                },

                async setMyLocationAsOffice() {
                    const colors = this.getSwalColors();
                    if (!this.userLat || !this.userLng) {
                        Swal.fire({ icon: 'warning', title: 'GPS Belum Siap', text: 'Tunggu hingga lokasi GPS terdeteksi.', ...colors });
                        return;
                    }

                    this.settingLocation = true;
                    try {
                        const res = await fetch("{{ route('attendance.update-office-location') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                latitude: this.userLat,
                                longitude: this.userLng,
                                max_radius_meters: 100
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.officeLat = data.office_lat;
                            this.officeLng = data.office_lng;
                            this.maxRadius = data.max_radius;
                            this.evaluateDistance();

                            Swal.fire({
                                icon: 'success',
                                title: 'Titik Kantor Diperbarui!',
                                text: data.message,
                                timer: 3000,
                                ...colors
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal memperbarui', text: data.message, ...colors });
                        }
                    } catch(e) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.', ...colors });
                    } finally {
                        this.settingLocation = false;
                    }
                },

                captureSnapshot() {
                    const video = this.videoElement;
                    const canvas = this.canvasElement;
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;

                    const ctx = canvas.getContext('2d');
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    return canvas.toDataURL('image/jpeg', 0.85);
                },

                async submitAttendance(type) {
                    const colors = this.getSwalColors();
                    if (this.geoStatus !== 'allowed' && !this.bypassGeofence) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Presensi Dikunci',
                            text: 'Anda harus berada di dalam radius 100m dari lokasi kantor untuk melakukan presensi.',
                            ...colors
                        });
                        return;
                    }

                    this.loading = true;
                    this.statusText = 'Memproses verifikasi & menyimpan presensi...';
                    
                    const imageData = this.captureSnapshot();
                    this.capturedImage = imageData;

                    try {
                        const response = await fetch("{{ route('attendance.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                tipe: type,
                                image: imageData,
                                user_id: this.selectedUserId || null,
                                latitude: this.userLat || this.officeLat,
                                longitude: this.userLng || this.officeLng,
                                bypass_geofence: this.bypassGeofence
                            })
                        });

                        const resData = await response.json();

                        if (response.ok && resData.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Presensi Berhasil!',
                                html: `<b>${resData.user.name}</b> (${resData.user.role})<br><span class="text-xs text-emerald-500 font-medium">${resData.message}</span>`,
                                timer: 3500,
                                ...colors
                            });
                            this.statusText = resData.message;
                        } else {
                            Swal.fire({ icon: 'error', title: 'Presensi Gagal', text: resData.message, ...colors });
                            this.statusText = resData.message || 'Presensi gagal. Silakan coba lagi.';
                        }
                    } catch (err) {
                        this.statusText = 'Terjadi kesalahan sistem.';
                    } finally {
                        setTimeout(() => {
                            this.capturedImage = null;
                            this.loading = false;
                        }, 2000);
                    }
                }
            }
        }
    </script>
</x-app-layout>
