<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Referentiels\EquipementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Referentiels\UserController;
use App\Http\Controllers\Referentiels\FournisseurController;
use App\Http\Controllers\Referentiels\MateriauController;
use App\Http\Controllers\Referentiels\FilialeController;
use App\Http\Controllers\Referentiels\ZoneStockageController;
use App\Http\Controllers\DemandeCollecteController;
use App\Http\Controllers\Referentiels\RoleController;
use App\Http\Controllers\Referentiels\PersonController; // تأكد من إضافة هذا السطر
use App\Http\Controllers\MissionCollecteController;
use App\Http\Controllers\StockMPController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\EtatController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'index'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');


// Route::get('/register', [RegisterController::class, 'index']);
// Route::post('/register', [RegisterController::class, 'store']);


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['check.login', 'no.cache'])
    ->name('dashboard');

    // Route::get('/logout', [LoginController::class, 'logout'])
    // ->name('logout');

    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
// Route::resource('users', UserController::class)
//     ->middleware(['check.login', 'no.cache']);


    Route::prefix('referentiels')->name('referentiels.')->group(function () {
       
        Route::get('/utilisateurs', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware(['check.login', 'no.cache', 'check_perm:view']);

    Route::post('/utilisateurs', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware(['check.login', 'check_perm:create']);

    Route::put('/utilisateurs/{id}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware(['check.login', 'check_perm:edit']);

    Route::delete('/utilisateurs/{id}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware(['check.login', 'check_perm:delete']);

    

        
    // استبدل سطر الـ resource الخاص بـ personnel بهذا التنظيم:
    Route::get('/personnel', [PersonController::class, 'index'])->name('people.index')->middleware(['check.login', 'no.cache', 'check_perm:view']);
    Route::post('/personnel', [PersonController::class, 'store'])->name('people.store')->middleware(['check.login', 'check_perm:create']);
    Route::put('/personnel/{id}', [PersonController::class, 'update'])->name('people.update')->middleware(['check.login', 'check_perm:edit']);
    Route::delete('/personnel/{id}', [PersonController::class, 'destroy'])->name('people.destroy')->middleware(['check.login', 'check_perm:delete']);

    
     Route::get('/fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index')->middleware(['check.login', 'no.cache', 'check_perm:view']);
     Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store')->middleware(['check.login', 'check_perm:create']);
    Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update'])->name('fournisseurs.update')->middleware(['check.login', 'check_perm:edit']);
    Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy'])->name('fournisseurs.destroy')->middleware(['check.login', 'check_perm:delete']);
     
     Route::get('/materiaux', [MateriauController::class, 'index'])->name('materiaux.index')->middleware(['check.login', 'no.cache', 'check_perm:view']);
        Route::post('/materiaux', [MateriauController::class, 'store'])->name('materiaux.store')->middleware(['check.login', 'check_perm:create']);
        Route::put('/materiaux/{id}', [MateriauController::class, 'update'])->name('materiaux.update')->middleware(['check.login', 'check_perm:edit']);
        Route::delete('/materiaux/{id}', [MateriauController::class, 'destroy'])->name('materiaux.destroy')->middleware(['check.login', 'check_perm:delete']);
       

        
        Route::get('/equipements', [EquipementController::class, 'index'])->name('equipements.index')->middleware(['check.login', 'no.cache', 'check_perm:view']);
    Route::post('/equipements', [EquipementController::class, 'store'])->name('equipements.store')->middleware(['check.login', 'check_perm:create']);
    Route::put('/equipements/{id}', [EquipementController::class, 'update'])->name('equipements.update') ->middleware(['check.login', 'check_perm:edit']);
    Route::delete('/equipements/{id}', [EquipementController::class, 'destroy'])->name('equipements.destroy')->middleware(['check.login', 'check_perm:delete']);

    });

    Route::get('/referentiels/roles', [RoleController::class, 'index'])
    ->name('referentiels.roles.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']); // 🌟 هنا view

Route::post('/referentiels/roles', [RoleController::class, 'store'])
    ->name('referentiels.roles.store')
    ->middleware(['check.login', 'check_perm:create']); // 🌟 هنا create

Route::put('/referentiels/roles/{id}', [RoleController::class, 'update'])
    ->name('referentiels.roles.update')
    ->middleware(['check.login', 'check_perm:edit']); // 🌟 هنا edit

Route::delete('/referentiels/roles/{id}', [RoleController::class, 'destroy'])
    ->name('referentiels.roles.destroy')
    ->middleware(['check.login', 'check_perm:delete']); // 🌟 هنا delete


    Route::get('/referentiels/filiales', [FilialeController::class, 'index'])
    ->name('referentiels.filiales.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']);

Route::post('/referentiels/filiales', [FilialeController::class, 'store'])
    ->name('referentiels.filiales.store')
    ->middleware(['check.login', 'check_perm:create']);

Route::put('/referentiels/filiales/{id}', [FilialeController::class, 'update'])
    ->name('referentiels.filiales.update')
    ->middleware(['check.login', 'check_perm:edit']);

Route::delete('/referentiels/filiales/{id}', [FilialeController::class, 'destroy'])
    ->name('referentiels.filiales.destroy')
    ->middleware(['check.login', 'check_perm:delete']);


    Route::get('/referentiels/zones', [ZoneStockageController::class, 'index'])
    ->name('referentiels.zones.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']);

// 2. حفظ منطقة تخزين جديدة (Create)
Route::post('/referentiels/zones', [ZoneStockageController::class, 'store'])
    ->name('referentiels.zones.store')
    ->middleware(['check.login', 'check_perm:create']);

// 3. تعديل منطقة تخزين موجودة (Edit)
Route::put('/referentiels/zones/{id}', [ZoneStockageController::class, 'update'])
    ->name('referentiels.zones.update')
    ->middleware(['check.login', 'check_perm:edit']);

// 4. حذف منطقة تخزين (Delete)
Route::delete('/referentiels/zones/{id}', [ZoneStockageController::class, 'destroy'])
    ->name('referentiels.zones.destroy')
    ->middleware(['check.login', 'check_perm:delete']);




// مجموعة مسارات موديل الجمع (Module Collecte)


// 1. عرض قائمة طلبات الجمع (View)
Route::get('/collecte/demandes', [DemandeCollecteController::class, 'index'])
    ->name('collecte.demandes.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']);

