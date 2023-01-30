<?php

namespace App\Services;

use App\Http\Resources\UploadToCloudResource;
use App\Models\File;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

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

    public function deleteFileById(int $fileId): void
    {
        $file = File::where('id', $fileId)->first();
        if ($file) {
            if ($file->meta_data) {
                $meta = json_decode($file->meta_data);
                $cloudinaryEngine = new CloudinaryEngine();
                $cloudinaryEngine->destroy($meta->publicId);
            }
            $file->delete();
        }
    }

    public function deleteMultipleFilesById(array $fileIds): void
    {
        $fileIds = array_filter($fileIds);
        // Log::info($fileIds);
        Log::info("multiple file". json_encode($fileIds));
        $files = File::whereIn('id', $fileIds)->get();
        foreach ($files as $key => $file) {
            if ($file->meta_data) {
                $meta = json_decode($file->meta_data);
                if ($meta->publicId) {
                    $cloudinaryEngine = new CloudinaryEngine();
                    $cloudinaryEngine->destroy($meta->publicId);
                }
                $file->forceDelete();
            }
        }
    }
}
