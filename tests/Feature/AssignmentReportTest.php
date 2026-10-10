<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_and_owner_can_access_rekap_bulanan_page(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $owner = User::factory()->create(['role' => 'owner']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Ahmad Fajar']);

        $assignment = Assignment::create([
            'title' => 'Tugas Desain Arsitektur',
            'description' => 'Instruksi tugas bulanan',
            'deadline' => now()->addDays(3),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $assignment->submissions()->create([
            'user_id' => $employee->id,
            'notes' => 'Sudah dikerjakan tepat waktu',
            'submitted_at' => now(),
        ]);

        // 1. Mentor access
        $mentorResponse = $this->actingAs($mentor)->get(route('assignments.rekap-bulanan'));
        $mentorResponse->assertStatus(200);
        $mentorResponse->assertSee('Rekap Tugas Bulanan');
        $mentorResponse->assertSee('Ahmad Fajar');
        $mentorResponse->assertSee('100%');
        $mentorResponse->assertSee('Tepat Waktu');

        // 2. Owner access
        $ownerResponse = $this->actingAs($owner)->get(route('assignments.rekap-bulanan'));
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertSee('Rekap Tugas Bulanan');
        $ownerResponse->assertSee('Ahmad Fajar');
    }

    public function test_regular_employee_cannot_access_rekap_bulanan_page(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($employee)->get(route('assignments.rekap-bulanan'));
        $response->assertStatus(403);

        $exportResponse = $this->actingAs($employee)->get(route('assignments.rekap-bulanan.export'));
        $exportResponse->assertStatus(403);
    }

    public function test_sidebar_navigation_displays_rekap_tugas_menu_only_for_mentor_and_owner(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $owner = User::factory()->create(['role' => 'owner']);
        $employee = User::factory()->create(['role' => 'karyawan']);

        // Mentor sees the menu
        $mentorResponse = $this->actingAs($mentor)->get(route('dashboard'));
        $mentorResponse->assertSee(route('assignments.rekap-bulanan'));
        $mentorResponse->assertSee('Rekap Tugas Bulanan');

        // Owner sees the menu
        $ownerResponse = $this->actingAs($owner)->get(route('dashboard'));
        $ownerResponse->assertSee(route('assignments.rekap-bulanan'));
        $ownerResponse->assertSee('Rekap Tugas Bulanan');

        // Employee does not see the menu
        $employeeResponse = $this->actingAs($employee)->get(route('dashboard'));
        $employeeResponse->assertDontSee(route('assignments.rekap-bulanan'));
        $employeeResponse->assertDontSee('Rekap Tugas Bulanan');
    }

    public function test_rekap_bulanan_filters_data_by_month_and_year(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Dewi Sartika']);

        // Assignment created in January 2026
        $assignmentJan = Assignment::create([
            'title' => 'Tugas Januari 2026',
            'description' => 'Tugas awal tahun',
            'deadline' => '2026-01-20 23:59:59',
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);
        $assignmentJan->created_at = '2026-01-05 10:00:00';
        $assignmentJan->save();

        // Assignment created in February 2026
        $assignmentFeb = Assignment::create([
            'title' => 'Tugas Februari 2026',
            'description' => 'Tugas bulan kedua',
            'deadline' => '2026-02-20 23:59:59',
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);
        $assignmentFeb->created_at = '2026-02-05 10:00:00';
        $assignmentFeb->save();

        // Filter for January 2026
        $responseJan = $this->actingAs($mentor)->get(route('assignments.rekap-bulanan', [
            'month' => 1,
            'year' => 2026,
        ]));
        $responseJan->assertStatus(200);
        $responseJan->assertViewHas('totalAssignments', 1);

        // Filter for February 2026
        $responseFeb = $this->actingAs($mentor)->get(route('assignments.rekap-bulanan', [
            'month' => 2,
            'year' => 2026,
        ]));
        $responseFeb->assertStatus(200);
        $responseFeb->assertViewHas('totalAssignments', 1);

        // Filter for March 2026 (empty)
        $responseMar = $this->actingAs($mentor)->get(route('assignments.rekap-bulanan', [
            'month' => 3,
            'year' => 2026,
        ]));
        $responseMar->assertStatus(200);
        $responseMar->assertViewHas('totalAssignments', 0);
    }

    public function test_mentor_and_owner_can_export_csv_rekap(): void
    {
        $mentor = User::factory()->create(['role' => 'mentor']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Budi Santoso']);

        $assignment = Assignment::create([
            'title' => 'Tugas Export Test',
            'description' => 'Tugas uji ekspor',
            'deadline' => now()->addDays(5),
            'created_by' => $mentor->id,
            'is_active' => true,
        ]);

        $assignment->submissions()->create([
            'user_id' => $employee->id,
            'notes' => 'Catatan export',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($mentor)->get(route('assignments.rekap-bulanan.export', [
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="rekap-tugas-', $response->headers->get('Content-Disposition'));
    }
}
