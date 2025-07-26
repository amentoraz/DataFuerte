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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }


    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            
            // We log the login success
            $log = new Log();
            $log->user_id = Auth::user()->id;
            $log->action = 'login';
            $log->loggable_type = 'App\Models\User';
            $log->loggable_id = null;
            // Convert the data to an array and then to JSON
            $logData = [
                'message' => "Login successful for email: " . $request->email,
                'email' => $request->email, // You can add more relevant data here
            ];
            $log->data = json_encode($logData); // Convert the array to a JSON string
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->save();
            

            $request->session()->regenerate();
            //return redirect()->intended('/myaccount/elements');
            return redirect()->to('/myaccount/elements');
        }

        // We log the login attempt
        
        $log = new Log();
        $log->user_id = null;
        $log->action = 'login';
        $log->loggable_type = 'App\Models\User';
        $log->loggable_id = null;
        $logData = [
            'message' => "Login attempt failed for email: " . $request->email,
            'email' => $request->email, // You can add more relevant data here
        ];
        $log->data = json_encode($logData); // Convert the array to a JSON string
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->severity = 'WARNING';
        $log->save();
        

        return back()->withErrors([
            'email' => 'Credenciales inválidas',
        ])->withInput();
    }
}
