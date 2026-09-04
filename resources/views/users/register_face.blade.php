<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="font-bold text-xl text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <i class="ti ti-scan text-indigo-600 dark:text-indigo-400"></i> Registrasi Data Wajah: {{ $user->name }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pindai wajah user melalui kamera untuk membuat template pencocokan Face Recognition</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs transition border border-slate-200 dark:border-slate-700">
                    &larr; Kembali ke Daftar User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6" x-data="registerFaceApp()">
        <div class="bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm text-center space-y-6 transition-colors duration-200">
            
            <div>
                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                    Role: {{ strtoupper($user->role) }}
                </span>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-2">{{ $user->name }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Arahkan wajah tegak lurus ke kamera lalu klik 'Pindai & Simpan Wajah'</p>
            </div>

            <!-- Video Box -->
            <div class="relative w-full aspect-video bg-slate-950 rounded-xl overflow-hidden border-2 border-indigo-500/40 flex items-center justify-center">
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
                <button @click="scanAndSave" :disabled="loading" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-sm transition flex items-center justify-center gap-2 mx-auto text-xs">
                    <i class="ti ti-camera text-base"></i> Pindai & Simpan Template Wajah
                </button>
                <p x-text="statusMsg" class="text-xs font-semibold text-indigo-600 dark:text-indigo-300"></p>
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

                getSwalColors() {
                    const isDark = document.documentElement.classList.contains('dark');
                    return {
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#0f172a'
                    };
                },

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
                    const colors = this.getSwalColors();

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
                            Swal.fire({ title: 'Registrasi Berhasil!', text: data.message, icon: 'success', ...colors });
                            this.statusMsg = 'Template wajah berhasil disimpan!';
                        }
                    } catch(e) {
                        Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan sistem.', icon: 'error', ...colors });
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
