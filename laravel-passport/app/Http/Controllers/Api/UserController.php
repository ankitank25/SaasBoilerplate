<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AccountUpdateRequest;
use App\Http\Resources\Api\Notification\NotificationCollection;
use App\Http\Resources\Api\User\UserMetaCollection;
use App\Http\Resources\Api\User\UserResource;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    /**
     * method to save user profile
     *
     * @return void
     *
     * @throws Throwable
     */
    public function update(AccountUpdateRequest $request)
    {
        try {
            $userModel = User::firstWhere('uuid', $request->uuid);

            $validated = $request->validated();
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $userEmailChanged = false;

            if (isset($validated['email']) && $validated['email'] != $request->user()->email) {
                $userModel->forceFill([
                    'email_verified_at' => null,
                ]);

                $userEmailChanged = true;
            }

            $userModel->update($validated);

            $changes = $userModel->getChanges();

            /* $notification = ['type' => 'profile_update', 'message' => __('Your profile was updated.'), 'status' => 1];

            if (isset($changes['password'])) {
                $notification['type'] = 'profile_password_update';
                $notification['message'] = __("Your account password was changed.");
            } elseif (isset($changes['twofa_type'])) {
                if ($changes['twofa_type'] == "off") {
                    $notification['type'] = 'profile_twofa_disabled';
                    $notification['message'] = __("Your account's Two Factor authentication got disabled.");
                } else if ($changes['twofa_type'] == "email") {
                    $notification['type'] = 'profile_twofa_email_enabled';
                    $notification['message'] = __("Your account's Two Factor authentication using email got enabled.");
                }
            } elseif ($userEmailChanged) {
                $notification['type'] = 'profile_email_updated';
                $notification['message'] = __("Your account email was changed.");
            }

            if ($notification['type'] == 'profile_update') {
                $notification['updated_at'] = Carbon::now();
                $userModel->notifications()->updateOrCreate(
                    ['type' => $notification['type']],
                    $notification
                );
            } else {
                $userModel->notifications()->create($notification);
            } */

            if (isset($validated['meta']) && is_array($validated['meta'])) {
                $metaFields = config('metafields.fields');
                foreach ($validated['meta'] as $key => $value) {
                    if (isset($metaFields[$key]) && $metaFields[$key] == true) {
                        $userModel->meta()->updateOrCreate(
                            ['name' => $key],
                            ['value' => $value]
                        );
                    }
                }
            }

            $errorCode = '';
            if ($userEmailChanged) {
                $userModel->sendEmailVerificationNotification();
                $errorCode = 'unverified';
            }

            return new ApiResponseResource([
                'success' => true,
                'error_code' => $errorCode,
                'message' => __('User profile has been updated successfully.'),
                'data' => ['user' => new UserResource($userModel)],
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            User::firstWhere('uuid', $request->uuid)->delete();

            return new ApiResponseResource([
                'success' => true,
                'message' => __('User account has been deleted successfully.'),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function listNotifications(Request $request)
    {
        try {
            $notifications = new NotificationCollection($request->user()->notifications()->orderByDesc('updated_at')->paginate(10));

            return new ApiResponseResource([
                'success' => true,
                'data' => $notifications,
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function getMeta(Request $request)
    {
        try {
            $userMeta = $request->user()->meta()->get();
            $collection = new UserMetaCollection($userMeta);

            return new ApiResponseResource([
                'success' => true,
                'data' => ['meta' => $collection],
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::error($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }
}
