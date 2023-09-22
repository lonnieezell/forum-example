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
            || ! isset($eventData['data']['author_id'])
        ) {
            return $eventData;
        }

        if (is_array($eventData['id'])) {
            $threadId = (int) $eventData['id'][0];
        } else {
            $threadId = (int) $eventData['id'];
        }

        $imageModel = model(ImageModel::class);

        $images = $imageModel->findForCheck($eventData['data']['author_id'], $threadId);

        if (! $images) {
            return $eventData;
        }

        foreach ($images as $image) {
            if (str_contains($eventData['data']['body'], $image->name)) {
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
            || ! isset($eventData['data']['author_id'])
        ) {
            return $eventData;
        }

        if (is_array($eventData['id'])) {
            $postId = (int) $eventData['id'][0];
        } else {
            $postId = (int) $eventData['id'];
        }

        $imageModel = model(ImageModel::class);

        $images = $imageModel->findForCheck($eventData['data']['author_id'], $eventData['data']['thread_id'], $postId);

        if (! $images) {
            return $eventData;
        }

        foreach ($images as $image) {
            if (str_contains($eventData['data']['body'], $image->name)) {
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
