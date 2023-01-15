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
        $this->authorize('create', BankAccount::class);
        /** @var User $user */
        $user = $request->user();
        if ($request->has('user_id')) {
            $user = User::where('id', $request->input('user_id'))->where('user_type', User::USER_TYPE_TRANSPORTER)->first();
            if (!$user) {
                return $this->respondError('The provided user is not a transporter or truck owner');
            }
        }
        if ($user->bankAccount()->exists()) {
            return $this->respondError('You are now allowed to create more than one bank_account');
        }
        $bankAccount =   $user->bankAccount()->create($request->validated());
        $bankAccount = $bankAccount->load('bank');
        return $this->respondSuccess(['bank_account' => $bankAccount], 'Bank detail created successfully');
    }

    /**
     */
    public function update(BankRequest $request, BankAccount $bankAccount): JsonResponse
    {
        $this->authorize('update', $bankAccount);
        if ($request->has('user_id')) {
            $user = User::where('id', $request->input('user_id'))->where('user_type', User::USER_TYPE_TRANSPORTER)->first();
            if (!$user) {
                return $this->respondError('The provided user is not a transporter or truck owner');
            }
        }
        $bankAccount->update($request->validated());
        return $this->respondSuccess([], 'Bank updated successfully');
    }
}
