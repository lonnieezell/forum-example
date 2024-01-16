<?php

namespace App\Models\Factories;

use App\Entities\Post;
use App\Models\PostModel;
use Faker\Generator;

class PostFactory extends PostModel
{
    /**
     * Factory method to create a fake posts for testing.
     */
    public function fake(Generator &$faker): Post
    {
        return new Post([
            'category_id'   => fake(CategoryFactory::class)->id,
            'thread_id'     => null,
            'reply_to'      => null,
            'author_id'     => model('UserModel')->orderBy('id', 'RANDOM')->first()->id,
            'editor_id'     => null,
            'edited_at'     => null,
            'edited_reason' => null,
            'body'          => $faker->paragraph(5, true),
            'ip_address'    => $faker->ipv4,
            'include_sig'   => false,
            'visible'       => true,
        ]);
    }
}
