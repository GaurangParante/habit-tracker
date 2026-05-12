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
                <h1 class="h4 mb-1">Manage Habits</h1>
                <p class="text-muted mb-0">Edit or delete habits from your list.</p>
            </div>
            <a class="btn btn-habit btn-sm" href="<?php echo e(route('habits.create')); ?>">
                <i class="fa-solid fa-plus me-1"></i>Add Habit
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="habit-card rounded-4 p-4">
        <?php if(session('status')): ?>
            <div class="alert alert-success"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <?php if($habits->isEmpty()): ?>
            <div class="text-center py-5">
                <p class="text-muted mb-3">No habits yet. Add one to get started.</p>
                <a class="btn btn-habit" href="<?php echo e(route('habits.create')); ?>">Create Your First Habit</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Habit</th>
                            <th>Frequency</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $habits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $habit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo e($habit->title); ?></div>
                                    <div class="text-muted small"><?php echo e($habit->description ?? 'No description'); ?></div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light text-uppercase"><?php echo e($habit->frequency_label ?? $habit->frequency); ?></span>
                                    <div class="text-muted small mt-1">Target: <?php echo e($habit->target_per_day); ?> / day</div>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('habits.edit', $habit)); ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('habits.destroy', $habit)); ?>" class="d-inline"
                                        onsubmit="return confirm('Delete this habit? This cannot be undone.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
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
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/habits/manage.blade.php ENDPATH**/ ?>