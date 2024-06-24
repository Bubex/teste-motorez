<?php

namespace App\Services\Vehicles;

interface VehicleSourceInterface
{
    public static function getAllowedFileExtensions();
    public static function getApiUrl();
    public static function importFromApi();
    public static function importFromFile($filePath);
    public static function saveVehicle($vehicle);
}
