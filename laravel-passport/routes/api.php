<?php

use App\Models\Space;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SpaceController;
use App\Http\Controllers\Api\SpaceRoleController;
use App\Http\Controllers\Api\Admin\MenuController;
use App\Http\Controllers\Api\Admin\PageController;
use App\Http\Controllers\Api\SpaceInviteController;
use App\Http\Controllers\Api\PageController as PublicPageController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::group(function () {
    /* Public routes */
    // Authentication routes
    Route::prefix('/auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/re-authenticate', [AuthController::class, 'reAuthenticate'])->name('re-authentication');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me'])->name('me')->middleware('verified');
            Route::patch('/me/space/{uuid}', [AuthController::class, 'setCurrentSpace'])->name('set.current.space')->middleware('verified');
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/logout-all', [AuthController::class, 'logoutEveryWhere'])->name('logout-every-where');
            Route::get('/sessions', [AuthController::class, 'sessions'])->name("sessions");
            Route::delete('/session/{id}', [AuthController::class, 'deleteSession'])->name("delete.session");

            //User Account
            Route::patch('/user/account/{uuid}', [UserController::class, 'update'])->name('update.account');
            Route::delete('/user/account/{uuid}', [UserController::class, 'delete'])->name('delete.account');

            // User notifications
            Route::get('/user/notifications', [UserController::class, 'listNotifications'])->name('list.notifications');
        });
    });
    Route::middleware('auth:api')->group(function () {
        // Email Verification Routes
        Route::get('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
        Route::get('/auth/email/resend-verification', [AuthController::class, 'emailVerificationSend'])->name('verification.notice');

        //User Spaces
        /* Route::get('/spaces', [SpaceController::class, 'index'])->name("space.list");
        Route::get('/space/{uuid}', [SpaceController::class, 'show'])->name("space.get");
        Route::post('/space', [SpaceController::class, 'store'])->name("space.create");
        Route::patch('/space/{uuid}', [SpaceController::class, 'update'])->name("space.update");
        Route::delete('/space/{uuid}', [SpaceController::class, 'destroy'])->name("space.delete");

        Route::bind('space', function($uuid) {
            return Space::where('uuid', $uuid)->first();
        });
        Route::prefix('/space/{space}')->group(function ($space) {
            //User Space Roles
            Route::get('/roles', [SpaceRoleController::class, 'index'])->name("space.role.list");
            Route::get('/role/{uuid}', [SpaceRoleController::class, 'show'])->name("space.role.get");
            Route::post('/role', [SpaceRoleController::class, 'create'])->name("space.role.create");
            Route::patch('/role/{uuid}', [SpaceRoleController::class, 'update'])->name("space.role.update");
            Route::delete('/role/{uuid}', [SpaceRoleController::class, 'delete'])->name("space.role.delete");
        }); */
        /* Route::bind('space', function($space) {
            return Space::findOrFail($space);
        }); */
        Route::apiResource('space', SpaceController::class);
        Route::apiResource('space.role', SpaceRoleController::class);

        Route::get('space/role/resources', [SpaceRoleController::class, 'resources']);

        //User Space Invitations
        Route::post('/space/invite/{uuid}', [SpaceInviteController::class, 'invite'])->name("space.invite");
        Route::get('/space/invite/accept/{uuid}', [SpaceInviteController::class, 'acceptInvite'])->name("space.invite.accept");
        Route::get('/space/invite/reject/{uuid}', [SpaceInviteController::class, 'rejectInvite'])->name("space.invite.reject");
        Route::delete('/space/invite/{uuid}', [SpaceInviteController::class, 'destroy'])->name("space.invite.delete");
    });

    // Admin routes
    Route::prefix('/admin')->middleware('auth:api')->group(function () {
        // User list
        Route::get('/user/list', [AdminUserController::class, 'list'])->name('list.users');
        Route::post('/user/create', [AdminUserController::class, 'create'])->name('create.user');
        Route::patch('/user/update/{uuid}', [UserController::class, 'update'])->name('update.user');
        Route::post('/user/updateStatus', [AdminUserController::class, 'updateStatus'])->name('update.user.status');
        Route::delete('/user/delete', [AdminUserController::class, 'delete'])->name('delete.user');

        // Page routes
        Route::get('/page/list', [PageController::class, 'list'])->name('list.pages');
        Route::post('/page/create', [PageController::class, 'create'])->name('create.page');
        Route::put('/page/update/{uuid}', [PageController::class, 'update'])->name('update.page');
        Route::delete('/page/delete', [PageController::class, 'delete'])->name('delete.pages');

        // Menu routes
        Route::get('/menu/list', [MenuController::class, 'list'])->name('list.menus');
        Route::post('/menu/create', [MenuController::class, 'create'])->name('create.menu');
        Route::put('/menu/update/{uuid}', [MenuController::class, 'update'])->name('update.menu');
        Route::delete('/menu/delete', [MenuController::class, 'delete'])->name('delete.menus');

        // Setting routes
        Route::post('/settings/save', [SiteController::class, 'save'])->name('save.site.settings');
    });

    Route::get('/site/config', [SiteController::class, 'config'])->name('get.site.settings');
    Route::get('/page/{slug}', [PublicPageController::class, 'get'])->name('get.page');
//});
