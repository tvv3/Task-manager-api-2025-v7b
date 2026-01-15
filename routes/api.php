<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TasksCommentsController;
use App\Http\Controllers\TasksUsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
// --- Auth Routes ---
// Get CSRF cookie for SPA
//////Route::post('/registerAdmin',[AuthController::class, 'registerAdmin']);
//the route registerAdmin must be kept but commented !!!!

//login route moved to web.api
// without login and logout placed in web.php
Route::post('/registerUser', [AuthController::class, 'registerUser'])->middleware('auth:sanctum');
Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

// --- User Info ---
/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return [
            'user' => User::where('id','=',$request->user()->id)->with('userRole')->first()
    ];
            //must put first not get or else it will bring [0]=> data instead of just data

});*/



//Route::post('/login', [AuthController::class, 'login']);
//use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])
    ->get('/user', function (Request $request) {
        return ['user' => $request->user()->load('userRole')];
    });*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    if (!$request->user()) {
        return response()->json(['user' => null], 200);
    }
    return [
        'user' => $request->user()->load('userRole')
    ];
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    /*
Route::get('/user', function (Request $request) {
    $user = $request->user()->load('userRole'); // load relation on the model
    return response()->json(['user' => $user]);
});//de pus auth sanctum !!!!!
*/
// --- Tasks ---
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('tasks', TaskController::class);
    Route::put('tasks/{task}/updateStatus', [TaskController::class, 'updateStatus']);

    Route::apiResource('tasksUsers', TasksUsersController::class)
        ->except(['index','show','update','destroy']);

    Route::get('/tasksUsers/potential-members/task/{task}', [TasksUsersController::class,'other_potential_users_for_task']);
    Route::delete('/tasksUsers/delete/task/{task}/user/{user}', [TasksUsersController::class,'destroy2']);

    Route::apiResource('tasksComments', TasksCommentsController::class)
        ->except(['index']);
    Route::get('/tasksComments/list/{task}', [TasksCommentsController::class,'list_comments_for_task']);
});
?>