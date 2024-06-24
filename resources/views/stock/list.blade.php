<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @php
                    $th = function ($content, $additionalClass = '') {
                        $classes = "px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider {$additionalClass}";
                        return "<th scope='col' class='{$classes}'>{$content}</th>";
                    };
                @endphp
                {!! $th('ID') !!}
                {!! $th('Marca') !!}
                {!! $th('Modelo') !!}
                {!! $th('Ano') !!}
                {!! $th('Cor') !!}
                {!! $th('KM') !!}
                {!! $th('Combustível') !!}
                {!! $th('Câmbio') !!}
                {!! $th('Portas') !!}
                {!! $th('Preço') !!}
                {!! $th('Opcionais') !!}
            </tr>
        </thead>
        <tbody>
            @php
                $td = function ($content, $additionalClass = '') {
                    $classes = "px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 {$additionalClass}";
                    return "<td class='{$classes}'>{$content}</td>";
                };
            @endphp
            @foreach ($vehicles as $vehicle)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">

                    {!! $td($vehicle->id) !!}
                    {!! $td($vehicle->brand, 'font-medium text-gray-900 dark:text-white') !!}
                    {!! $td($vehicle->model . "<br/><span class='text-xs text-gray-500'>" . $vehicle->version . "</span>") !!}
                    {!! $td($vehicle->year) !!}
                    {!! $td($vehicle->color) !!}
                    {!! $td(number_format($vehicle->mileage, 0, '', '.')) !!}
                    {!! $td($vehicle->fuel) !!}
                    {!! $td($vehicle->transmission) !!}
                    {!! $td($vehicle->doors) !!}
                    {!! $td(number_format($vehicle->price, 2, ',', '.')) !!}

                    <td class="text-sm text-gray-500 dark:text-gray-400 py-2 space-y-1">
                        @foreach ($vehicle->optionals as $optional)
                            <span
                                class="inline-block bg-blue-200 text-blue-800 px-1.5 py-0.5 rounded-full text-xs font-semibold mr-0.5">{{ $optional->optional }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="p-6 pagination">
    {{ $vehicles->links() }}
</div>