<?php

namespace App\Models\Factories;

use App\Entities\Forum;
use App\Models\ForumModel;
use Faker\Generator;

class ForumFactory extends ForumModel
{
    /**
     * Factory method to create a fake forums for testing.
     */
    public function fake(Generator &$faker): Forum
    {
        $title = $faker->sentence(3);

        return new Forum([
            'title' => $title,
            'description' => $faker->paragraph(3),
            'parent_id' => null,
            'order' => 0,
            'active' => 1,
            'private' => 0,
        ]);
    }
}
