<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Presensi Wajah Real-Time</h1>
                <p class="text-xs text-slate-400 mt-0.5">Sistem deteksi & verifikasi presensi wajah otomatis via kamera browser</p>
            </div>
            <a href="{{ route('attendance.index') }}" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700 transition">
                &larr; Rekap Absensi
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="faceAttendanceApp()">
        
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-5 sm:p-6 shadow-sm relative space-y-5">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h3 class="text-sm font-bold text-white">Live Camera Stream</h3>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Posisikan wajah Anda berada di tengah kotak pemindai</p>
                </div>

                @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                    <div class="w-full sm:w-auto">
                        <select x-model="selectedUserId" class="bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded-lg px-3 py-2 w-full focus:ring-1 focus:ring-indigo-500">
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
                <button @click="submitAttendance('masuk')" :disabled="loading" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                    <i class="ti ti-login text-base"></i> Check-In (Masuk)
                </button>
                <button @click="submitAttendance('pulang')" :disabled="loading" class="w-full sm:w-auto px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                    <i class="ti ti-logout text-base"></i> Check-Out (Pulang)
                </button>
            </div>

            <div class="text-center pt-2">
                <p x-text="statusText" class="text-xs text-slate-400 font-medium"></p>
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
                statusText: 'Kamera aktif. Posisikan wajah dan pilih Check-In atau Check-Out.',

                init() {
                    this.videoElement = document.getElementById('webcam');
                    this.canvasElement = document.getElementById('snapshotCanvas');
                    this.startCamera();
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
                                user_id: this.selectedUserId || null
                            })
                        });

                        const resData = await response.json();

                        if (resData.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Presensi Berhasil!',
                                html: `<b class="text-slate-100">${resData.user.name}</b> (${resData.user.role})<br><span class="text-xs text-emerald-400 font-medium">${resData.message}</span>`,
                                timer: 3000,
                                background: '#0f172a',
                                color: '#f8fafc'
                            });
                            this.statusText = resData.message;
                        } else {
                            Swal.fire({ icon: 'error', title: 'Presensi Gagal', text: resData.message, background: '#0f172a', color: '#f8fafc' });
                            this.statusText = 'Presensi gagal. Silakan coba lagi.';
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