// 2. حفظ طلب جمع جديد (Create)
Route::post('/collecte/demandes', [DemandeCollecteController::class, 'store'])
    ->name('collecte.demandes.store')
    ->middleware(['check.login', 'check_perm:create']);

// 3. تعديل بيانات طلب جمع موجود (Edit)
Route::put('/collecte/demandes/{id}', [DemandeCollecteController::class, 'update'])
    ->name('collecte.demandes.update')
    ->middleware(['check.login', 'check_perm:edit']);

// 4. العمليات الخاصة بدورة حياة الطلب (صلاحيات مخصصة)
Route::patch('/collecte/demandes/{id}/valider', [DemandeCollecteController::class, 'valider'])
    ->name('collecte.demandes.valider')
    ->middleware(['check.login', 'check_perm:validate']);

Route::patch('/collecte/demandes/{id}/refuser', [DemandeCollecteController::class, 'refuser'])
    ->name('collecte.demandes.refuser')
    ->middleware(['check.login', 'check_perm:reject']);

Route::patch('/collecte/demandes/{id}/annuler', [DemandeCollecteController::class, 'annuler'])
    ->name('collecte.demandes.annuler')
    ->middleware(['check.login', 'check_perm:cancel']);

// 5. حذف طلب جمع نهائياً (Delete)
Route::delete('/collecte/demandes/{id}', [DemandeCollecteController::class, 'destroy'])
    ->name('collecte.demandes.destroy')
    ->middleware(['check.login', 'check_perm:delete']);




