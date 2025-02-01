<?php

namespace App\Http\Requests;

use App\ScrapeStrategies\ScrapeStrategyInterface;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScrapeRequest extends FormRequest
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
            'class' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!class_exists($value)) {
                        $fail("The {$attribute} must be a valid class.");
                    } elseif (!is_subclass_of($value, ScrapeStrategyInterface::class)) {
                        $fail("The {$attribute} must extend ScrapeStrategyInterface.");
                    }
                },
            ],
        ];
    }
}
