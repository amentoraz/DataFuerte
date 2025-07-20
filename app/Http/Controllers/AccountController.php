<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Password;
use App\Models\Text;

class AccountController extends Controller
{
    public function passwords(Request $request)
    {
        
        $passwords = Password::with('user')->paginate(10);       

        $user = $request->user();
        $activeTab = 'passwords';

        return view('account.passwords', compact('passwords', 'user', 'activeTab'));

    }

    public function texts(Request $request)
    {
        $texts = Text::with('user')->paginate(10); 

        $user = $request->user();
        $activeTab = 'texts';

        return view('account.texts', compact('texts', 'user', 'activeTab'));
    }
}