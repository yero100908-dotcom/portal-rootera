<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-calendar-event text-purple-400"></i> Kalender Konten & Social Media Tagging
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Penjadwalan rilis konten interaktif, penandaan platform, & penugasan PIC</p>
            </div>
            <button @click="$dispatch('open-content-modal')" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i class="ti ti-plus text-sm"></i> + Tambah Rencana Konten
            </button>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="contentCalendarApp()">
        
        <!-- Legend, Filters & Interactive Controls Bar -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-4 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Workflow Status Markers -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="font-semibold text-slate-400 text-[11px] uppercase me-1">Status Highlighter:</span>
                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 border border-slate-700 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/10 text-amber-400 border border-amber-500/20 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Produksi
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Review
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] bg-purple-500/10 text-purple-400 border border-purple-500/20 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span> Scheduled
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Published
                </span>
            </div>

            <!-- Platform & Status Filter Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <label class="text-[11px] font-semibold text-slate-400 uppercase">Platform:</label>
                    <select id="platformFilter" class="bg-slate-950 border border-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua Channel</option>
                        @foreach($platforms as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-[11px] font-semibold text-slate-400 uppercase">Status:</label>
                    <select id="statusFilter" class="bg-slate-950 border border-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <!-- FullCalendar Display Grid -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-4 sm:p-6 shadow-sm">
            <div id="calendar" class="fc-dark-theme min-h-[620px]"></div>
        </div>

        <!-- Content Event Modal / Rencana Konten Form -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="ti ti-calendar-plus text-purple-400"></i> <span x-text="editMode ? 'Edit / Detail Rencana Konten' : 'Tambah Rencana Konten Baru'"></span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form @submit.prevent="saveContent" class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Judul / Topik Konten *</label>
                        <input type="text" x-model="form.title" required placeholder="Contoh: Video Tutorial Setup Laravel 13" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Brief Copywriting & Concept</label>
                        <textarea x-model="form.brief_copywriting" rows="3" placeholder="Tuliskan ide visual, hook, atau draf caption..." class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Target Channel *</label>
                            <select x-model="form.platform" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                                @foreach($platforms as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Status Workflow *</label>
                            <select x-model="form.status" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                                @foreach($statuses as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Jadwal Rilis *</label>
                            <input type="datetime-local" x-model="form.scheduled_at" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Penanggung Jawab (PIC)</label>
                            <select x-model="form.assigned_to" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                                <option value="">-- Pilih Assignee --</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ strtoupper($usr->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-between">
                        <button type="button" x-show="editMode" @click="deleteContent" class="px-3 py-1.5 bg-rose-500/10 text-rose-400 text-xs font-semibold rounded-lg border border-rose-500/20">
                            Hapus Rencana
                        </button>
                        <div class="flex items-center gap-2 ms-auto">
                            <button type="button" @click="showModal = false" class="px-3.5 py-1.5 bg-slate-800 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700">Batal</button>
                            <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Jadwal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- FullCalendar Styling & Custom Badges -->
    <style>
        .fc { --fc-border-color: rgba(51, 65, 85, 0.4); }
        .fc .fc-toolbar-title { color: #f8fafc; font-size: 1.1rem; font-weight: 700; }
        .fc .fc-button-primary { background-color: #0f172a; border-color: #1e293b; color: #cbd5e1; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
        .fc .fc-button-primary:hover { background-color: #1e293b; color: #fff; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(51, 65, 85, 0.4); }
        .fc-daygrid-day-number { color: #94a3b8; font-weight: 600; font-size: 0.8rem; }
        .fc-daygrid-day:hover { background-color: rgba(30, 41, 59, 0.4); cursor: pointer; }
        .fc-event { cursor: pointer; border-radius: 6px; border: none; padding: 2px 4px; }
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
                        
                        // Custom Event Rendering with Social Media Badges & Status Highlighter
                        eventContent: function(arg) {
                            const props = arg.event.extendedProps;
                            const platform = props.platform || 'General';
                            const iconClass = props.platform_icon || 'ti-brand-line';
                            const statusColor = props.status_color || '#6366f1';
                            
                            const el = document.createElement('div');
                            el.className = 'flex items-center gap-1.5 text-[11px] font-semibold truncate text-white p-1 rounded';
                            el.style.backgroundColor = statusColor;
                            
                            el.innerHTML = `
                                <i class="ti ${iconClass} text-xs"></i>
                                <span class="truncate">${arg.event.title}</span>
                            `;
                            return { domNodes: [el] };
                        },

                        // Grid Click -> Open Modal with Clicked Date Prefilled
                        dateClick: function(info) {
                            self.resetForm();
                            // Prefill date clicked
                            const clickedDate = info.dateStr;
                            self.form.scheduled_at = clickedDate + 'T10:00';
                            self.showModal = true;
                        },

                        // Event Click -> View / Edit Modal
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

                        // Event Drag & Drop Date Update
                        eventDrop: function(info) {
                            self.updateEventDate(info.event);
                        }
                    });

                    self.calendar.render();

                    // Global listener for Header button
                    window.addEventListener('open-content-modal', () => {
                        self.resetForm();
                        self.showModal = true;
                    });

                    // Filter Change Listeners
                    const applyFilters = () => {
                        const platform = document.getElementById('platformFilter').value;
                        const status = document.getElementById('statusFilter').value;
                        
                        const url = new URL("{{ route('content.events') }}");
                        if (platform) url.searchParams.set('platform', platform);
                        if (status) url.searchParams.set('status', status);
                        
                        self.calendar.removeAllEventSources();
                        self.calendar.addEventSource(url.toString());
                    };

                    document.getElementById('platformFilter').addEventListener('change', applyFilters);
                    document.getElementById('statusFilter').addEventListener('change', applyFilters);
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
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Jadwal konten berhasil disimpan!', showConfirmButton: false, timer: 2500, background: '#0f172a', color: '#f8fafc' });
                        }
                    } catch (e) {}
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
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Jadwal tayang diperbarui', showConfirmButton: false, timer: 2000, background: '#0f172a', color: '#f8fafc' });
                    } catch(e) {}
                },

                async deleteContent() {
                    if (!confirm('Hapus rencana konten ini?')) return;
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
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Rencana konten dihapus', showConfirmButton: false, timer: 2500, background: '#0f172a', color: '#f8fafc' });
                    } catch (e) {}
                }
            }
        }
    </script>
</x-app-layout>
