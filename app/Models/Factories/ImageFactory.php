<?php

namespace App\Models\Factories;

use App\Entities\Image;
use App\Models\ImageModel;
use Faker\Generator;

class ImageFactory extends ImageModel
{
    /**
     * Factory method to create a fake posts for testing.
     */
    public function fake(Generator &$faker): Image
    {
        return new Image([
            'user_id'    => null,
            'thread_id'  => null,
            'post_id'    => null,
            'name'       => $faker->regexify('[0-9]{10}_[a-z0-9]{20}') . '.jpg',
            'size'       => random_int(100000, 2_000_000),
            'is_used'    => 0,
            'ip_address' => $faker->ipv4,
        ]);
    }
}
