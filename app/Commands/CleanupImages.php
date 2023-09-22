<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use Exception;

class CleanupImages extends BaseCommand
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
    protected $name = 'cleanup:images';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Delete images that are no longer used.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cleanup:images <hours>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'hours' => 'After how many hours image is treated as expired.'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     *
     * @throws Exception
     */
    public function run(array $params)
    {
        $hours = array_shift($params) ?? 3;

        $db    = db_connect();
        $query = $db->table('images')
            ->where('is_used', 0)
            ->where('created_at <', Time::now()->subHours($hours)->format('Y-m-d H:i:s'))
            ->get();

        $delete = [];

        // Get images that are no longer used
        while ($row = $query->getUnbufferedRow()) {
            $path = ['uploads', $row->user_id, $row->name];
            // Delete image from disc
            unlink(FCPATH . implode(DIRECTORY_SEPARATOR, $path));
            // Save ID for later
            $delete[] = $row->id;
        }

        // Delete images from DB
        if (! empty($delete)) {
            $db->table('images')->whereIn('id', $delete)->delete();
        }

        CLI::write('Image cleanup finished.', 'green');
        CLI::write('Deleted images: ' . count($delete), 'light_yellow');
    }
}
