<?php

namespace App\Services\Vehicles\VehiclesSources;

use App\Jobs\ImportVehiclesJob;
use App\Models\Vehicle;
use App\Services\Vehicles\VehicleSourceBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RevendaMais extends VehicleSourceBase
{
    public static function getAllowedFileExtensions()
    {
        return ['xml'];
    }

    public static function getApiUrl()
    {
        return 'https://api.revendamais.com.br';
    }

    public static function importFromApi()
    {
        $response = Http::get(self::getApiUrl() . '/api/estoque');

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
        $xml = simplexml_load_file($filePath);
        $json = json_encode($xml);
        $data = json_decode($json, true);

        if (isset($data['veiculos']['veiculo'])) {
            foreach ($data['veiculos']['veiculo'] as $vehicleData) {
                ImportVehiclesJob::dispatch($vehicleData, self::class);
            }
        } else {
            throw new \Exception('Formato de arquivo inválido');
        }
    }

    public static function saveVehicle($vehicleData)
    {
        $last_update = \DateTime::createFromFormat('d/m/Y H:i', $vehicleData['ultimaAtualizacao'])->format('Y-m-d H:i:s');

        $vehicle = Vehicle::updateOrCreate(
            ['id' => $vehicleData['codigoVeiculo']],
            [
                'id' => $vehicleData['codigoVeiculo'],
                'brand' => $vehicleData['marca'],
                'model' => $vehicleData['modelo'],
                'year' => $vehicleData['ano'],
                'version' => $vehicleData['versao'],
                'color' => $vehicleData['cor'],
                'mileage' => $vehicleData['quilometragem'],
                'fuel' => array_key_exists('tipoCombustivel', $vehicleData) ? self::getNormalizedFuelField($vehicleData['tipoCombustivel']) : 'Não informado',
                'transmission' => array_key_exists('cambio', $vehicleData) ? self::getNormalizedTransmissionField($vehicleData['cambio']) : 'Não informado',
                'doors' => $vehicleData['portas'],
                'price' => $vehicleData['precoVenda'],
                'last_update' => $last_update,
            ]
        );

        $optionalIds = self::saveOptionals($vehicleData['opcionais']['opcional'] ?? []);
        $vehicle->optionals()->sync($optionalIds);
    }
}
