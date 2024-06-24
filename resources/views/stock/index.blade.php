<x-app-layout x-data="filterHandler({{ json_encode($filters) }})" x-init="initFilters">

    @push('head')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vehicle Stock') }}
        </h2>
    </x-slot>

    <div class="pt-6" id="import-progress">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="progress-bar-container" id="progress-bar-container">
                        <p class="mb-2">Importação de veículos em andamento...</p>
                        <div class="progress-bar" id="progress-bar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-right">
                    <x-primary-button class="" @click="isDrawerOpen = true" :disabled="$vehicles->count() === 0">
                        Filtrar
                    </x-primary-button>
                    <x-primary-button class="ml-auto" @click="$dispatch('open-modal', 'vehicles-import')">
                        Importar Veículos
                    </x-primary-button>
                </div>

                <div x-show="isDrawerOpen" class="fixed inset-0 z-50 flex" x-transition>
                    <div class="fixed inset-0 bg-black bg-opacity-50" @click="isDrawerOpen = false"></div>

                    @include('stock.filter')
                </div>

                <div x-show="isFiltering"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-32 w-32"></div>
                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100" id="vehicleList">
                    @include('stock.list')
                </div>
            </div>
        </div>

        <x-modal name="vehicles-import" focusable>
            <form method="post" action="{{ route('stock.import') }}" class="p-6" enctype="multipart/form-data"
                x-data="{ importMethod: 'api' }">
                @csrf

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Importar Veículos') }}
                </h2>

                <div class="mb-4">
                    <label for="source"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Fonte') }}</label>
                    <select id="source" name="source"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-gray-300">
                        @foreach ($sources as $source)
                            <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <span
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Método de Importação') }}</span>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="import_method" value="api" x-model="importMethod"
                                class="form-radio text-indigo-600 dark:text-indigo-400">
                            <span class="dark:text-white text-sm ml-2">{{ __('Importar via API') }}</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" name="import_method" value="file" x-model="importMethod"
                                class="form-radio text-indigo-600 dark:text-indigo-400">
                            <span class="dark:text-white text-sm ml-2">{{ __('Importar via arquivo') }}</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4" x-show="importMethod === 'file'" x-cloak>
                    <label for="file"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Arquivo') }}</label>
                    <input type="file" id="file" name="file"
                        class="mt-1 block w-full text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close-modal', 'vehicles-import')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ms-3">
                        {{ __('Importar') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectElements = document.querySelectorAll('.multiple-select');
            selectElements.forEach(select => {
                $(select).select2({
                    placeholder: $(select).attr('placeholder')
                });
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('filterHandler', (filters) => ({
                isFiltering: false,
                isDrawerOpen: false,
                isCustomDate: false,
                initFilters() {
                    this.$nextTick(() => {
                        this.initializeSlider('year_range', filters.year.min, filters.year.max, 'year_min', 'year_max', { step: 1 });
                        this.initializeSlider('price_range', filters.price.min, filters.price.max, 'price_min', 'price_max', { isCurrency: true, step: 500 });
                        this.initializeSlider('mileage_range', filters.mileage.min, filters.mileage.max, 'mileage_min', 'mileage_max');
                        this.initializeSlider('doors_range', filters.doors.min, filters.doors.max, 'doors_min', 'doors_max', { step: 1 });
                    });

                    function startProgressBar() {
                        $('#import-progress').show();
                    }

                    function stopProgressBar() {
                        $('#import-progress').hide();
                    }

                    Echo.channel('job-status')
                        .listen('JobStatusUpdated', (e) => {
                            if (e.message.includes("Initializing vehicles import")) {
                                startProgressBar();
                            } else if (e.message.includes("Vehicles import finished") || e.message.includes("Error processing vehicle ID")) {
                                stopProgressBar();
                                toastr.success('Importação concluída com sucesso!');
                                setTimeout(() => window.location.reload(), 5000)
                            }
                        });
                },
                initializeSlider(id, min, max, minRef, maxRef, options = {}) {
                    function getValue(value) {
                        return options.isCurrency ? parseFloat(value) : parseInt(value)
                    }

                    const slider = document.getElementById(id);
                    noUiSlider.create(slider, {
                        start: [getValue(min), getValue(max)],
                        connect: true,
                        tooltips: true,
                        step: options.step ?? 500,
                        range: {
                            'min': getValue(min),
                            'max': getValue(max)
                        },
                        format: {
                            to: value => getValue(value),
                            from: value => getValue(value)
                        }
                    });
                    slider.noUiSlider.on('update', (values) => {
                        this.$refs[minRef].value = values[0];
                        this.$refs[maxRef].value = values[1];
                    });
                },
                async filterVehicles(page = 1) {
                    this.isDrawerOpen = false;
                    this.isFiltering = true;
                    const form = document.getElementById('filterForm');
                    const params = new URLSearchParams(new FormData(form)).toString();
                    const url = `{{ route('stock.index') }}?page=${page}&${params}`;
                    try {
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.text();
                        if (data.trim() === '') {
                            toastr.error('Nenhum resultado encontrado.');
                        } else {
                            document.querySelector('#vehicleList').innerHTML = data;
                        }

                        document.querySelectorAll('#vehicleList .pagination a').forEach(link => {
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                const page = new URL(link.href).searchParams.get('page');
                                this.filterVehicles(page);
                            });
                        });
                    } catch (error) {
                        toastr.error('Não foi possível filtrar a lista de veículos. Tente novamente mais tarde.');
                    } finally {
                        this.isFiltering = false;
                    }
                },
                resetFilters() {
                    const form = document.getElementById('filterForm');
                    form.reset();

                    this.$refs.year_min.value = filters.year.min;
                    this.$refs.year_max.value = filters.year.max;
                    const yearSlider = document.getElementById('year_range').noUiSlider;
                    yearSlider.set([filters.year.min, filters.year.max]);

                    this.$refs.price_min.value = filters.price.min;
                    this.$refs.price_max.value = filters.price.max;
                    const priceSlider = document.getElementById('price_range').noUiSlider;
                    priceSlider.set([filters.price.min, filters.price.max]);

                    this.$refs.mileage_min.value = filters.mileage.min;
                    this.$refs.mileage_max.value = filters.mileage.max;
                    const mileageSlider = document.getElementById('mileage_range').noUiSlider;
                    mileageSlider.set([filters.mileage.min, filters.mileage.max]);

                    this.$refs.doors_min.value = filters.doors.min;
                    this.$refs.doors_max.value = filters.doors.max;
                    const doorsSlider = document.getElementById('doors_range').noUiSlider;
                    doorsSlider.set([filters.doors.min, filters.doors.max]);

                    $('.multiple-select').val(null).trigger('change');

                    this.filterVehicles();
                },
                toggleCustomDate(event) {
                    this.isCustomDate = event.target.value === 'custom';
                }
            }));
        });
    </script>
</x-app-layout>