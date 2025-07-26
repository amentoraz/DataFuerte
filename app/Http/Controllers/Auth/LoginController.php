<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
/*            
            // We log the login success
            $log = new Log();
            $log->user_id = Auth::user()->id;
            $log->action = 'login';
            $log->loggable_type = 'App\Models\User';
            $log->loggable_id = null;
            $log->data = "Login successful for email: " . $request->email;
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->save();
*/            

            $request->session()->regenerate();
            //return redirect()->intended('/myaccount/elements');
            return redirect()->to('/myaccount/elements');
        }

        // We log the login attempt
        /*
        $log = new Log();
        $log->user_id = null;
        $log->action = 'login';
        $log->loggable_type = 'App\Models\User';
        $log->loggable_id = null;
        $log->data = "Login attempt failed for email: " . $request->email;
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->severity = 'WARNING';
        $log->save();
        */

        return back()->withErrors([
            'email' => 'Credenciales inválidas',
        ])->withInput();
    }
}
