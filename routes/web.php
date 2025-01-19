<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\UserController; // Added this line
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::controller(MailController::class)->prefix('mail')->name('mail.')->group(function () {
        Route::get('/', 'index')->name('inbox');
    });
    Route::resource('projects', ProjectController::class);
    Route::post('project/team', [ProjectController::class, 'addMember'])->name('projects.addMember');
    Route::get('projects/{project}/tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');

    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus']);

    Route::resource('routines', RoutineController::class)->except(['show']);
    Route::get('routines/showAll', [RoutineController::class, 'showAll'])->name('routines.showAll');
    Route::get('routines/daily', [RoutineController::class, 'showDaily'])->name('routines.showDaily');
    Route::get('routines/weekly', [RoutineController::class, 'showWeekly'])->name('routines.showWeekly');
    Route::get('routines/monthly', [RoutineController::class, 'showMonthly'])->name('routines.showMonthly');
    Route::resource('files', FileController::class);
    Route::resource('notes', NoteController::class);
    Route::prefix('reminders')->name('reminders.')->middleware(['auth', 'member'])->group(function () {
        Route::get('/', [ReminderController::class, 'index'])->name('index')->withoutMiddleware('member');
        Route::get('/create', [ReminderController::class, 'create'])->name('create');
        Route::post('/', [ReminderController::class, 'store'])->name('store');
        Route::get('/{reminder}/edit', [ReminderController::class, 'edit'])->name('edit');
        Route::put('/{reminder}', [ReminderController::class, 'update'])->name('update');
        Route::delete('/{reminder}', [ReminderController::class, 'destroy'])->name('destroy');
        Route::get('/{reminder}', [ReminderController::class, 'show'])->name('show')->withoutMiddleware('member');
    });
    Route::resource('checklist-items', ChecklistItemController::class);
    Route::get('checklist-items/{checklistItem}/update-status', [ChecklistItemController::class, 'updateStatus'])->name('checklist-items.update-status');

    // Content routes
    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::post('/content', [ContentController::class, 'store'])->name('content.store');
    Route::put('/content/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::delete('/content/{id}', [ContentController::class, 'destroy'])->name('content.destroy');
    Route::post('/content/{id}/like', [ContentController::class, 'like'])->name('content.like');
    Route::post('/content/{id}/comment', [ContentController::class, 'comment'])->name('content.comment');
    Route::post('/content/{id}/view', [ContentController::class, 'view'])->name('content.view');
    Route::get('/content/normalization-divisors', [ContentController::class, 'showNormalizationDivisors'])
        ->name('content.normalization-divisors');
    Route::get('/content/normalized-matrix', [ContentController::class, 'showNormalizedMatrix'])
        ->name('content.normalized-matrix');
    Route::get('/content/weighted-normalized-matrix', [ContentController::class, 'weightedNormalizedMatrix'])->name('content.weighted-normalized-matrix');
    Route::get('/content/ideal-solutions', [ContentController::class, 'showIdealSolutions'])->name('content.ideal-solutions');
    Route::get('/content/separation-measures', [ContentController::class, 'showSeparationMeasures'])->name('content.separation-measures');
    Route::get('/content/relative-closeness', [ContentController::class, 'showRelativeCloseness'])->name('content.relative-closeness');
    Route::get('/topsis', [App\Http\Controllers\ContentController::class, 'topsis'])->name('topsis');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.dashboard');

    Route::get('/profile/edit', function () {
        return view('auth.edit');
    })->name('profile.edit');

    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
