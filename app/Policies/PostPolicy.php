<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * ブログ記事の権限。管理画面（Filament）は、編集ページを開くとき・ボタンを出すとき・処理を実行するときの
 * すべてでこのポリシーを見るので、URL を直接開いたり処理を直接呼んだりしても、ここで止まる。
 *
 * 管理画面のユーザーは管理者だけ（公開の会員登録は無い）なので、ファイル由来かどうかだけで分けている。
 */
class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** ファイル由来の記事は、管理画面で直しても次の取り込みで上書きされるので編集させない */
    public function update(User $user, Post $post): Response
    {
        return $post->isFromFile()
            ? Response::deny(__('post_import.policy.update_denied'))
            : Response::allow();
    }

    /**
     * 管理画面で作った記事は削除できる。
     * ファイル由来の記事は、元のファイルが無くなって下書きに戻ったものだけ削除できる（Post::isRemovedFileDraft）。
     */
    public function delete(User $user, Post $post): Response
    {
        if (! $post->isFromFile() || $post->isRemovedFileDraft()) {
            return Response::allow();
        }

        return Response::deny(__('post_import.policy.delete_denied'));
    }

    /** まとめての削除は使わない（1件ずつ、上の delete の条件で消す） */
    public function deleteAny(User $user): bool
    {
        return false;
    }
}
