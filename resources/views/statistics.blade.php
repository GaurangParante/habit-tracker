<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Statistics</h1>
                <p class="text-muted mb-0">Progress overview with daily, weekly, and monthly insights.</p>
            </div>
            <span class="badge text-bg-light text-uppercase">Updated {{ $today->format('M j, Y') }}</span>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="habit-card rounded-4 p-4 text-center">
                        <h3 class="h6 text-muted text-uppercase">Daily</h3>
                        <div class="chart-wrap">
                            <canvas id="dailyChart" width="180" height="180"></canvas>
                        </div>
                        <p class="text-muted small mb-0">Today completion</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="habit-card rounded-4 p-4 text-center">
                        <h3 class="h6 text-muted text-uppercase">Weekly</h3>
                        <div class="chart-wrap">
                            <canvas id="weeklyChart" width="180" height="180"></canvas>
                        </div>
                        <p class="text-muted small mb-0">Last 7 days</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="habit-card rounded-4 p-4 text-center">
                        <h3 class="h6 text-muted text-uppercase">Monthly</h3>
                        <div class="chart-wrap">
                            <canvas id="monthlyChart" width="180" height="180"></canvas>
                        </div>
                        <p class="text-muted small mb-0">Current month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Individual Habit Statistics</h2>
                <span class="text-muted small">Weekly and monthly completion rates.</span>
            </div>
        </div>
        @forelse ($habitStats as $habit)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="habit-card rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h3 class="h6 mb-1">{{ $habit['title'] }}</h3>
                            <p class="text-muted small mb-0">{{ $habit['description'] ?? 'No description' }}</p>
                        </div>
                        <span class="badge text-bg-light text-uppercase">{{ $habit['frequency'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="text-center">
                            <div class="chart-mini mx-auto">
                                <canvas id="habit-weekly-{{ $habit['id'] }}" width="120" height="120"></canvas>
                            </div>
                            <div class="text-muted small text-uppercase mt-2">Weekly</div>
                            <div class="fw-semibold">{{ $habit['weeklyPercent'] }}%</div>
                        </div>
                        <div class="text-center">
                            <div class="chart-mini mx-auto">
                                <canvas id="habit-monthly-{{ $habit['id'] }}" width="120" height="120"></canvas>
                            </div>
                            <div class="text-muted small text-uppercase mt-2">Monthly</div>
                            <div class="fw-semibold">{{ $habit['monthlyPercent'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="habit-card rounded-4 p-4 text-center">
                    <p class="text-muted mb-0">No habits yet. Add one to see individual stats.</p>
                </div>
            </div>
        @endforelse
    </div>

    @push('styles')
        <style>
            .chart-wrap {
                position: relative;
                width: 180px;
                height: 180px;
                margin: 0 auto 0.5rem;
            }

            .chart-mini {
                position: relative;
                width: 120px;
                height: 120px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const centerTextPlugin = {
                id: 'centerText',
                afterDraw(chart, args, pluginOptions) {
                    const { ctx } = chart;
                    const text = pluginOptions.text ?? '';
                    ctx.save();
                    ctx.font = '600 20px Sora';
                    ctx.fillStyle = '#53806c';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const { width, height } = chart;
                    ctx.fillText(text, width / 2, height / 2);
                    ctx.restore();
                }
            };

            function makeDonut(canvasId, percent, color) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Remaining'],
                        datasets: [{
                            data: [percent, Math.max(0, 100 - percent)],
                            backgroundColor: [color, '#e7edeb'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            centerText: { text: `${percent}%` }
                        }
                    },
                    plugins: [centerTextPlugin]
                });
            }

            makeDonut('dailyChart', {{ $dailyPercent }}, '#1f7a5b');
            makeDonut('weeklyChart', {{ $weeklyPercent }}, '#2b9d7a');
            makeDonut('monthlyChart', {{ $monthlyPercent }}, '#0f5a44');

            @foreach ($habitStats as $habit)
                makeDonut('habit-weekly-{{ $habit['id'] }}', {{ $habit['weeklyPercent'] }}, '#1f7a5b');
                makeDonut('habit-monthly-{{ $habit['id'] }}', {{ $habit['monthlyPercent'] }}, '#2b9d7a');
            @endforeach
        </script>
    @endpush
</x-app-layout>
