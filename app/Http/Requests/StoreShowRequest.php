<?php

namespace App\Http\Requests;

use App\Models\Show;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShowRequest extends FormRequest
{
    private const TIME_FIELDS = [
        'load_in_at',
        'meal_at',
        'soundcheck_at',
        'doors_at',
        'show_at',
        'show_end_at',
        'load_out_at',
    ];

    public function authorize(): bool
    {
        return $this->user()?->can('manage shows') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        if (blank($this->input('travel_mode'))) {
            $normalized['travel_mode'] = 'van';
        }

        foreach (self::TIME_FIELDS as $field) {
            $value = $this->input($field);

            if (blank($value)) {
                $normalized[$field] = null;
                continue;
            }

            $normalized[$field] = $this->normalizeTimeValue($value);
        }

        $this->merge($normalized);
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'tour_id' => ['nullable', Rule::exists('tours', 'id')->where('owner_id', $userId)],
            'date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'travel_origin' => ['nullable', 'string', 'max:255'],
            'travel_mode' => ['required', Rule::in(array_keys(Show::TRAVEL_MODE_OPTIONS))],
            'flight_origin' => ['nullable', 'string', 'max:255'],
            'flight_destination' => ['nullable', 'string', 'max:255'],
            'flight_duration_estimate' => ['nullable', 'string', 'max:255'],
            'flight_notes' => ['nullable', 'string', 'max:5000'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Show::STATUS_OPTIONS))],
            'load_in_at' => ['nullable', 'date_format:H:i:s'],
            'meal_at' => ['nullable', 'date_format:H:i:s'],
            'soundcheck_at' => ['nullable', 'date_format:H:i:s'],
            'doors_at' => ['nullable', 'date_format:H:i:s'],
            'show_at' => ['nullable', 'date_format:H:i:s'],
            'show_end_at' => ['nullable', 'date_format:H:i:s'],
            'load_out_at' => ['nullable', 'date_format:H:i:s'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_role' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email:rfc', 'max:255'],
            'lighting_notes' => ['nullable', 'string', 'max:5000'],
            'lighting_validated' => ['nullable', 'boolean'],
            'sound_notes' => ['nullable', 'string', 'max:5000'],
            'sound_validated' => ['nullable', 'boolean'],
            'space_notes' => ['nullable', 'string', 'max:5000'],
            'space_validated' => ['nullable', 'boolean'],
            'general_notes' => ['nullable', 'string', 'max:5000'],
            'general_validated' => ['nullable', 'boolean'],
        ];
    }

    private function normalizeTimeValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        foreach (['H:i:s', 'H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('H:i:s');
            } catch (\Throwable) {
            }
        }

        return $value;
    }
}
