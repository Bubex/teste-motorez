<?php

namespace App\Services\Vehicles;

use App\Models\VehicleOptional;
use App\Services\Vehicles\VehicleSourceInterface;
use Illuminate\Support\Facades\Log;

abstract class VehicleSourceBase implements VehicleSourceInterface
{
    protected static function getNormalizedFuelField($fuel)
    {
        $gas = 'Gasolina';
        $ethanol = 'Álcool';
        $flex = 'Flex';
        $diesel = 'Diesel';
        $gnv = 'Gás Natural';
        $hybrid = 'Híbrido';
        $eletric = 'Elétrico';

        $normalizedFuel = [
            'gasolina' => $gas,
            'alcool' => $ethanol,
            'flex' => $flex,
            'diesel' => $diesel,
            'gas natural' => $gnv,
            'gás natural' => $gnv,
            'GNV' => $gnv,
            'hibrido' => $hybrid,
            'híbrido' => $hybrid,
            'eletrico' => $eletric,
            'elétrico' => $eletric,
        ];

        return $normalizedFuel[strtolower($fuel)] ?? 'Não informado';
    }

    protected static function getNormalizedTransmissionField($transmission)
    {
        $manual = 'Manual';
        $automated = 'Automatizada';
        $automatic = 'Automática';
        $semiAutomated = 'Semi-Automática';
        $cvt = 'CVT';

        $normalizedTransmission = [
            'manual' => $manual,
            'automatizada' => $automated,
            'automatica' => $automatic,
            'automática' => $automatic,
            'semi-automatica' => $semiAutomated,
            'semi-automática' => $semiAutomated,
            'cvt' => $cvt,
        ];

        return $normalizedTransmission[strtolower($transmission)] ?? 'Não informado';
    }

    protected static function saveOptionals($optionals)
    {
        $optionalIds = [];
        if (is_array($optionals)) {
            foreach ($optionals as $optionalName) {
                $optional = VehicleOptional::firstOrCreate(['optional' => $optionalName]);
                $optionalIds[] = $optional->id;
            }
        }
        return $optionalIds;
    }

    abstract public static function saveVehicle($vehicleData);
}
