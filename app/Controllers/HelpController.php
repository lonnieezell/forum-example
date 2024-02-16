<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class HelpController extends BaseController
{
    /**
     * Show folders and files. Search option.
     */
    public function index()
    {
        $markdownPages = service('markdownpages', ROOTPATH . 'help');

        if ($this->request->is('post')) {
            $rules = [
                'search' => ['permit_empty', 'string', 'alpha_numeric_space', 'min_length[4]', 'max_length[32]'],
            ];

            if (! $this->validateData($this->request->getPost(), $rules)) {
                alerts()->set('error', $this->validator->getError('search'));

                if ($this->request->header('REFERER')?->getValueLine() !== url_to('pages')) {
                    return '';
                }

                return $this->render('help/_index', ['pages' => $markdownPages]);
            }

            $searchQuery = trim((string) $this->request->getPost('search'));

            if ($searchQuery === '') {
                if ($this->request->header('REFERER')?->getValueLine() !== url_to('pages')) {
                    return '';
                }

                return $this->render('help/_index', ['pages' => $markdownPages]);
            }

            return $this->render('help/_search_results', ['search' => $markdownPages->search($searchQuery)]);
        }

        return $this->render('help/index', ['pages' => $markdownPages]);
    }

    /**
     * Show page.
     */
    public function show(...$slug)
    {
        $slug = implode('/', $slug);

        $markdownPages = service('markdownpages', ROOTPATH . 'help');
        if (! $page = $markdownPages->file($slug)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->render('help/show', ['page' => $page]);
    }
}
