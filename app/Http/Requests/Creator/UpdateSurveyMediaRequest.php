<?php

namespace App\Http\Requests\Creator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSurveyMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mediaType = (string) $this->input('media_type');

        return [
            'media_type' => ['required', Rule::in(['image', 'video', 'audio', 'other'])],
            'media_file' => ['nullable', 'file', 'max:10240', $this->mediaMimeRule($mediaType)],
            'alt_text' => ['nullable', 'string', 'max:1000'],
            'caption_file' => ['nullable', 'file', 'max:5120', 'mimes:vtt,srt,txt'],
            'transcript_text' => ['nullable', 'string', 'max:20000'],
            'sign_language_video_url' => ['nullable', 'url', 'max:2000'],
            'position' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'alt_text' => $this->trimText($this->input('alt_text')),
            'transcript_text' => $this->trimText($this->input('transcript_text')),
            'sign_language_video_url' => $this->trimText($this->input('sign_language_video_url')),
        ]);
    }

    private function mediaMimeRule(string $mediaType): string
    {
        return match ($mediaType) {
            'image' => 'mimes:jpg,jpeg,png,gif,webp',
            'video' => 'mimes:mp4,webm,ogv,ogg',
            'audio' => 'mimes:mp3,wav,ogg,oga',
            default => 'mimes:pdf,txt,doc,docx',
        };
    }

    private function trimText(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
