<?php

namespace App\Controllers;

class LegalController extends BaseController
{
    public function privacy()
    {
        return $this->render('privacy');
    }

    public function terms()
    {
        return $this->render('terms');
    }
}
