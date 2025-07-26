<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Log;
use Illuminate\Support\Str;
class LogController extends Controller
{

    // *******************************************
    //               Password zone
    // *******************************************


    public function index(Request $request)
    {       
        // Only for superuser (user_id = 1)
        if ($request->user()->name != 'administrator') {
            return redirect()->route('account.elements')->with('error', 'You are not authorized to access this page.');
        }

        // Get logs
        $logs = Log::orderBy('created_at', 'desc')->paginate(30);

        // Get user data
        $user = $request->user();

        // Get active tab
        $activeTab = 'logs';

        return view('logs.index', compact('logs', 'user', 'activeTab'));
    }
}