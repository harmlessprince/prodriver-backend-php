<?php

namespace App\Services;

use App\Exceptions\CompanyProfileExistsException;
use App\Models\Company;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use App\Services\CloudinaryFileService;
use App\Utils\DocumentType;

class CompanyService
{

    public function __construct(private readonly CloudinaryFileService $cloudinaryFileService)
    {
    }

    /**
     * @throws CompanyProfileExistsException
     */
    public function createCompany(string $name,
                                  string $email,
                                  string $phone_number,
                                  string $rc_number, int $user_id,
                                  string $description = null,
                                  int    $cac_document_id = null,
                                  int    $goods_in_transit_insurance_id = null,
                                  int    $fidelity_insurance_id = null
    ): Company
    {
        /** @var  User $user */
        $user = User::query()->findOrFail($user_id);
        if ($user->company()->exists()) {
            throw new CompanyProfileExistsException(CompanyProfileExistsException::MESSAGE);
        }
        /** @var  Company $company */
        $company = $user->company()->create([
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
        return $company;
    }

    public function createFidelityInsurance(Company $company, $file_id): void
    {
        /** @var Document $document */
        $document = $company->fidelityInsurance()->create([
            'user_id' => $company->user_id,
            'file_id' => $file_id,
            'document_type' => DocumentType::FIDELITY_INSURANCE['key'],
            'status' => 'submitted'
        ]);
        $this->cloudinaryFileService->takeOwnerShip(
            [$file_id],
            File::OWNER_TYPE_DOCUMENT, $document->id
        );
    }

    public function createGoodsInTransitInsuranceDoc(Company $company, $file_id): void
    {
        /** @var Document $document */
        $document = $company->goodsInTransitInsurance()->create([
            'user_id' => $company->user_id,
            'file_id' => $file_id,
            'document_type' => DocumentType::GOODS_IN_TRANSIT_INSURANCE['key'],
            'status' => 'submitted'
        ]);
        $this->cloudinaryFileService->takeOwnerShip(
            [$file_id],
            File::OWNER_TYPE_DOCUMENT,
            $document->id
        );
    }

    public function createCacDocument(Company $company, $fileId): void
    {
        /** @var Document $document */
        $document = $company->cacDocument()->create([
            'user_id' => $company->user_id,
            'file_id' => $fileId,
            'document_type' => DocumentType::CAC_DOCUMENT['key'],
            'status' => 'submitted'
        ]);
        $this->cloudinaryFileService->takeOwnerShip(
            [$fileId],
            File::OWNER_TYPE_DOCUMENT,
            $document->id
        );
    }
}
