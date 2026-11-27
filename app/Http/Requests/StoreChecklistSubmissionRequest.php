<?php

namespace App\Http\Requests;

use App\Models\Restaurant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChecklistSubmissionRequest extends FormRequest
{
    /**
     * Halaman checklist publik tidak memakai login.
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
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->where(
                    'restaurant_id',
                    $this->restaurantId()
                ),
            ],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['array'],
            'answers.*.question_id' => ['required', 'integer', 'distinct'],
            'answers.*.value' => ['nullable'],
            'answers.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Nama staff wajib dipilih.',
            'employee_id.exists' => 'Staff yang dipilih tidak terdaftar di outlet ini.',
            'answers.required' => 'Jawaban checklist wajib diisi.',
            'answers.min' => 'Jawaban checklist wajib diisi.',
            'answers.*.question_id.required' => 'Pertanyaan checklist tidak valid.',
            'answers.*.question_id.distinct' => 'Pertanyaan checklist tidak boleh duplikat.',
            'answers.*.remarks.max' => 'Remarks maksimal 500 karakter.',
        ];
    }

    private function restaurantId(): ?int
    {
        $restaurant = $this->route('restaurant');

        return $restaurant instanceof Restaurant ? $restaurant->id : null;
    }
}
