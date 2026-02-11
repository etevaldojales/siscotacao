<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CotacaoController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\NewJustificativaController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/pedido/confirmar/{cotacaoId}/{fornecedorId}', [HomeController::class, 'confirmarPedido'])->name('pedido.confirmar');
Route::get('/cotacao/fornecedor/{cotacaoId}/{fornecedorId}', [CotacaoController::class, 'listItensCotacao'])->name('cotacao.fornecedor');
//Route::post('fornecedor-cotacao', [CotacaoController::class, 'storeFornecedorCotacao'])->name('fornecedor-cotacao.batch.store');
Route::post('fornecedor-cotacao-batch', [CotacaoController::class, 'storeFornecedorCotacaoBatch'])->name('fornecedor-cotacao.batch.store.batch');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// ROTAS PÚBLICAS
Route::get('/', function () {
    //return view('welcome');
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
});

// Disable default register route by redirecting to login
Route::get('register', function () {
    return redirect('login');
})->name('register');

// New admin register routes outside namespace group
Route::get('/admin/register', function () {
    return view('admin.register');
})->name('admin.register');



Route::namespace('App\Http\Controllers')->group(function () {
            Auth::routes();

            Route::get('/home', [HomeController::class, 'index'])->name('home');
            Route::get('cotacoes-active', [HomeController::class, 'getActiveCotacoes'])->name('home.cotacoes.active');
            Route::post('itens-cotacao', [CotacaoController::class, 'getItensCotacao'])->name('home.itens.cotacao');
            Route::post('fornecedor-details', [HomeController::class, 'getFornecedorDetails'])->name('home.fornecedor.details');
            Route::post('fornecedor-cotacao-list', [CotacaoController::class, 'listFornecedorCotacao'])->name('fornecedor-cotacao.list');
            Route::get('/admin/produto', [ProdutoController::class, 'index'])->name('produto')->middleware('role:administrator,comprador');
            Route::post('/admin/salvar-produto', [ProdutoController::class, 'store'])->name('salvar-produto')->middleware('role:administrator,comprador');
            Route::post('aprovar-item', [CotacaoController::class, 'aprovarItem'])->name('aprovar.item')->middleware('role:administrator,comprador');
            Route::post('/admin/remover-produto', [ProdutoController::class, 'destroy'])->name('remover-produto')->middleware('role:administrator');
            Route::post('/admin/marcas-produto', [ProdutoController::class, 'getMarcasByProduto'])->name('produtos.marcas');
            Route::get('/admin/produtos-list', [ProdutoController::class, 'getActiveProducts'])->name('produtos.list');
            Route::get('/admin/produtos-categorias-comprador', [ProdutoController::class, 'getProductsAndCategoriesForComprador'])->name('produtos.categorias.comprador')->middleware('role:administrator,comprador');
            Route::get('/admin/produtos-filter', [ProdutoController::class, 'filterProducts'])->name('produtos.filter');

            // Justificativa routes
            Route::get('/admin/justificativa', [NewJustificativaController::class, 'index'])->name('justificativa')->middleware('role:administrator');
            Route::post('load-justificativas', [NewJustificativaController::class, 'load'])->name('load.justificativas')->middleware('role:administrator,comprador');
            Route::post('/admin/save-justificativa', [NewJustificativaController::class, 'store'])->name('save.justificativa')->middleware('role:administrator');
            Route::post('justificativa-get', [NewJustificativaController::class, 'show'])->name('justificativa.get')->middleware('role:administrator');
            Route::post('justificativa-delete', [NewJustificativaController::class, 'destroy'])->name('justificativa.delete')->middleware('role:administrator');

            // Categoria routes
            Route::get('/admin/categoria', [CategoriaController::class, 'index'])->name('categoria')->middleware('role:administrator,comprador');
            Route::get('/admin/categoria/{id}', [CategoriaController::class, 'show'])->name('categoria.show')->middleware('role:administrator,comprador');
            Route::post('/admin/salvar-categoria', [CategoriaController::class, 'store'])->name('salvar-categoria')->middleware('role:administrator,comprador');
            //Route::post('/admin/categoria/{id}', [CategoriaController::class, 'update'])->name('categoria.update')->middleware('role:administrator');
            Route::post('/admin/remover-categoria', [CategoriaController::class, 'destroy'])->name('remover-categoria')->middleware('role:administrator');

            // Rotas Fornecedores
            Route::get('/admin/fornecedores', [FornecedorController::class, 'index'])->name('fornecedor.index')->middleware('role:administrator,comprador');
            Route::post('/admin/fornecedores-search', [FornecedorController::class, 'search'])->name('fornecedores-search')->middleware('role:administrator,comprador');
            Route::post('admin/salvar-fornecedor', [FornecedorController::class, 'store'])->name('fornecedor.store')->middleware('role:administrator,comprador');
            Route::post('admin/carrega-fornecedor', [FornecedorController::class, 'get'])->name('fornecedor.get')->middleware('role:administrator,comprador');
            Route::post('admin/remover-fornecedor', [FornecedorController::class, 'destroy'])->name('fornecedor.destroy')->middleware('role:administrator');
            Route::get('/admin/fornecedores/active', [FornecedorController::class, 'active'])->name('fornecedores.active')->middleware('role:administrator,comprador');

            // Rotas Marca
            Route::get('/admin/marcas', [MarcaController::class, 'index'])->name('marca.index')->middleware('role:administrator,comprador');
            Route::post('/admin/marcas-search', [MarcaController::class, 'search'])->name('marcas-search')->middleware('role:administrator,comprador');
            Route::post('admin/salvar-marca', [MarcaController::class, 'store'])->name('marca.store')->middleware('role:administrator,comprador');
            Route::post('admin/carrega-marca', [MarcaController::class, 'get'])->name('marca.get')->middleware('role:administrator,comprador');
            Route::post('admin/remover-marca', [MarcaController::class, 'destroy'])->name('marca.destroy')->middleware('role:administrator');

            // Rotas Cotacao
            Route::get('/admin/cotacoes', [CotacaoController::class, 'index'])->name('cotacao.index')->middleware('role:administrator,comprador');
            Route::post('/admin/cotacoes-search', [CotacaoController::class, 'search'])->name('cotacoes-search')->middleware('role:administrator,comprador');
            Route::post('admin/salvar-cotacao', [CotacaoController::class, 'store'])->name('cotacao.store')->middleware('role:administrator,comprador');
            Route::post('admin/carrega-cotacao', [CotacaoController::class, 'get'])->name('cotacao.get')->middleware('role:administrator,comprador');
            Route::post('admin/remover-cotacao', [CotacaoController::class, 'destroy'])->name('cotacao.destroy')->middleware('role:administrator,comprador');
            Route::post('admin/cotacao-send', [CotacaoController::class, 'sendCotacaoEmails'])->name('cotacao.send')->middleware('role:administrator,comprador');
            Route::post('cotacao-aprovar', [HomeController::class, 'aprovarCotacao'])->name('cotacao.aprovar');
            Route::get('/admin/cotacoes/{id}/pdf', [CotacaoController::class, 'generatePDF'])->name('cotacao.pdf')->middleware('role:administrator,comprador');


            // Itens Cotacao routes
            Route::post('admin/itens-cotacao-list', [CotacaoController::class, 'listItens'])->name('itens-cotacao.list')->middleware('role:administrator,comprador');
            Route::post('admin/itens-cotacao-store', [CotacaoController::class, 'storeItem'])->name('itens-cotacao.store')->middleware('role:administrator,comprador');
            Route::post('admin/itens-cotacao-get', [CotacaoController::class, 'getItem'])->name('itens-cotacao.get')->middleware('role:administrator,comprador');
            Route::post('admin/itens-cotacao-destroy', [CotacaoController::class, 'destroyItem'])->name('itens-cotacao.destroy')->middleware('role:administrator,comprador');

            // Perfis e Permissoes routes
            Route::middleware('role:administrator')->group(function () {
                Route::get('/admin/perfis', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
                Route::post('admin/save-roles', [App\Http\Controllers\RoleController::class, 'store'])->name('save-roles')->middleware('role:administrator');
                Route::post('/remover-roles', [App\Http\Controllers\RoleController::class, 'destroy'])->name('remover-roles')->middleware('role:administrator');
                Route::get('admin/permissoes', [App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index');
                Route::get('admin/permissions/create', [App\Http\Controllers\PermissionController::class, 'create'])->name('permissions.create')->middleware('role:administrator');
                Route::post('save-permissions', [App\Http\Controllers\PermissionController::class, 'store'])->name('save-permissions')->middleware('role:administrator');
                Route::post('admin/permissions/assign', [App\Http\Controllers\PermissionController::class, 'assignPermissionsToRole'])->name('permissions.assign')->middleware('role:administrator');

                // Users management routes
                Route::get('admin/usuarios', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
                Route::post('users/roles/{user}', [App\Http\Controllers\UserController::class, 'updateRoles'])->name('users.updateRoles');
                Route::post('users/{user}', [App\Http\Controllers\UserController::class, 'updateUser'])->name('users.updateUser');
                Route::post('admin/user-get', [App\Http\Controllers\UserController::class, 'getUser'])->name('user.get');
                Route::post('admin/user-save', [App\Http\Controllers\UserController::class, 'saveUser'])->name('user.save');

                Route::post('admin/check-cnpj', [App\Http\Controllers\UserController::class, 'checkCnpj'])->name('check.cnpj');
            });

            // User profile routes
            Route::middleware('auth')->group(function () {
                Route::get('/admin/profile', [App\Http\Controllers\UserProfileController::class, 'show'])->name('profile.show');
                Route::post('/admin/profile', [App\Http\Controllers\UserProfileController::class, 'update'])->name('profile.update')->middleware('role: administrator');
            });

            Route::middleware('role:administrator')->group(function () {
                Route::post('/admin/register', [RegisteredUserController::class, 'store'])->name('admin.register.store');

                Route::get('admin/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');
                Route::get('admin/menu/data', [App\Http\Controllers\MenuController::class, 'getMenuData'])->name('menu.data');
                Route::post('admin/menu/data/especifico', [App\Http\Controllers\MenuController::class, 'getMenuDataEspecifico'])->name('menu.data.esp');

                Route::post('/admin/salvar-menu', [App\Http\Controllers\MenuController::class, 'store'])->name('salvar-menu');
                //Route::post('/menu/update/{id}', [App\Http\Controllers\MenuController::class, 'update'])->name('menu.update');
                Route::post('admin/remover-menu', [App\Http\Controllers\MenuController::class, 'destroy'])->name('menu.destroy');

                Route::get('admin/produto/upload', [ProdutoUploadController::class, 'showUploadForm'])->name('produto.upload.form');
                Route::post('admin/processa/upload', [ProdutoUploadController::class, 'upload'])->name('produto.upload');

            });

            // Pedido routes
            Route::get('/admin/pedidos', [PedidoController::class, 'index'])->name('pedidos.index')->middleware('role:administrator,comprador');
            Route::post('/admin/salvar-pedidos', [PedidoController::class, 'store'])->name('pedidos.store')->middleware('role:administrator,comprador');
            Route::post('/admin/pedidos-search', [PedidoController::class, 'search'])->name('pedidos.search')->middleware('role:administrator,comprador');
            Route::patch('/admin/pedidos/{id}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.updateStatus')->middleware('role:administrator,comprador');
            Route::patch('/admin/pedidos/{id}', [PedidoController::class, 'update'])->name('pedidos.update')->middleware('role:administrator,comprador');
            Route::post('/admin/pedido-get', [PedidoController::class, 'get'])->name('pedidos.get')->middleware('role:administrator,comprador');

            // Itens Pedido routes
            Route::post('/admin/list-pedidos-itens', [PedidoController::class, 'listItensPedido'])->name('list-pedidos-itens')->middleware('role:administrator,comprador');
            Route::post('/admin/salvar-item-pedido', [PedidoController::class, 'storeItemPedido'])->name('salvar-item-pedido')->middleware('role:administrator,comprador');
            Route::post('/admin/itens-pedido-get', [PedidoController::class, 'getItemPedido'])->name('itens-pedido.get')->middleware('role:administrator,comprador');
            Route::post('/admin/remover-item-pediido', [PedidoController::class, 'destroyItemPedido'])->name('remover-item-pediido')->middleware('role:administrator,comprador');

            Route::get('/admin/pedidos/{id}/show', [PedidoController::class, 'show'])->name('pedidos.show')->middleware('role:administrator,comprador');

            // Route to get categories by CNPJ comprador
            Route::post('/admin/categories-by-cnpj', [ProdutoUploadController::class, 'getCategoriesByCnpj'])->name('categories.by.cnpj')->middleware('role:administrator,comprador');
        });

