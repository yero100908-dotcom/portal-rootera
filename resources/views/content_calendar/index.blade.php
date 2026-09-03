<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-calendar-event text-indigo-400"></i> Kalender Konten (Content Planner & Scheduler)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pemetaan jadwal tayang sosial media, status alur kerja produksi, dan penugasan team</p>
        </div>
        <div>
            <button @click="$dispatch('open-content-modal')" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <i class="ti ti-plus text-lg"></i> Tambah Jadwal Konten
            </button>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="contentCalendarApp()">
        
        <!-- Legend & Filters Bar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Status Legend Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="font-semibold text-slate-400 me-2">Workflow Legend:</span>
                <span class="px-2.5 py-1 rounded-lg bg-gray-600/30 text-gray-300 border border-gray-500/30 font-semibold">&bull; Ide / Draft</span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/30 text-amber-300 border border-amber-500/30 font-semibold">&bull; Produksi</span>
                <span class="px-2.5 py-1 rounded-lg bg-blue-500/30 text-blue-300 border border-blue-500/30 font-semibold">&bull; Review</span>
                <span class="px-2.5 py-1 rounded-lg bg-purple-500/30 text-purple-300 border border-purple-500/30 font-semibold">&bull; Scheduled</span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 font-semibold">&bull; Published</span>
            </div>

            <!-- Platform Filters -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-400">Platform:</label>
                <select id="platformFilter" class="bg-slate-950 border border-slate-700 text-white text-xs rounded-xl px-3 py-2">
                    <option value="">Semua Platform</option>
                    @foreach($platforms as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- FullCalendar Container Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
            <div id="calendar" class="fc-dark-theme min-h-[650px]"></div>
        </div>

        <!-- Content Event Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur flex items-center justify-center p-4" x-cloak>
            <div class="bg-slate-900 border border-slate-800 w-full max-w-xl rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="ti ti-pencil text-indigo-400"></i> <span x-text="editMode ? 'Edit Jadwal Konten' : 'Tambah Konten Baru'"></span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="ti ti-x text-xl"></i></button>
                </div>

                <form @submit.prevent="saveContent" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Judul Konten / Topik *</label>
                        <input type="text" x-model="form.title" required placeholder="Contoh: Tips Membangun Personal Branding di 2026" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Brief Copywriting / Deskripsi</label>
                        <textarea x-model="form.brief_copywriting" rows="3" placeholder="Garis besar brief, pesan utama, atau hashtag..." class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Platform Target *</label>
                            <select x-model="form.platform" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach($platforms as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Alur Kerja *</label>
                            <select x-model="form.status" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach($statuses as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal & Jam Rencana Tayang *</label>
                            <input type="datetime-local" x-model="form.scheduled_at" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Penanggung Jawab (Assignee)</label>
                            <select x-model="form.assigned_to" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Mentor / Staff --</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ strtoupper($usr->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <button type="button" x-show="editMode" @click="deleteContent" class="px-4 py-2 bg-rose-500/20 text-rose-300 font-semibold rounded-xl text-sm border border-rose-500/30">
                            Hapus Konten
                        </button>
                        <div class="flex items-center gap-3 ms-auto">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 font-semibold rounded-xl text-sm border border-slate-700">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl text-sm shadow">Simpan Jadwal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Custom FullCalendar CSS overrides -->
    <style>
        .fc { --fc-border-color: rgba(51, 65, 85, 0.5); }
        .fc .fc-toolbar-title { color: #f8fafc; font-size: 1.25rem; font-weight: 700; }
        .fc .fc-button-primary { background-color: #1e293b; border-color: #334155; color: #f1f5f9; }
        .fc .fc-button-primary:hover { background-color: #334155; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(51, 65, 85, 0.4); }
        .fc-daygrid-day-number { color: #94a3b8; font-weight: 600; font-size: 0.85rem; }
        .fc-event { cursor: pointer; border-radius: 8px; font-weight: 600; padding: 2px 6px; font-size: 0.8rem; }
    </style>

    <script>
        function contentCalendarApp() {
            return {
                showModal: false,
                editMode: false,
                calendar: null,
                form: {
                    id: null,
                    title: '',
                    brief_copywriting: '',
                    platform: 'Instagram',
                    status: 'draft',
                    scheduled_at: '',
                    assigned_to: ''
                },

                init() {
                    const self = this;
                    const calendarEl = document.getElementById('calendar');

                    self.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,listWeek'
                        },
                        events: "{{ route('content.events') }}",
                        editable: true,
                        dateClick: function(info) {
                            self.resetForm();
                            self.form.scheduled_at = info.dateStr + 'T10:00';
                            self.showModal = true;
                        },
                        eventClick: function(info) {
                            const props = info.event.extendedProps;
                            self.editMode = true;
                            self.form.id = info.event.id;
                            self.form.title = props.title_raw;
                            self.form.brief_copywriting = props.brief || '';
                            self.form.platform = props.platform;
                            self.form.status = props.status;
                            self.form.assigned_to = props.assigned_to || '';
                            self.form.scheduled_at = info.event.start.toISOString().slice(0, 16);
                            self.showModal = true;
                        },
                        eventDrop: function(info) {
                            self.updateEventDate(info.event);
                        }
                    });

                    self.calendar.render();

                    window.addEventListener('open-content-modal', () => {
                        self.resetForm();
                        self.showModal = true;
                    });

                    document.getElementById('platformFilter').addEventListener('change', function(e) {
                        const val = e.target.value;
                        const url = new URL("{{ route('content.events') }}");
                        if (val) url.searchParams.set('platform', val);
                        self.calendar.removeAllEventSources();
                        self.calendar.addEventSource(url.toString());
                    });
                },

                resetForm() {
                    this.editMode = false;
                    this.form = {
                        id: null,
                        title: '',
                        brief_copywriting: '',
                        platform: 'Instagram',
                        status: 'draft',
                        scheduled_at: new Date().toISOString().slice(0, 16),
                        assigned_to: ''
                    };
                },

                async saveContent() {
                    const isUpdate = this.editMode && this.form.id;
                    const url = isUpdate ? `/content/${this.form.id}` : "{{ route('content.store') }}";
                    const method = isUpdate ? 'PUT' : 'POST';

                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (res.ok) {
                            this.showModal = false;
                            this.calendar.refetchEvents();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Jadwal konten berhasil disimpan!', showConfirmButton: false, timer: 3000 });
                        }
                    } catch (e) {
                        console.error('Save Content Error:', e);
                    }
                },

                async updateEventDate(event) {
                    try {
                        await fetch(`/content/${event.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                scheduled_at: event.start.toISOString().slice(0, 16)
                            })
                        });
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Jadwal tayang diperbarui!', showConfirmButton: false, timer: 2000 });
                    } catch(e) {}
                },

                async deleteContent() {
                    if (!confirm('Yakin ingin menghapus jadwal konten ini?')) return;
                    try {
                        await fetch(`/content/${this.form.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        this.showModal = false;
                        this.calendar.refetchEvents();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Konten berhasil dihapus.', showConfirmButton: false, timer: 3000 });
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>
