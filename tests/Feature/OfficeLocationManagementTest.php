<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeLocationManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: Owner dan Mentor berwenang memperbarui titik lokasi kantor dan melihat tombolnya di DOM
     */
    public function test_owner_and_mentor_can_update_office_location_and_see_controls_in_dom(): void
    {
        $managers = [
            'owner' => User::factory()->create(['role' => 'owner', 'name' => 'Wibowo Owner']),
            'mentor' => User::factory()->create(['role' => 'mentor', 'name' => 'Rafael Mentor']),
        ];

        foreach ($managers as $roleName => $manager) {
            // 1. POST update office location
            $response = $this->actingAs($manager)->postJson(route('attendance.update-office-location'), [
                'latitude' => -6.208800,
                'longitude' => 106.845600,
                'max_radius_meters' => 150,
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
                'office_lat' => -6.2088,
                'office_lng' => 106.8456,
                'max_radius' => 150,
            ]);

            // 2. DOM Check on /attendance/scan (GET attendance.create)
            $scanPage = $this->actingAs($manager)->get(route('attendance.create'));
            $scanPage->assertStatus(200);
            $scanPage->assertSee('Atur Lokasi Ini Sebagai Kantor');
            $scanPage->assertSee('Titik Kantor Saat Ini:');
            $scanPage->assertSee('Bypass Geofence');
        }
    }

    /**
     * Skenario 2: Role Admin dan seluruh role selain Owner & Mentor dilarang mengubah lokasi kantor dan tombol tersembunyi di DOM
     */
    public function test_admin_and_other_roles_cannot_update_office_location_and_controls_hidden_in_dom(): void
    {
        $restrictedRoles = [
            'admin' => User::factory()->create(['role' => 'admin', 'name' => 'Admin Mono']),
            'it' => User::factory()->create(['role' => 'it', 'name' => 'IT Yero']),
            'sosmed' => User::factory()->create(['role' => 'sosmed', 'name' => 'Sosmed Prayoga']),
            'cs' => User::factory()->create(['role' => 'cs', 'name' => 'CS Ghaitsaa']),
            'karyawan' => User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan Budi']),
            'siswa' => User::factory()->create(['role' => 'siswa', 'name' => 'Siswa Dani']),
        ];

        foreach ($restrictedRoles as $roleName => $user) {
            // 1. Percobaan POST update office location harus ditolak dengan 403 Forbidden
            $response = $this->actingAs($user)->postJson(route('attendance.update-office-location'), [
                'latitude' => -6.999999,
                'longitude' => 107.999999,
                'max_radius_meters' => 500,
            ]);

            $response->assertStatus(403);

            // 2. DOM Check on /attendance/scan: Tombol "Atur Lokasi Ini Sebagai Kantor" & "Bypass Geofence" TIDAK ADA
            $scanPage = $this->actingAs($user)->get(route('attendance.create'));
            $scanPage->assertStatus(200);
            $scanPage->assertDontSee('Atur Lokasi Ini Sebagai Kantor');
            $scanPage->assertDontSee('Bypass Geofence');
        }
    }

    /**
     * Skenario 3: Isolasi Bypass Geofence - Admin dilarang bypass lokasi saat presensi
     */
    public function test_admin_cannot_bypass_geofence_when_outside_radius(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Mono']);

        // Data presensi kamera dari jarak jauh (> 50 km dari kantor) dengan mencoba bypass_geofence
        $dummyImage = 'data:image/jpeg;base64,'.base64_encode('fake-image-content');
        $response = $this->actingAs($admin)->postJson(route('attendance.store'), [
            'tipe' => 'masuk',
            'image' => $dummyImage,
            'latitude' => -6.917464, // Bandung (~150km away from default Jakarta)
            'longitude' => 107.619122,
            'bypass_geofence' => true, // Admin mencoba bypass
        ]);

        // Karena admin bukan owner/mentor, bypass_geofence diabaikan dan presensi ditolak karena di luar radius
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonFragment([
            'success' => false,
        ]);
    }
}
