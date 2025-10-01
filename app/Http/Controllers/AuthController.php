<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function registerUser(Request $request)
    {
       Gate::allowIf(fn(User $user) => $user->isAdministrator() === true, 'You are not an administrator', 403);
 
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email'=> 'required|email|unique:users',
            'password'=>'required|min:8|confirmed'
        ]);


        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $ur = new UsersRole();
        $ur->user_id = $user->id;
        $ur->role = 'user';
        $ur->save();

        // optional auto-login new user
        /*Auth::login($user);
        $request->session()->regenerate();
         */

        $user = User::where('id','=',$user->id)->first();//must put first here otherwise it will put an array [0]=> data
        if ($user) {
        $user=$user->load('userRole'); 
        }
        else
        {
           return response()->json(['errors' => ['form' => ['User was not created!']]], 401);
        
        }
        return [
            'user' => $user,
            'message' => 'User registered successfully'
        ];
    }

    /**
     * Register an admin.
     */
    public function registerAdmin(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email'=> 'required|email|unique:users',
            'password'=>'required|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $ur = new UsersRole();
        $ur->user_id = $user->id;
        $ur->role = 'admin';
        $ur->save();

        // optional auto-login new admin
        /*Auth::login($user);
        $request->session()->regenerate();
        */
        $user=User::where('id','=',$user->id)->with('userRole')->first();
       
        return [
            'user' => $user,
            'message' => 'Admin registered successfully'
        ];
    }

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['errors' => ['email' => ['Wrong credentials!']]], 401);
        }

        //$request->session()->regenerate();//??
        $user_id=Auth::user()->id;
        $user = User::where('id','=',$user_id)->first();//must put first here otherwise it will put an array [0]=> data
        if ($user) {
        $user=$user->load('userRole'); 
        }
        else
        {
           return response()->json(['errors' => ['email' => ['No role defined!']]], 401);
        
        }

        return [
            'user' => $user,
            'message' => 'Login successful'
        ];
    }

    /**
     * Logout user, invalidate session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ['message' => 'You were logged out!'];
    }

    /**
     * Change password (only admins).
     */
    public function changePassword(Request $request)
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator() === true, 'You are not an administrator', 403);

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::where('email', '=', $request->email)->first();

        if (!$user) {
            return ['errors' => ['email'=> ["Wrong email!"]]];
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return [
            'user' => $user,
            'message' => 'Password changed successfully'
        ];
    }
}

?>
