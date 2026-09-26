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

        $submitResponse->assertRedirect(route('assignments.show', $assignment->id));
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
}
