<?php

namespace App\Services;

use App\Exceptions\CompanyProfileExistsException;
use App\Models\Company;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use App\Services\CloudinaryFileService;
use App\Utils\DocumentType;
use Error;
use Illuminate\Support\Facades\DB;

class CompanyService
{

    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }

    /**
     * @throws CompanyProfileExistsException
     */
    public function createCompany(
        ?string $name,
        ?string $email,
        ?string $phone_number,
        int $user_id,
        string $rc_number = null,
        string $description = null,
        int    $cac_document_id = null,
        int    $goods_in_transit_insurance_id = null,
        int    $fidelity_insurance_id = null
    ): Company {

        try {
            DB::beginTransaction();
            /** @var  User $user */
            $user = User::query()->findOrFail($user_id);
            // if ($user->company()->exists()) {
            //     throw new CompanyProfileExistsException(CompanyProfileExistsException::MESSAGE, 403);
            // }
            /** @var  Company $company */
            $company = $user->company()->updateOrCreate(['user_id' => $user->id], [
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone_number,
                'rc_number' => $rc_number,
                'description' => $description
            ]);
            if ($cac_document_id) {
                $this->createCacDocument($company, $cac_document_id);
            }
            if ($goods_in_transit_insurance_id) {
                $this->createGoodsInTransitInsuranceDoc($company, $goods_in_transit_insurance_id);
            }
            if ($fidelity_insurance_id) {
                /** @var Document $document */
                $this->createFidelityInsurance($company, $fidelity_insurance_id);
            }
            DB::commit();
            return $company;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Error($th->getMessage());
        }
    }

    public function createFidelityInsurance(Company $company, $file_id): void
    {
        /** @var Document $document */
        $document = $company->fidelityInsurance()->updateOrCreate(
            [
                'user_id' => $company->user_id,
                'document_type' => DocumentType::FIDELITY_INSURANCE['key'],
                'document_name' => DocumentType::FIDELITY_INSURANCE['name'],
            ],
            [
                'file_id' => $file_id,
                'status' => Document::PENDING
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip(
            [$file_id],
            File::OWNER_TYPE_DOCUMENT,
            $document->id
        );
    }

    public function createGoodsInTransitInsuranceDoc(Company $company, $file_id): void
    {
        /** @var Document $document */
        $document = $company->goodsInTransitInsurance()->updateOrCreate(
            [
                'user_id' => $company->user_id,
                'document_type' => DocumentType::GOODS_IN_TRANSIT_INSURANCE['key'],
                'document_name' => DocumentType::GOODS_IN_TRANSIT_INSURANCE['name'],
            ],
            [

                'file_id' => $file_id,
                'status' => Document::PENDING
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip(
            [$file_id],
            File::OWNER_TYPE_DOCUMENT,
            $document->id
        );
    }

    public function createCacDocument(Company $company, $fileId): void
    {
        /** @var Document $document */
        $document = $company->cacDocument()->updateOrCreate(
            [
                'user_id' => $company->user_id,
                'document_type' => DocumentType::CAC_DOCUMENT['key'],
                'document_name' => DocumentType::CAC_DOCUMENT['name'],
            ],
            [

                'file_id' => $fileId,
                'status' => Document::PENDING
            ]
        );
        $this->cloudinaryFileService->takeOwnerShip(
            [$fileId],
            File::OWNER_TYPE_DOCUMENT,
            $document->id
        );
    }
}
