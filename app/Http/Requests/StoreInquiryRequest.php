<?php

namespace App\Http\Requests;

use App\Models\Inquiry;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->isSubmittedAfterMinimumTime()) {
                // 項目なし・改ざん・未来・速すぎる送信は、理由を悟らせないよう全て同じ文言にする
                $validator->errors()->add('form_started_at', __('inquiry.rejected'));
            }
        });
    }

    /**
     * フォーム表示時刻（暗号化済み）から、MIN_SUBMIT_SECONDS 以上たっているか。
     * 暗号化しているのは、bot が時刻を書き換えて待ち時間を偽装できないようにするため。
     */
    private function isSubmittedAfterMinimumTime(): bool
    {
        $encrypted = $this->input('form_started_at');
        if (! is_string($encrypted)) {
            return false;
        }

        try {
            $startedAt = Crypt::decryptString($encrypted);
        } catch (DecryptException) {
            return false;
        }

        if (! ctype_digit($startedAt)) {
            return false;
        }

        $elapsed = now()->getTimestamp() - (int) $startedAt;

        // 負数（未来の時刻）も、3秒未満として弾かれる
        return $elapsed >= Inquiry::MIN_SUBMIT_SECONDS;
    }
}
