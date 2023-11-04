<?php

namespace App\Http\Controllers\Api;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\User\LoggedIn;
use App\Events\User\LoggedOut;
use App\Exceptions\ApiException;
use App\Events\User\LoggedOutAll;
use App\Events\User\EmailVerified;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Resources\ApiResponseResource;
use App\Events\User\ResentVerificationEmail;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\User\UserSessionCollection;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Models\SpaceRole;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    /**
     * Method to register user
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['uuid'] = Str::uuid()->toString();
            $validated['status'] = User::USER_STATUS_ACTIVE;
            $user = User::create($validated);
            event(new Registered($user));

            $token = $user->createToken($request->header('X-Platform', 'Browser'));

            $accessToken = $token->token;
            $accessToken->device_info = $request->header('User-Agent') ?: '';
            $accessToken->ip = $request->ip();
            $accessToken->save();

            $errorCode = ($user->hasVerifiedEmail() === false) ? 'unverified' : '';

            return new ApiResponseResource([
                'success' => true,
                'error_code' => $errorCode,
                'message' => __('You are successfully registered.'),
                'data' => ['token' => $token->accessToken, 'user' => new UserResource($user)],
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Send email address verification email.
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function emailVerificationSend(Request $request)
    {
        try {
            $user = $request->user();
            $user->sendEmailVerificationNotification();

            event(new ResentVerificationEmail($user));

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Verification email has been resent.'),
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

    /**
     * Verify email address
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        try {
            $request->fulfill();

            $user = $request->user();
            $accessToken = $user->token();

            $accessToken->two_factor_verified_at = now();
            $accessToken->two_factor_code = '999999';
            $accessToken->two_factor_expires_at = null;

            $accessToken->save();

            event(new EmailVerified($user));

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Email has been verified successfully.'),
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

    /**
     * Method to login user
     *
     * @return ApiResponseResource
     *
     * @throws ApiException when invalid credentials provided.
     * @throws Throwable
     */
    public function login(LoginRequest $request)
    {
        try {
            $request->validated();

            $rememberMe = $request->remember_me || false;

            $credentials = $request->only(['email', 'password']);
            $credentials['status'] = User::USER_STATUS_ACTIVE;
            if (Auth::attempt($credentials, $rememberMe)) {
                $user = Auth::user();

                if($rememberMe === true) {
                    Passport::personalAccessTokensExpireIn(now()->addDays(30));
                }

                $token = $user->createToken($request->header('X-Platform', 'Browser'));

                $accessToken = $token->token;
                $accessToken->device_info = $request->header('User-Agent') ?: '';
                $accessToken->ip = $request->ip();
                $accessToken->save();

                if ($user->two_factor_type == 'email') {
                    //$user->notify(new SendTwoFactorCodeNotification($token->accessToken));
                }

                event(new LoggedIn($user));

                $errorCode = ($user->hasVerifiedEmail() === false) ? 'unverified' : '';

                return new ApiResponseResource([
                    'success' => true,
                    'error_code' => $errorCode,
                    'message' => __('You have been logged in successfully.'),
                    'data' => ['token' => $token->accessToken, 'user' => new UserResource($user)],
                ]);
            } else {
                throw new ApiException(__('Invalid credentials provided.'), 403);
            }
        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Re authenticate user to update account credential infomration.
     *
     * @return ApiResponseResource
     *
     * @throws ApiException when invalid credentials provided.
     * @throws Throwable
     */
    public function reAuthenticate(LoginRequest $request)
    {
        try {
            $validated = $request->validated();
            if (Auth::attempt($request->only(['email', 'password']))) {
                $user = Auth::user();

                event(new LoggedIn($user));

                $userResource = new UserResource($user);

                return new ApiResponseResource([
                    'success' => true,
                    'message' => __('Re-Authentication successful.'),
                    'data' => ['user' => $userResource],
                ]);
            } else {
                throw new ApiException(__('Invalid credentials provided.'), 422);
            }
        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Logout current user
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->token()->delete();

            event(new LoggedOut($user));

            return new ApiResponseResource([
                'success' => true,
                'message' => __('You are now logged out.'),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Logout user from all his/her logged in devides
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function logoutEveryWhere(Request $request)
    {
        try {
            $user = $request->user();

            $tokens = $user->tokens()->where('id', '!=', $user->token()->id);

            if ($tokens->count() > 0) {
                $tokens->delete();
                event(new LoggedOutAll($user));
            }

            return new ApiResponseResource([
                'success' => true,
                'message' => __('You are now logged out from all logged in devices.'),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Method to provide current logged in user information.
     *
     * @return ApiResponseResource
     *
     * @throws Throwable
     */
    public function me(Request $request)
    {
        try {
            return new ApiResponseResource([
                'success' => true,
                'data' => ['user' => new UserResource($request->user())],
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    public function setCurrentSpace(Request $request, string $uuid)
    {
        try {
            $request->user()->current_space = $uuid;
            $request->user()->save();
            return new ApiResponseResource([
                'success' => true,
                'data' => $request->user()->currentRole()->first(),
            ], 200);
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Send password reset token on forgot password request
     *
     * @return ApiResponseResource
     *
     * @throws ApiException when no user associated with provided email address.
     * @throws Throwable
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $request->validated();
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return new ApiResponseResource([
                'success' => true,
                'message' => 'You must received and email, if there is an account associated with email address provided.',
            ], 200);

            throw new Exception("Forgot password failed. Status $status", 500);
        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $request->validated();

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::INVALID_TOKEN) {
                throw new ApiException(__('Invalid reset token provided. Try requesting new reset password email :LINK.', ['LINK' => '<a class="text-indigo-600 underline" href="'.config('app.frontend_url').'/forgot-password">here</a>']), 422);
            }

            if ($status === Password::INVALID_USER) {
                throw new ApiException(__('Unable to reset password for the requested user.'), 422);
            }

            if ($status !== Password::PASSWORD_RESET) {
                throw new Exception("Reset password failed. Status $status", 500);
            }

            return new ApiResponseResource([
                'success' => true,
                'message' => 'Password has been reset successfully.',
            ], 200);

        } catch (ApiException $e) {
            Log::error('API Exception: '.$e->getMessage());

            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error('API Response Error: '.$th->getMessage());
            Log::critical($th);

            return new ApiResponseResource([
                'success' => false,
                'message' => __('There was an error processing the request.'),
            ], 500);
        }
    }

    /**
     * Method to provide current logged in user sessions.
     *
     * @param Request $request
     * @throws Throwable
     * @return ApiResponseResource
     */
    public function sessions(Request $request)
    {
        try {
            $collection = new UserSessionCollection($request->user()->tokens);

            return new ApiResponseResource([
                'success' => true,
                'data' => $collection
            ], 200);
        } catch (Throwable $th) {
            Log::error("API Response Error: " . $th->getMessage());
            Log::critical($th);
            return new ApiResponseResource([
                'success' => false,
                'message' => __('messages.default.error'),
            ], 500);
        }
    }

    /**
     * Delete current user's session
     *
     * @param Request $request
     * @throws Throwable
     * @return ApiResponseResource
     */
    public function deleteSession(Request $request)
    {
        try {
            if(!$request->id) {
                throw new ApiException("Valid session ID needs to be provided.", 422);
            }

            $user = $request->user();
            $tokens = $user->tokens;

            if(($token = $tokens->firstWhere('id', $request->id))) {
                $token->delete();
            }

            $filtered = $tokens->filter(function ($value) use($request)  {
                return $value->id != $request->id;
            });

            $collection = new UserSessionCollection($filtered);

            return new ApiResponseResource([
                'success' => true,
                'message' => __('Session deleted successfully.'),
                'data' => $collection
            ], 200);
        } catch (ApiException $e) {
            Log::error("API Exception: " . $e->getMessage());
            return new ApiResponseResource([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (Throwable $th) {
            Log::error("API Response Error: " . $th->getMessage());
            Log::critical($th);
            return new ApiResponseResource([
                'success' => false,
                'message' => __('messages.default.error'),
            ], 500);
        }
    }
}
