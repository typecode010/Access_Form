<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    use HasFactory;

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TEXT = 'text';
    public const TYPE_RATING = 'rating';
    public const TYPE_FILE = 'file';

    public const ALLOWED_TYPES = [
        self::TYPE_MULTIPLE_CHOICE,
        self::TYPE_TEXT,
        self::TYPE_RATING,
        self::TYPE_FILE,
    ];

    protected $fillable = [
        'survey_id',
        'type',
        'prompt',
        'help_text',
        'is_required',
        'position',
        'settings_json',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'settings_json' => 'array',
    ];

    /**
     * Parent survey relation.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Question options ordered by position.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    /**
     * Determine whether this question type requires options.
     */
    public function requiresOptions(): bool
    {
        return $this->type === self::TYPE_MULTIPLE_CHOICE;
    }
}
