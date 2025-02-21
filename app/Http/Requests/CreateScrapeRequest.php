<?php

namespace App\Http\Requests;

use App\ScrapeStrategies\ScrapeStrategy;
use Illuminate\Foundation\Http\FormRequest;

class CreateScrapeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'website_id' => ['required', 'integer', 'exists:websites,id'],
            'scrape_type_id' => ['required', 'integer', 'exists:scrape_types,id'],
            'url' => ['required', 'string', 'url'],
            'prompt' => ['present', 'string'],
            'strategy' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!ScrapeStrategy::tryFrom($value)) {
                        $fail("The {$attribute} must be a valid ScrapeStrategy.");
                    }
                },
            ],
        ];
    }
}
