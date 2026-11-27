<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklistSectionRequest extends FormRequest
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
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'restaurant_id.required' => 'Outlet wajib dipilih.',
            'restaurant_id.exists' => 'Outlet tidak ditemukan.',
            'name.required' => 'Nama section wajib diisi.',
            'name.max' => 'Nama section maksimal 120 karakter.',
            'sort_order.min' => 'Urutan tidak boleh negatif.',
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

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
