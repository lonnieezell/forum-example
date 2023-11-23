<?php

namespace App\Models\Factories;

use App\Entities\ModerationReport;
use App\Models\ModerationReportModel;
use Faker\Generator;

class ModerationReportFactory extends ModerationReportModel
{
    /**
     * Factory method to create a fake moderation report for testing.
     */
    public function fake(Generator &$faker): ModerationReport
    {
        $comment = $faker->sentence(3);

        return new ModerationReport([
            'resource_id'   => 0,
            'resource_type' => 'thread',
            'comment'       => $comment,
            'user_id'       => null,
        ]);
    }
}
