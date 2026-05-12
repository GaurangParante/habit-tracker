<?php
    $priorityClass = match ($todo->priority) {
        'high' => 'text-bg-danger',
        'medium' => 'text-bg-warning',
        default => 'text-bg-success',
    };
    $isOverdue = $todo->due_date && $todo->due_date->lt($today) && $todo->status === 'pending';
?>

<div class="todo-card border rounded-4 p-3 d-flex flex-column flex-md-row justify-content-between gap-3 <?php echo e($isOverdue ? 'border-danger' : ''); ?>"
    data-status="<?php echo e($todo->status); ?>" data-due-date="<?php echo e(optional($todo->due_date)->format('Y-m-d')); ?>">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2 todo-badges">
            <span class="badge <?php echo e($priorityClass); ?>"><?php echo e(ucfirst($todo->priority)); ?></span>
            <?php if($todo->due_date): ?>
                <span class="text-muted small">Due <?php echo e($todo->due_date->format('M j, Y')); ?></span>
            <?php endif; ?>
            <?php if($isOverdue): ?>
                <span class="badge text-bg-danger" data-overdue-badge="true">Overdue</span>
            <?php endif; ?>
        </div>
        <div class="fw-semibold"><?php echo e($todo->title); ?></div>
        <div class="text-muted small"><?php echo e($todo->description ?? 'No description'); ?></div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="form-check form-switch m-0">
            <input class="form-check-input todo-toggle" type="checkbox" role="switch"
                data-todo-id="<?php echo e($todo->id); ?>" <?php if($todo->status === 'completed'): echo 'checked'; endif; ?>>
        </div>
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal"
            data-bs-target="#editTodoModal-<?php echo e($todo->id); ?>">
            <i class="fa-solid fa-pen"></i>
        </button>
        <form method="POST" action="<?php echo e(route('todos.destroy', $todo)); ?>"
            onsubmit="return confirm('Delete this todo? This cannot be undone.');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="btn btn-outline-danger btn-sm" type="submit">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="modal fade" id="editTodoModal-<?php echo e($todo->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('todos.update', $todo)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Todo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="title-<?php echo e($todo->id); ?>">Title</label>
                        <input class="form-control" id="title-<?php echo e($todo->id); ?>" name="title" value="<?php echo e($todo->title); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description-<?php echo e($todo->id); ?>">Description</label>
                        <textarea class="form-control" id="description-<?php echo e($todo->id); ?>" name="description" rows="3"><?php echo e($todo->description); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="priority-<?php echo e($todo->id); ?>">Priority</label>
                        <select class="form-select" id="priority-<?php echo e($todo->id); ?>" name="priority">
                            <option value="high" <?php if($todo->priority === 'high'): echo 'selected'; endif; ?>>High</option>
                            <option value="medium" <?php if($todo->priority === 'medium'): echo 'selected'; endif; ?>>Medium</option>
                            <option value="low" <?php if($todo->priority === 'low'): echo 'selected'; endif; ?>>Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="due-<?php echo e($todo->id); ?>">Due date</label>
                        <input class="form-control" type="date" id="due-<?php echo e($todo->id); ?>" name="due_date"
                            value="<?php echo e(optional($todo->due_date)->format('Y-m-d')); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-habit" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\wamp64\www\habit_tracker\resources\views/todos/partials/card.blade.php ENDPATH**/ ?>