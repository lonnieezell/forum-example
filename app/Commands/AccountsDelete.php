<?php

namespace App\Commands;

use App\Entities\User;
use App\Models\UserDeleteModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AccountsDelete extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Forum';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'accounts:delete';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Permanently delete scheduled accounts';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'accounts:delete';

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $toDelete = model(UserDeleteModel::class)->expired()->findAll();

        if ($toDelete === []) {
            CLI::write('No accounts to delete.');

            return EXIT_SUCCESS;
        }

        $userModel = model(UserModel::class);
        $deleteIds = array_column($toDelete, 'user_id');
        $users     = $userModel
            ->allowCallbacks(false)
            ->onlyDeleted()
            ->whereIn('id', $deleteIds)
            ->findAll();

        foreach ($users as $user) {
            // prevent permanent delete of parent posts
            $userModel->builder('posts')
                ->where('author_id', $user->id)
                ->where('marked_as_deleted !=', null)
                ->set('author_id', null)
                ->set('body', null)
                ->update();

            // delete account (and all related data)
            if ($userModel->builder()->delete($user->id)) {
                // Mark unused images
                if ($this->markUnusedImages($user)) {
                    CLI::write('Marked images for removal');
                }

                CLI::write(sprintf('User %s has been permanently deleted', $user->id), 'green');
                $this->sendNotification($user);
            }
        }

        CLI::write('Accounts removal finished.', 'green');
        CLI::write('Deleted accounts: ' . count($users), 'light_yellow');

        return EXIT_SUCCESS;
    }

    /**
     * Send notification.
     */
    protected function sendNotification(User $user)
    {
        return service('queue')->push('emails', 'email-simple-message', [
            'to'      => $user->email,
            'subject' => 'Your account has been permanently deleted',
            'message' => view('_emails/account_deleted', [
                'user' => $user,
            ]),
        ]);
    }

    /**
     * Mark unused images.
     */
    protected function markUnusedImages(User $user): bool
    {
        $db = db_connect();

        // Mark images by threads
        $query = $db->table('threads')
            ->select('id')
            ->where('author_id', $user->id);

        $db->table('images')
            ->whereIn('thread_id', $query)
            ->update(['is_used' => 0]);

        $affected1 = $db->affectedRows();

        // Mark images by posts
        $query = $db->table('posts')
            ->select('id')
            ->where('deleted_at', $user->deleted_at);

        $db->table('images')
            ->whereIn('post_id', $query)
            ->update(['is_used' => 0]);

        $affected2 = $db->affectedRows();

        return $affected1 > 0 || $affected2 > 0;
    }
}
