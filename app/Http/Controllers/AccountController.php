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
        // Retrieve only passwords related with current user
        $passwords = Password::where('user_id', $request->user()->id)->paginate(10);       

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
        $password->hmac = $request->hmac;
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

    public function getPasswordData(Request $request, $id) 
    {
        // We get password data from database (only if it belongs to current user)
        $password = Password::find($id);
        if ($password->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // We print it in JSON
        return response()->json($password);
    }



    // *******************************************
    //                  Texts zone
    // *******************************************

    public function texts(Request $request)
    {
        $texts = Text::where('user_id', $request->user()->id)->paginate(10); 

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
        $text->content = $request->textEncrypted;
        $text->iv = $request->iv;
        $text->salt = $request->salt;
        $text->hmac = $request->hmac;
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

    public function getTextData($id) 
    {
        $text = Text::find($id);
        if ($text->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // We print it in JSON
        return response()->json($text);
    }
}