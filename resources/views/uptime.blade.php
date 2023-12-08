@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <div class="absolute top-0 right-0 z-0 w-32 h-32 rounded-tr-lg pattern-wavy pattern-blue-500 pattern-bg-transparent pattern-size-16 pattern-opacity-2"></div>
    <div class="relative flex items-center justify-between space-x-2">
        <div class="absolute top-0 flex justify-center w-full py-0.5">
            <div class="px-2 text-sm font-bold text-gray-900 rounded-full {{$this->getStatusColor()}}">{{ $status }}</div>
        </div>

        <div class="flex items-center space-x-2">
            <x-ohdear-pulse::logo>
                <x-ohdear-pulse::logo-uptime  />
            </x-ohdear-pulse::logo>

            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Uptime</h2>
        </div>

        <div>
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/uptime/report" target="_blank" class="text-xs font-bold text-gray-700 uppercase transition-colors dark:text-gray-400 dark:hover:text-brand ">
                <x-ohdear-pulse::logo-ohdear class="inline-block w-10" />
            </a>
        </div>
    </div>

    <div class="relative grid h-full grid-cols-1 gap-4 pt-4 mt-4" id="performance">
        <div class="absolute top-0 left-0 flex items-center space-x-1 text-xs">
            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
            <span>{{$this->getData()[0][1]}}ms</span>
        </div>
        <div
            wire:ignore
            class="h-full"
            x-data="{
                init() {
                    let chart = new Chart(
                        this.$refs.uptime,
                        {
                            type: 'line',
                            data: {
                                labels: @js($this->getLabels()),
                                datasets: [
                                    {
                                        label: 'Response time',
                                        borderColor: '#55B685',
                                        data: @js($this->getData()),
                                        borderCapStyle: 'round',
                                        pointHitRadius: 10,
                                        pointStyle: false,
                                        tension: 0.2,
                                        borderWidth: 1,
                                        fill: true,
                                        backgroundColor: function(context) {
                                            const chart = context.chart;
                                            const {ctx, chartArea} = chart;

                                            if (!chartArea) {
                                                // This case happens on initial chart load
                                                return;
                                            }
                                            return getGradient(ctx, chartArea);
                                        },
                                    },
                                ],
                            },
                            options: {
                                maintainAspectRatio: false,
                                layout: {
                                    autoPadding: false,
                                    padding: {
                                        top: 1,
                                    },
                                },

                                scales: {
                                    x: {
                                        display: false,
                                    },
                                    y: {
                                        display: false,
                                        min: 0,
                                        max: 100,
                                    },
                                },
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        position: 'nearest',
                                        intersect: false,
                                        callbacks: {
                                            beforeBody: (context) => context
                                                .map(item => `${item.dataset.label}: ${item.formattedValue}ms`)
                                                .join(', '),
                                            label: () => null,
                                        },
                                    },
                                },
                            },
                        }
                    )

                    function getGradient(ctx, chartArea) {
                        let width, height, gradient;
                        const chartWidth = chartArea.right - chartArea.left;
                        const chartHeight = chartArea.bottom - chartArea.top;
                        if (!gradient || width !== chartWidth || height !== chartHeight) {
                            // Create the gradient because this is either the first
                            // render or the size of the chart has changed
                            width = chartWidth;
                            height = chartHeight;
                            gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(1, '#00FF2920');
                            gradient.addColorStop(0, '#D6FFEA00');
                        }
                        return gradient;
                    }

                    Livewire.on('uptime-chart-update', ({ queues }) => {
                        if (chart === undefined) {
                            return
                        }

                        chart.update()
                    })
                }
            }"
        >
            <canvas x-ref="uptime" class=""></canvas>
        </div>
        <div class="flex justify-between text-xs dark:text-gray-500">
            <span>Now</span>

            <span>{{\Carbon\Carbon::createFromTimestamp($this->getData()[count($this->getData()) -1][0]/1000)->diffInMinutes(now(), true)}} min ago</span>
        </div>
    </div>

    </div>
</x-pulse::card>
