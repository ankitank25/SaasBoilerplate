<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\User\UserCollection;
use App\Http\Resources\Api\User\UserResource;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public function list(Request $request)
    {
        try {
            $users = new UserCollection(User::where('id', '!=', $request->user()->id)->paginate(20));

            return new ApiResponseResource([
                'success' => true,
                'data' => $users,
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }

    public function create(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['uuid'] = Str::uuid()->toString();
            $validated['email_verified_at'] = now();

            $user = User::create($validated);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('messages.register.success'),
                'data' => ['user' => new UserResource($user)],
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('messages.default.error'),
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'status' => 'required|integer',
            ]);

            $users = User::whereIn('uuid', $validated['ids'])->update(['status' => $validated['status']]);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Users status has been updated.'),
            ], 200);
        } catch (ValidationException $e) {
            Log::error('ValidationException Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('messages.default.error'),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
            ]);

            User::whereIn('uuid', $validated['ids'])->delete();

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Selected user(s) deleted successfully.',
            ], 200);
        } catch (ValidationException $e) {
            Log::error('ValidationException Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.'),
            ], 500);
        }
    }
}
