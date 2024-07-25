<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\frontend\FrontendController;

Route::any('/', [App\Http\Controllers\frontend\FrontendController::class, 'index']);

Route::prefix('adminpnlx')->group(function () {
    Route::match(['get', 'post'], '', [App\Http\Controllers\adminpnlx\LoginController::class, 'login'])->name('adminpnlx');
    Route::match(['get', 'post'], 'forget_password', [App\Http\Controllers\adminpnlx\LoginController::class, 'forgetPassword'])->name('forgetPassword');
    Route::match(['get', 'post'], 'reset_password/{validstring}', [App\Http\Controllers\adminpnlx\LoginController::class, 'resetPassword'])->name('reset_password/{validstring}');
    Route::match(['get', 'post'], 'save_password', [App\Http\Controllers\adminpnlx\LoginController::class, 'save_password'])->name('save_password');

    Route::middleware(['AuthAdmin'])->group(function () {
        /*dashboard Route */
        Route::get('dashboard', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'showdashboard'])->name('dashboard');
        Route::get('logout', [App\Http\Controllers\adminpnlx\LoginController::class, 'logout'])->name('logout');
        Route::match(['get', 'post'], 'myaccount', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'myaccount'])->name('myaccount');
        Route::match(['get', 'post'], 'changedPassword', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'changedPassword'])->name('changedPassword');

        /* users routes */
        Route::match(['get', 'post'], '/customers', [App\Http\Controllers\adminpnlx\UsersController::class, 'index'])->name('users.index');
        Route::match(['get', 'post'], '/customers/create', [App\Http\Controllers\adminpnlx\UsersController::class, 'create'])->name('users.create');
        Route::post("/customers/save",[App\Http\Controllers\adminpnlx\UsersController::class, 'save'])->name('users.save');
        Route::match(['get', 'post'], '/customers/edit/{enuserid}', [App\Http\Controllers\adminpnlx\UsersController::class, 'edit'])->name('users.edit');
        Route::post("/customers/update/{enuserid}",[App\Http\Controllers\adminpnlx\UsersController::class, 'update'])->name('users.update');
        Route::get('customers/show/{enuserid}', [App\Http\Controllers\adminpnlx\UsersController::class, 'view'])->name('users.show');
        Route::get('customers/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'destroy'])->name('users.delete');
        Route::get('customers/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\UsersController::class, 'changeStatus'])->name('users.status');
        Route::match(['get', 'post'], 'customers/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'changedPassword'])->name('users.changedPassword');
        Route::get('customers/send-credentials/{id}', [App\Http\Controllers\adminpnlx\UsersController::class, 'sendCredentials'])->name('users.sendCredentials');
        Route::get('customers/deleterow/{id?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'deleterow'])->name('users.deleterow');
        /* users routes */

        /* cms manager routes */
        Route::resource('cms-manager', App\Http\Controllers\adminpnlx\CmspagesController::class);
        Route::get('cms-manager/destroy/{encmsid?}', [App\Http\Controllers\adminpnlx\CmspagesController::class, 'destroy'])->name('cms-manager.delete');
        //  cms manager routes 

        /* faq routes */
        Route::resource('faqs', App\Http\Controllers\adminpnlx\FaqController::class);
        Route::get('faqs/destroy/{enfaqid?}', [App\Http\Controllers\adminpnlx\FaqController::class, 'destroy'])->name('faqs.delete');
        /* faq routes */

        /* Language setting start */
        Route::resource('language-settings', App\Http\Controllers\adminpnlx\LanguageSettingsController::class);
        Route::match(['get', 'post'], 'language-settings/update1/{id?}', [App\Http\Controllers\adminpnlx\LanguageSettingsController::class, 'update1'])->name('language-settings.update1');
        /* Language setting start */

        /** email templates routing**/
        Route::resource('email-templates', App\Http\Controllers\adminpnlx\EmailtemplateController::class);
        Route::match(['get', 'post'], 'email-templates/get-constant', [App\Http\Controllers\adminpnlx\EmailtemplateController::class, 'getConstant'])->name('email-templates.getConstant');
        /** email templates routing**/

        /** email logs routing**/
        Route::match(['get', 'post'], 'email-logs', [App\Http\Controllers\adminpnlx\EmailLogsController::class, 'index'])->name('emaillogs.listEmail');
        Route::match(['get', 'post'], 'email-logs/email_details/{enmailid?}', [App\Http\Controllers\adminpnlx\EmailLogsController::class, 'emailDetail'])->name('emaillogs.emailDetail');
        /** email logs routing**/

        /** settings routing**/
        Route::resource('settings', App\Http\Controllers\adminpnlx\SettingsController::class);
        Route::match(['get', 'post'], '/settings/prefix/{enslug?}', [App\Http\Controllers\adminpnlx\SettingsController::class, 'prefix'])->name('settings.prefix');
        Route::get('settings/destroy/{ensetid?}', [App\Http\Controllers\adminpnlx\SettingsController::class, 'destroy'])->name('settings.delete');
        /** settings routing**/

        /** Access Control Routes Starts **/
        Route::resource('acl', App\Http\Controllers\adminpnlx\AclController::class);
        Route::get('acl/destroy/{enaclid?}', [App\Http\Controllers\adminpnlx\AclController::class, 'destroy'])->name('acl.delete');
        Route::get('acl/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\AclController::class, 'changeStatus'])->name('acl.status');
        Route::post('acl/add-more/add-more', [App\Http\Controllers\adminpnlx\AclController::class, 'addMoreRow'])->name('acl.addMoreRow');
        Route::get('acl/delete-function/{id}', [App\Http\Controllers\adminpnlx\AclController::class, 'delete_function'])->name('acl.delete_function');

        /* staff routes */
        Route::resource('staff', App\Http\Controllers\adminpnlx\StaffController::class);
        Route::get('staff/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\StaffController::class, 'changeStatus'])->name('staff.status');
        Route::get('staff/destroy/{enstfid?}', [App\Http\Controllers\adminpnlx\StaffController::class, 'destroy'])->name('staff.delete');
        Route::match(['get', 'post'], 'staff/changed-password/{enstfid?}', [App\Http\Controllers\adminpnlx\StaffController::class, 'changedPassword'])->name('staff.changerpassword');
        Route::match(['get', 'post'], 'staff/get-designations', [App\Http\Controllers\adminpnlx\StaffController::class, 'getDesignations'])->name('staff.getDesignations');
        Route::match(['get', 'post'], 'staff/get-staff-permission', [App\Http\Controllers\adminpnlx\StaffController::class, 'getStaffPermission'])->name('staff.getStaffPermission');

        /* Lookups manager  module  routing start here */
        Route::match(['get', 'post'], '/lookups-manager/{type}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'index'])->name('lookups-manager.index');
        Route::match(['get', 'post'], '/lookups-manager/add/{type}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'add'])->name('lookups-manager.add');
        Route::get('lookups-manager/destroy/{enlokid?}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'destroy'])->name('lookups-manager.delete');
        Route::get('lookups-manager/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'changeStatus'])->name('lookups-manager.status');
        Route::match(['get', 'post'], 'lookups-manager/{type?}/edit/{enlokid?}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'update'])->name('lookups-manager.edit');
        /* Lookups manager  module  routing start here */

        /* Lookups manager  module  routing start here */
        Route::match(['get', 'post'],'seo-page-manager', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'index'])->name('SeoPage.index');
        Route::get('seo-page-manager/add-doc', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'addDoc'])->name('SeoPage.create');
        Route::post('seo-page-manager/add-doc', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'saveDoc'])->name('SeoPage.save');
        Route::get('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'editDoc'])->name('SeoPage.edit');
        Route::post('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'updateDoc'])->name('SeoPage.update');
        Route::any('seo-page-manager/delete-page/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'deletePage'])->name('SeoPage.delete');
        /* Lookups manager  module  routing start here */

        /**  Designations routes **/
        Route::match(['get', 'post'], '/designations', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'index'])->name('designations.index');
        Route::match(['get', 'post'], 'designations/add', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'add'])->name('designations.add');
        Route::match(['get', 'post'], 'designations/edit/{endesid?}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'update'])->name('designations.edit');
        Route::get('designations/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'changeStatus'])->name('designations.status');
        Route::get('designations/delete/{endesid}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'delete'])->name('designations.delete');
        /* Designations routes */

    });
});

Route::get('/base/uploder', [App\Http\Controllers\Controller::class, 'saveCkeditorImages']);
Route::post('/base/uploder', [App\Http\Controllers\Controller::class, 'saveCkeditorImages']);