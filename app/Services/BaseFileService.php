<?php

namespace App\Services;

use App\Http\Resources\UploadToCloudResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;

abstract class BaseFileService
{
    public const MAX_FILE_SIZE_KB = 10 * 1024; //10MB

    public function getFileValidationRules(): array
    {
        $mimetypesRule = 'mimetypes:' . implode(',', array_keys(File::MIMETYPES));
        // $mimetypesRule = null;
        $sizeRule = 'max:' . self::MAX_FILE_SIZE_KB;

        return array_filter(['required', 'file', $mimetypesRule, $sizeRule]);
    }

    public function uploadFile(UploadedFile $uploadedFile, User $user, Model $owner = null): File
    {
        $file = new File();
        $file->name = $uploadedFile->getClientOriginalName();
        $file->mimetype = $uploadedFile->getMimeType();
        $file->type = File::MIMETYPES[$file->mimetype];
        $file->provider = env('FILESYSTEM_DISK');
        $file->creator_id = $user->id;
        if ($owner) {
            $file->owner_type = $owner->getMorphClass();
            $file->owner_id = $owner->id;
        }
        $file->save();
        if ($file->type === File::TYPE_IMAGE) {
            $baseFolder = strtolower(Config::get('app.name')) . '/images/';
            [$file, $uploadedFile, $path] = $this->compressImage($file, $uploadedFile);
            $uploadDetail = $this->uploadToCloud($uploadedFile, $baseFolder);
            return $this->updateFileWithResponseData($file, $uploadDetail->url, $path, $uploadDetail->meta_data);
        } else {
            $folder = strtolower(env('APP_NAME')) . '/' . Str::plural($file->type);
            $uploadDetail = $this->uploadToCloud($uploadedFile, $folder);
            $this->updateFileWithResponseData($file, $uploadDetail->url, $folder, $uploadDetail->meta_data);
        }
        return $file->refresh();
    }

    protected function convertHeicToJpg(File $file, UploadedFile $uploadedFile): File
    {
        $path = $uploadedFile->getRealPath();

        $output = null;
        $status = null;
        $command = "heic-cli -i {$path} -o {$path}";
        exec($command, $output, $status);
        if ($status !== 0) {
            Log::error(
                new \Exception('HEIC to JPG conversion failed', $status),
                array('status' => $status, 'output' => $output),
            );
        }
        // Update file data
        $file->mimetype = $uploadedFile->getMimeType();
        return $file;
    }

    protected function updateFileWithResponseData(File $file, string $url, string $path, $meta_data = null): File
    {
        $file->path = $path;
        $file->url = $url;
        $file->meta_data = $meta_data;
        $file->save();
        return $file;
    }

    protected function compressImage(File $file, UploadedFile $uploadedFile): array
    {
        $baseFolder = strtolower(Config::get('app.name')) . '/images/';
        $path = null;
        try {
            $path = $baseFolder . Str::random(32);
            if ($ext = $uploadedFile->guessExtension()) {
                $path .= ".{$ext}";
                if (Str::startsWith($ext, 'hei')) {
                    $file = $this->convertHeicToJpg($file, $uploadedFile);
                }
            }
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($uploadedFile);
        } catch (\Exception $error) {
            Log::error($error);
        }
        return [
            $file,
            $uploadedFile,
            $path,
        ];
    }

    public function takeOwnerShip(array $fileIds, string $owner_type, int $owner_id): void
    {
        File::query()->whereIn('id', $fileIds)->update([
            'owner_type' => $owner_type,
            'owner_id' => $owner_id,
        ]);
    }


    public abstract function deleteFile(File $file): void;

    public abstract function uploadToCloud(UploadedFile $uploadedFile, $folder): UploadToCloudResource;
}


