<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAccessibilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'high_contrast_enabled',
        'dyslexia_friendly_enabled',
        'keyboard_nav_enforced',
        'text_size',
        'reduced_motion',
    ];

    protected $casts = [
        'high_contrast_enabled' => 'boolean',
        'dyslexia_friendly_enabled' => 'boolean',
        'keyboard_nav_enforced' => 'boolean',
        'reduced_motion' => 'boolean',
    ];

    /**
     * Parent survey relation.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Default accessibility settings for new surveys.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'high_contrast_enabled' => false,
            'dyslexia_friendly_enabled' => false,
            'keyboard_nav_enforced' => true,
            'text_size' => 'md',
            'reduced_motion' => false,
        ];
    }
}
