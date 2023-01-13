<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Services\CloudinaryFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
//    private  $fileService
    public function __construct(private readonly CloudinaryFileService $fileService)
    {
    }

    /**
     * @throws ValidationException
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $this->validate($request, [
            'file' => $this->fileService->getFileValidationRules(),
        ]);

        /** @var User $user */
        $user = $request->user();
       $file =  $this->fileService->uploadFile($request->file('file'), $user);
        return $this->respondSuccess([
            'file' => $file
        ], 'File uploaded successfully');
    }

    /**
     * @throws ValidationException
     */
    public function uploadFiles(Request $request): JsonResponse
    {
        $this->validate($request, [
           'files' => ['required', 'array'],
           'files.*' => $this->fileService->getFileValidationRules(),
        ]);
        /** @var  User $user */
        $user = $request->user();
        $files = array_map(function (UploadedFile $uploadedFile) use ($user) {
            return $this->fileService->uploadFile($uploadedFile, $user);
        }, $request->file('files'));
        return $this->respondSuccess(['files' => $files], 'Files uploaded successfully');
    }

    public function deleteFile(Request $request, File $file): JsonResponse
    {
        /** @var $user User */
        $user = $request->user();
        if ($file->creator_id == $user->id){
            $this->fileService->deleteFile($file);
        }
        return $this->respondSuccess([], 'File deleted successfully');
    }
}
