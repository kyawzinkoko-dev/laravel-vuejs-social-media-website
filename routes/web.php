<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
//dashboard
Route::get('/', [HomeController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');
/**
 * Public profile route
 */
Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile');
//public group 
Route::get('/g/{group:slug}',[GroupController::class,'profile'])->name('group.profile');
Route::middleware('auth')->group(function () {
    //prifile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/update-image', [ProfileController::class, 'updateImage'])->name('profile.updateImage');
    //goroup section
    Route::post('group',[GroupController::class,'store'])->name('group.create');
    Route::put('/group/{group:slug}',[GroupController::class,'update'])->name('group.update');
    Route::post('/group/update-image/{group:slug}', [GroupController::class,'updateImage'])->name('group.updateImage');
    Route::post("/group/invite/{group:slug}",[GroupController::class,'inviteUser'])->name('group.inviteUser');
    Route::get('/group/approve-invitation/{token}',[GroupController::class,'approveInvitation'])->name('group.approveInvitation');
    Route::post('/group/join/{group:slug}',[GroupController::class,'joinGroup'])->name('group.joinGroup');
    Route::post('/group/aprove-request/{group:slug}',[GroupController::class,'approveRequest'])->name('group.approveRequest');
    Route::post('/group/change-role/{group:slug}',[GroupController::class,'changeRole'])->name('group.changeRole');
    Route::delete('/group/delete-user/{group:slug}',[GroupController::class,'deleteUser'])->name('group.deleteUser');
    //post section 

    Route::post('/post', [PostController::class, 'store'])->name('post.create');
    Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/post/download/{attachment}',[PostController::class,'downloadAttachments'])->name('post.download');
    Route::post('/post/{post}/reaction',[PostController::class,'postReaction'])->name('post.reaction');
    //posts'comment 
    Route::post('/post/{post}/comment',[PostController::class,'createComment'])->name('comment.create');
    Route::delete('/comment/{comment}',[PostController::class,'deleteComment'])->name('comment.delete');
    Route::put('/comment/{comment}',[PostController::class,'updateComment'])->name('comment.update');
    Route::post('/comment/reaction/{comment}',[PostController::class,'commentReaction'])->name('comment.reaction');
});


require __DIR__.'/auth.php';
