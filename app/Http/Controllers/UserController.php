<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userTypes = User::ALL_USER_TYPES;
        if (!in_array($request->query('user_type'), $userTypes)) {
            return $this->respondError('Please provide a valid user type in params');
        }
        $users = User::query()->select('')->where('user_type', $request->query('user_type'))->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }

    public function getAllTransporters()
    {
        $users = User::query()->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type')->where('user_type', User::USER_TYPE_TRANSPORTER)->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }
    public function getAllCargoOwners()
    {
        $users = User::query()->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type')->where('user_type', User::USER_TYPE_CARGO_OWNER)->get();
        return $this->respondSuccess(['users' => $users], 'User fetched successfully');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request, UserService $userService)
    {
       $user = $userService->createUser((object)$request->validated());
       return $this->respondSuccess(['user' => $user], 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = $user->load($user->myRelations($user->user_type));
        return $this->respondSuccess(['user' => $user], 'User fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequest $request, User $user)
    {
        $user->update($request->validated());
        return $this->respondSuccess([], 'User profile updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
