<?php

namespace App\UseCases\Inquiry;

use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class StoreAction
{
    /**
     * お問い合わせを保存し、管理者に通知する。
     *
     * @param  array{name: string, email: string, message: string}  $data
     */
    public function __invoke(array $data): Inquiry
    {
        $inquiry = Inquiry::create($data);

        $this->notifyAdmin($inquiry);

        return $inquiry;
    }

    /**
     * 通知メールが失敗しても、問い合わせは保存済みで管理画面から確認できる。
     * 送信者にエラーを見せて再送させると二重登録になるため、ここでは例外を握りつぶしてログに残す。
     */
    private function notifyAdmin(Inquiry $inquiry): void
    {
        $to = config('site.admin_email');

        if (! $to) {
            return;
        }

        try {
            Mail::to($to)->send(new InquiryReceived($inquiry));
        } catch (Throwable $e) {
            Log::error('お問い合わせの通知メールを送れませんでした', [
                'inquiry_id' => $inquiry->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
