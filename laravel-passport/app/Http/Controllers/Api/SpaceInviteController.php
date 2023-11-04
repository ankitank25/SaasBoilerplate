<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\User;
use App\Models\SpaceInvite;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Requests\Api\Space\InviteRequest;
use App\Mail\SpaceInvitation;
use App\Models\SpaceRole;
use Illuminate\Support\Facades\Mail;

class SpaceInviteController extends Controller
{
    public function invite(InviteRequest $request) 
    {
        try {
            $validated = $request->validated();
            if($space = $request->user()->spaces()->firstWhere('id', $request->uuid)) {
                $email = $validated['email'];

                $role = SpaceRole::where("id", $validated['role'])->first();

                if(!$role) {
                    return new ApiResponseResource([
                        'success' => false,
                        'message' => __("Requested role does not exist.")
                    ], 422);
                }

                $userId = null;

                $user = User::firstWhere('email', $email);

                if($user) {
                    $userId = $user->id;
                }
                
                $invite = [
                    'space_id' => $space->id,
                    'role_id' => $role->id,
                    'user_id' => $userId,
                    'email' => $email,
                    'status' => SpaceInvite::INVITE_STATUS_PENDING,
                ];

                $existingInvites = SpaceInvite::where('space_id', $space->id)->where('email', $email)->where('status', SpaceInvite::INVITE_STATUS_ACCEPTED);

                if($existingInvites->count()) {
                    return new ApiResponseResource([
                        'success' => true,
                        'message' => __(":EMAIL already accepted your invitation.", ['EMAIL' => $email])
                    ], 200);
                }
                
                $invite = SpaceInvite::updateOrCreate(['space_id' => $space->id, 'email' => $email], $invite);

                Mail::to($email)->send(new SpaceInvitation($space, $invite, $request->user()));
                
                return new ApiResponseResource([
                    'success' => true,
                    'message' => __('Invitation has been sent.')
                ], 200);
            }

            throw new ApiException("Space not found.", 401);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    public function acceptInvite(Request $request, string $uuid) 
    {
        try {
            $invite = $request->user()->spaceInvites()->where('id', $uuid)->where('status', SpaceInvite::INVITE_STATUS_PENDING)->first();
            if($invite) {
                if(!$request->user()->spaces()->firstWhere('id', $invite->space_id)) {
                    $request->user()->spaces()->attach($invite->space_id, ['role_id' => $invite->role_id]);
                }
                
                $invite->delete();

                return new ApiResponseResource([
                    'success' => true,
                    'message' => __('Invitation has been accepted.')
                ], 200);
            }

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Invitation not found.')
            ], 404);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    public function rejectInvite(Request $request, string $uuid)
    {
        try {
            $invite = $request->user()->spaceInvites()->where('id', $uuid)->where('status', SpaceInvite::INVITE_STATUS_PENDING)->first();
            if($invite) {
                $invite->update(['status' => SpaceInvite::INVITE_STATUS_REJECTED]);
            }

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Invitation has been rejected.')
            ], 200);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }

    public function destroy(Request $request, string $invitation)
    {
        try {
            SpaceInvite::where("id", $invitation)->delete();
            return new ApiResponseResource([
                'success' => true,
                'message' => __('Space invitation has been removed successfully.')
            ], 200);
        }
        catch(ApiException $e) {
            Log::error("API Exception: ". $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
        catch(Throwable $th) {
            Log::error("API Response Error: ". $th->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing request. Please check logs.')
            ], 500);
        }
    }
}
