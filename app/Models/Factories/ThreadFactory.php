<?php

namespace App\Models\Factories;

use App\Entities\Thread;
use App\Models\ThreadModel;
use Faker\Generator;

class ThreadFactory extends ThreadModel
{
    /**
     * Factory method to create a fake threads for testing.
     */
    public function fake(Generator &$faker): Thread
    {
        $title = $faker->sentence(3);

        return new Thread([
            'title'       => $title,
            'body'        => $faker->paragraphs(3, true),
            'author_id'   => fake(UserFactory::class)->id,
            'views'       => $faker->numberBetween(0, 1000),
            'closed'      => false,
            'sticky'      => false,
            'visible'     => true,
            'category_id' => fake(CategoryFactory::class)->id,
        ]);
    }
}
