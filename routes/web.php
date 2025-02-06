<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\site\HomeController as SiteHomeController;
use App\Http\Controllers\admin\HomeController as AdminHomeController;
use App\Http\Controllers\admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\admin\Auth\RegisterController as AdminRegisterController;
use App\Http\Controllers\admin\UsersController as AdminUsersController;
use App\Http\Controllers\admin\ProfileController as AdminProfileController;
use App\Http\Controllers\admin\SavingController as SavingController;
use App\Http\Controllers\admin\LoanController as LoanController;
use App\Http\Controllers\admin\LoanPaymentController as LoanPaymentController;
use App\Http\Controllers\admin\SocialFundController as SocialFundController;
use App\Http\Controllers\admin\MemberManagementController as MemberManagementController;
use App\Http\Controllers\admin\SavingsReportController;
use App\Http\Controllers\admin\InterestManagementController as InterestManagementController;
use App\Http\Controllers\Admin\InterestRateController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SiteHomeController::class, 'index']);

/* Route::prefix('painel')->group(function () {
    Route::get('/', [AdminHomeController::class, 'index'])->name('admin');
    Route::get('login', [AdminLoginController::class, 'index'])->name('login');
    Route::post('login', [AdminLoginController::class, 'authenticate']);

    Route::get('register', [AdminRegisterController::class, 'index'])->name('register');
    Route::post('register', [AdminRegisterController::class,'register']);
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::get('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::resource('users', AdminUsersController::class);
    Route::get('users/{user}/export', [AdminUserController::class, 'export'])->name('users.export');
    Route::get('profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::get('profile/save/{id}', [AdminProfileController::class, 'save'])->name('save');
    Route::put('profile/save/{id}', [AdminProfileController::class, 'save'])->name('save');
    Route::resource('members', MemberManagementController::class)->names('admin.members');
    Route::get('members/{member}', [MemberManagementController::class, 'show'])->name('members.show');
    Route::resource('savings', SavingController::class)->names('admin.savings');
    //index de poupancas
    //Route::get('savings', [SavingsController::class, 'index'])->name('savings');

    Route::get('savings/reports', [SavingController::class, 'report'])->name('admin.savings.report');
    Route::get('savings/export', [SavingsReportController::class, 'export'])->name('admin.savings.export');
    Route::get('loans', [LoanController::class, 'index'])->name('admin.loans');
    Route::post('loans', [loanController::class, 'registerPayment'])->name('loans.register-payment');
    Route::get('loans/{loan}', [LoanController::class,'show'])->name('admin.loans.show');
    Route::put('loans/{loan}', [LoanController::class,'updateStatus'])->name('admin.loans.update-status');
    Route::get('loans/export', [LoanController::class, 'export'])->name('admin.loans.export');

    //loans payment controller resouce
    Route::resource('loan-payments', LoanPaymentController::class)->names('admin.loan-payments');
    //social fund controller resource
    Route::resource('social-funds', SocialFundController::class);
    Route::get('interest-rates', [InterestManagementController::class, 'index'])->name('admin.interest-rates');
    Route::post('interest-management/set-rate', [InterestRateController::class, 'setRate'])->name('admin.interest-management.set-rate');
    Route::get('interest-management/calculate', [InterestManagementController::class,'monthlyInterest'])->name('admin.interest-management.calculate');
}); */
Route::prefix('painel')->group(function () {
    Route::get('/', [AdminHomeController::class, 'index'])->name('admin');
    
    // Autenticação
    Route::get('login', [AdminLoginController::class, 'index'])->name('login');
    Route::post('login', [AdminLoginController::class, 'authenticate']);
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    Route::get('register', [AdminRegisterController::class, 'index'])->name('register');
    Route::post('register', [AdminRegisterController::class, 'register']);
    
    // Gerenciamento de usuários e perfil
    Route::resource('users', AdminUsersController::class);
    Route::get('users/{user}/export', [AdminUsersController::class, 'export'])->name('users.export');
    Route::resource('profile', AdminProfileController::class)->only(['index', 'update']);
    
    // Membros e Poupanças
    Route::resource('members', MemberManagementController::class)->names('admin.members');
    Route::resource('savings', SavingController::class)->names('admin.savings');
    Route::get('savings/reports', [SavingController::class, 'report'])->name('admin.savings.report');
    Route::get('savings/export', [SavingsReportController::class, 'export'])->name('admin.savings.export');
    
    // Empréstimos
    // Rota para exibir a lista de empréstimos
    Route::get('loans', [LoanController::class, 'index'])->name('admin.loans.index');

    // Rota para exibir os detalhes de um empréstimo
    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('admin.loans.show');

    // Rota para criar um novo empréstimo
    Route::get('loans/create', [LoanController::class, 'create'])->name('admin.loans.create');
    Route::post('loans', [LoanController::class, 'store'])->name('admin.loans.store');

    // Rota para atualizar o status de um empréstimo
    Route::put('loans/{loan}', [LoanController::class, 'update'])->name('admin.loans.update');
    
    // Rota para registrar um pagamento de empréstimo
    Route::post('loans/{loan}/payment', [LoanController::class, 'registerPayment'])->name('admin.loans.registerPayment');
    
    // Pagamentos de Empréstimos
    Route::resource('loan-payments', LoanPaymentController::class)->names('admin.loan-payments');
    
    // Fundos Sociais e Taxas de Juros
    // Rota para exibir a lista de fundos sociais
    Route::get('social-funds', [SocialFundController::class, 'index'])->name('admin.social-funds.index');

    // Rota para exibir os detalhes de um fundo social específico
    Route::get('social-funds/{socialFund}', [SocialFundController::class, 'show'])->name('admin.social-funds.show');

    // Rota para criar um novo fundo social
    Route::get('social-funds/create', [SocialFundController::class, 'create'])->name('admin.social-funds.create');
    Route::post('social-funds', [SocialFundController::class, 'store'])->name('admin.social-funds.store');

    // Rota para editar um fundo social
    Route::get('social-funds/{socialFund}/edit', [SocialFundController::class, 'edit'])->name('admin.social-funds.edit');
    Route::put('social-funds/{socialFund}', [SocialFundController::class, 'update'])->name('admin.social-funds.update');

    // Rota para excluir um fundo social
    Route::delete('social-funds/{socialFund}', [SocialFundController::class, 'destroy'])->name('admin.social-funds.destroy');
    Route::get('interest-rates', [InterestManagementController::class, 'index'])->name('admin.interest-rates');
    Route::post('interest-management/set-rate', [InterestRateController::class, 'setRate'])->name('admin.interest-management.set-rate');
    Route::get('interest-management/calculate', [InterestManagementController::class, 'monthlyInterest'])->name('admin.interest-management.calculate');
});

Route::fallback(function () {
    return view('errors.404');
});