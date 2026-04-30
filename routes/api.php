<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(["message" => "welcome"]);
});

Route::controller(AuthController::class)->group(function () {
    Route::post("/register", "register");
    Route::post("/login", "login");
    Route::post("/auth/email-verify", "verifyEmail");
    Route::post("/auth/resend-email-verify", "resendEmailVerify");
    Route::post("/auth/send-password-recovery", "sendPasswordRecovery");
    Route::post("/auth/password-recovery", "passwordRecovery");
});

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// Route protette
Route::middleware(["auth:sanctum"])->group(function () {
    Route::apiResource('/posts', PostController::class)->except(['index', 'show']);
    Route::apiResource('/users', UserController::class);
    Route::post('/users/{user}/avatar', [UserController::class, 'updateAvatar']);
    Route::apiResource('/comments', CommentController::class);
    Route::apiResource('/likes', LikeController::class);
    Route::apiResource('/comment-likes', CommentLikeController::class);
    Route::delete("/logout", [AuthController::class, "logout"]);
});
