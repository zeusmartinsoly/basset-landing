<?php

namespace App\Filament\Pages\Concerns;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatesSettingsData
{
    /**
     * Validate settings and report errors under the form's "data." state path so Filament shows them on the fields.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     *
     * @throws ValidationException
     */
    protected function validateSettings(array $data, array $rules, array $messages = [], array $attributes = []): void
    {
        try {
            Validator::validate($data, $rules, $messages, $attributes);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(
                collect($exception->errors())
                    ->mapWithKeys(fn (array $errors, string $key): array => ["data.{$key}" => $errors])
                    ->all(),
            );
        }
    }
}
