@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class . 'overflow-hidden'" style="min-height: 240px;">

    <div class="absolute bottom-[-1px] left-0 w-full h-full rounded-lg  bg-gradient-to-t from-gray-100 to-gray-100/0 dark:from-gray-800/30 dark:to-gray-800/0 bg-grid"></div>
    <div class="absolute top-0 right-0 z-0 w-32 h-32 rounded-tr-lg pattern-wavy pattern-blue-500 pattern-bg-transparent pattern-size-16 pattern-opacity-2"></div>

    <div class="relative flex items-center justify-between space-x-2">

        <div class="absolute top-0 flex justify-center w-full py-0.5">
            <div class="px-2 text-sm font-bold shadow-2xl rounded-full  {{$this->getStatusColor()}}">{{ $status }}</div>
        </div>

        <div class="flex items-center space-x-2 !ml-0 relative">
            <x-ohdear-pulse::logo>
                <x-ohdear-pulse::logo-uptime  />
            </x-ohdear-pulse::logo>

            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Uptime</h2>
        </div>

        <div class="relative">
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/uptime/report" target="_blank">
                <x-ohdear-pulse::logo-ohdear />
            </a>
        </div>
    </div>

    <div class="absolute top-0 left-0 grid w-full h-[calc(100%-4rem)] grid-cols-1 gap-4 mt-20 overflow-hidden" id="performance">
        <div class="w-[1px] h-[calc(100%-3rem)] absolute border top-1 left-[2.15rem] border-dashed border-emerald-400/20 dark:border-emerald-400/10"></div>
        <div class="absolute top-0 flex items-center px-6 space-x-1 text-xs left-2">
            <div class="relative flex items-center justify-center w-2 h-2 rounded-full bg-emerald-500 dark:border-t dark:bg-gradient-to-t dark:from-emerald-400 dark:border-emerald-200 dark:to-emerald-300">

            </div>
            <span>{{$this->performanceRecords[0][1]}}ms</span>
        </div>
        <div
            wire:ignore
            class="absolute top-0 w-full h-full left-9"
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
                                        data: @js($this->performanceRecords),
                                        borderCapStyle: 'round',
                                        pointHitRadius: 20,
                                        pointRadius: 0,
                                        tension: 0.2,
                                        borderWidth: 1,
                                        fill: true,
                                        hover: {
                                            mode: 'nearest'
                                        },
                                        pointHoverRadius: 3,
                                        pointHoverBackgroundColor: '#55B685',
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
                                interaction: {
                                    mode: 'index',
                                    intersect: true,
                                },
                                scales: {
                                    x: {
                                        display: false,
                                    },
                                    y: {
                                        display: false,
                                        min: 0,
                                        max: @js($this->maxPerformanceRecord),
                                    },
                                },
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        position: 'nearest',
                                        intersect: true,
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
        <div class="absolute flex justify-between w-full px-6 text-xs text-gray-400 bottom-8 dark:text-gray-500">
            <span>Now</span>

            <span>{{\Carbon\Carbon::createFromTimestamp($this->performanceRecords[count($this->performanceRecords) -1][0]/1000)->diffInMinutes(now(), true)}} min ago</span>
        </div>
    </div>
</x-pulse::card>
