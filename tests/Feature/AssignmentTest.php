<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_assignment(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($manager)->post(route('assignments.store'), [
            'title' => 'Tugas Test UI Design',
            'description' => 'Deskripsi tugas pengujian UI',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'file_attachment' => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('assignments.index'));
        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Test UI Design',
            'created_by' => $manager->id,
        ]);
    }

    public function test_employee_cannot_create_assignment(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($employee)->get(route('assignments.create'));
        $response->assertStatus(403);

        $postResponse = $this->actingAs($employee)->post(route('assignments.store'), [
            'title' => 'Tugas Ilegal',
            'description' => 'Mencoba membuat tugas',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);
        $postResponse->assertStatus(403);
    }

    public function test_employee_can_view_assignment_and_submit(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Laravel API',
            'description' => 'Kerjakan API CRUD',
            'deadline' => now()->addDays(3),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('assignments.show', $assignment->id));
        $response->assertStatus(200);

        $submitResponse = $this->actingAs($employee)->post(route('assignments.submit', $assignment->id), [
            'submission_url' => 'https://github.com/employee/laravel-api',
            'notes' => 'Sudah selesai dikerjakan pak',
            'submission_file' => UploadedFile::fake()->create('laporan.pdf', 200, 'application/pdf'),
        ]);

        $submitResponse->assertRedirect(route('assignments.index'));
        $submitResponse->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $employee->id,
            'notes' => 'Sudah selesai dikerjakan pak',
        ]);
    }

    public function test_manager_cannot_submit_assignment(): void
    {
        $manager = User::factory()->create(['role' => 'owner']);

        $assignment = Assignment::create([
            'title' => 'Tugas Untuk Karyawan',
            'description' => 'Instruksi tugas',
            'deadline' => now()->addDays(3),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($manager)->post(route('assignments.submit', $assignment->id), [
            'notes' => 'Mencoba mengumpulkan sebagai owner',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_manager_can_grade_submission(): void
    {
        $manager = User::factory()->create(['role' => 'owner']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Database Migration',
            'description' => 'Buat tabel baru',
            'deadline' => now()->addDays(2),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $employee->id,
            'notes' => 'Hasil rancangan database',
            'submitted_at' => now(),
        ]);

        $gradeResponse = $this->actingAs($manager)->post(route('submissions.grade', $submission->id), [
            'score' => 95,
            'feedback' => 'Sangat rapi dan sesuai skema database',
        ]);

        $gradeResponse->assertStatus(302);
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'score' => 95,
            'feedback' => 'Sangat rapi dan sesuai skema database',
        ]);
    }

    public function test_employee_cannot_grade_submission(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $anotherEmployee = User::factory()->create(['role' => 'karyawan']);
        $manager = User::factory()->create(['role' => 'admin']);

        $assignment = Assignment::create([
            'title' => 'Tugas Khusus',
            'description' => 'Deskripsi',
            'deadline' => now()->addDays(2),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $anotherEmployee->id,
            'notes' => 'Jawaban',
            'submitted_at' => now(),
        ]);

        $gradeResponse = $this->actingAs($employee)->post(route('submissions.grade', $submission->id), [
            'score' => 100,
            'feedback' => 'Mencoba menilai sendiri',
        ]);

        $gradeResponse->assertStatus(403);
    }

    public function test_employee_submission_updates_status_counters_and_tabs_properly(): void
    {
        Storage::fake('public');

        $manager = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Frontend Blade',
            'description' => 'Kerjakan halaman blade responsif',
            'deadline' => now()->addDays(3),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        // 1. Sebelum submit: tugas muncul di tab pending, stat pending = 1, submitted = 0
        $responseBefore = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'pending']));
        $responseBefore->assertStatus(200);
        $responseBefore->assertViewHas('myPendingCount', 1);
        $responseBefore->assertViewHas('mySubmittedCount', 0);
        $responseBefore->assertSee('Tugas Frontend Blade');
        $responseBefore->assertSee('Belum Dikumpul');

        // 2. Submit tugas
        $submitResponse = $this->actingAs($employee)->post(route('assignments.submit', $assignment->id), [
            'submission_url' => 'https://github.com/employee/blade-ui',
            'notes' => 'Tugas sudah selesai',
            'submission_file' => UploadedFile::fake()->create('jawaban.pdf', 200, 'application/pdf'),
        ]);

        $submitResponse->assertRedirect(route('assignments.index'));
        $submitResponse->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $employee->id,
            'submission_url' => 'https://github.com/employee/blade-ui',
            'notes' => 'Tugas sudah selesai',
        ]);

        // 3. Setelah submit di tab pending: tugas tidak lagi muncul, counter pending = 0, submitted = 1
        $responseAfterPending = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'pending']));
        $responseAfterPending->assertStatus(200);
        $responseAfterPending->assertViewHas('myPendingCount', 0);
        $responseAfterPending->assertViewHas('mySubmittedCount', 1);
        $responseAfterPending->assertDontSee('Tugas Frontend Blade');
        $responseAfterPending->assertSee('Semua Tugas Sudah Dikumpulkan!');

        // 4. Di tab submitted (Riwayat Pengumpulan): tugas muncul dengan badge "Sudah Dikumpul"
        $responseAfterSubmitted = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'submitted']));
        $responseAfterSubmitted->assertStatus(200);
        $responseAfterSubmitted->assertSee('Tugas Frontend Blade');
        $responseAfterSubmitted->assertSee('Sudah Dikumpul');
        $responseAfterSubmitted->assertDontSee('Kerjakan Sekarang');
        $responseAfterSubmitted->assertSee('Detail & Jawaban');
    }

    public function test_overdue_assignment_does_not_appear_in_pending_tab(): void
    {
        $manager = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $overdueAssignment = Assignment::create([
            'title' => 'Tugas Sudah Kedaluwarsa',
            'description' => 'Tugas yang deadline-nya sudah lewat',
            'deadline' => now()->subDay(),
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'pending']));
        $response->assertStatus(200);
        $response->assertViewHas('myPendingCount', 0);
        $response->assertDontSee('Tugas Sudah Kedaluwarsa');
    }

    public function test_mentor_and_owner_see_check_submissions_button_on_assignment_card(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $student = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Review Portofolio',
            'description' => 'Kumpulkan portofolio desain UI/UX',
            'deadline' => now()->addDays(5),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $assignment->submissions()->create([
            'user_id' => $student->id,
            'submission_url' => 'https://dribbble.com/student',
            'notes' => 'Portofolio siap direview',
            'submitted_at' => now(),
        ]);

        // Mentor sees "Periksa Pengumpulan (1 Jawaban)"
        $mentorResponse = $this->actingAs($mentor)->get(route('assignments.index'));
        $mentorResponse->assertStatus(200);
        $mentorResponse->assertSee('Periksa Pengumpulan');
        $mentorResponse->assertSee('1 Jawaban');
        $mentorResponse->assertSee(route('assignments.submissions', $assignment->id));
        $mentorResponse->assertSee(route('assignments.edit', $assignment->id));

        // Owner also sees "Periksa Pengumpulan (1 Jawaban)"
        $owner = User::factory()->create(['role' => 'owner']);
        $ownerResponse = $this->actingAs($owner)->get(route('assignments.index'));
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee('Periksa Pengumpulan');
        $ownerResponse->assertSee('1 Jawaban');
        $ownerResponse->assertSee(route('assignments.submissions', $assignment->id));
    }

    public function test_mentor_and_owner_can_access_submissions_detail_page(): void
    {
        Storage::fake('public');

        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Budi Santoso']);

        $assignment = Assignment::create([
            'title' => 'Tugas Integrasi Payment Gateway',
            'description' => 'Implementasi Midtrans Snap API',
            'deadline' => now()->addDays(2),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $assignment->submissions()->create([
            'user_id' => $employee->id,
            'submission_url' => 'https://github.com/budi/payment-gateway',
            'notes' => 'Sudah ditest di sandbox',
            'submitted_at' => now(),
        ]);

        // Test Mentor access
        $mentorResponse = $this->actingAs($mentor)->get(route('assignments.submissions', $assignment->id));
        $mentorResponse->assertStatus(200);
        $mentorResponse->assertSee('Tugas Integrasi Payment Gateway');
        $mentorResponse->assertSee('Budi Santoso');
        $mentorResponse->assertSee('https://github.com/budi/payment-gateway');
        $mentorResponse->assertSee('Sudah ditest di sandbox');
        $mentorResponse->assertSee('Kembali ke Manajemen Tugas');

        // Test Owner access
        $owner = User::factory()->create(['role' => 'owner']);
        $ownerResponse = $this->actingAs($owner)->get(route('assignments.submissions', $assignment->id));
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee('Tugas Integrasi Payment Gateway');
        $ownerResponse->assertSee('Budi Santoso');
    }

    public function test_employee_cannot_access_submissions_detail_page(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Internal',
            'description' => 'Instruksi tugas',
            'deadline' => now()->addDays(2),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('assignments.submissions', $assignment->id));
        $response->assertStatus(403);
    }

    public function test_mentor_can_extend_user_due_date_and_extended_user_can_submit(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $budi = User::factory()->create(['role' => 'karyawan', 'name' => 'Budi Extension']);
        $dewi = User::factory()->create(['role' => 'karyawan', 'name' => 'Dewi No Extension']);

        $assignment = Assignment::create([
            'title' => 'Tugas Sudah Lewat Deadline',
            'description' => 'Tugas yang batas waktunya telah lampau',
            'deadline' => now()->subDay(),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        // 1. Mentor extends due date for Budi
        $customDueDate = now()->addDays(2)->format('Y-m-d H:i:s');
        $extendResponse = $this->actingAs($mentor)->post(route('assignments.extend-user', $assignment->id), [
            'user_id' => $budi->id,
            'custom_due_date' => $customDueDate,
            'reason' => 'Izin sakit opname 3 hari',
        ]);

        $extendResponse->assertStatus(302);
        $this->assertDatabaseHas('assignment_user_overrides', [
            'assignment_id' => $assignment->id,
            'user_id' => $budi->id,
            'is_unlocked' => true,
            'reason' => 'Izin sakit opname 3 hari',
        ]);

        // 2. Budi sees the assignment in pending tab & custom deadline banner
        $budiPendingResponse = $this->actingAs($budi)->get(route('assignments.index', ['tab' => 'pending']));
        $budiPendingResponse->assertStatus(200);
        $budiPendingResponse->assertSee('Tugas Sudah Lewat Deadline');
        $budiPendingResponse->assertSee('Tenggat Khusus Anda:');

        // 3. Budi can successfully submit the assignment
        $budiSubmitResponse = $this->actingAs($budi)->post(route('assignments.submit', $assignment->id), [
            'submission_url' => 'https://github.com/budi/extension-task',
            'notes' => 'Terima kasih atas perpanjangan waktunya mentor',
        ]);
        $budiSubmitResponse->assertRedirect(route('assignments.index'));
        $budiSubmitResponse->assertSessionHas('success', 'Tugas berhasil dikumpulkan!');

        // 4. Dewi (not extended) CANNOT submit because deadline has passed
        $dewiSubmitResponse = $this->actingAs($dewi)->post(route('assignments.submit', $assignment->id), [
            'submission_url' => 'https://github.com/dewi/task',
            'notes' => 'Mencoba submit setelah deadline',
        ]);
        $dewiSubmitResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $dewi->id,
        ]);
    }

    public function test_employee_cannot_extend_user_due_date(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $targetUser = User::factory()->create(['role' => 'karyawan']);
        $mentor = User::factory()->create(['role' => 'mentor']);

        $assignment = Assignment::create([
            'title' => 'Tugas Test Otorisasi Extension',
            'description' => 'Deskripsi',
            'deadline' => now()->addDays(2),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->post(route('assignments.extend-user', $assignment->id), [
            'user_id' => $targetUser->id,
            'custom_due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'reason' => 'Mencoba extend sendiri',
        ]);

        $response->assertStatus(403);
    }

    public function test_mentor_grades_submission_with_status_and_employee_sees_score(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor', 'name' => 'Mentor Utama']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Siti Nurhaliza']);

        $assignment = Assignment::create([
            'title' => 'Tugas Desain Grafis Promosi',
            'description' => 'Rancang banner promosi',
            'deadline' => now()->addDays(3),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $employee->id,
            'submission_url' => 'https://figma.com/design/banner',
            'notes' => 'Desain banner selesai dibuat',
            'submitted_at' => now(),
        ]);

        // Mentor grades submission with score, feedback, and status 'graded'
        $gradeResponse = $this->actingAs($mentor)->post(route('submissions.grade', $submission->id), [
            'score' => 92,
            'feedback' => 'Komposisi warna dan tipografi sangat profesional',
            'status' => 'graded',
        ]);

        $gradeResponse->assertStatus(302);
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'score' => 92,
            'status' => 'graded',
            'graded_by' => $mentor->id,
            'feedback' => 'Komposisi warna dan tipografi sangat profesional',
        ]);

        // Employee visits assignments.index (submitted tab) and sees the score and feedback
        $employeeIndexResponse = $this->actingAs($employee)->get(route('assignments.index', ['tab' => 'submitted']));
        $employeeIndexResponse->assertStatus(200);
        $employeeIndexResponse->assertSee('Sudah Dinilai');
        $employeeIndexResponse->assertSee('92/100');
        $employeeIndexResponse->assertSee('Komposisi warna dan tipografi sangat profesional');

        // Employee visits detail page (assignments.show)
        $employeeShowResponse = $this->actingAs($employee)->get(route('assignments.show', $assignment->id));
        $employeeShowResponse->assertStatus(200);
        $employeeShowResponse->assertSee('92');
        $employeeShowResponse->assertSee('Komposisi warna dan tipografi sangat profesional');
        $employeeShowResponse->assertSee('Telah Dinilai &amp; Selesai', false);
    }

    public function test_mentor_can_grade_submission_via_ajax_without_page_reload(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        $assignment = Assignment::create([
            'title' => 'Tugas Frontend API',
            'description' => 'Kerjakan API',
            'deadline' => now()->addDays(3),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $submission = $assignment->submissions()->create([
            'user_id' => $employee->id,
            'submission_url' => 'https://github.com/api',
            'notes' => 'API siap',
            'submitted_at' => now(),
        ]);

        $ajaxResponse = $this->actingAs($mentor)->postJson(route('submissions.grade', $submission->id), [
            'score' => 88,
            'feedback' => 'Bagus dan rapi',
            'status' => 'graded',
        ]);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
        ]);
        $this->assertEquals(88, $submission->fresh()->score);
    }
}
