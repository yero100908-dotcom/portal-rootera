<?php

namespace App\Services;

use App\Models\User;

class FaceRecognitionService
{
    /**
     * Calculate Euclidean distance between two face descriptor arrays.
     */
    public function calculateDistance(array $descriptor1, array $descriptor2): float
    {
        if (count($descriptor1) !== count($descriptor2)) {
            return 999.0; // Return high distance if dimensions mismatch
        }

        $sum = 0.0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    /**
     * Match a given descriptor against active users in the system.
     */
    public function matchFace(array $inputDescriptor, float $threshold = 0.6): ?User
    {
        $users = User::whereNotNull('face_descriptor')
            ->where('is_active', true)
            ->get();

        $bestMatchUser = null;
        $lowestDistance = 999.0;

        foreach ($users as $user) {
            $storedDescriptor = json_decode($user->face_descriptor, true);
            if (! is_array($storedDescriptor)) {
                continue;
            }

            $distance = $this->calculateDistance($inputDescriptor, $storedDescriptor);

            if ($distance < $threshold && $distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatchUser = $user;
            }
        }

        return $bestMatchUser;
    }
}
