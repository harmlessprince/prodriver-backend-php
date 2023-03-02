<?php

namespace App\Http\Controllers;

use App\Exceptions\CompanyProfileExistsException;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function __construct(public readonly CompanyService $companyService) {
        
    }
    /**
     * @throws CompanyProfileExistsException
     */
    public function store(UpdateCompanyRequest $request, CompanyService $companyService): \Illuminate\Http\JsonResponse
    {

        /** @var User $user */
        $user = $request->user();
        if ($request->has('user_id')) {
            $user = User::where('id', $request->input('user_id'))->first();
            if (!$user) {
                return $this->respondError('The provided user id doest not exists');
            }
        }
        $company = $companyService->createCompany(
            name: $request->name,
            email: $request->email,
            phone_number: $request->phone_number,
            user_id: $user->id,
            rc_number: $request->rc_number,
            description: $request->description,
            cac_document_id: $request->cac_document_id,
            goods_in_transit_insurance_id: $request->goods_in_transit_insurance_id,
            fidelity_insurance_id: $request->fidelity_insurance_id
        );

        return $this->respondSuccess(['company' => $company], 'Company created successfully');
    }

    public function show(Company $company): \Illuminate\Http\JsonResponse
    {
        return $this->respondSuccess(['company' => $company->load(Company::RELATIONS)]);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company = $this->companyService->createCompany(
            name: $request->input('name', $company->name),
            email: $request->input('email', $company->email),
            phone_number: $request->input('phone_number', $company->phone_number),
            user_id: $company->user_id,
            rc_number: $request->input('rc_number', $company->rc_number),
            description: $request->input('description', $company->description),
            cac_document_id: $request->cac_document_id,
            goods_in_transit_insurance_id: $request->goods_in_transit_insurance_id,
            fidelity_insurance_id: $request->fidelity_insurance_id
        );
        // /** @var User $user */
        // $user = $company->user;
        // $user = $user->load($user->myRelations($user->user_type));
        return $this->respondSuccess(['company' => $company->refresh()], 'Company profile updated successfully');
    }
}
