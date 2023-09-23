<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ImageUpload extends BaseConfig
{
    /**
     * Is image upload is enabled.
     */
    public bool $enabled = true;

    /**
     * Image max size.
     */
    public int $fileSize = 2048;

    /**
     * Image mime types.
     */
    public array $fileMime = ['image/png', 'image/jpeg'];

    /**
     * Image file extensions.
     */
    public array $fileExt = ['png', 'jpg', 'jpeg'];

    /**
     * Image upload URL.
     */
    public string $uploadUrl = '/images/upload';

    /**
     * Return mimes as string.
     */
    public function getMime(): string
    {
        return implode(',', $this->fileMime);
    }

    /**
     * Return extensions as string.
     */
    public function getExt(): string
    {
        return implode(',', $this->fileExt);
    }
}
