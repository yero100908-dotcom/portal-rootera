<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Skenario 1: Penambahan Tugas (Task Creation) oleh Owner dan Mentor
     */
    public function test_owner_and_mentor_can_create_assignments_with_all_fields_and_dom_feedback(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Wibowo Owner']);
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Rafael Mentor']);

        // 1. Owner creates task with complete fields & attachment
        $file = UploadedFile::fake()->create('panduan_arsitektur.pdf', 500, 'application/pdf');
        $ownerCreateResponse = $this->actingAs($owner)->post(route('assignments.store'), [
            'title' => 'Tugas Integrasi Payment Gateway & Webhook',
            'description' => 'Implementasikan webhook payment gateway dengan signature verification.',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'file_attachment' => $file,
            'is_active' => '1',
        ]);

        $ownerCreateResponse->assertRedirect(route('assignments.index'));
        $ownerCreateResponse->assertSessionHas('success', 'Tugas baru berhasil dibuat.');

        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Integrasi Payment Gateway & Webhook',
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        // Verify Owner sees newly created task and action button in index DOM
        $ownerIndexResponse = $this->actingAs($owner)->get(route('assignments.index'));
        $ownerIndexResponse->assertStatus(200);
        $ownerIndexResponse->assertSee('Tugas Integrasi Payment Gateway &amp; Webhook', false);
        $ownerIndexResponse->assertSee('Periksa Pengumpulan');
        $ownerIndexResponse->assertSee('Wibowo Owner');

        // 2. Mentor creates task
        $mentorCreateResponse = $this->actingAs($mentor)->post(route('assignments.store'), [
            'title' => 'Tugas Redesign Landing Page Mobile-First',
            'description' => 'Desain landing page responsif dengan Tailwind CSS & Alpine.js.',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'is_active' => '1',
        ]);

        $mentorCreateResponse->assertRedirect(route('assignments.index'));
        $mentorCreateResponse->assertSessionHas('success', 'Tugas baru berhasil dibuat.');

        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Redesign Landing Page Mobile-First',
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        // Verify Mentor sees their task in DOM
        $mentorIndexResponse = $this->actingAs($mentor)->get(route('assignments.index'));
        $mentorIndexResponse->assertStatus(200);
        $mentorIndexResponse->assertSee('Tugas Redesign Landing Page Mobile-First');
        $mentorIndexResponse->assertSee('Rafael Mentor');
    }

    /**
     * Skenario 2: Visibilitas Tugas Lintas Seluruh Role Penerima (IT, Sosmed, CS, Karyawan)
     */
    public function test_assignment_visibility_across_all_recipient_roles(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Owner Utama']);
        $assignment = Assignment::create([
            'title' => 'Modul Standard Operating Procedure (SOP) Q4',
            'description' => 'Pelajari dan buat ringkasan implementasi SOP tim masing-masing.',
            'deadline' => now()->addDays(4),
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $recipientRoles = [
            'admin' => User::factory()->create(['role' => 'admin', 'name' => 'Staff Admin Mono']),
            'it' => User::factory()->create(['role' => 'it', 'name' => 'Staff IT Yero']),
            'sosmed' => User::factory()->create(['role' => 'sosmed', 'name' => 'Staff Sosmed Prayoga']),
            'cs' => User::factory()->create(['role' => 'cs', 'name' => 'Staff CS Ghaitsaa']),
            'karyawan' => User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan Umum Budi']),
            'siswa' => User::factory()->create(['role' => 'siswa', 'name' => 'Siswa Magang Dani']),
        ];

        foreach ($recipientRoles as $role => $user) {
            $response = $this->actingAs($user)->get(route('assignments.index', ['tab' => 'pending']));
            $response->assertStatus(200);
            $response->assertSee('Modul Standard Operating Procedure (SOP) Q4');
            $response->assertSee('Belum Dikumpul');
            $response->assertSee('Kirim Tugas');
            $response->assertSee('Owner Utama');

            // Detail page view
            $detailResponse = $this->actingAs($user)->get(route('assignments.show', $assignment->id));
            $detailResponse->assertStatus(200);
            $detailResponse->assertSee('Form Pengumpulan Tugas');
            $detailResponse->assertSee('Belum Dikumpulkan');
            $detailResponse->assertDontSee('Periksa Pengumpulan');
        }
    }

    /**
     * Skenario 3: Pengumpulan Tugas (Task Submission Flow) Lintas Role & DOM State Transition
     */
    public function test_task_submission_flow_and_dom_state_transitions_across_roles(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Mentor Senior']);
        $assignment = Assignment::create([
            'title' => 'Tugas Analisis Performa Web & Optimasi Asset',
            'description' => 'Lakukan profiling bundle size dan audit Core Web Vitals.',
            'deadline' => now()->addDays(3),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $recipients = [
            'admin' => User::factory()->create(['role' => 'admin', 'name' => 'Staff Admin Mono']),
            'it' => User::factory()->create(['role' => 'it', 'name' => 'Staff IT Alex']),
            'sosmed' => User::factory()->create(['role' => 'sosmed', 'name' => 'Staff Sosmed Rian']),
            'cs' => User::factory()->create(['role' => 'cs', 'name' => 'Staff CS Maya']),
        ];

        foreach ($recipients as $role => $user) {
            $submissionFile = UploadedFile::fake()->create("laporan_{$role}.pdf", 300, 'application/pdf');

            // 1. Submit task with file, url, and notes
            $submitResponse = $this->actingAs($user)->post(route('assignments.submit', $assignment->id), [
                'submission_file' => $submissionFile,
                'submission_url' => "https://github.com/company/{$role}-optimization",
                'notes' => "Laporan pengerjaan dari divisi {$role}.",
            ]);

            $submitResponse->assertRedirect(route('assignments.index'));
            $submitResponse->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');

            $this->assertDatabaseHas('assignment_submissions', [
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
                'submission_url' => "https://github.com/company/{$role}-optimization",
                'notes' => "Laporan pengerjaan dari divisi {$role}.",
                'status' => 'submitted',
                'score' => null,
            ]);

            // 2. DOM Check on Index (Pending tab vs Submitted tab)
            // On pending tab: should not appear anymore
            $pendingIndex = $this->actingAs($user)->get(route('assignments.index', ['tab' => 'pending']));
            $pendingIndex->assertStatus(200);
            $pendingIndex->assertDontSee('Tugas Analisis Performa Web & Optimasi Asset');

            // On submitted tab: appears with "Sudah Dikumpul" badge and "Detail & Jawaban" button
            $submittedIndex = $this->actingAs($user)->get(route('assignments.index', ['tab' => 'submitted']));
            $submittedIndex->assertStatus(200);
            $submittedIndex->assertSee('Tugas Analisis Performa Web & Optimasi Asset');
            $submittedIndex->assertSee('Sudah Dikumpul');
            $submittedIndex->assertSee('Detail &amp; Jawaban', false);

            // 3. DOM Check on Detail (Show view)
            $showPage = $this->actingAs($user)->get(route('assignments.show', $assignment->id));
            $showPage->assertStatus(200);
            $showPage->assertSee('Sudah Dikumpulkan');
            $showPage->assertSee('Tugas Anda sedang menunggu review');
            $showPage->assertSee("Laporan pengerjaan dari divisi {$role}.");
            $showPage->assertSee("https://github.com/company/{$role}-optimization");
        }

        // Verify Mentor sees all 3 submissions in review page
        $mentorSubmissionsPage = $this->actingAs($mentor)->get(route('assignments.submissions', $assignment->id));
        $mentorSubmissionsPage->assertStatus(200);
        $mentorSubmissionsPage->assertSee('Staff IT Alex');
        $mentorSubmissionsPage->assertSee('Staff Sosmed Rian');
        $mentorSubmissionsPage->assertSee('Staff CS Maya');
        $mentorSubmissionsPage->assertSee('Belum Dinilai');
    }

    /**
     * Skenario 4: Alur Penilaian (Grading Flow) oleh Mentor & Owner, Nilai Tersimpan, dan Isolasi Nilai
     */
    public function test_mentor_and_owner_grading_flow_and_score_isolation_across_roles(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Wibowo Owner']);
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Rafael Mentor']);

        $assignment = Assignment::create([
            'title' => 'Tugas Keamanan Sistem & Sanitasi Input',
            'description' => 'Review SQL injection & XSS vulnerabilities.',
            'deadline' => now()->addDays(4),
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $userIT = User::factory()->create(['role' => 'it', 'name' => 'Fajar IT']);
        $userSosmed = User::factory()->create(['role' => 'sosmed', 'name' => 'Dinda Sosmed']);

        $subIT = $assignment->submissions()->create([
            'user_id' => $userIT->id,
            'submission_url' => 'https://github.com/fajar/security-audit',
            'notes' => 'Audit keamanan backend selesai',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $subSosmed = $assignment->submissions()->create([
            'user_id' => $userSosmed->id,
            'submission_url' => 'https://canva.com/dinda/cybersecurity-infographic',
            'notes' => 'Infografis edukasi keamanan cyber',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // 1. Mentor grades userIT via AJAX
        $mentorGradeResponse = $this->actingAs($mentor)->postJson(route('submissions.grade', $subIT->id), [
            'score' => 96,
            'feedback' => 'Implementasi rate limiting dan input validation sangat komprehensif.',
            'status' => 'graded',
        ]);

        $mentorGradeResponse->assertStatus(200);
        $mentorGradeResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $subIT->id,
            'score' => 96,
            'feedback' => 'Implementasi rate limiting dan input validation sangat komprehensif.',
            'status' => 'graded',
            'graded_by' => $mentor->id,
        ]);

        // 2. Owner grades userSosmed via regular POST redirect
        $ownerGradeResponse = $this->actingAs($owner)->post(route('submissions.grade', $subSosmed->id), [
            'score' => 88,
            'feedback' => 'Visual infografis sangat menarik dan mudah dipahami tim non-teknis.',
            'status' => 'graded',
        ]);

        $ownerGradeResponse->assertRedirect(route('assignments.submissions', $assignment->id));
        $ownerGradeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $subSosmed->id,
            'score' => 88,
            'feedback' => 'Visual infografis sangat menarik dan mudah dipahami tim non-teknis.',
            'status' => 'graded',
            'graded_by' => $owner->id,
        ]);

        // 3. Verify userIT DOM: Sees their exact score 96, does NOT see 88
        $itShow = $this->actingAs($userIT)->get(route('assignments.show', $assignment->id));
        $itShow->assertStatus(200);
        $itShow->assertSee('Telah Dinilai &amp; Selesai', false);
        $itShow->assertSee('Nilai: 96/100');
        $itShow->assertSee('96');
        $itShow->assertSee('Implementasi rate limiting dan input validation sangat komprehensif.');
        $itShow->assertDontSee('88');
        $itShow->assertDontSee('Visual infografis sangat menarik');

        $itIndex = $this->actingAs($userIT)->get(route('assignments.index', ['tab' => 'submitted']));
        $itIndex->assertStatus(200);
        $itIndex->assertSee('Sudah Dinilai (Nilai: 96/100)');
        $itIndex->assertSee('Lihat Nilai (96/100)');

        // 4. Verify userSosmed DOM: Sees their exact score 88, does NOT see 96
        $sosmedShow = $this->actingAs($userSosmed)->get(route('assignments.show', $assignment->id));
        $sosmedShow->assertStatus(200);
        $sosmedShow->assertSee('Telah Dinilai &amp; Selesai', false);
        $sosmedShow->assertSee('Nilai: 88/100');
        $sosmedShow->assertSee('88');
        $sosmedShow->assertSee('Visual infografis sangat menarik dan mudah dipahami tim non-teknis.');
        $sosmedShow->assertDontSee('96');
        $sosmedShow->assertDontSee('Implementasi rate limiting dan input validation');

        $sosmedIndex = $this->actingAs($userSosmed)->get(route('assignments.index', ['tab' => 'submitted']));
        $sosmedIndex->assertStatus(200);
        $sosmedIndex->assertSee('Sudah Dinilai (Nilai: 88/100)');
        $sosmedIndex->assertSee('Lihat Nilai (88/100)');
    }

    /**
     * Skenario 5: Alur Permintaan Revisi (Revision Needed Flow)
     */
    public function test_revision_needed_flow_renders_proper_dom_badges_and_allows_resubmission(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Mentor Budi']);
        $employee = User::factory()->create(['role' => 'it', 'name' => 'Adit IT']);

        $assignment = Assignment::create([
            'title' => 'Tugas Pembuatan Dokumentasi API Swagger',
            'description' => 'Lengkapi dokumentasi OpenAPI 3.0.',
            'deadline' => now()->addDays(5),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $employee->id,
            'submission_url' => 'https://api-docs.internal.com',
            'notes' => 'Dokumentasi endpoint auth sudah dibuat',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Mentor requests revision with score 60 and feedback
        $this->actingAs($mentor)->post(route('submissions.grade', $submission->id), [
            'score' => 60,
            'feedback' => 'Endpoint laporan kas dan webhook payment masih belum didokumentasikan. Mohon lengkapi.',
            'status' => 'revision_needed',
        ]);

        // Employee visits detail page
        $showResponse = $this->actingAs($employee)->get(route('assignments.show', $assignment->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Perlu Revisi Jawaban');
        $showResponse->assertSee('Perlu Revisi');
        $showResponse->assertSee('60');
        $showResponse->assertSee('Endpoint laporan kas dan webhook payment masih belum didokumentasikan. Mohon lengkapi.');
        $showResponse->assertSee('Kirim Berkas Revisi');

        // Employee visits index page
        $indexResponse = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'submitted']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Perlu Revisi (Nilai: 60/100)');

        // Employee resubmits revision
        $resubmitResponse = $this->actingAs($employee)->post(route('assignments.submit', $assignment->id), [
            'submission_url' => 'https://api-docs.internal.com/v2',
            'notes' => 'Revisi dokumentasi: seluruh endpoint kas & webhook telah ditambahkan.',
        ]);

        $resubmitResponse->assertRedirect(route('assignments.index'));
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'submission_url' => 'https://api-docs.internal.com/v2',
            'notes' => 'Revisi dokumentasi: seluruh endpoint kas & webhook telah ditambahkan.',
            'status' => 'submitted',
        ]);
    }

    /**
     * Skenario 6: RBAC & Otorisasi Ketat (Non-Manager Dilarang Akses Form Tambah & Endpoint Grading)
     */
    public function test_rbac_recipient_roles_cannot_access_manager_endpoints_and_elements_hidden(): void
    {
        $recipientRoles = ['admin', 'it', 'sosmed', 'cs', 'karyawan', 'siswa'];

        $owner = User::factory()->create(['role' => 'owner']);
        $assignment = Assignment::create([
            'title' => 'Tugas Keamanan RBAC',
            'description' => 'Test RBAC',
            'deadline' => now()->addDays(3),
            'created_by' => $owner->id,
            'is_active' => true,
        ]);

        $dummySubmission = $assignment->submissions()->create([
            'user_id' => $owner->id,
            'submitted_at' => now(),
        ]);

        foreach ($recipientRoles as $roleName) {
            $user = User::factory()->create(['role' => $roleName]);

            // 1. Cannot access GET /assignments/create (Form Tambah Tugas)
            $createViewResponse = $this->actingAs($user)->get(route('assignments.create'));
            $createViewResponse->assertStatus(403);

            // 2. Cannot POST /assignments (Simpan Tugas Baru)
            $storeResponse = $this->actingAs($user)->post(route('assignments.store'), [
                'title' => 'Ilegal Task Creation',
                'description' => 'Hacker task',
                'deadline' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ]);
            $storeResponse->assertStatus(403);

            // 3. Cannot access Review/Submissions page
            $submissionsViewResponse = $this->actingAs($user)->get(route('assignments.submissions', $assignment->id));
            $submissionsViewResponse->assertStatus(403);

            // 4. Cannot grade any submission
            $gradeResponse = $this->actingAs($user)->post(route('submissions.grade', $dummySubmission->id), [
                'score' => 100,
                'feedback' => 'Self-grading unauthorized',
            ]);
            $gradeResponse->assertStatus(403);

            // 5. Cannot extend custom deadline
            $extendResponse = $this->actingAs($user)->post(route('assignments.extend-user', $assignment->id), [
                'user_id' => $user->id,
                'custom_due_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
            ]);
            $extendResponse->assertStatus(403);

            // 6. On index page DOM: "Buat Tugas Baru" button is hidden
            $indexResponse = $this->actingAs($user)->get(route('assignments.index'));
            $indexResponse->assertStatus(200);
            $indexResponse->assertDontSee('Buat Tugas Baru');
            $indexResponse->assertDontSee('Periksa Pengumpulan');
        }
    }

    /**
     * Skenario 7: Validasi Batas Nilai & Penanganan Input Error (Grading Validation Boundaries)
     */
    public function test_grading_validation_boundaries(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'it']);

        $assignment = Assignment::create([
            'title' => 'Tugas Batas Skor',
            'description' => 'Deskripsi',
            'deadline' => now()->addDays(2),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $employee->id,
            'notes' => 'Submission',
            'submitted_at' => now(),
        ]);

        // Score > 100 should fail validation
        $invalidOverScore = $this->actingAs($mentor)->post(route('submissions.grade', $submission->id), [
            'score' => 105,
        ]);
        $invalidOverScore->assertSessionHasErrors('score');

        // Score < 0 should fail validation
        $invalidUnderScore = $this->actingAs($mentor)->post(route('submissions.grade', $submission->id), [
            'score' => -10,
        ]);
        $invalidUnderScore->assertSessionHasErrors('score');

        // Non-numeric score should fail validation
        $invalidStringScore = $this->actingAs($mentor)->post(route('submissions.grade', $submission->id), [
            'score' => 'Sempurna',
        ]);
        $invalidStringScore->assertSessionHasErrors('score');
    }

    /**
     * Skenario 8: Role Admin Spesifik - Tidak Bisa Memberi Tugas, Hanya Bisa Menerima & Mengumpulkan Tugas
     */
    public function test_admin_cannot_create_or_manage_assignments_and_can_only_receive_and_submit_assignments(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Mentor Utama']);
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Mono']);

        // 1. Admin dilarang membuat tugas baru (GET /assignments/create -> 403)
        $adminCreateResponse = $this->actingAs($admin)->get(route('assignments.create'));
        $adminCreateResponse->assertStatus(403);

        // 2. Admin dilarang menyimpan tugas baru (POST /assignments -> 403)
        $adminStoreResponse = $this->actingAs($admin)->post(route('assignments.store'), [
            'title' => 'Tugas Ilegal oleh Admin',
            'description' => 'Admin dilarang membuat tugas',
            'deadline' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);
        $adminStoreResponse->assertStatus(403);

        // Mentor membuat tugas resmi
        $assignment = Assignment::create([
            'title' => 'Tugas Konfigurasi Server & Keamanan Sistem',
            'description' => 'Lakukan instalasi Nginx, SSL, dan firewall hardening.',
            'deadline' => now()->addDays(4),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        // 3. Admin dilarang mengedit tugas (GET /assignments/{id}/edit -> 403)
        $this->actingAs($admin)->get(route('assignments.edit', $assignment->id))->assertStatus(403);

        // 4. Admin dilarang memperbarui tugas (PUT /assignments/{id} -> 403)
        $this->actingAs($admin)->put(route('assignments.update', $assignment->id), [
            'title' => 'Tugas Diubah oleh Admin',
            'description' => 'Deskripsi baru',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ])->assertStatus(403);

        // 5. Admin dilarang menghapus tugas (DELETE /assignments/{id} -> 403)
        $this->actingAs($admin)->delete(route('assignments.destroy', $assignment->id))->assertStatus(403);

        // 6. Admin dilarang mengakses halaman review submissions mentor (GET /assignments/{id}/submissions -> 403)
        $this->actingAs($admin)->get(route('assignments.submissions', $assignment->id))->assertStatus(403);

        // 7. Admin dilarang mengakses rekap tugas bulanan (GET /assignments/rekap-bulanan -> 403)
        $this->actingAs($admin)->get(route('assignments.rekap-bulanan'))->assertStatus(403);

        // 8. Admin dilarang memberikan perpanjangan waktu tugas (POST /assignments/{id}/extend-user -> 403)
        $this->actingAs($admin)->post(route('assignments.extend-user', $assignment->id), [
            'user_id' => $admin->id,
            'custom_due_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
        ])->assertStatus(403);

        // 9. DOM Check di Index untuk Admin: Tidak ada tombol "Buat Tugas Baru", ada tombol "Kirim Tugas" & badge "Belum Dikumpul"
        $adminIndexResponse = $this->actingAs($admin)->get(route('assignments.index'));
        $adminIndexResponse->assertStatus(200);
        $adminIndexResponse->assertDontSee('Buat Tugas Baru');
        $adminIndexResponse->assertDontSee('Periksa Pengumpulan');
        $adminIndexResponse->assertSee('Tugas Konfigurasi Server &amp; Keamanan Sistem', false);
        $adminIndexResponse->assertSee('Belum Dikumpul');
        $adminIndexResponse->assertSee('Kirim Tugas');

        // 10. DOM Check di Detail untuk Admin: Muncul Form Pengumpulan Tugas, tidak ada tombol Edit / Hapus
        $adminShowResponse = $this->actingAs($admin)->get(route('assignments.show', $assignment->id));
        $adminShowResponse->assertStatus(200);
        $adminShowResponse->assertSee('Form Pengumpulan Tugas');
        $adminShowResponse->assertSee('Belum Dikumpulkan');
        $adminShowResponse->assertDontSee('Edit Tugas');
        $adminShowResponse->assertDontSee('Hapus');

        // 11. Admin Mengumpulkan Tugas (POST /assignments/{id}/submit)
        $submissionFile = UploadedFile::fake()->create('laporan_server_admin.pdf', 250, 'application/pdf');
        $adminSubmitResponse = $this->actingAs($admin)->post(route('assignments.submit', $assignment->id), [
            'submission_file' => $submissionFile,
            'submission_url' => 'https://github.com/admin-mono/server-hardening',
            'notes' => 'Hardening server dan konfigurasi SSL telah selesai dikerjakan.',
        ]);

        $adminSubmitResponse->assertRedirect(route('assignments.index'));
        $adminSubmitResponse->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $admin->id,
            'submission_url' => 'https://github.com/admin-mono/server-hardening',
            'notes' => 'Hardening server dan konfigurasi SSL telah selesai dikerjakan.',
            'status' => 'submitted',
        ]);

        // 12. DOM Check setelah kumpul: Admin melihat badge "Sudah Dikumpul" & "Detail & Jawaban"
        $adminAfterSubmitIndex = $this->actingAs($admin)->get(route('assignments.index', ['tab' => 'submitted']));
        $adminAfterSubmitIndex->assertStatus(200);
        $adminAfterSubmitIndex->assertSee('Sudah Dikumpul');
        $adminAfterSubmitIndex->assertSee('Detail &amp; Jawaban', false);

        // 13. Mentor melihat submission Admin di halaman periksa pengumpulan & memberi nilai 95
        $adminSub = AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $admin->id)->first();
        $this->actingAs($mentor)->post(route('submissions.grade', $adminSub->id), [
            'score' => 95,
            'feedback' => 'Konfigurasi firewall UFW dan SSL Let\'s Encrypt sangat rapi dan aman.',
            'status' => 'graded',
        ]);

        // 14. Admin melihat nilai di DOM show & index
        $adminShowGraded = $this->actingAs($admin)->get(route('assignments.show', $assignment->id));
        $adminShowGraded->assertStatus(200);
        $adminShowGraded->assertSee('Telah Dinilai &amp; Selesai', false);
        $adminShowGraded->assertSee('Nilai: 95/100');
        $adminShowGraded->assertSee('95');
        $adminShowGraded->assertSee('Konfigurasi firewall UFW dan SSL Let\'s Encrypt sangat rapi dan aman.');

        $adminIndexGraded = $this->actingAs($admin)->get(route('assignments.index', ['tab' => 'submitted']));
        $adminIndexGraded->assertStatus(200);
        $adminIndexGraded->assertSee('Sudah Dinilai (Nilai: 95/100)');
        $adminIndexGraded->assertSee('Lihat Nilai (95/100)');
    }

    /**
     * Skenario 9: Uji Coba Komprehensif DOM & Responsivitas di Seluruh 8 Role (Owner, Mentor, Admin, IT, Sosmed, CS, Karyawan, Siswa)
     */
    public function test_all_eight_roles_functional_and_responsive_dom_verification(): void
    {
        $creator = User::factory()->create(['role' => 'mentor', 'name' => 'Mentor Pembimbing']);

        $assignment = Assignment::create([
            'title' => 'Tugas Integrasi Multi-Peran Responsif 2026',
            'description' => 'Desain arsitektur microservices dan uji coba antarmuka responsif di semua perangkat.',
            'deadline' => now()->addDays(7),
            'created_by' => $creator->id,
            'is_active' => true,
        ]);

        // ==========================================
        // 1. PENGUJIAN ROLE PEMBUAT TUGAS (Owner & Mentor)
        // ==========================================
        $managers = [
            'owner' => User::factory()->create(['role' => 'owner', 'name' => 'Bapak Owner']),
            'mentor' => $creator,
        ];

        foreach ($managers as $roleName => $managerUser) {
            // Index DOM: Harus melihat elemen manajemen & tombol aksi pembuat tugas
            $managerIndex = $this->actingAs($managerUser)->get(route('assignments.index'));
            $managerIndex->assertStatus(200);
            $managerIndex->assertSee('Buat Tugas Baru');
            $managerIndex->assertSee('Total Tugas');
            $managerIndex->assertSee('Tugas Aktif');
            $managerIndex->assertSee('Jawaban Masuk');
            $managerIndex->assertSee('Periksa Pengumpulan');
            // Check responsive grid tokens in manager DOM
            $managerIndex->assertSee('grid grid-cols-2 lg:grid-cols-4', false);
            $managerIndex->assertSee('grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3', false);

            // Show DOM: Harus melihat tombol Edit, Hapus, dan kedua mode tampilan (Desktop table & Mobile cards)
            $managerShow = $this->actingAs($managerUser)->get(route('assignments.show', $assignment->id));
            $managerShow->assertStatus(200);
            $managerShow->assertSee('Edit Tugas');
            $managerShow->assertSee('hidden md:block', false); // Desktop Table Container
            $managerShow->assertSee('block md:hidden', false); // Mobile Stacked Cards Container
            $managerShow->assertSee('Tenggat Waktu (Deadline)');

            // Submissions Review DOM: Khusus Owner & Mentor
            $managerSubmissions = $this->actingAs($managerUser)->get(route('assignments.submissions', $assignment->id));
            $managerSubmissions->assertStatus(200);
            $managerSubmissions->assertSee('Pengecekan Tugas');
            $managerSubmissions->assertSee('Form Penilaian');
            $managerSubmissions->assertSee('Buka Kembali / Perpanjang Tenggat');

            // Rekap Bulanan DOM: Khusus Owner & Mentor
            $managerRekap = $this->actingAs($managerUser)->get(route('assignments.rekap-bulanan'));
            $managerRekap->assertStatus(200);
            $managerRekap->assertSee('Rekap Tugas Bulanan (Monthly Report)');
            $managerRekap->assertSee('Ekspor CSV / Excel');
        }

        // ==========================================
        // 2. PENGUJIAN SELURUH 6 ROLE PENERIMA TUGAS
        // (Admin, IT, Sosmed, CS, Karyawan, Siswa)
        // ==========================================
        $recipients = [
            'admin' => User::factory()->create(['role' => 'admin', 'name' => 'Admin Penerima']),
            'it' => User::factory()->create(['role' => 'it', 'name' => 'IT Developer']),
            'sosmed' => User::factory()->create(['role' => 'sosmed', 'name' => 'Sosmed Content']),
            'cs' => User::factory()->create(['role' => 'cs', 'name' => 'CS Support']),
            'karyawan' => User::factory()->create(['role' => 'karyawan', 'name' => 'Staff Karyawan']),
            'siswa' => User::factory()->create(['role' => 'siswa', 'name' => 'Siswa Prakerin']),
        ];

        foreach ($recipients as $roleName => $recipientUser) {
            // A. Keamanan Otorisasi (RBAC): Dilarang membuat, mengedit, atau mengakses review
            $this->actingAs($recipientUser)->get(route('assignments.create'))->assertStatus(403);
            $this->actingAs($recipientUser)->post(route('assignments.store'), [
                'title' => 'Tugas Terlarang',
                'description' => 'Deskripsi',
                'deadline' => now()->addDay()->format('Y-m-d H:i:s'),
            ])->assertStatus(403);
            $this->actingAs($recipientUser)->get(route('assignments.submissions', $assignment->id))->assertStatus(403);
            $this->actingAs($recipientUser)->get(route('assignments.rekap-bulanan'))->assertStatus(403);

            // B. Index DOM: Menampilkan status penerima tugas, tab responsif, dan tombol kirim tugas
            $recipientIndex = $this->actingAs($recipientUser)->get(route('assignments.index'));
            $recipientIndex->assertStatus(200);
            $recipientIndex->assertDontSee('Buat Tugas Baru');
            $recipientIndex->assertDontSee('Periksa Pengumpulan');
            $recipientIndex->assertSee('Belum Dikumpul');
            $recipientIndex->assertSee('Kirim Tugas');
            // Responsive mobile tabs & filter bar check
            $recipientIndex->assertSee('overflow-x-auto no-scrollbar', false);
            $recipientIndex->assertSee('grid grid-cols-2 lg:grid-cols-4', false);

            // C. Show DOM: Menampilkan Form Pengumpulan Tugas responsif (input file, url, catatan)
            $recipientShow = $this->actingAs($recipientUser)->get(route('assignments.show', $assignment->id));
            $recipientShow->assertStatus(200);
            $recipientShow->assertSee('Form Pengumpulan Tugas');
            $recipientShow->assertSee('Belum Dikumpulkan');
            $recipientShow->assertDontSee('Edit Tugas');
            $recipientShow->assertDontSee('Hapus');
            $recipientShow->assertDontSee('Nilai / Score (0 - 100)');

            // D. Pengumpulan Tugas Nyata (POST submit)
            $uploadedDoc = UploadedFile::fake()->create("berkas_{$roleName}.docx", 180, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $submitPost = $this->actingAs($recipientUser)->post(route('assignments.submit', $assignment->id), [
                'submission_file' => $uploadedDoc,
                'submission_url' => "https://github.com/rootera/{$roleName}-project",
                'notes' => "Pengerjaan modul dari role {$roleName} telah tuntas.",
            ]);
            $submitPost->assertRedirect(route('assignments.index'));
            $submitPost->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');

            // E. Verifikasi Database
            $this->assertDatabaseHas('assignment_submissions', [
                'assignment_id' => $assignment->id,
                'user_id' => $recipientUser->id,
                'submission_url' => "https://github.com/rootera/{$roleName}-project",
                'status' => 'submitted',
            ]);

            // F. DOM State Transition di Index & Detail setelah pengumpulan
            $afterSubmitIndex = $this->actingAs($recipientUser)->get(route('assignments.index', ['tab' => 'submitted']));
            $afterSubmitIndex->assertStatus(200);
            $afterSubmitIndex->assertSee('Sudah Dikumpul');
            $afterSubmitIndex->assertSee('Detail &amp; Jawaban', false);

            $afterSubmitShow = $this->actingAs($recipientUser)->get(route('assignments.show', $assignment->id));
            $afterSubmitShow->assertStatus(200);
            $afterSubmitShow->assertSee('Sudah Dikumpulkan');
            $afterSubmitShow->assertSee("https://github.com/rootera/{$roleName}-project");
            $afterSubmitShow->assertSee("Pengerjaan modul dari role {$roleName} telah tuntas.");
        }

        // ==========================================
        // 3. PENGUJIAN DOM REKAP & PERIKSA OLEH MENTOR UNTUK SELURUH 6 PENERIMA
        // ==========================================
        $submissionsCheck = $this->actingAs($creator)->get(route('assignments.submissions', $assignment->id));
        $submissionsCheck->assertStatus(200);
        foreach ($recipients as $roleName => $recipientUser) {
            $submissionsCheck->assertSee($recipientUser->name);
            $submissionsCheck->assertSee("https://github.com/rootera/{$roleName}-project");
        }
    }
}
