toshLabのお問い合わせフォームから連絡が届きました。

お名前：{{ $inquiry->name }}
メール：{{ $inquiry->email }}
受信日時：{{ $inquiry->created_at->format('Y-m-d H:i') }}

ご相談内容：
{{ $inquiry->message }}

----
このメールに返信すると、相手のメールアドレスあてに送られます。
管理画面：{{ url('/admin/inquiries') }}
