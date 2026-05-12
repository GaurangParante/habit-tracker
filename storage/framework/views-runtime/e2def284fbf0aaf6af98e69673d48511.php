<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">Statistics</h1>
                <p class="text-muted mb-0">Progress overview with daily, weekly, and monthly insights.</p>
            </div>
            <span class="badge text-bg-light text-uppercase">Updated <?php echo e($today->format('M j, Y')); ?></span>
        </div>
     <?php $__env->endSlot(); ?>

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
        <?php $__empty_1 = true; $__currentLoopData = $habitStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $habit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="habit-card rounded-4 p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h3 class="h6 mb-1"><?php echo e($habit['title']); ?></h3>
                            <p class="text-muted small mb-0"><?php echo e($habit['description'] ?? 'No description'); ?></p>
                        </div>
                        <span class="badge text-bg-light text-uppercase"><?php echo e($habit['frequency']); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="text-center">
                            <div class="chart-mini mx-auto">
                                <canvas id="habit-weekly-<?php echo e($habit['id']); ?>" width="120" height="120"></canvas>
                            </div>
                            <div class="text-muted small text-uppercase mt-2">Weekly</div>
                            <div class="fw-semibold"><?php echo e($habit['weeklyPercent']); ?>%</div>
                        </div>
                        <div class="text-center">
                            <div class="chart-mini mx-auto">
                                <canvas id="habit-monthly-<?php echo e($habit['id']); ?>" width="120" height="120"></canvas>
                            </div>
                            <div class="text-muted small text-uppercase mt-2">Monthly</div>
                            <div class="fw-semibold"><?php echo e($habit['monthlyPercent']); ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="habit-card rounded-4 p-4 text-center">
                    <p class="text-muted mb-0">No habits yet. Add one to see individual stats.</p>
                </div>
            </div>
        <?php endif; ?>
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
                        <?php if($bestHabit): ?>
                            <div class="fw-semibold"><?php echo e($bestHabit['habit']->title); ?></div>
                            <div class="text-muted small mb-2"><?php echo e($bestHabit['habit']->description ?? 'No description'); ?></div>
                            <div class="fw-semibold"><?php echo e($bestHabit['score']); ?>% <span class="text-muted small">(<?php echo e($bestHabit['label']); ?>)</span></div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Add some habits to see your top performer.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="habit-card rounded-4 p-4">
                        <h3 class="h6 text-muted text-uppercase">Needs Attention</h3>
                        <?php if($worstHabit): ?>
                            <div class="fw-semibold"><?php echo e($worstHabit['habit']->title); ?></div>
                            <div class="text-muted small mb-2"><?php echo e($worstHabit['habit']->description ?? 'No description'); ?></div>
                            <div class="fw-semibold"><?php echo e($worstHabit['score']); ?>% <span class="text-muted small">(<?php echo e($worstHabit['label']); ?>)</span></div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">You are off to a great start.</p>
                        <?php endif; ?>
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

    <?php $__env->startPush('styles'); ?>
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
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
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

            makeDonut('dailyChart', <?php echo e($dailyPercent); ?>, '#1f7a5b');
            makeDonut('weeklyChart', <?php echo e($weeklyPercent); ?>, '#2b9d7a');
            makeDonut('monthlyChart', <?php echo e($monthlyPercent); ?>, '#0f5a44');

            <?php $__currentLoopData = $habitStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $habit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                makeDonut('habit-weekly-<?php echo e($habit['id']); ?>', <?php echo e($habit['weeklyPercent']); ?>, '#1f7a5b');
                makeDonut('habit-monthly-<?php echo e($habit['id']); ?>', <?php echo e($habit['monthlyPercent']); ?>, '#2b9d7a');
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            const weeklyPattern = <?php echo json_encode($weeklyPattern ?? [], 15, 512) ?>;
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

            const heatmapData = <?php echo json_encode($heatmap ?? [], 15, 512) ?>;
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
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/statistics.blade.php ENDPATH**/ ?>