// 1. عرض قائمة مهام الجمع (Index)
// 1. عرض قائمة مهام الجمع (Index)
Route::get('/collecte/missions', [MissionCollecteController::class, 'index'])
    ->name('collecte.missions.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']);

// 2. حفظ مهمة جمع جديدة (Create)
Route::post('/collecte/missions', [MissionCollecteController::class, 'store'])
    ->name('collecte.missions.store')
    ->middleware(['check.login', 'check_perm:create']);

// 3. تعديل بيانات مهمة جمع موجودة (Edit)
Route::put('/collecte/missions/{id}', [MissionCollecteController::class, 'update'])
    ->name('collecte.missions.update')
    ->middleware(['check.login', 'check_perm:edit']);

// 4. انطلاق الشاحنة (صلاحية مخصصة: start)
Route::patch('/collecte/missions/{id}/demarrer', [MissionCollecteController::class, 'demarrer'])
    ->name('collecte.missions.demarrer')
    ->middleware(['check.login', 'check_perm:start']);

// 5. عودة الشاحنة وإغلاق المهمة (صلاحية مخصصة: close)
Route::patch('/collecte/missions/{id}/cloturer', [MissionCollecteController::class, 'cloturer'])
    ->name('collecte.missions.cloturer')
    ->middleware(['check.login', 'check_perm:close']);

// 6. حذف مهمة جمع (Delete)
Route::delete('/collecte/missions/{id}', [MissionCollecteController::class, 'destroy'])
    ->name('collecte.missions.destroy')
    ->middleware(['check.login', 'check_perm:delete']);

    // 7. إلغاء مهمة جمع في مرحلة En cours (صلاحية مخصصة: cancel)
Route::patch('/collecte/missions/{id}/annuler', [MissionCollecteController::class, 'annuler'])
->name('collecte.missions.annuler')
->middleware(['check.login', 'check_perm:cancel']);
    

    Route::middleware(['check.login', 'no.cache'])->group(function () {
    
        // 1. Display Index
        Route::get('/receptions', [ReceptionController::class, 'index'])
            ->name('reception.index')
            ->middleware('check_perm:view');
            
        // 2. Stage 1: Store Pesage Brut
        Route::post('/receptions', [ReceptionController::class, 'store'])
            ->name('reception.store')
            ->middleware('check_perm:create');
            
        // 3. Stage 2: Store Tare (صلاحية خاصة بالـ Tare)
        Route::put('/receptions/{id}/tare', [ReceptionController::class, 'storeTare'])
            ->name('reception.tare')
            ->middleware('check_perm:tare'); // 👈 تم التعديل
        
        // 4. Stage 3: Validate & Stock (صلاحية خاصة بالتأكيد)
        Route::put('/receptions/{id}/validate', [ReceptionController::class, 'validateReception'])
            ->name('reception.validate')
            ->middleware('check_perm:validate'); // 👈 تم التعديل
            
        // 5. Delete Reception
        Route::delete('/receptions/{id}', [ReceptionController::class, 'destroy'])
            ->name('reception.destroy')
            ->middleware('check_perm:delete');

            // 3. Update Reception (المسار المضاف للتعديل العام) 👈
    Route::put('/receptions/{id}', [ReceptionController::class, 'update'])
    ->name('reception.update')
    ->middleware('check_perm:edit');
            
    });

    Route::get('/stock-mp', [StockMPController::class, 'index'])
    ->name('stock.index')
    ->middleware(['check.login', 'no.cache', 'check_perm:view']);



    Route::middleware(['check.login', 'no.cache'])->group(function () {
    // صفحة العرض والفلترة
    // 📄 مسار عرض الصفحات الأربع (demande, mission, reception, stock)
    Route::get('/etats/{type}', [EtatController::class, 'index'])
        ->middleware('check_perm:view')
        ->name('etats.index');

    // 🔴 مسار تصدير الـ PDF لكل الأنواع
    Route::get('/etats/pdf/{type}', [EtatController::class, 'exportPdf'])
        ->middleware('check_perm:view')
        ->name('etats.pdf');
});