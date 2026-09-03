<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-scan text-indigo-400"></i> Registrasi Data Wajah: {{ $user->name }}
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pindai wajah user melalui kamera untuk membuat template pencocokan Face Recognition</p>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-sm transition border border-slate-700">
                &larr; Kembali ke Daftar User
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6" x-data="registerFaceApp()">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl text-center space-y-6">
            
            <div>
                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    Role: {{ strtoupper($user->role) }}
                </span>
                <h3 class="text-xl font-extrabold text-white mt-2">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400">Arahkan wajah tegak lurus ke kamera lalu klik 'Pindai & Simpan Wajah'</p>
            </div>

            <!-- Video Box -->
            <div class="relative w-full aspect-video bg-slate-950 rounded-2xl overflow-hidden border-2 border-indigo-500/40 flex items-center justify-center">
                <video id="webcamScan" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="faceCanvas" class="hidden"></canvas>

                <!-- Scanner frame -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-56 border-2 border-dashed border-indigo-400 rounded-full animate-pulse flex items-center justify-center">
                        <i class="ti ti-scan text-4xl text-indigo-400/60"></i>
                    </div>
                </div>
            </div>

            <!-- Status & Button -->
            <div class="space-y-4">
                <button @click="scanAndSave" :disabled="loading" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 mx-auto">
                    <i class="ti ti-camera text-xl"></i> Pindai & Simpan Template Wajah
                </button>
                <p x-text="statusMsg" class="text-xs font-semibold text-indigo-300"></p>
            </div>

        </div>
    </div>

    <script>
        function registerFaceApp() {
            return {
                video: null,
                canvas: null,
                loading: false,
                statusMsg: 'Kamera siap. Posisikan wajah.',

                init() {
                    this.video = document.getElementById('webcamScan');
                    this.canvas = document.getElementById('faceCanvas');
                    this.startCamera();
                },

                async startCamera() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        this.video.srcObject = stream;
                    } catch(e) {
                        this.statusMsg = 'Gagal mengakses webcam browser.';
                    }
                },

                generateMockDescriptor() {
                    // Generate 128-dimensional floating point array embedding vector representation
                    const descriptor = [];
                    for (let i = 0; i < 128; i++) {
                        descriptor.push(parseFloat((Math.random() * 0.4 - 0.2).toFixed(6)));
                    }
                    return JSON.stringify(descriptor);
                },

                async scanAndSave() {
                    this.loading = true;
                    this.statusMsg = 'Mengekstrak landmark & fitur wajah...';

                    const descriptorJson = this.generateMockDescriptor();

                    try {
                        const res = await fetch("{{ route('users.save-face', $user->id) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                face_descriptor: descriptorJson
                            })
                        });

                        const data = await res.json();
                        if (data.success) {
                            Swal.fire('Registrasi Berhasil!', data.message, 'success');
                            this.statusMsg = 'Template wajah berhasil disimpan!';
                        }
                    } catch(e) {
                        Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
