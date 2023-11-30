<?php

namespace App\Controllers;

class ErrorController extends BaseController
{
    /**
     * Displays a general error page based on the status code.
     * These are intended for display within normal usage of
     * the application, even when logged in, so should NOT
     * default to the `production` error page.
     *
     * This is primarily used by the `deny()` method in the
     * Policy class.
     */
    public function general()
    {
        $status = session('status');

        $view = is_file(APPPATH . "Views/errors/html/error_{$status}.php")
            ? "errors/html/error_{$status}.php"
            : 'errors/html/error.php';

        return $this->render($view, [
            'status'  => session('status'),
            'message' => session('message') ?? session('error'),
        ]);
    }
}
