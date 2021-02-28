<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers;

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


// GENERAL PAGES
Route::middleware(['auth','csrf'])->group(function()
{

    Route::get('/', [Controllers\DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/Kontakt', [Controllers\ContactController::class, 'index'])->name('contact.index');
    Route::post('/Kontakt', [Controllers\Support\ProblemController::class, 'store'])->name('contact.store');

    Route::get('/User/create', [Controllers\RightsController::class, 'create'])->name('rights.create');
    Route::get('/User/edit/{id}', [Controllers\RightsController::class, 'edit'])->name('rights.edit');
    Route::get('/User/edit/{id}/user', [Controllers\RightsController::class, 'editNoDepartment'])->name('rights.edit.nodepartment');
    Route::put('/User/edit/{id}/rights/user', [Controllers\RightsController::class, 'updateNoDepartment'])->name('userrights.edit.nodepartment');
    Route::get('/User/{id}', [Controllers\UserController::class, 'index'] )->name('user.index');


    Route::get('/Logout', [Controllers\AuthControllers\LoginController::class, 'logout'])->name('login.logout');


    Route::get('/about', function () { return view('About.index');})->name('about');

    Route::get('/document', [Controllers\UserController::class, 'redirectToDrive'])->name('docs.redirect');
    Route::get('/document/shared', [Controllers\UserController::class, 'redirectToSharedfile'])->name('docs.shared');

    Route::get('/Admin/Support/Problem/{id}', [Controllers\Support\ProblemController::class, 'show'])->name('support.show');
    Route::delete('/Admin/Support/Problem/{id}', [Controllers\Support\ProblemController::class, 'delete'])->name('support.delete');
    Route::post('/Admin/Support/Problem/{id}', [Controllers\Support\MessageController::class, 'store'])->name('message.store');

    Route::get('/Admin/Support/Problem/{id}/{problem_id}', [Controllers\Support\ProblemController::class, 'show'])->name('support.preshow');

    Route::delete('/Notification/delete/{id}', [Controllers\NotificationController::class, 'delete'])->name('notification.delete');
});

// ADMIN-ONLY ROUTES
Route::middleware(['auth.admin','csrf'])->group(function() {

    Route::get('/Rechtesystem/User/{pagination}', [Controllers\RightsController::class, 'index'])
        ->name('rights.index')
        ->where('pagination', '[0-9]+');

    Route::get('/Admin/create', [Controllers\RightsAdminController::class, 'create'])->name('rights.admin.create');
    Route::get('/Admin/edit/{id}', [Controllers\RightsAdminController::class, 'edit'])->name('rights.admin.edit');
    Route::get('/Admin/{id}', [Controllers\AdminController::class, 'index'] )->name('admin.index');

    Route::get('/Rechtesystem/Admin/{pagination}', [Controllers\RightsAdminController::class, 'index'])
        ->name('rights.admin.index')
        ->where('pagination', '[0-9]+');

    Route::get('/Admin/Support/{pagination}', [Controllers\Support\ProblemController::class, 'index'])->name('support.index');
    Route::delete('/Admin/delete/{id}', [Controllers\AdminController::class, 'delete'])->name('admin.delete');
    Route::put('/Admin/edit/{id}/rights', [Controllers\RightsAdminController::class, 'update'])->name('adminrights.edit');

    Route::delete('/User/delete/{id}', [Controllers\UserController::class, 'delete'])->name('user.delete');
    Route::put('/User/edit/{id}/rights', [Controllers\RightsController::class, 'update'])->name('userrights.edit');
    Route::put('/ToUser/edit/{id}/rights', [Controllers\AdminController::class, 'switchToUser'])->name('adminrights.switchToUser');
    Route::put('/ToAdmin/edit/{id}/rights', [Controllers\UserController::class, 'switchToAdmin'])->name('userrights.switchToAdmin');
});


// HTTP REQUESTS
Route::middleware(['cors','csrf'])->group(function()
{

    //get folderPath for the filepath  in the drive navigation bar
    Route::get('drive/path/{id}/{token}/{fileid}', [Controllers\DocumentController::class, 'getFolderPath'])->withoutMiddleware(['csrf']);
    Route::get('drive/path/shared/{id}/{token}/{fileid}', [Controllers\DocumentController::class, 'getSharedFolderPath'])->withoutMiddleware(['csrf']);

    //get notifications
    Route::get('drive/notification/{id}/{token}/{pagination}', [Controllers\NotificationController::class, 'driveIndex'])->withoutMiddleware(['csrf']);

    //delete notifications
    Route::get('drive/notification/{id}/{token}/{messageId}/delete', [Controllers\NotificationController::class, 'driveDelete'])->withoutMiddleware(['csrf']);

    //get notifications
    Route::get('/notification/{pagination}', [Controllers\NotificationController::class, 'index'])->name('notification.index');


    // verify id and token wildcards
    Route::middleware('HttpAuth')->group(function(){

        // get files
        Route::get('/drive/request/mydocument/{id}/{token}', [Controllers\Drive\MyDocumentController::class, 'index']);
        Route::get('/drive/request/shareddocument/{id}/{token}', [Controllers\Drive\SharedDocumentController::class, 'index']);
        Route::get('/drive/request/departmentdocument/{department}/{id}/{token}', [Controllers\Drive\DepartmentDocumentController::class, 'show']);

        // get all files
        Route::get('/drive/request/mydocumentfiles/{id}/{token}', [Controllers\Drive\MyDocumentController::class, 'indexAll']);
        Route::get('/drive/request/shareddocumentfiles/{id}/{token}', [Controllers\Drive\SharedDocumentController::class, 'indexAll']);
        Route::get('/drive/request/departmentdocument/{department}/files/{id}/{token}', [Controllers\Drive\DepartmentDocumentController::class, 'indexAll']);

        //get fileversions
        Route::get('/drive/request/mydocument/{id}/{token}/{group_version_id}/version/file', [Controllers\FileController::class, 'getVersions']);

        //get folders
        Route::get('/drive/request/mydocument/folder/{id}/{token}', [Controllers\FolderController::class, 'index']);
        Route::get('/drive/request/shareddocument/folder/{id}/{token}', [Controllers\SharedFolderController::class, 'index']);
        Route::get('/drive/request/departmentdocument/folder/{id}/{token}/{departmentID}', [Controllers\DepartmentFolderController::class, 'index']);


        //child folder and files
        Route::get('/drive/request/folder/childdata/file/{id}/{token}/{folderid}', [Controllers\FileController::class, 'show']);
        Route::get('/drive/request/folder/childdata/allfiles/{id}/{token}/{folderid}', [Controllers\FileController::class, 'showAll']);
        Route::get('/drive/request/folder/childdata/folder/{id}/{token}/{folderid}', [Controllers\FolderController::class, 'show']);

        Route::get('/drive/request/department/{id}/{token}/get', [Controllers\Drive\DepartmentDocumentController::class, 'getAuthorizedUserDepartments']);
        Route::get('/drive/request/currentuser/{id}/{token}', [Controllers\UserController::class, 'show'])->withoutMiddleware(['csrf']);

        // move file to another folder
        Route::post('/drive/request/{id}/{token}/move/file', [Controllers\FileController::class, 'moveTo'])->withoutMiddleware(['csrf']);

        //uploading files
        Route::post('/drive/request/mydocument/{id}/{token}/upload/file', [Controllers\Drive\MyDocumentController::class, 'store'])->withoutMiddleware(['csrf']);
        Route::post('/drive/request/departmentdocument/{departmentid}/{id}/{token}/upload/file', [Controllers\Drive\DepartmentDocumentController::class, 'store'])->withoutMiddleware(['csrf']);
        Route::post('/drive/request/mydocument/{id}/{token}/upload/sharedfile/{requester_id}/{fileid}', [Controllers\Drive\SharedDocumentController::class, 'store'])->withoutMiddleware(['csrf']);

        //uploading folders
        //Später die route mit parentfolder id
        //   Route::post('/drive/request/mydocument/{id}/{token}/upload/folder/{parentfolder?}', [Controllers\FolderController::class, 'store'])->withoutMiddleware(['csrf']);
        Route::post('/drive/request/mydocument/{id}/{token}/upload/folder', [Controllers\FolderController::class, 'store'])->withoutMiddleware(['csrf']);
        Route::post('/drive/request/departmentdocument/{departmentid}/{id}/{token}/upload/folder', [Controllers\DepartmentFolderController::class, 'store'])->withoutMiddleware(['csrf']);

        //downloading files
        Route::get('/drive/request/mydocument/{id}/{token}/{hashname}/download', [Controllers\Drive\MyDocumentController::class, 'download'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/departmentdocument/{id}/{token}/{hashname}/download', [Controllers\Drive\DepartmentDocumentController::class, 'download'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/shareddocument/{id}/{token}/{hashname}/{fileID}/download', [Controllers\SharedFileController::class, 'download'])->withoutMiddleware(['csrf']);

        Route::post('/drive/request/mydocument/{id}/{token}/upload/sharedfolder/{requester_id}/{folderid}', [Controllers\SharedFolderController::class, 'store'])->withoutMiddleware(['csrf']);

        // move file to another folder
        Route::post('/drive/request/{id}/{token}/move/folder', [Controllers\FolderController::class, 'moveTo'])->withoutMiddleware(['csrf']);

        //downloading folders
        Route::get('/drive/request/mydocument/{id}/{token}/{folderid}/folder/download', [Controllers\FolderController::class, 'download'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/departmentdocument/{id}/{token}/{folderid}/folder/download', [Controllers\FolderController::class, 'download'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/shareddocument/folder/download/{id}/{token}/{folderid}', [Controllers\SharedFolderController::class, 'download'])->withoutMiddleware(['csrf']);

        //deleting files
        Route::get('/drive/request/mydocument/{id}/{token}/delete/{fileid}', [Controllers\Drive\MyDocumentController::class, 'delete'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/mydocument/{id}/{token}/delete/folder/{folderid}', [Controllers\FolderController::class, 'delete'])->withoutMiddleware(['csrf']);

        Route::get('/drive/request/shareddocument/{id}/{token}/delete/{fileid}', [Controllers\SharedFileController::class, 'delete'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/shareddocument/{id}/{token}/delete/folder/{folderid}', [Controllers\SharedFolderController::class, 'delete'])->withoutMiddleware(['csrf']);

        //query
        Route::get('/drive/request/mydocument/{id}/{token}/{query}/{usertype}', [Controllers\UserController::class, 'queryUser'])->withoutMiddleware(['csrf']);
        Route::get('/drive/request/departmentdocument/{id}/{token}/{query}', [Controllers\UserController::class, 'queryDepartmentUser'])->withoutMiddleware(['csrf']);

        //Get type of user
        Route::get('/drive/request/usertype/{id}/{token}', [Controllers\UserController::class, 'getUserType'])->withoutMiddleware(['csrf']);

        //versionize file
        Route::post('/drive/request/mydocument/{id}/{token}/version/file/change', [Controllers\FileController::class, 'versionize'])->withoutMiddleware(['csrf']);

        //add new file version
        Route::post('/drive/request/mydocument/{id}/{token}/version/file/create', [Controllers\FileController::class, 'addNewVersion'])->withoutMiddleware(['csrf']);

        //update filename and keywords
        Route::post('/drive/request/mydocument/{id}/{token}/file/update', [Controllers\FileController::class, 'updateFile'])->withoutMiddleware(['csrf']);

    });

});



//NO-LOGIN PAGES

Route::get('/Register', [Controllers\AuthControllers\RegistrationController::class, 'index'])->name('registration');
Route::POST('/Register', [Controllers\AuthControllers\RegistrationController::class, 'store'])->name('registration.store');

Route::get('/Login', [Controllers\AuthControllers\LoginController::class, 'index'])->name('login');

Route::post('/Login', [Controllers\AuthControllers\LoginController::class, 'login'])->name('login.logging');

Route::get('/ForgetPassword', [Controllers\AuthControllers\LoginController::class, 'index'])->name('forgetpassword');

Route::get('/Impressum', function () { return view('Policy.Impressum');})->name('impressum');
Route::get('/Policy', function () { return view('Policy.Policy');})->name('policy');

Route::get('/lang', function () {
    if (App::isLocale('en')) {
        Cookie::queue( Cookie::make('lang', 'de') );

    }
    else{
        Cookie::queue( Cookie::make('lang', 'en') );
    }
    return back();
})->name('changeLanguage');

Route::get('/langEn', function () {
    Cookie::queue( Cookie::make('lang', 'en') );

    return redirect()->route('docs.redirect');
})->name('changeLanguageToEn');

Route::get('/langDe', function () {
    Cookie::queue( Cookie::make('lang', 'de') );

    return redirect()->route('docs.redirect');
})->name('changeLanguageToDe');



