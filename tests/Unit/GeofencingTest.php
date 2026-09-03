<?php

namespace Tests\Unit;

use App\Services\GeofencingService;
use Tests\TestCase;

class GeofencingTest extends TestCase
{
    public function test_haversine_distance_calculation_at_same_point_is_zero(): void
    {
        $lat = -6.200000;
        $lng = 106.816666;

        $distance = GeofencingService::calculateDistance($lat, $lng, $lat, $lng);

        $this->assertEquals(0, $distance);
    }

    public function test_within_office_radius_allows_close_location(): void
    {
        $officeLat = (float) config('attendance.office_latitude', -6.200000);
        $officeLng = (float) config('attendance.office_longitude', 106.816666);

        // Slightly offset location (~10m away)
        $userLat = $officeLat + 0.000050;
        $userLng = $officeLng;

        $result = GeofencingService::isWithinOfficeRadius($userLat, $userLng);

        $this->assertTrue($result['is_allowed']);
        $this->assertLessThanOrEqual(100, $result['distance_meters']);
    }

    public function test_outside_office_radius_blocks_far_location(): void
    {
        $officeLat = (float) config('attendance.office_latitude', -6.200000);
        $officeLng = (float) config('attendance.office_longitude', 106.816666);

        // Far away location (> 5km away)
        $userLat = $officeLat + 0.050000;
        $userLng = $officeLng + 0.050000;

        $result = GeofencingService::isWithinOfficeRadius($userLat, $userLng);

        $this->assertFalse($result['is_allowed']);
        $this->assertGreaterThan(100, $result['distance_meters']);
    }
}
