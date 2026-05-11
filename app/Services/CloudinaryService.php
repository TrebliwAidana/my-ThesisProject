<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
                'url' => ['secure' => true],
            ])
        );
    }

    public function upload($file, string $folder = 'vsulhs-sslg/documents'): array
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => $folder,
                'resource_type' => 'auto',
                'access_mode'   => 'public',
            ]
        );

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId): void
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => 'raw',
            ]);
        } catch (\Exception $e) {
            // Silently fail if file doesn't exist
        }
    }
}