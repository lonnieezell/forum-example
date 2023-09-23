<?php

namespace App\Controllers\Discussions;

use App\Controllers\BaseController;
use App\Models\ImageModel;
use ReflectionException;

/**
 * Class Image
 */
class ImageController extends BaseController
{
    /**
     * Upload image and return uploaded file URL.
     *
     * @throws ReflectionException
     */
    public function upload()
    {
        if (! $this->policy->can('images.upload')) {
            return $this->response->setStatusCode(401)->setJSON([
                'error' => 'You are not allowed to upload images.',
            ]);
        }

        if (! $this->validate([
            'image' => [
                'uploaded[image]', 'max_size[image,2048]',
                'mime_in[image,image/png,image/jpeg]', 'ext_in[image,png,jpg,jpeg]',
            ],
        ])) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => implode(PHP_EOL, $this->validator->getErrors()),
            ]);
        }

        $file = $this->request->getFile('image');

        // Check if image was successfully uploaded
        if (! $file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => $file->getErrorString() . '(' . $file->getError() . ')',
            ]);
        }

        $newName = $file->getRandomName();
        $newPath = implode(DIRECTORY_SEPARATOR, ['uploads', user_id()]);

        // Create directory if needed
        if (! is_dir($newPath) && ! mkdir($newPath, 0755, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Creating directory failed.',
            ]);
        }

        // Check if image was successfully moved
        if (! $file->move(FCPATH . $newPath, $newName)) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Check your permissions to the final destination folder.',
            ]);
        }

        // Save file name to the DB
        if (! model(ImageModel::class)->insert([
            'user_id'    => user_id(),
            'name'       => $newName,
            'size'       => $file->getSize(),
            'ip_address' => $this->request->getIPAddress(),
        ])) {
            unlink($newPath . DIRECTORY_SEPARATOR . $newName);

            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Saving file to the DB failed.',
            ]);
        }

        return $this->response->setJSON([
            'data' => ['filePath' => $newPath . DIRECTORY_SEPARATOR . $newName],
        ]);
    }
}
