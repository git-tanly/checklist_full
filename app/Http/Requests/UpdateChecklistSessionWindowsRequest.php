<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChecklistSessionWindowsRequest extends FormRequest
{
    /**
     * Master data hanya boleh dikelola Super Admin (route sudah dibatasi middleware role).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'restaurant_id' => ['nullable', 'integer', 'exists:restaurants,id'],
            'remove_override' => ['nullable', 'boolean'],
            'windows' => ['required', 'array', 'min:1'],
            'windows' => ['required', 'array', 'size:3'],
            'windows.*.session_type' => ['required', 'in:breakfast,lunch,dinner'],
            'windows.*.start_time' => ['required', 'date_format:H:i'],
            'windows.*.end_time' => ['required', 'date_format:H:i'],
            'windows.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validasi silang: jam tidak boleh sama, dan rentang sesi tidak boleh
     * tumpang tindih pada scope yang sama (mendukung window lintas tengah malam).
     *
     * @return array<string, mixed>
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $windows = collect($this->input('windows', []))
                ->filter(fn ($window) => is_array($window))
                ->values();

            $activeWindows = $windows
                ->filter(fn ($window) => (bool) ($window['is_active'] ?? false))
                ->filter(fn ($window) => ! empty($window['start_time']) && ! empty($window['end_time'])
                    && preg_match('/^\d{2}:\d{2}$/', (string) $window['start_time'])
                    && preg_match('/^\d{2}:\d{2}$/', (string) $window['end_time']))
                ->map(fn ($window) => [
                    'session_type' => $window['session_type'],
                    'start' => Carbon::createFromFormat('H:i', $window['start_time']),
                    'end' => Carbon::createFromFormat('H:i', $window['end_time']),
                ]);

            // Sesi aktif tidak boleh berdurasi nol.
            foreach ($activeWindows as $window) {
                if ($window['start']->equalTo($window['end'])) {
                    $validator->errors()->add(
                        'windows.'.$window['session_type'].'.end_time',
                        'Jam mulai dan jam selesai tidak boleh sama.'
                    );
                }
            }

            // Bandingkan setiap pasangan sesi aktif: rentang tidak boleh beririsan.
            // Interval dinormalisasi: window lintas tengah malam (end <= start)
            // dipecah menjadi dua interval [start, 24:00) dan [00:00, end).
            $intervals = [];

            foreach ($activeWindows as $window) {
                if ($window['end']->greaterThan($window['start'])) {
                    $intervals[] = ['session' => $window['session_type'], 'start' => $window['start'], 'end' => $window['end']];

                    continue;
                }

                $intervals[] = [
                    'session' => $window['session_type'],
                    'start' => $window['start'],
                    'end' => Carbon::createFromFormat('H:i', '23:59'),
                ];
                $intervals[] = [
                    'session' => $window['session_type'],
                    'start' => Carbon::createFromFormat('H:i', '00:00'),
                    'end' => $window['end'],
                ];
            }

            for ($i = 0; $i < count($intervals); $i++) {
                for ($j = $i + 1; $j < count($intervals); $j++) {
                    $first = $intervals[$i];
                    $second = $intervals[$j];

                    $overlaps = $first['start']->lessThan($second['end'])
                        && $second['start']->lessThan($first['end']);

                    if ($overlaps) {
                        $validator->errors()->add(
                            'windows',
                            'Jam sesi '.$first['session'].' beririsan dengan sesi '.$second['session'].'.'
                        );

                        return;
                    }
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'windows.required' => 'Data jam sesi wajib diisi.',
            'windows.size' => 'Data jam sesi tidak lengkap (harus ada 3 sesi).',
            'windows.*.session_type.in' => 'Sesi tidak valid.',
            'windows.*.start_time.date_format' => 'Format jam mulai harus HH:MM.',
            'windows.*.end_time.date_format' => 'Format jam selesai harus HH:MM.',
            'restaurant_id.exists' => 'Outlet tidak ditemukan.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data['restaurant_id'] = $data['restaurant_id'] ?? null;
        $data['remove_override'] = (bool) ($data['remove_override'] ?? false);

        $data['windows'] = collect($data['windows'] ?? [])
            ->map(fn (array $window) => [
                'session_type' => $window['session_type'],
                'start_time' => $window['start_time'].':00',
                'end_time' => $window['end_time'].':00',
                'is_active' => (bool) ($window['is_active'] ?? false),
            ])
            ->values()
            ->all();

        return $data;
    }
}
