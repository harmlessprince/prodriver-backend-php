<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GuarantorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TonnageController;
use App\Http\Controllers\TransporterDriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripStatusController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\TruckTypeController;
use App\Http\Controllers\UpdateProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaybillStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('auth')->group(function () {
    //Normal Login
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgot'])->name('password.request');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset');
    Route::post('/verify/forgot/password/reset/token', [ForgotPasswordController::class, 'verifyResetPasswordToken'])->name('verify.reset.password.token');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', [AuthenticationController::class, 'user']);
        Route::get('/logout', [AuthenticationController::class, 'logout']);
        Route::get('/email/verify/{token}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('/email/resend/token', [VerificationController::class, 'resend'])->name('verification.resend');
    });
});
Route::middleware('auth:sanctum')->group(function () {
    //file upload
    Route::post('upload/file', [FileController::class, 'uploadFile']);
    Route::post('upload/multiple/files', [FileController::class, 'uploadFiles']);
    Route::delete('delete/file/{file}', [FileController::class, 'deleteFile']);
    Route::post('delete/multiple/files', [FileController::class, 'deleteMultipleFile']);

    //country
    Route::get('countries',  [CountryController::class, 'index']);
    Route::get('countries/{country}', [CountryController::class, 'show']);

    //state
    Route::get('states', [StateController::class, 'index']);

    //utils
    Route::get('banks', [BankController::class, 'index']);
    //Onboarding
    Route::patch('update/profile', [UpdateProfileController::class, 'update']);
    Route::patch('update/company/{company}', [CompanyController::class, 'update']);
    Route::post('store/company', [CompanyController::class, 'store']);
    Route::post('store/guarantor', [GuarantorController::class, 'store']);
    Route::patch('update/guarantor/{guarantor}', [GuarantorController::class, 'update']);
    Route::post('store/banks/', [BankDetailController::class, 'store']);
    Route::patch('update/banks/{bankAccount}', [BankDetailController::class, 'update']);

    //Truck requests
    Route::apiResource('truckRequests', OrderController::class);
    Route::post('accept/request/{order}/from/transporter', [OrderController::class, 'acceptRequest']);
    Route::post('match/request/{order}/to/transporter', [OrderController::class, 'matchRequest']);
    Route::post('approve/accepted/{acceptedOrder}', [OrderController::class, 'approveRequest']);
    Route::get('accepted/request/{order}', [OrderController::class, 'allAcceptedRequest']);
    Route::get('cancel/request/{order}', [OrderController::class, 'cancelRequest']);
    //driver endpoints
    Route::apiResource('drivers', DriverController::class);
    Route::patch('change/driver/picture/{driver}', [DriverController::class, 'changeDriverPicture']);
    Route::patch('change/driver/license/picture/{driver}', [DriverController::class, 'changeDriverLicensePicture']);
    Route::patch('change/driver/owner/{driver}', [DriverController::class, 'changeDriverOwner']);
    Route::apiResource('trucks', TruckController::class);

    Route::apiResource('trips', TripController::class)->only('index', 'update');
    Route::patch('updateWaybillStatus/{trip}', [TripController::class, 'updateWaybillStatus']);
    Route::patch('updateTripStatus/{trip}', [TripController::class, 'updateTripStatus']);
    //users
    Route::apiResource('users', UserController::class);
    Route::get('/cargo-owners', [UserController::class, 'getAllCargoOwners']);
    Route::get('/transporters', [UserController::class, 'getAllTransporters']);
    Route::get('/account-managers', [UserController::class, 'getAllAccountManagers']);
    Route::get('transporters/{transporter}/driver', [TransporterDriverController::class, 'index']);

    //truck types
    Route::apiResource('truckTypes', TruckTypeController::class)->only(['store', 'index', 'destroy', 'update']);
    Route::apiResource('tonnages', TonnageController::class)->only(['store', 'index', 'destroy', 'update']);
    Route::apiResource('tripStatuses', TripStatusController::class);
    Route::apiResource('waybillStatuses', WaybillStatusController::class);
    Route::apiResource('documents', DocumentController::class);


    Route::get('admin/stats', [AnalyticsController::class, 'adminStats']);

    Route::get('transporter/stats', [AnalyticsController::class, 'transporterStats']);

    Route::post('import/trips', [TripController::class, 'importTrips']);

    Route::get('orders/financial/statuses', [OrderController::class, 'financialStatues']);
});
