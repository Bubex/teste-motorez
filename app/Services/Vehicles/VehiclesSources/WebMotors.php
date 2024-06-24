<?php

namespace App\Services\Vehicles\VehiclesSources;

use App\Jobs\ImportVehiclesJob;
use App\Models\Vehicle;
use App\Services\Vehicles\VehicleSourceBase;
use Illuminate\Support\Facades\Http;

class WebMotors extends VehicleSourceBase
{
    public static function getAllowedFileExtensions()
    {
        return ['json'];
    }

    public static function getApiUrl()
    {
        return 'https://api.webmotors.com.br';
    }

    public static function importFromApi()
    {
        $response = Http::get(self::getApiUrl() . '/api/v1/estoque');

        if ($response->successful()) {
            $vehicles = $response->json('veiculos');

            foreach ($vehicles as $vehicleData) {
                ImportVehiclesJob::dispatch($vehicleData, self::class);
            }
        } else {
            throw new \Exception('Erro ao importar dados da Webmotors');
        }
    }

    public static function importFromFile($filePath)
    {
        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if (isset($data['veiculos'])) {
            foreach ($data['veiculos'] as $vehicleData) {
                ImportVehiclesJob::dispatch($vehicleData, self::class);
            }
        } else {
            throw new \Exception('Formato de arquivo inválido');
        }
    }

    public static function saveVehicle($vehicleData)
    {
        $vehicle = Vehicle::updateOrCreate(
            ['id' => $vehicleData['id']],
            [
                'id' => $vehicleData['id'],
                'brand' => $vehicleData['marca'],
                'model' => $vehicleData['modelo'],
                'year' => $vehicleData['ano'],
                'version' => $vehicleData['versao'],
                'color' => $vehicleData['cor'],
                'mileage' => $vehicleData['km'],
                'fuel' => array_key_exists('combustivel', $vehicleData) ? self::getNormalizedFuelField($vehicleData['combustivel']) : 'Não informado',
                'transmission' => array_key_exists('cambio', $vehicleData) ? self::getNormalizedTransmissionField($vehicleData['cambio']) : 'Não informado',
                'doors' => $vehicleData['portas'],
                'price' => $vehicleData['preco'],
                'last_update' => $vehicleData['date'],
            ]
        );


        $optionalIds = self::saveOptionals($vehicleData['opcionais'] ?? []);
        $vehicle->optionals()->sync($optionalIds);
    }
}
