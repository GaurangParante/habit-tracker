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
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <div>
                                <div class="chart-wrap">
                                    <canvas id="dailyChart" width="180" height="180"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Today</p>
                            </div>
                            <div>
                                <div class="chart-mini mx-auto">
                                    <canvas id="previousDailyChart" width="120" height="120"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Previous day</p>
                                <p class="text-muted small mb-0">{{ $previousDayLabel }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="habit-card rounded-4 p-4 text-center">
                        <h3 class="h6 text-muted text-uppercase">Weekly</h3>
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <div>
                                <div class="chart-wrap">
                                    <canvas id="weeklyChart" width="180" height="180"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Last 7 days</p>
                            </div>
                            <div>
                                <div class="chart-mini mx-auto">
                                    <canvas id="previousWeeklyChart" width="120" height="120"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Previous week</p>
                                <p class="text-muted small mb-0">{{ $previousWeekRange }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="habit-card rounded-4 p-4 text-center">
                        <h3 class="h6 text-muted text-uppercase">Monthly</h3>
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <div>
                                <div class="chart-wrap">
                                    <canvas id="monthlyChart" width="180" height="180"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Current month</p>
                            </div>
                            <div>
                                <div class="chart-mini mx-auto">
                                    <canvas id="previousMonthlyChart" width="120" height="120"></canvas>
                                </div>
                                <p class="text-muted small mb-0">Previous month</p>
                                <p class="text-muted small mb-0">{{ $previousMonthLabel }}</p>
                            </div>
                        </div>
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

    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-7">
            <div class="habit-card rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Weekly Pattern</h2>
                        <p class="text-muted small mb-0">Which weekdays you complete habits most often.</p>
                    </div>
                    <span class="badge text-bg-light text-uppercase">Last 8 weeks</span>
                </div>
                <canvas id="weeklyPatternChart" height="140"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="row g-4 h-100">
                <div class="col-12">
                    <div class="habit-card rounded-4 p-4">
                        <h3 class="h6 text-muted text-uppercase">Best Habit</h3>
                        @if ($bestHabit)
                            <div class="fw-semibold">{{ $bestHabit['habit']->title }}</div>
                            <div class="text-muted small mb-2">{{ $bestHabit['habit']->description ?? 'No description' }}</div>
                            <div class="fw-semibold">{{ $bestHabit['score'] }}% <span class="text-muted small">({{ $bestHabit['label'] }})</span></div>
                        @else
                            <p class="text-muted small mb-0">Add some habits to see your top performer.</p>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    <div class="habit-card rounded-4 p-4">
                        <h3 class="h6 text-muted text-uppercase">Needs Attention</h3>
                        @if ($worstHabit)
                            <div class="fw-semibold">{{ $worstHabit['habit']->title }}</div>
                            <div class="text-muted small mb-2">{{ $worstHabit['habit']->description ?? 'No description' }}</div>
                            <div class="fw-semibold">{{ $worstHabit['score'] }}% <span class="text-muted small">({{ $worstHabit['label'] }})</span></div>
                        @else
                            <p class="text-muted small mb-0">You are off to a great start.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Completion Heatmap</h2>
                        <p class="text-muted small mb-0">Daily habit completions over the last 90 days.</p>
                    </div>
                    <span class="badge text-bg-light text-uppercase">Heatmap</span>
                </div>
                <div id="heatmapGrid" class="heatmap-grid"></div>
                <div class="d-flex align-items-center gap-2 mt-3 text-muted small">
                    <span>Less</span>
                    <span class="heatmap-swatch heatmap-0"></span>
                    <span class="heatmap-swatch heatmap-1"></span>
                    <span class="heatmap-swatch heatmap-2"></span>
                    <span class="heatmap-swatch heatmap-3"></span>
                    <span class="heatmap-swatch heatmap-4"></span>
                    <span>More</span>
                </div>
            </div>
        </div>
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

            .heatmap-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(14px, 1fr));
                gap: 6px;
            }

            .heatmap-cell {
                width: 14px;
                height: 14px;
                border-radius: 4px;
                background: #e5ece9;
            }

            .heatmap-0 { background: #e5ece9; }
            .heatmap-1 { background: #c5ded1; }
            .heatmap-2 { background: #9ccbb7; }
            .heatmap-3 { background: #65b097; }
            .heatmap-4 { background: #2f8c6a; }

            .heatmap-swatch {
                width: 14px;
                height: 14px;
                display: inline-block;
                border-radius: 4px;
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
            makeDonut('previousDailyChart', {{ $previousDailyPercent }}, '#88b6a4');
            makeDonut('previousWeeklyChart', {{ $previousWeeklyPercent }}, '#7cbca3');
            makeDonut('previousMonthlyChart', {{ $previousMonthlyPercent }}, '#6aa993');

            @foreach ($habitStats as $habit)
                makeDonut('habit-weekly-{{ $habit['id'] }}', {{ $habit['weeklyPercent'] }}, '#1f7a5b');
                makeDonut('habit-monthly-{{ $habit['id'] }}', {{ $habit['monthlyPercent'] }}, '#2b9d7a');
            @endforeach

            const weeklyPattern = @json($weeklyPattern ?? []);
            const weeklyLabels = weeklyPattern.map(item => item.day);
            const weeklyData = weeklyPattern.map(item => item.completion_rate);
            const weeklyCtx = document.getElementById('weeklyPatternChart');
            if (weeklyCtx) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyLabels,
                        datasets: [{
                            label: 'Completion rate',
                            data: weeklyData,
                            backgroundColor: '#2b9d7a',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                ticks: { callback: (value) => `${value}%` }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            const heatmapData = @json($heatmap ?? []);
            const heatmapGrid = document.getElementById('heatmapGrid');
            if (heatmapGrid && heatmapData.length) {
                const maxCount = Math.max(...heatmapData.map(item => item.count));
                heatmapData.forEach((entry) => {
                    const cell = document.createElement('div');
                    let level = 0;
                    if (maxCount > 0) {
                        const ratio = entry.count / maxCount;
                        level = ratio >= 0.75 ? 4 : ratio >= 0.5 ? 3 : ratio >= 0.25 ? 2 : ratio > 0 ? 1 : 0;
                    }
                    cell.className = `heatmap-cell heatmap-${level}`;
                    cell.title = `${entry.date}: ${entry.count} completed`;
                    heatmapGrid.appendChild(cell);
                });
            }
        </script>
    @endpush
</x-app-layout>
