<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
echo ("OK!"); die;

        return view('account.index', ['user' => $request->user()]);
    }
}