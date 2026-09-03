<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-camera text-indigo-400"></i> Presensi Wajah Real-Time (Face Recognition)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Posisikan wajah Anda tegak lurus di depan kamera untuk melakukan Check-In atau Check-Out</p>
        </div>
        <div>
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-sm transition border border-slate-700">
                &larr; Rekap Absensi
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8" x-data="faceAttendanceApp()">
        <!-- Camera Stream Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span> Real-time Web Camera Feed
                    </h3>
                    <p class="text-xs text-slate-400">Pastikan pencahayaan cukup dan wajah terlihat jelas di frame</p>
                </div>
                
                <!-- Role selection for Admin/Owner manual override, otherwise automatic for logged in user -->
                @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                    <div class="w-full md:w-auto">
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Presensi Untuk (Admin Override)</label>
                        <select x-model="selectedUserId" class="bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Mode Otomatis (Face Recognition) --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }} ({{ strtoupper($usr->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Video & Canvas Display Box -->
            <div class="relative w-full aspect-video bg-slate-950 rounded-2xl overflow-hidden border-2 border-indigo-500/30 shadow-inner flex items-center justify-center group">
                <!-- Video Feed -->
                <video id="webcam" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>

                <!-- Face Scanner Target Overlay Box -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-64 h-64 border-2 border-dashed border-indigo-400/80 rounded-3xl relative flex items-center justify-center shadow-[0_0_50px_rgba(99,102,241,0.25)]">
                        <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-indigo-400"></div>
                        <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-indigo-400"></div>
                        <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-indigo-400"></div>
                        <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-indigo-400"></div>
                        
                        <div class="text-center bg-slate-900/70 backdrop-blur px-3 py-1.5 rounded-full border border-indigo-500/30">
                            <span class="text-xs font-bold text-indigo-300 animate-pulse"><i class="ti ti-scan"></i> Posisikan Wajah</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden Canvas for Snapshot Capture -->
                <canvas id="snapshotCanvas" class="hidden"></canvas>

                <!-- Captured Snapshot Overlay Preview -->
                <div x-show="capturedImage" class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-4">
                    <img :src="capturedImage" class="w-64 h-48 object-cover rounded-2xl border-2 border-indigo-500 shadow-xl mb-4">
                    <p class="text-xs text-indigo-300 font-semibold mb-2">Snapshot Presensi Berhasil Diambil!</p>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button @click="submitAttendance('masuk')" :disabled="loading" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="ti ti-login text-xl"></i> Check-In (Masuk)
                </button>
                <button @click="submitAttendance('pulang')" :disabled="loading" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="ti ti-logout text-xl"></i> Check-Out (Pulang)
                </button>
            </div>

            <!-- Note / Status Display -->
            <div class="mt-6 text-center">
                <p x-text="statusText" class="text-xs text-slate-400 font-medium"></p>
            </div>
        </div>
    </div>

    <script>
        function faceAttendanceApp() {
            return {
                videoElement: null,
                canvasElement: null,
                stream: null,
                capturedImage: null,
                selectedUserId: '',
                loading: false,
                statusText: 'Kamera aktif. Silakan tekan tombol Check-In atau Check-Out.',

                init() {
                    this.videoElement = document.getElementById('webcam');
                    this.canvasElement = document.getElementById('snapshotCanvas');
                    this.startCamera();
                },

                async startCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 1280, height: 720, facingMode: 'user' }
                        });
                        this.videoElement.srcObject = this.stream;
                    } catch (err) {
                        console.error('Kamera gagal diakses:', err);
                        this.statusText = 'Gagal mengakses kamera browser. Mohon izinkan akses webcam.';
                        Swal.fire('Akses Kamera Ditolak', 'Silakan izinkan akses kamera di browser Anda.', 'error');
                    }
                },

                captureSnapshot() {
                    const video = this.videoElement;
                    const canvas = this.canvasElement;
                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;

                    const ctx = canvas.getContext('2d');
                    // Draw video image onto canvas (unmirror horizontally)
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    return canvas.toDataURL('image/jpeg', 0.85);
                },

                async submitAttendance(type) {
                    this.loading = true;
                    this.statusText = 'Memproses pencocokan wajah & menyimpan log presensi...';
                    
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
                                html: `<b>${resData.user.name}</b> (${resData.user.role})<br><span class="text-sm font-semibold text-emerald-400">${resData.message}</span>`,
                                timer: 3500
                            });
                            this.statusText = resData.message;
                        } else {
                            Swal.fire('Gagal Presensi', resData.message || 'Wajah tidak terverifikasi.', 'error');
                            this.statusText = 'Presensi gagal. Coba lagi.';
                        }
                    } catch (err) {
                        console.error('Presensi Error:', err);
                        Swal.fire('Error System', 'Terjadi kesalahan komunikasi dengan server.', 'error');
                        this.statusText = 'Terjadi kesalahan sistem.';
                    } finally {
                        setTimeout(() => {
                            this.capturedImage = null;
                            this.loading = false;
                        }, 2500);
                    }
                }
            }
        }
    </script>
</x-app-layout>
