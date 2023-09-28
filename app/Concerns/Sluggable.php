<?php

namespace App\Concerns;

trait Sluggable
{
    /**
     * Ensures the record has a unique slug if none is provided for it.
     */
    public function generateSlug(array $data): array
    {
        if (! isset($data['data']['slug']) || empty($data['data']['slug'])) {
            $data['data']['slug'] = $this->createUniqueSlug($data['data']['title']);
        }

        return $data;
    }

    /**
     * Creates a unique slug for the given title,
     * based off of url_title. Adds a number to the end
     * if the slug already exists in the database.
     */
    public function createUniqueSlug(string $title): string
    {
        $slug = url_title($title, '-', true);

        $count        = 1;
        $originalSlug = $slug;

        while ($this->allowCallbacks(false)->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
