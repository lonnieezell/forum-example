<?php

namespace App\Models\Factories;

use App\Entities\User;
use App\Models\UserModel;
use Faker\Generator;

class UserFactory extends UserModel
{
    /**
     * Factory method to create a fake user for testing.
     */
    public function fake(Generator &$faker): User
    {
        return new User([
            'username' => $this->generateUniqueUsername($faker->userName),
            'name'     => $faker->name,
            'email'    => $faker->email,
            'password' => $faker->password,
            'active'   => true,
            'country'  => $faker->country,
            'timezone' => $faker->timezone,
        ]);
    }

    private function generateUniqueUsername(string $username): string
    {
        $username = url_title($username, '-', true);

        if ($this->where('username', $username)->first()) {
            $username .= '-' . random_string('alnum', 4);
        }

        return $username;
    }
}
