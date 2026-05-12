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
        <div>
            <h1 class="h4 mb-1">Add Habit</h1>
            <p class="text-muted mb-0">Define a new habit and its tracking frequency.</p>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="habit-card rounded-4 p-4">
                <form method="POST" action="<?php echo e(route('habits.store')); ?>">
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
                        <label class="form-label" for="frequency_type">Frequency</label>
                        <select class="form-select" id="frequency_type" name="frequency_type" required>
                            <option value="">Select frequency</option>
                            <option value="daily" <?php if(old('frequency_type') === 'daily'): echo 'selected'; endif; ?>>Daily</option>
                            <option value="days_of_week" <?php if(old('frequency_type') === 'days_of_week'): echo 'selected'; endif; ?>>Specific days</option>
                            <option value="times_per_week" <?php if(old('frequency_type') === 'times_per_week'): echo 'selected'; endif; ?>>X times per week</option>
                            <option value="monthly" <?php if(old('frequency_type') === 'monthly'): echo 'selected'; endif; ?>>Monthly</option>
                        </select>
                        <?php $__errorArgs = ['frequency_type'];
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

                    <div class="mb-3" data-frequency="days_of_week">
                        <label class="form-label">Select days</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php $__currentLoopData = ['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="btn btn-outline-secondary btn-sm">
                                    <input class="form-check-input me-1" type="checkbox" name="frequency_days[]"
                                        value="<?php echo e($value); ?>" <?php if(in_array($value, old('frequency_days', []), true)): echo 'checked'; endif; ?>>
                                    <?php echo e($label); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['frequency_days'];
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

                    <div class="mb-3" data-frequency="times_per_week">
                        <label class="form-label" for="frequency_times">Times per week</label>
                        <input class="form-control" type="number" min="1" max="7" id="frequency_times" name="frequency_times"
                            value="<?php echo e(old('frequency_times', 3)); ?>">
                        <?php $__errorArgs = ['frequency_times'];
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

                    <div class="mb-3" data-frequency="monthly">
                        <label class="form-label" for="monthly_day">Day of month</label>
                        <input class="form-control" type="number" min="1" max="28" id="monthly_day" name="monthly_day"
                            value="<?php echo e(old('monthly_day', 1)); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="target_per_day">Target per day</label>
                        <input class="form-control" type="number" min="1" max="50" id="target_per_day" name="target_per_day"
                            value="<?php echo e(old('target_per_day', 1)); ?>" required>
                        <?php $__errorArgs = ['target_per_day'];
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
                    <div class="d-flex gap-2">
                        <button class="btn btn-habit" type="submit">Save Habit</button>
                        <a class="btn btn-outline-secondary" href="<?php echo e(route('habits.manage')); ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const frequencySelect = document.getElementById('frequency_type');
            const sections = document.querySelectorAll('[data-frequency]');

            function syncFrequencySections() {
                const value = frequencySelect ? frequencySelect.value : '';
                sections.forEach((section) => {
                    section.style.display = section.dataset.frequency === value ? 'block' : 'none';
                });
            }

            if (frequencySelect) {
                frequencySelect.addEventListener('change', syncFrequencySections);
                syncFrequencySections();
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
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/habits/create.blade.php ENDPATH**/ ?>