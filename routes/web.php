<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use PHPUnit\Framework\Attributes\PostCondition;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
//home page 
Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
/**
 * Public profile route
 */
Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile');
//public group 
Route::get('/g/{group:slug}', [GroupController::class, 'profile'])->name('group.profile');

Route::middleware('auth')->group(function () {
    //group 
    Route::prefix('/group')->group(function () {
        Route::post('/', [GroupController::class, 'store'])->name('group.create');
        Route::put('/{group:slug}', [GroupController::class, 'update'])->name('group.update');
        Route::post('/update-image/{group:slug}', [GroupController::class, 'updateImage'])->name('group.updateImage');
        Route::post("/invite/{group:slug}", [GroupController::class, 'inviteUser'])->name('group.inviteUser');
        Route::get('/approve-invitation/{token}', [GroupController::class, 'approveInvitation'])->name('group.approveInvitation');
        Route::post('/join/{group:slug}', [GroupController::class, 'joinGroup'])->name('group.joinGroup');
        Route::post('/aprove-request/{group:slug}', [GroupController::class, 'approveRequest'])->name('group.approveRequest');
        Route::post('/change-role/{group:slug}', [GroupController::class, 'changeRole'])->name('group.changeRole');
        Route::delete('/delete-user/{group:slug}', [GroupController::class, 'deleteUser'])->name('group.deleteUser');
    });
     //prifile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/update-image', [ProfileController::class, 'updateImage'])->name('profile.updateImage');
    });
    //post
    Route::prefix('post')->group(function(){
        Route::get('/{post}', [PostController::class, 'view'])->name('post.view');
        Route::post('/', [PostController::class, 'store'])->name('post.create');
        Route::put('/{post}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('post.destroy');
        Route::get('/download/{attachment}', [PostController::class, 'downloadAttachments'])->name('post.download');
        Route::post('/{post}/reaction', [PostController::class, 'postReaction'])->name('post.reaction');

        //commentcreate
        Route::post('/{post}/comment', [PostController::class, 'createComment'])->name('comment.create');
         });
    //comment
  Route::prefix('comment')->group(function(){
    Route::delete('/{comment}', [PostController::class, 'deleteComment'])->name('comment.delete');
    Route::put('/{comment}', [PostController::class, 'updateComment'])->name('comment.update');
    Route::post('/reaction/{comment}', [PostController::class, 'commentReaction'])->name('comment.reaction');
  }); 
   //global search 
   Route::get('/search/{search?}',[SearchController::class,'search'])->name('search');
  
   
  //user controller 
  Route::post('/user/follow/{user}',[UserController::class,'follow'])->name('user.follow');

  
});


require __DIR__ . '/auth.php';
