<?php

use Illuminate\Support\Facades\Route;

// Controllers do Site
use App\Http\Controllers\site\HomeController as SiteHomeController;

// Controllers do Admin
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\Auth\RegisterController;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\SavingController;
use App\Http\Controllers\admin\SavingCycleController;
use App\Http\Controllers\admin\LoanController;
use App\Http\Controllers\admin\LoanPaymentController;
use App\Http\Controllers\admin\SocialFundController;
use App\Http\Controllers\admin\MemberManagementController;
use App\Http\Controllers\admin\SavingsReportController;
use App\Http\Controllers\admin\InterestRatesController;
use App\Http\Controllers\admin\InterestDistributionController;
use App\Http\Controllers\admin\ReportController;

// Rota principal do site
Route::get('/', [HomeController::class, 'index']);

// Rotas do Painel Administrativo
Route::prefix('painel')->group(function () {
    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('admin');

    // Autenticação
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate']);
    Route::any('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Rotas protegidas por autenticação
    Route::middleware('auth')->group(function () {
        // Usuários
        Route::resource('users', UsersController::class);
        Route::get('users/{user}/export', [UsersController::class, 'export'])
            ->name('users.export');

        // Perfil
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        //profile save
        Route::put('profile/{id}', [ProfileController::class, 'save'])->name('profile.save');

        // Membros
        Route::resource('members', MemberManagementController::class);
        Route::get('members/export/{format}', [MembersController::class, 'export'])->name('members.export');
        
        // Poupanças
        Route::resource('savings', SavingController::class);
        Route::get('savings/reports', [SavingController::class, 'report'])
            ->name('savings.report');
        Route::get('savings/export', [SavingsReportController::class, 'export'])
            ->name('savings.export');
        Route::get('savings/export/excel', [SavingController::class, 'exportExcel'])->name('savings.export.excel');
        Route::get('savings/export/pdf', [SavingController::class, 'exportPDF'])->name('savings.export.pdf');
        
        // Ciclos de Poupança
        Route::resource('saving-cycles', SavingCycleController::class);

        // Empréstimos
        Route::resource('loans', LoanController::class);
        Route::post('loans/{loan}/payment', [LoanController::class, 'registerPayment'])
            ->name('loans.register-payment');
        Route::get('loans.export', [LoanController::class, 'export'])
            ->name('loans.export');

        // Pagamentos de Empréstimos
        Route::resource('loan-payments', LoanPaymentController::class);
        Route::get('loan-payments/filter', [LoanPaymentsController::class, 'filter'])->name('loan-payments.filter');
        Route::get('loan-payments/export', [LoanPaymentsController::class, 'export']);

        // Fundo Social
        Route::resource('social-funds', SocialFundController::class);

        // Gestão de Juros
        Route::get('interest-rates', [InterestRatesController::class, 'index'])->name('interest-rates.index');
        Route::post('interest-rates/set', [InterestRatesController::class, 'setRate'])->name('interest-rates.set');
        Route::get('interest-rates/calculate', [InterestRatesController::class, 'calculateDistribution'])->name('interest-rates.calculate');
        Route::post('interest-rates/distribute', [InterestRatesController::class, 'distribute'])->name('interest-rates.distribute');
        Route::get('interest-rates/report', [InterestRatesController::class, 'report'])->name('interest-rates.report');
        Route::get('interest-rates/export', [InterestRatesController::class, 'export'])->name('interest-rates.export'); 
        Route::get('interest-distribution', [InterestDistributionController::class, 'index'])
            ->name('interest-distribution.index');
        Route::get('interest-distribution/create', [InterestDistributionController::class,'create'])->name('interest-distributions.create');
        Route::post('interest-distribution/store', [InterestDistributionController::class,'store'])->name('interest-distributions.store');
        Route::post('interest-distribution/calculate', [InterestDistributionController::class, 'calculate'])
            ->name('interest-distribution.calculate');
        Route::get('interest-distributions/export', [InterestDistributionController::class, 'export'])
            ->name('interest-distributions.export');
            });
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('reports/generate', [ReportController::class, 'generateReport'])->name('admin.reports.generate');
        Route::get('reports/members/{user}', [ReportController::class, 'memberReport'])
            ->name('reports.member');

});


// Rota para página não encontrada
Route::fallback(function () {
    return view('errors.404');
});