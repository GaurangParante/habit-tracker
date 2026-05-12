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
                <h1 class="h4 mb-1">Todos</h1>
                <p class="text-muted mb-0">Stay on top of your tasks alongside your habits.</p>
            </div>
            <?php if(session('status')): ?>
                <span class="badge text-bg-success"><?php echo e(session('status')); ?></span>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="habit-card rounded-4 p-4">
                <h2 class="h5 mb-3">Add Todo</h2>
                <form method="POST" action="<?php echo e(route('todos.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" value="<?php echo e(old('title')); ?>" required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="priority">Priority</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="high" <?php if(old('priority') === 'high'): echo 'selected'; endif; ?>>High</option>
                            <option value="medium" <?php if(old('priority', 'medium') === 'medium'): echo 'selected'; endif; ?>>Medium</option>
                            <option value="low" <?php if(old('priority') === 'low'): echo 'selected'; endif; ?>>Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="due_date">Due date</label>
                        <input class="form-control" type="date" id="due_date" name="due_date" value="<?php echo e(old('due_date')); ?>">
                    </div>
                    <button class="btn btn-habit" type="submit">Add Todo</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Pending</h2>
                    <span class="text-muted small"><?php echo e($pendingTodos->count()); ?> tasks</span>
                </div>
                <div id="pendingTodos" class="d-flex flex-column gap-3">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingTodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('todos.partials.card', ['todo' => $todo, 'today' => $today], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted mb-0" data-empty>No pending tasks. Great job!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="habit-card rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Completed</h2>
                    <span class="text-muted small"><?php echo e($completedTodos->count()); ?> tasks</span>
                </div>
                <div id="completedTodos" class="d-flex flex-column gap-3">
                    <?php $__empty_1 = true; $__currentLoopData = $completedTodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $todo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('todos.partials.card', ['todo' => $todo, 'today' => $today], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted mb-0" data-empty>Completed tasks will show up here.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                    const pendingWrap = document.getElementById('pendingTodos');
                    const completedWrap = document.getElementById('completedTodos');
                    if (!card || !pendingWrap || !completedWrap) return;

                    card.classList.remove('opacity-50');
                    card.dataset.status = data.status;

                    const dueDate = card.dataset.dueDate;
                    const isOverdue = dueDate && dueDate < "<?php echo e($today->toDateString()); ?>";
                    const badge = card.querySelector('[data-overdue-badge]');
                    if (data.status === 'completed') {
                        if (badge) badge.remove();
                        card.classList.remove('border-danger');
                        completedWrap.prepend(card);
                    } else {
                        if (isOverdue && !badge) {
                            const badgeEl = document.createElement('span');
                            badgeEl.className = 'badge text-bg-danger';
                            badgeEl.dataset.overdueBadge = 'true';
                            badgeEl.textContent = 'Overdue';
                            const badgeRow = card.querySelector('.todo-badges');
                            if (badgeRow) badgeRow.appendChild(badgeEl);
                        }
                        if (!isOverdue && badge) badge.remove();
                        card.classList.toggle('border-danger', !!isOverdue);
                        pendingWrap.prepend(card);
                    }

                    [pendingWrap, completedWrap].forEach((wrap) => {
                        const hasCards = !!wrap.querySelector('.todo-card');
                        const empty = wrap.querySelector('[data-empty]');
                        if (!hasCards && !empty) {
                            const message = document.createElement('p');
                            message.className = 'text-muted mb-0';
                            message.dataset.empty = 'true';
                            message.textContent = wrap.id === 'pendingTodos'
                                ? 'No pending tasks. Great job!'
                                : 'Completed tasks will show up here.';
                            wrap.appendChild(message);
                        }
                        if (hasCards && empty) {
                            empty.remove();
                        }
                    });
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
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/todos/index.blade.php ENDPATH**/ ?>