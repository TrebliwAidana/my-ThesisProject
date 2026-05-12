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
        $mimeType    = $file->getMimeType();
        $isRawFile   = in_array($mimeType, [
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ]);

        // ✅ Use explicit 'raw' resource_type for ZIP/binary files
        // so Cloudinary serves them publicly without authentication
        $resourceType = $isRawFile ? 'raw' : 'auto';

        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => $folder,
                'resource_type' => $resourceType,
                'access_mode'   => 'public',
                'type'          => 'upload', // ✅ force upload type (not authenticated)
            ]
        );

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId, string $resourceType = 'raw'): void
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);
        } catch (\Exception $e) {
            // Silently fail if file does not exist
        }
    }
}