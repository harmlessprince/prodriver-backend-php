<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BankDetailController extends Controller
{
    public function store(BankRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->bankAccount()->exists()) {
            return $this->respondError('You are now allowed to create more than one bank_account');
        }
        $user->bankAccount()->create($request->validated());
        return $this->respondSuccess([], 'Bank detail created successfully');
    }

    /**
     */
    public function update(BankRequest $request, BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->update($request->validated());
        return $this->respondSuccess([], 'Bank updated successfully');
    }
}
