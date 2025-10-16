<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CanalDeVendaController;
use App\Http\Controllers\CaracteristicasController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ColecaoController;
use App\Http\Controllers\ContatosController;
use App\Http\Controllers\EnderecosController;
use App\Http\Controllers\EscritorioRepresentacaoController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\FornecedoresController;
use App\Http\Controllers\FornecedorInsumoController;
use App\Http\Controllers\InsumoCategoriaController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PalavraChaveController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TravelOrderController;
use App\Http\Controllers\TipoVariacaoController;
use App\Http\Controllers\TipoVariacaoProdutoController;
use App\Http\Controllers\UnidadeMedidaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendedorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//login and register routes
Route::middleware(['api'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/getMe', [AuthController::class, 'getaccount']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::get('/refresh-token', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api'])->group(function () {
    // Travel Orders
    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [TravelOrderController::class, 'index']);
        Route::post('/', [TravelOrderController::class, 'store']);
        Route::get('/{travelOrder}', [TravelOrderController::class, 'show'])->whereUuid('travelOrder');
        Route::patch('/{travelOrder}/status', [TravelOrderController::class, 'updateStatus'])->whereUuid('travelOrder');
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->whereUuid('notification');
    });

    // Locations
    Route::prefix('locations')->group(function () {
        Route::get('/cities', [LocationController::class, 'cities']);
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])->whereUuid('user');
            Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->whereUuid('user');
        });
    });
});
