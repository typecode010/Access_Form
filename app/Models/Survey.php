<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Survey extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Survey $survey): void {
            if (! $survey->accessibilitySettings()->exists()) {
                $survey->accessibilitySettings()->create(SurveyAccessibilitySetting::defaults());
            }
        });
    }

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'status',
        'public_slug',
    ];

    /**
     * Survey creator relationship.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Survey questions ordered by position.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('position');
    }

    /**
     * Survey accessibility settings.
     */
    public function accessibilitySettings(): HasOne
    {
        return $this->hasOne(SurveyAccessibilitySetting::class);
    }

    /**
     * Survey responses.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Survey media assets.
     */
    public function media(): HasMany
    {
        return $this->hasMany(SurveyMedia::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Accessibility issues for the survey.
     */
    public function accessibilityIssues(): HasMany
    {
        return $this->hasMany(AccessibilityIssue::class);
    }

    /**
     * Exports generated for the survey.
     */
    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }
}
