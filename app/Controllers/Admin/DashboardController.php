<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\ImageModel;
use App\Models\ModerationReportModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use App\Models\UserModel;

class DashboardController extends AdminController
{
    /**
     * Displays basic statistics about the forum.
     */
    public function index()
    {
        $threads = model(ThreadModel::class);
        $posts   = model(PostModel::class);
        $users   = model(UserModel::class);
        $reports = model(ModerationReportModel::class);
        $images  = model(ImageModel::class);

        return $this->render('admin/dashboard', [
            'threadCount'         => $threads->countAllResults(),
            'todaysThreadCount'   => $threads->where('created_at >=', date('Y-m-d'))->countAllResults(),
            'reportedThreadCount' => $reports->where('resource_type', 'thread')->countAllResults(),
            'postCount'           => $posts->countAllResults(),
            'todaysPostCount'     => $posts->where('created_at >=', date('Y-m-d'))->countAllResults(),
            'reportedPostCount'   => $reports->where('resource_type', 'post')->countAllResults(),
            'userCount'           => $users->countAllResults(),
            'activeUserCount'     => $users->where('last_active >=', date('Y-m-d H:i:s', strtotime('-30 days')))->countAllResults(),
            'todaysUserCount'     => $users->where('created_at >=', date('Y-m-d'))->countAllResults(),
            'imageCount'          => $images->countAllResults(),
            'imageSize'           => $images->totalStorageUsed(),
        ]);
    }
}
