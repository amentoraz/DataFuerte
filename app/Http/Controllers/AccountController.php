<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Password;
use App\Models\Text;

class AccountController extends Controller
{

    // *******************************************
    //               Password zone
    // *******************************************


    public function passwords(Request $request)
    {
        
        $passwords = Password::with('user')->paginate(10);       

        $user = $request->user();
        $activeTab = 'passwords';

        return view('account.passwords', compact('passwords', 'user', 'activeTab'));

    }


    public function storePassword(Request $request)
    {
        try {
        $request->validate([
            'key' => 'required|string|max:255',
            'passwordEncrypted' => 'required|string',
            'iv' => 'required|string',
            'salt' => 'required|string',
        ]);
        } catch (\Exception $e) {
            return redirect()->route('account.passwords')->with('error', 'Error adding password: ' . $e->getMessage());
        }

        $password = new Password();
        $password->key = $request->key;
        $password->content = $request->passwordEncrypted;
        $password->iv = $request->iv;
        $password->salt = $request->salt;
        $password->user_id = $request->user()->id;
        $password->save();

        return redirect()->route('account.passwords');
    }


    public function deletePassword(Request $request, $id)
    {
        $password = Password::find($id);
        $password->delete();
        return redirect()->route('account.passwords')->with('success', 'Password removed.');;
    }

    public function getPasswordData($id) 
    {
        $password = Password::find($id);
        // We print it in JSON
        return response()->json($password);
    }



    // *******************************************
    //                  Texts zone
    // *******************************************

    public function texts(Request $request)
    {
        $texts = Text::with('user')->paginate(10); 

        $user = $request->user();
        $activeTab = 'texts';

        return view('account.texts', compact('texts', 'user', 'activeTab'));
    }

    public function storeText(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
        ]);

        $text = new Text();
        $text->key = $request->key;
        $text->content = "";
        $text->user_id = $request->user()->id;
        $text->save();

        return redirect()->route('account.texts');
    }

    public function deleteText(Request $request, $id)
    {
        $text = Text::find($id);
        $text->delete();
        return redirect()->route('account.texts')->with('success', 'Text removed.');;
    }
}