<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'media_type',
        'file_path',
        'alt_text',
        'caption_path',
        'transcript_text',
        'sign_language_video_url',
        'position',
    ];

    /**
     * Parent survey relation.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
