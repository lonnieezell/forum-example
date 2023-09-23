<?php

namespace App\Concerns;

use App\Models\ImageModel;
use ReflectionException;

trait HasImages
{
    /**
     * @throws ReflectionException
     */
    protected function updateThreadImages(array $eventData): array
    {
        if (! $eventData['result']
            || (is_array($eventData['id']) && count($eventData['id']) > 1)
        ) {
            return $eventData;
        }

        $threadId = is_array($eventData['id']) ? (int) $eventData['id'][0] : (int) $eventData['id'];

        $imageModel = model(ImageModel::class);
        $images     = $imageModel->findForCheck($eventData['data']['author_id'], $threadId);

        if (! $images) {
            return $eventData;
        }

        foreach ($images as $image) {
            $fileUrl = base_url(implode('/', ['uploads', $eventData['data']['author_id'], $image->name]));
            if (str_contains((string) $eventData['data']['body'], $fileUrl)) {
                $image->thread_id = $threadId;
                $image->is_used   = true;
            } else {
                $image->is_used = false;
            }
        }

        $imageModel->updateBatch($images, 'id');

        return $eventData;
    }

    /**
     * @throws ReflectionException
     */
    protected function updatePostImages(array $eventData): array
    {
        if (! $eventData['result']
            || (is_array($eventData['id']) && count($eventData['id']) > 1)
        ) {
            return $eventData;
        }

        $postId = is_array($eventData['id']) ? (int) $eventData['id'][0] : (int) $eventData['id'];

        $imageModel = model(ImageModel::class);
        $images     = $imageModel->findForCheck($eventData['data']['author_id'], $eventData['data']['thread_id'], $postId);

        if (! $images) {
            return $eventData;
        }

        foreach ($images as $image) {
            $fileUrl = base_url(implode('/', ['uploads', $eventData['data']['author_id'], $image->name]));
            if (str_contains((string) $eventData['data']['body'], $fileUrl)) {
                $image->thread_id = (int) $eventData['data']['thread_id'];
                $image->post_id   = $postId;
                $image->is_used   = true;
            } else {
                $image->is_used = false;
            }
        }

        $imageModel->updateBatch($images, 'id');

        return $eventData;
    }
}
