<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGuarantorRequest;
use App\Models\Document;
use App\Models\File;
use App\Models\Guarantor;
use App\Models\User;
use App\Services\CloudinaryFileService;
use App\Utils\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuarantorController extends Controller
{
    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }

    public function store(UpdateGuarantorRequest $request): JsonResponse
    {
        /** @var  User $user */
        $user = $request->user();
        if ($user->guarantors()->count() == User::MAXIMUM_NO_OF_GUARANTORS) {
            return $this->respondError('You are not allowed to create more than 2 guarantors');
        }
        try {
            $data = $request->validated();
            $id_card_id = $request->id_card_id;
            unset($data['id_card_id']);
            DB::beginTransaction();
            /** @var Guarantor $guarantor */
            $guarantor = $user->guarantors()->create($data);
            //claim file ownership;
            $this->cloudinaryFileService->takeOwnerShip([$id_card_id], File::OWNER_TYPE_GUARANTOR, $guarantor->id);
            //create document for verification
            $guarantor->idCard()->create([
                'user_id' => $guarantor->user_id,
                'file_id' => $id_card_id,
                'document_type' => DocumentType::GUARANTOR_ID_CARD['key'],
                'document_name' => DocumentType::GUARANTOR_ID_CARD['name'],
                'status' => 'submitted'
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return $this->respondError('An error occurred while creating your guarantor, please try again later', 500);
        }
        return $this->respondSuccess([], 'Guarantor create successfully');
    }

    public function update(UpdateGuarantorRequest $request, Guarantor $guarantor): JsonResponse
    {

        /** @var  User $user */
        $user = $request->user();
        if ($user->id != $guarantor->user_id) {
            return $this->respondError('You are not allowed to update a guarantor that does not belong to you');
        }
        try {
            $data = $request->validated();
            $id_card_id = $request->id_card_id;
            unset($data['id_card_id']);
            DB::beginTransaction();
            $guarantor->update($data);
            $guarantor = $guarantor->load('idCard');
            //claim file ownership;
            if ($guarantor->idCard && $guarantor->idCard->file_id && $guarantor->idCard->file_id != $id_card_id) {
                $file_id = (int)$guarantor->idCard->file_id;
                /** @var File $file */
                $file = File::query()->where('id',$file_id)->first();
                $this->cloudinaryFileService->deleteFile($file);
                $this->cloudinaryFileService->takeOwnerShip([$id_card_id], File::OWNER_TYPE_GUARANTOR, $guarantor->id);
            }
            //create document for verification
            $guarantor->idCard()->update([
                'file_id' => $id_card_id,
                'document_type' => DocumentType::GUARANTOR_ID_CARD['key'],
                'document_name' => DocumentType::GUARANTOR_ID_CARD['name'],
                'status' => Document::PENDING,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return $this->respondError('An error occurred while creating your guarantor, please try again later', 500);
        }
        return $this->respondSuccess([], 'Guarantor created successfully');
    }
}
