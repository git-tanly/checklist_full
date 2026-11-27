<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklistQuestionRequest extends FormRequest
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
            'session_type' => ['required', 'in:breakfast,lunch,dinner'],
            'check_point' => ['required', 'string', 'max:2000'],
            'key_check_point' => ['nullable', 'string', 'max:2000'],
            'answer_type' => ['required', 'in:number,yes_no'],
            'std_min' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'std_max' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'temp_max' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'unit' => ['nullable', 'string', 'max:16'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validasi silang threshold agar konfigurasi tidak masuk akal ditolak.
     *
     * @return array<string, mixed>
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $answerType = $this->input('answer_type');
            $stdMin = $this->filled('std_min') ? (float) $this->input('std_min') : null;
            $stdMax = $this->filled('std_max') ? (float) $this->input('std_max') : null;
            $tempMax = $this->filled('temp_max') ? (float) $this->input('temp_max') : null;

            // Rentang tidak boleh terbalik.
            if ($stdMin !== null && $stdMax !== null && $stdMin > $stdMax) {
                $validator->errors()->add('std_min', 'Std Min tidak boleh lebih besar dari Std Max.');
            }

            if ($answerType === 'yes_no') {
                // Pertanyaan yes/no tidak memakai angka.
                foreach (['std_min' => $stdMin, 'std_max' => $stdMax, 'temp_max' => $tempMax] as $field => $value) {
                    if ($value !== null) {
                        $validator->errors()->add($field, 'Batas angka tidak digunakan untuk jawaban Yes/No.');
                    }
                }

                return;
            }

            // Pertanyaan angka wajib punya minimal satu batas penilaian.
            if ($stdMin === null && $stdMax === null && $tempMax === null) {
                $validator->errors()->add('temp_max', 'Isi minimal satu batas: Std Min, Std Max, atau Suhu Max.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'session_type.required' => 'Sesi wajib dipilih.',
            'session_type.in' => 'Sesi tidak valid.',
            'check_point.required' => 'Check Point wajib diisi.',
            'check_point.max' => 'Check Point maksimal 2000 karakter.',
            'answer_type.required' => 'Tipe jawaban wajib dipilih.',
            'answer_type.in' => 'Tipe jawaban tidak valid.',
            'std_min.numeric' => 'Std Min harus berupa angka.',
            'std_max.numeric' => 'Std Max harus berupa angka.',
            'temp_max.numeric' => 'Suhu Max harus berupa angka.',
            'unit.max' => 'Unit maksimal 16 karakter.',
        ];
    }

    /**
     * Normalisasi input sebelum digunakan controller/model.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data['key_check_point'] = $data['key_check_point'] ?? null;
        $data['unit'] = $data['unit'] ?? null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        // Yes/No tidak boleh menyimpan threshold.
        if ($data['answer_type'] === 'yes_no') {
            $data['std_min'] = null;
            $data['std_max'] = null;
            $data['temp_max'] = null;
        }

        return $data;
    }
}
