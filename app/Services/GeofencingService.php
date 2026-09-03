<?php

namespace App\Services;

class GeofencingService
{
    /**
     * Calculate distance between two GPS coordinates in meters using the Haversine formula.
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Check if a given coordinate is within the allowed office radius.
     */
    public static function isWithinOfficeRadius(float $userLat, float $userLng): array
    {
        $officeLat = (float) config('attendance.office_latitude', -6.200000);
        $officeLng = (float) config('attendance.office_longitude', 106.816666);
        $maxRadius = (float) config('attendance.max_radius_meters', 100);

        $distance = self::calculateDistance($userLat, $userLng, $officeLat, $officeLng);
        $isAllowed = $distance <= $maxRadius;

        return [
            'is_allowed' => $isAllowed,
            'distance_meters' => $distance,
            'max_radius_meters' => $maxRadius,
            'office_lat' => $officeLat,
            'office_lng' => $officeLng,
        ];
    }
}
