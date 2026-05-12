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
                <h1 class="h4 mb-1">Today's Habits</h1>
                <p class="text-muted mb-0">Stay focused for <?php echo e($today->format('F j, Y')); ?></p>
            </div>
            <?php if(session('status')): ?>
                <span class="badge text-bg-success"><?php echo e(session('status')); ?></span>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="habit-card rounded-4 p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Today's Habits</h2>
                <p class="text-muted small mb-0">Toggle to mark completion for today.</p>
            </div>
            <a class="btn btn-habit btn-sm" href="<?php echo e(route('habits.create')); ?>">
                <i class="fa-solid fa-plus me-1"></i>Add Habit
            </a>
        </div>

        <?php if($habits->isEmpty()): ?>
            <div class="text-center py-5">
                <p class="text-muted mb-3">No habits yet. Add one to start tracking today.</p>
                <a class="btn btn-habit" href="<?php echo e(route('habits.create')); ?>">Create Your First Habit</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Habit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Streak</th>
                            <th class="text-center">Score</th>
                            <th class="text-center">Progress</th>
                            <th class="text-end">Toggle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $habits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $habit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo e($habit->title); ?></div>
                                    <div class="text-muted small"><?php echo e($habit->description ?? 'No description'); ?></div>
                                    <span class="badge text-bg-light text-uppercase mt-2"><?php echo e($habit->frequency_label ?? $habit->frequency); ?></span>
                                    <div class="text-muted small mt-2">Target: <?php echo e($habit->target_per_day); ?> / day</div>
                                </td>
                                <td class="text-center">
                                    <span class="habit-status-label small">Pending</span>
                                    <div class="text-muted small mt-1">Missed <?php echo e($habit->missed_this_week); ?>x this week</div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-semibold"><?php echo e($habit->current_streak); ?></div>
                                    <div class="text-muted small">Current</div>
                                    <div class="text-muted small">Best <?php echo e($habit->longest_streak); ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-semibold"><?php echo e($habit->score); ?>%</div>
                                    <div class="text-muted small"><?php echo e($habit->score_label); ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <button class="btn btn-outline-secondary btn-sm habit-count-btn" type="button"
                                            data-action="decrease" data-habit-id="<?php echo e($habit->id); ?>">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <span class="fw-semibold habit-count" data-habit-id="<?php echo e($habit->id); ?>">
                                            <?php echo e($habit->today_count); ?>

                                        </span>
                                        <span class="text-muted small">/ <?php echo e($habit->target_per_day); ?></span>
                                        <button class="btn btn-outline-secondary btn-sm habit-count-btn" type="button"
                                            data-action="increase" data-habit-id="<?php echo e($habit->id); ?>"
                                            data-target="<?php echo e($habit->target_per_day); ?>">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="habit-toggle-wrap d-inline-block">
                                        <input class="habit-toggle-input habit-toggle" type="checkbox"
                                            id="toggle-<?php echo e($habit->id); ?>"
                                            data-habit-id="<?php echo e($habit->id); ?>"
                                            <?php if((bool) $habit->today_status): echo 'checked'; endif; ?>>
                                        <label class="habit-toggle-control" for="toggle-<?php echo e($habit->id); ?>"
                                            aria-label="Toggle habit completion">
                                            <span class="toggle-handle"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Achievements</h2>
                        <p class="text-muted small mb-0">Badges unlocked as you build momentum.</p>
                    </div>
                </div>
                <?php if($achievements->isEmpty()): ?>
                    <p class="text-muted mb-0">Complete habits to unlock your first badge.</p>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-3">
                        <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="border rounded-4 px-3 py-2">
                                <div class="fw-semibold"><?php echo e($achievement->name); ?></div>
                                <div class="text-muted small"><?php echo e($achievement->description); ?></div>
                                <div class="text-muted small">Unlocked <?php echo e(optional($achievement->pivot->unlocked_at)->format('M j, Y')); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-12 col-xl-7">
            <div class="habit-card rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Today&apos;s Todos</h2>
                        <p class="text-muted small mb-0">Tasks due today.</p>
                    </div>
                    <a class="btn btn-outline-secondary btn-sm" href="<?php echo e(route('todos.index')); ?>">
                        <i class="fa-solid fa-list-check me-1"></i>All Todos
                    </a>
                </div>

                <form id="quickTodoForm" class="d-flex flex-column flex-md-row gap-2 mb-3">
                    <input class="form-control" type="text" name="title" placeholder="Quick add a todo" required>
                    <select class="form-select" name="priority">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <button class="btn btn-habit" type="submit">Add</button>
                </form>

                <div id="todayTodosList" class="d-flex flex-column gap-2">
                    <?php $__empty_1 = true; $__currentLoopData = $todayTodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('todos.partials.card', ['todo' => $todo, 'today' => $today], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted mb-0" data-empty>No tasks due today.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="habit-card rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-1">Overdue</h2>
                        <p class="text-muted small mb-0">Pending tasks past due date.</p>
                    </div>
                </div>
                <div id="overdueTodosList" class="d-flex flex-column gap-2">
                    <?php $__empty_1 = true; $__currentLoopData = $overdueTodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('todos.partials.card', ['todo' => $todo, 'today' => $today], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted mb-0" data-empty>Nothing overdue.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const todayDate = "<?php echo e($today->toDateString()); ?>";

            function updateStatusLabel(toggle) {
                const row = toggle.closest('tr');
                const label = row ? row.querySelector('.habit-status-label') : null;
                if (!label) {
                    return;
                }
                if (toggle.checked) {
                    label.textContent = 'Completed';
                    label.classList.add('is-complete');
                } else {
                    label.textContent = 'Pending';
                    label.classList.remove('is-complete');
                }
            }

            document.querySelectorAll('.habit-toggle').forEach((toggle) => {
                updateStatusLabel(toggle);
                toggle.addEventListener('change', async (event) => {
                    const habitId = event.target.dataset.habitId;
                    const status = event.target.checked ? 1 : 0;

                    const response = await fetch("<?php echo e(route('habits.toggle')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ habit_id: habitId, date: todayDate, status }),
                    });

                    if (!response.ok) {
                        event.target.checked = !event.target.checked;
                        updateStatusLabel(event.target);
                        alert('Unable to update habit. Please try again.');
                        return;
                    }

                    const data = await response.json();
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    if (countDisplay && typeof data.count !== 'undefined') {
                        countDisplay.textContent = data.count;
                    }
                    updateStatusLabel(event.target);
                });
            });

            function syncPlusButtons() {
                document.querySelectorAll('.habit-count-btn[data-action="increase"]').forEach((btn) => {
                    const habitId = btn.dataset.habitId;
                    const target = parseInt(btn.dataset.target, 10) || 1;
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    const count = countDisplay ? (parseInt(countDisplay.textContent, 10) || 0) : 0;
                    btn.disabled = count >= target;
                });
            }

            syncPlusButtons();

            document.querySelectorAll('.habit-count-btn').forEach((btn) => {
                btn.addEventListener('click', async (event) => {
                    const habitId = event.currentTarget.dataset.habitId;
                    const action = event.currentTarget.dataset.action;
                    const countDisplay = document.querySelector(`.habit-count[data-habit-id="${habitId}"]`);
                    const toggle = document.getElementById(`toggle-${habitId}`);
                    if (!countDisplay || !toggle) return;

                    let count = parseInt(countDisplay.textContent, 10) || 0;
                    if (action === 'increase') {
                        const target = parseInt(event.currentTarget.dataset.target, 10) || 1;
                        if (count >= target) {
                            syncPlusButtons();
                            return;
                        }
                        count = count + 1;
                    } else {
                        count = Math.max(0, count - 1);
                    }

                    const response = await fetch("<?php echo e(route('habits.toggle')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ habit_id: habitId, date: todayDate, status: count > 0 ? 1 : 0, count }),
                    });

                    if (!response.ok) {
                        alert('Unable to update habit. Please try again.');
                        return;
                    }

                    const data = await response.json();
                    countDisplay.textContent = data.count ?? count;
                    toggle.checked = data.status ?? count > 0;
                    updateStatusLabel(toggle);
                    syncPlusButtons();
                });
            });

            const quickTodoForm = document.getElementById('quickTodoForm');
            if (quickTodoForm) {
                quickTodoForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const formData = new FormData(quickTodoForm);
                    const payload = {
                        title: formData.get('title'),
                        priority: formData.get('priority'),
                        due_date: "<?php echo e($today->toDateString()); ?>",
                    };

                    const response = await fetch("<?php echo e(route('todos.store')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        alert('Unable to add todo.');
                        return;
                    }

                    window.location.reload();
                });
            }

            function syncDashboardTodoEmptyStates() {
                const groups = [
                    { id: 'todayTodosList', emptyText: 'No tasks due today.' },
                    { id: 'overdueTodosList', emptyText: 'Nothing overdue.' },
                ];

                groups.forEach((group) => {
                    const wrap = document.getElementById(group.id);
                    if (!wrap) return;
                    const hasCards = !!wrap.querySelector('.todo-card');
                    const empty = wrap.querySelector('[data-empty]');
                    if (!hasCards && !empty) {
                        const message = document.createElement('p');
                        message.className = 'text-muted mb-0';
                        message.dataset.empty = 'true';
                        message.textContent = group.emptyText;
                        wrap.appendChild(message);
                    }
                    if (hasCards && empty) {
                        empty.remove();
                    }
                });
            }

            document.querySelectorAll('.todo-toggle').forEach((toggle) => {
                toggle.addEventListener('change', async (event) => {
                    const todoId = event.target.dataset.todoId;
                    const response = await fetch("<?php echo e(route('todos.toggle')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ todo_id: todoId }),
                    });

                    if (!response.ok) {
                        event.target.checked = !event.target.checked;
                        alert('Unable to update todo.');
                        return;
                    }

                    const data = await response.json();
                    const card = event.target.closest('.todo-card');
                    const todayWrap = document.getElementById('todayTodosList');
                    const overdueWrap = document.getElementById('overdueTodosList');
                    if (!card || !todayWrap || !overdueWrap) return;

                    card.dataset.status = data.status;
                    const dueDate = card.dataset.dueDate;
                    const isOverdue = dueDate && dueDate < todayDate;
                    const badge = card.querySelector('[data-overdue-badge]');
                    const badgeRow = card.querySelector('.todo-badges');

                    if (data.status === 'completed') {
                        if (badge) badge.remove();
                        card.classList.remove('border-danger');
                        card.remove();
                    } else {
                        if (isOverdue && !badge) {
                            const badgeEl = document.createElement('span');
                            badgeEl.className = 'badge text-bg-danger';
                            badgeEl.dataset.overdueBadge = 'true';
                            badgeEl.textContent = 'Overdue';
                            if (badgeRow) badgeRow.appendChild(badgeEl);
                        }
                        if (!isOverdue && badge) badge.remove();
                        card.classList.toggle('border-danger', !!isOverdue);

                        if (isOverdue) {
                            overdueWrap.prepend(card);
                        } else if (dueDate === todayDate) {
                            todayWrap.prepend(card);
                        }
                    }

                    syncDashboardTodoEmptyStates();
                });
            });
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
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/dashboard.blade.php ENDPATH**/ ?>