<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\Vehicles\VehiclesSources\RevendaMais;
use App\Services\Vehicles\VehiclesSources\WebMotors;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    protected $sources = [
        'WebMotors' => WebMotors::class,
        'RevendaMais' => RevendaMais::class,
    ];

    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->when($request->brand, fn($query) => $query->whereIn('brand', $request->brand))
            ->when($request->model, fn($query) => $query->where('model', 'LIKE', '%' . $request->model . '%'))
            ->when($request->year_min, fn($query) => $query->where('year', '>=', $request->year_min))
            ->when($request->year_max, fn($query) => $query->where('year', '<=', $request->year_max))
            ->when($request->fuel, fn($query) => $query->whereIn('fuel', $request->fuel))
            ->when($request->transmission, fn($query) => $query->whereIn('transmission', $request->transmission))
            ->when($request->color, fn($query) => $query->whereIn('color', $request->color))
            ->when($request->price_min, fn($query) => $query->where('price', '>=', $request->price_min))
            ->when($request->price_max, fn($query) => $query->where('price', '<=', $request->price_max))
            ->when($request->mileage_min, fn($query) => $query->where('mileage', '>=', $request->mileage_min))
            ->when($request->mileage_max, fn($query) => $query->where('mileage', '<=', $request->mileage_max))
            ->when($request->doors_min, fn($query) => $query->where('doors', '>=', $request->doors_min))
            ->when($request->doors_max, fn($query) => $query->where('doors', '<=', $request->doors_max))
            ->with('optionals')
            ->when($request->sort_by, function ($query) use ($request) {
                $sort_params = explode("-", $request->sort_by);
                $query->orderBy($sort_params[0], $sort_params[1]);
            })
            ->when($request->date_filter, function ($query) use ($request) {
                if ($request->date_filter === '7_days') {
                    $query->where('updated_at', '>=', now()->subDays(7));
                } elseif ($request->date_filter === '15_days') {
                    $query->where('updated_at', '>=', now()->subDays(15));
                } elseif ($request->date_filter === '30_days') {
                    $query->where('updated_at', '>=', now()->subDays(30));
                } elseif ($request->date_filter === 'custom') {
                    if ($request->start_date && $request->end_date) {
                        $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
                    }
                }
            })
            ->paginate(10);

        if ($request->ajax()) {
            return view('stock.list', compact('vehicles'))->render();
        }

        $brands = Vehicle::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $fuels = Vehicle::select('fuel')->distinct()->orderBy('fuel')->pluck('fuel');
        $transmissions = Vehicle::select('transmission')->distinct()->orderBy('transmission')->pluck('transmission');
        $colors = Vehicle::select('color')->distinct()->orderBy('color')->pluck('color');
        $minYear = Vehicle::min('year');
        $maxPrice = Vehicle::max('price');
        $maxMileage = Vehicle::max('mileage');
        $minDoors = Vehicle::min('doors');
        $maxDoors = Vehicle::max('doors');

        $sources = array_keys($this->sources);

        return view('stock.index', [
            'vehicles' => $vehicles,
            'sources' => $sources,
            'filters' => [
                'brands' => $brands,
                'fuels' => $fuels,
                'transmissions' => $transmissions,
                'colors' => $colors,
                'year' => [
                    'min' => $minYear,
                    'max' => Carbon::now()->year
                ],
                'price' => [
                    'min' => 0,
                    'max' => $maxPrice
                ],
                'mileage' => [
                    'min' => 0,
                    'max' => $maxMileage
                ],
                'doors' => [
                    'min' => $minDoors,
                    'max' => $maxDoors
                ]
            ]
        ]);
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'source' => ['required', Rule::in(array_keys($this->sources))],
                'import_method' => ['required', Rule::in(['api', 'file'])],
            ]);

            $sourceClass = $this->sources[$request->source];
            $source = new $sourceClass();

            if ($request->import_method === 'file') {
                $allowedExtensions = $source::getAllowedFileExtensions();

                $request->validate([
                    'file' => 'required|file|mimes:' . implode(',', $allowedExtensions),
                ]);

                $filePath = $request->file('file')->getRealPath();
                $source::importFromFile($filePath);
            } elseif ($request->import_method === 'api') {
                $source::importFromApi();
            }

            return redirect()->back()->with('success', 'Importação iniciada...');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
