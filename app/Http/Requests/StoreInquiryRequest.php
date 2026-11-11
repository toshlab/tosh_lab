<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            // スパム対策のおとり欄。人には見えないので、入力があればbotとみなす
            'website' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => __('inquiry.attributes.name'),
            'email' => __('inquiry.attributes.email'),
            'message' => __('inquiry.attributes.message'),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            // botに「おとり欄が原因」と気づかせないよう、あいまいな文言にする
            'website.prohibited' => __('inquiry.rejected'),
        ];
    }
}
