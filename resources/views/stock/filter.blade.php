<div class="relative bg-white dark:bg-gray-800 w-[500px] h-full shadow-xl transition-transform transform -translate-x-full"
    @click.away="isDrawerOpen = false"
    :class="{ 'translate-x-0': isDrawerOpen, '-translate-x-full': !isDrawerOpen }"
    >
    <div class="h-full flex flex-col p-4">
        <h2 class="text-lg font-semibold">Filtrar Veículos</h2>

        <form id="filterForm" class="flex-1 flex flex-col overflow-hidden" @submit.prevent="filterVehicles">
            <div class="flex-1 grid grid-cols-2 gap-5 auto-rows-min overflow-x-hidden overflow-y-auto mb-4 pr-2">
                <!-- Marca -->
                <div class="">
                    <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marca</label>
                    <select id="brand" name="brand[]" multiple class="multiple-select" style="width:100%;" placeholder="Marca">
                        @foreach($filters['brands'] as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Modelo -->
                <div class="">
                    <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modelo</label>
                    <input type="text" name="model" id="model" placeholder="Modelo"
                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:text-gray-900">
                </div>

                <!-- Ano -->
                <div class="">
                    <label for="year_range"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ano</label>
                    <div class="px-5 py-3 mt-6">
                        <div id="year_range" class="nouislider"></div>
                        <input type="hidden" name="year_min" x-ref="year_min">
                        <input type="hidden" name="year_max" x-ref="year_max">
                    </div>
                </div>

                <!-- Quilometragem -->
                <div class="">
                    <label for="mileage_range"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quilometragem</label>
                    <div class="px-5 py-3 mt-6">
                        <div id="mileage_range" class="nouislider"></div>
                        <input type="hidden" name="mileage_min" x-ref="mileage_min">
                        <input type="hidden" name="mileage_max" x-ref="mileage_max">
                    </div>
                </div>
                
                <!-- Preço -->
                <div class="">
                    <label for="price_range"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço</label>
                    <div class="px-5 py-3 mt-6">
                        <div id="price_range" class="nouislider"></div>
                        <input type="hidden" name="price_min" x-ref="price_min">
                        <input type="hidden" name="price_max" x-ref="price_max">
                    </div>
                </div>

                <!-- Portas -->
                <div class="">
                    <label for="doors_range"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Portas</label>
                    <div class="px-5 py-3 mt-6">
                        <div id="doors_range" class="nouislider"></div>
                        <input type="hidden" name="doors_min" x-ref="doors_min">
                        <input type="hidden" name="doors_max" x-ref="doors_max">
                    </div>
                </div>

                <!-- Combustível -->
                <div class="">
                    <label for="fuel"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Combustível</label>
                    <select id="fuel" name="fuel[]" multiple class="multiple-select" style="width:100%;" placeholder="Combustível">
                        @foreach($filters['fuels'] as $fuel)
                            <option value="{{ $fuel }}">{{ $fuel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Transmissão -->
                <div class="">
                    <label for="transmission"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Câmbio</label>
                    <select id="transmission" name="transmission[]" multiple class="multiple-select"
                        style="width:100%;" placeholder="Câmbio">
                        @foreach($filters['transmissions'] as $transmission)
                            <option value="{{ $transmission }}">{{ $transmission }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cor -->
                <div class="">
                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cor</label>
                    <select id="color" name="color[]" multiple class="multiple-select" style="width:100%;" placeholder="Cor">
                        @foreach($filters['colors'] as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="">
                    <label for="date_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Cadastro/Importação</label>
                    <select name="date_filter" id="date_filter" @change="toggleCustomDate" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                        <option value="">Selecione um período</option>
                        <option value="7_days">Últimos 7 dias</option>
                        <option value="15_days">Últimos 15 dias</option>
                        <option value="30_days">Últimos 30 dias</option>
                        <option value="custom">Período personalizado</option>
                    </select>
                </div>

                <div x-show="isCustomDate">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data Mínima de Cadastro</label>
                    <input type="date" name="start_date" id="start_date" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                </div>

                <div x-show="isCustomDate">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data Máxima de Cadastro</label>
                    <input type="date" name="end_date" id="end_date" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                </div>

                <!-- Ordenação -->
                <div class="">
                    <label for="sort_by"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ordenar</label>
                    <select name="sort_by" id="sort_by" class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight      focus:outline-none focus:shadow-outline">
                        <option value="">Ordenar por</option>
                        <option value="brand-asc">Marca (De A a Z)</option>
                        <option value="brand-desc">Marca (De Z a A)</option>
                        <option value="model-asc">Modelo (De A a Z)</option>
                        <option value="model-desc">Modelo (De Z a A)</option>
                        <option value="year-asc">Ano (Mais antigos)</option>
                        <option value="year-desc">Ano (Mais novos)</option>
                        <option value="price-asc">Preço (Mais baratos)</option>
                        <option value="price-desc">Preço (Mais caros)</option>
                        <option value="mileage-asc">Quilometragem (Menos rodados)</option>
                        <option value="mileage-desc">Quilometragem (Mais rodados)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t-2 border-gray-600">
                <x-secondary-button class="w-full justify-center" @click.prevent="resetFilters">
                    Limpar Filtros
                </x-secondary-button>
                <x-primary-button class="w-full justify-center mt-2">
                    Aplicar Filtros
                </x-primary-button>
            </div>
        </form>
    </div>
    <button @click="isDrawerOpen = false" class="absolute top-0 right-0 m-4">
        Fechar
    </button>
</div>