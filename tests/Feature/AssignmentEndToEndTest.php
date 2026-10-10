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
            'it' => User::factory()->create(['role' => 'it', 'name' => 'Staff IT Yero']),
            'sosmed' => User::factory()->create(['role' => 'sosmed', 'name' => 'Staff Sosmed Prayoga']),
            'cs' => User::factory()->create(['role' => 'cs', 'name' => 'Staff CS Ghaitsaa']),
            'karyawan' => User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan Umum Budi']),
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
        $recipientRoles = ['it', 'sosmed', 'cs', 'karyawan', 'siswa'];

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
}
