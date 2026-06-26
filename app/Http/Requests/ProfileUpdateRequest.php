<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'photo' => ['nullable', 'image', 'max:2048'],
            'cropped_photo' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    // Remove metadata prefix (e.g. "data:image/jpeg;base64,")
                    $data = preg_replace('/^data:image\/\w+;base64,/', '', $value);
                    $sizeInBytes = (strlen($data) * 3) / 4;
                    if ($sizeInBytes > 2 * 1024 * 1024) {
                        $fail('Ukuran foto profil tidak boleh melebihi 2MB.');
                    }
                }
            ],
        ];
    }
}
