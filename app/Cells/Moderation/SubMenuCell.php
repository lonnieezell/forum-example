<?php

namespace App\Cells\Moderation;

use App\Models\ModerationReportModel;
use CodeIgniter\View\Cells\Cell;

class SubMenuCell extends Cell
{
    public array $count = [];

    public function mount(int $userId)
    {
        $this->count = model(ModerationReportModel::class)->countByType($userId);
    }
}
