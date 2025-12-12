<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SavingController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\SocialFundController;
use App\Http\Controllers\API\InterestController;
use App\Http\Controllers\API\ReportController;

// Rotas públicas
// Route::prefix('v1')->group(function () {
//     // Autenticação
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);

//     // Rotas protegidas
//     Route::middleware('auth:sanctum')->group(function () {
//         // Perfil do usuário
//         // Route::get('profile', [UserController::class, 'profile']);
//         // Route::put('profile', [UserController::class, 'update']);

//         // Poupanças
//         // Route::apiResource('savings', SavingController::class);
//         // Route::get('savings/cycle/{cycle}', [SavingController::class, 'getByCycle']);
//         // Route::post('savings/upload-proof', [SavingController::class, 'uploadProof']);

//         // Empréstimos
//         // Route::apiResource('loans', LoanController::class);
//         // Route::post('loans/{loan}/payment', [LoanController::class, 'makePayment']);
//         // Route::get('loans/history', [LoanController::class, 'paymentHistory']);

//         // Fundo Social
//         // Route::apiResource('social-funds', SocialFundController::class);
//         // Route::get('social-funds/penalties', [SocialFundController::class, 'getPenalties']);

//         // Juros
//         // Route::prefix('interests')->group(function () {
//         //     Route::get('/', [InterestController::class, 'index']);
//         //     Route::get('distribution', [InterestController::class, 'distribution']);
//         //     Route::get('calculation', [InterestController::class, 'calculate']);
//         // });

//         // Relatórios
//         // Route::prefix('reports')->group(function () {
//         //     Route::get('summary', [ReportController::class, 'summary']);
//         //     Route::get('savings', [ReportController::class, 'savings']);
//         //     Route::get('loans', [ReportController::class, 'loans']);
//         //     Route::get('social-funds', [ReportController::class, 'socialFunds']);
//         // });

//         // Logout
//         // Route::post('logout', [AuthController::class, 'logout']);
//     });
// });

// Tratamento de erros
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint não encontrado.'
    ], 404);
});