<?php

namespace App\Services;

class GeoFencingService
{
    /**
     * Calculate distance between two GPS coordinates using the Haversine formula.
     * Returns distance in meters.
     */
    public function calculateDistance(
        float $lat1, float $lng1,
        float $lat2, float $lng2
    ): float {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if given coordinates are within the allowed radius of office location.
     */
    public function isWithinRadius(
        float $userLat, float $userLng,
        float $officeLat, float $officeLng,
        int $radiusMeters
    ): bool {
        $distance = $this->calculateDistance($userLat, $userLng, $officeLat, $officeLng);
        return $distance <= $radiusMeters;
    }

    /**
     * Validate against all active office locations.
     * Returns the closest office if within radius, or null.
     */
    public function validateAgainstOffices(float $lat, float $lng): ?array
    {
        $locations = \App\Models\OfficeLocation::where('is_active', true)->get();

        $closestOffice = null;
        $shortestDistance = PHP_INT_MAX;

        foreach ($locations as $location) {
            $distance = $this->calculateDistance(
                $lat, $lng,
                (float) $location->latitude,
                (float) $location->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = (int) round($distance);
                $closestOffice = [
                    'office' => $location,
                    'distance' => $shortestDistance,
                    'within_radius' => $distance <= $location->radius_meters,
                ];
            }
        }

        return $closestOffice;
    }

    /**
     * Basic fake GPS / mock location detection.
     * Checks if accuracy is suspiciously perfect or coordinates are impossible.
     */
    public function detectFakeGps(
        float $lat, float $lng,
        float $accuracy
    ): bool {
        // Accuracy of 0 is impossible from real GPS
        if ($accuracy === 0.0) {
            return true;
        }

        // Suspiciously perfect accuracy (< 1 meter is unrealistic for mobile GPS)
        if ($accuracy < 1.0) {
            return true;
        }

        // Validate coordinate bounds (Indonesia bounding box roughly)
        $indonesiaBounds = [
            'minLat' => -11.0,
            'maxLat' => 6.0,
            'minLng' => 95.0,
            'maxLng' => 141.0,
        ];

        if ($lat < $indonesiaBounds['minLat'] || $lat > $indonesiaBounds['maxLat'] ||
            $lng < $indonesiaBounds['minLng'] || $lng > $indonesiaBounds['maxLng']) {
            return true; // outside Indonesia bounds
        }

        return false;
    }
}
