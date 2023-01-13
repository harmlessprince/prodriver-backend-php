<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpdateProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $attributes = $request->validated();
        $profileData = [];
        $spouseData = [];
        $nextOfKinData = [];
        $userTableColumns = $this->getUserTableColumns();
        foreach ($attributes as $key => $value) {
            if (!is_null($value)) {
                if (Str::startsWith($key, 'next_of_kin')) {
                    $nextOfKinData[$key] = $value;
                } else if (Str::startsWith($key, 'spouse')) {
                    $spouseData[$key] = $value;
                } else {
                    if (in_array($key, $userTableColumns)) {
                        $profileData[$key] = $value;
                    }
                }
            }
        }
        $user->update($profileData);
        if ($user->marital_status == 'married' && $spouseData) {
            $user->spouse()->updateOrCreate(['user_id' => $user->id], $spouseData);
        }
        if (count($nextOfKinData) > 0) {
            $user->nextOfKin()->updateOrCreate(['user_id' => $user->id], $nextOfKinData);
        }
        $relations = $user->myRelations($user->user_type);
        return $this->respondWithResource(new UserResource($user->load($relations)), 'Profile updated successfully');
    }

    private function getUserTableColumns(): array
    {
        //cache the columns for 6hrs
        return Cache::remember('user_table_columns', 21600, function () {
            $user = new User();
            $tableName = $user->getTable();
            return Schema::getColumnListing($tableName);
        });
    }
}
