<?php

namespace App\Database\Seeds;

use App\Entities\Forum;
use App\Models\Factories\ForumFactory;
use App\Models\Factories\PostFactory;
use App\Models\Factories\ThreadFactory;
use App\Models\Factories\UserFactory;
use CodeIgniter\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    private array $forums = [
        'Operations' => ['parent' => null, 'order' => 1, 'active' => 1, 'private' => 1, 'permissions' => ['admin.access', 'forums.moderate']],
            'Framework Planning' => ['parent' => 'Operations', 'order' => 1, 'active' => 1, 'private' => 1],
            'Website Planning' => ['parent' => 'Operations', 'order' => 2, 'active' => 1, 'private' => 1],
            'Social Stuff' => ['parent' => 'Operations', 'order' => 3, 'active' => 1, 'private' => 1],
        'General' => ['parent' => null, 'order' => 2, 'active' => 1, 'private' => 0],
            'News and Discussion' => ['parent' => 'General', 'order' => 1, 'active' => 1, 'private' => 0],
            'Events' => ['parent' => 'General', 'order' => 2, 'active' => 1, 'private' => 0],
            'Lounge' => ['parent' => 'General', 'order' => 3, 'active' => 1, 'private' => 0],
            'Regional User Groups' => ['parent' => 'General', 'order' => 4, 'active' => 1, 'private' => 0],
        'Using CodeIgniter' => ['parent' => null, 'order' => 3, 'active' => 1, 'private' => 0],
            'Installation and Setup' => ['parent' => 'Using CodeIgniter', 'order' => 1, 'active' => 1, 'private' => 0],
            'Model-View-Controller' => ['parent' => 'Using CodeIgniter', 'order' => 2, 'active' => 1, 'private' => 0],
            'Libraries & Helpers' => ['parent' => 'Using CodeIgniter', 'order' => 3, 'active' => 1, 'private' => 0],
            'Best Practices' => ['parent' => 'Using CodeIgniter', 'order' => 4, 'active' => 1, 'private' => 0],
            'Choosing CodeIgntier' => ['parent' => 'Using CodeIgniter', 'order' => 5, 'active' => 1, 'private' => 0],
            'General Help' => ['parent' => 'Using CodeIgniter', 'order' => 6, 'active' => 1, 'private' => 0],
        'CodeIgniter 4' => ['parent' => null, 'order' => 4, 'active' => 1, 'private' => 0],
            'CodeIgniter 4 Roadmap' => ['parent' => 'CodeIgniter 4', 'order' => 1, 'active' => 1, 'private' => 0],
            'CodeIgniter 4 Development' => ['parent' => 'CodeIgniter 4', 'order' => 2, 'active' => 1, 'private' => 0],
            'CodeIgniter 4 Feature Requests' => ['parent' => 'CodeIgniter 4', 'order' => 3, 'active' => 1, 'private' => 0],
            'CodeIgniter 4 Discussion' => ['parent' => 'CodeIgniter 4', 'order' => 4, 'active' => 1, 'private' => 0],
            'CodeIgniter 4 Addins' => ['parent' => 'CodeIgniter 4', 'order' => 5, 'active' => 1, 'private' => 0],
        'Development' => ['parent' => null, 'order' => 5, 'active' => 1, 'private' => 0],
            'CodeIgniter 3.x' => ['parent' => 'Development', 'order' => 1, 'active' => 1, 'private' => 0],
            'CodeIgniter 2.x' => ['parent' => 'Development', 'order' => 2, 'active' => 1, 'private' => 0],
            'Issues' => ['parent' => 'Development', 'order' => 3, 'active' => 1, 'private' => 0],
            'Netbeans Plugin' => ['parent' => 'Development', 'order' => 4, 'active' => 1, 'private' => 0],
        'External Resources' => ['parent' => null, 'order' => 6, 'active' => 1, 'private' => 0],
            'Spotlight' => ['parent' => 'External Resources', 'order' => 1, 'active' => 1, 'private' => 0],
            'Learn More' => ['parent' => 'External Resources', 'order' => 2, 'active' => 1, 'private' => 0],
            'Jobs' => ['parent' => 'External Resources', 'order' => 3, 'active' => 1, 'private' => 0],
            'Addins' => ['parent' => 'External Resources', 'order' => 4, 'active' => 1, 'private' => 0],
        'Archived Discussions' => ['parent' => null, 'order' => 7, 'active' => 1, 'private' => 0],
            'Archived General Discussion' => ['parent' => 'Archived Discussions', 'order' => 1, 'active' => 1, 'private' => 0],
            'Archived Libraries & Helpers' => ['parent' => 'Archived Discussions', 'order' => 2, 'active' => 1, 'private' => 0],
            'Archived Development & Programming' => ['parent' => 'Archived Discussions', 'order' => 3, 'active' => 1, 'private' => 0],
    ];

    public function run()
    {
        helper('test');
        $forumModel = model('ForumModel');

        $this->seedDemoUsers();

        foreach ($this->forums as $name => $info) {
            $parent = $info['parent']
                ? $forumModel->where('title', $info['parent'])->first()
                : null;

            $forum = $forumModel->where('title', $name)->first();

            if (! $forum) {
                fake(ForumFactory::class, [
                    'title' => $name,
                    'parent_id' => $parent !== null ? $parent->id : null,
                    'order' => $info['order'],
                    'active' => $info['active'],
                    'private' => $info['private'],
                    'permissions' => $info['permissions'] ?? null,
                ], true);

                $forum = $forumModel->where('title', $name)->first();
                db_connect()->table('forums')
                    ->where('id', $forum->id)
                    ->update([
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $this->createThreads($forum);
        }
    }

    private function seedDemoUsers()
    {
        // If the total users in system is less than 10, then seed some
        // demo users.
        if (model('UserModel')->countAllResults() < 10) {

            helper('setting');

            for ($i = 0; $i < 10; $i++) {
                $user = fake(UserFactory::class, null, true);
                db_connect()->table('auth_groups_users')
                    ->insert([
                        'user_id' => $user->id,
                        'group' => setting('AuthGroups.defaultGroup'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

            }
        }
    }

    private function createThreads(Forum $forum)
    {
        $numPerForum = 10;

        for ($i = 0; $i < $numPerForum; $i++) {
            $thread = fake(ThreadFactory::class, [
                'forum_id' => $forum->id,
            ], true);

            db_connect()->table('threads')
                    ->where('id', $thread->id)
                    ->update([
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            $numReplies = rand(0, 10);

            for ($j = 0; $j < $numReplies; $j++) {
                $post = fake(PostFactory::class, [
                    'forum_id' => $forum->id,
                    'thread_id' => $thread->id,
                ], true);

                db_connect()->table('posts')
                    ->where('id', $post->id)
                    ->update([
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }
    }
}
