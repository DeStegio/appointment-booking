<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('start_at');
        if (!$raw) return;

        $formats = ['Y-m-d H:i:s', 'Y-m-d\TH:i', Carbon::ISO8601, Carbon::RFC3339];
        $dt = null;

        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, $raw, config('app.timezone'));
                if ($d !== false) { $dt = $d; break; }
            } catch (\Throwable $e) { /* try next */ }
        }

        if (!$dt) {
            try { $dt = Carbon::parse($raw, config('app.timezone')); } catch (\Throwable $e) {}
        }

        if ($dt) {
            $dt->second(0);
            $this->merge(['start_at' => $dt->format('Y-m-d H:i:s')]);
        }
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['required','integer','exists:users,id'],
            'service_id'  => ['required','integer','exists:services,id'],
            'start_at'    => ['required','date_format:Y-m-d H:i:s','after:now'],
            'notes'       => ['nullable','string'],
        ];
    }
}

