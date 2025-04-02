<?php

namespace App\Http\Requests;

use App\ScrapeTypes\ScrapeType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScrapeTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'prompt' => ['present', 'string'],
            'type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!ScrapeType::tryFrom($value)) {
                        $fail("The {$attribute} must be a valid ScrapeType.");
                    }
                },
            ],
        ];
    }
}
