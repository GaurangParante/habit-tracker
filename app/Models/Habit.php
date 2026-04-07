<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',
        'frequency_type',
        'frequency_value',
        'target_per_day',
        'current_streak',
        'longest_streak',
    ];

    protected $casts = [
        'frequency_value' => 'array',
    ];

    public function getFrequencyLabelAttribute(): string
    {
        $type = $this->frequency_type ?? $this->frequency;
        $value = $this->frequency_value ?? [];

        return match ($type) {
            'days_of_week' => implode(', ', array_map(fn ($day) => ucfirst(substr($day, 0, 3)), $value)),
            'times_per_week' => sprintf('%sx / week', (int) ($value['times'] ?? 0)),
            'monthly' => 'Monthly',
            'weekly' => 'Weekly',
            default => 'Daily',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }
}
