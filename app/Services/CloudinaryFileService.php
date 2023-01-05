<?php

namespace App\Services;

use App\Http\Resources\UploadToCloudResource;
use App\Models\File;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\UploadedFile;

class CloudinaryFileService extends BaseFileService
{

    public function uploadToCloud(UploadedFile $uploadedFile, $folder): UploadToCloudResource
    {
        /** @var CloudinaryEngine $response */
        $response = cloudinary()->upload($uploadedFile->getRealPath(), ['folder' => $folder]);
        $uploadToCloudResource = new UploadToCloudResource();
        $uploadToCloudResource->url = $response->getSecurePath();
        $uploadToCloudResource->meta_data = [
            'publicId' => $response->getPublicId(),
            'signature' => $response->getSignature(),
            'size' => $response->getReadableSize(),
            'url' => $response->getPath(),
            'placeholder' => $response->getPlaceHolder()
        ];
        return $uploadToCloudResource;
    }

    public function deleteFile(File $file): void
    {
        if ($file->meta_data) {
            $meta = json_decode($file->meta_data);
            $cloudinaryEngine = new CloudinaryEngine();
            $cloudinaryEngine->destroy($meta->publicId);
        }
        $file->delete();
    }
}
