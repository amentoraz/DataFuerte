<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use Illuminate\Support\Str;

class ElementController extends Controller
{

    // *******************************************
    //               Password zone
    // *******************************************


    public function index(Request $request, $uuid = 0)
    {        
        
        // Retrieve all elements related with current user
        $elements = Element::where('user_id', $request->user()->id)
            ->where('parent', $uuid)
            ->orderBy('key', 'asc')            
            ->paginate(10);       

        // If $uuid != 0, we are not in the root folder
        if ($uuid != 0) {
            // So we add the parent of the parent element to the elements array as "../"
            $parent = Element::find($uuid);
            if ($parent->parent == 0) {
                $parent->key = "../";
                $parent->uuid = 0;
                $elements->prepend($parent);
            } else {
                $parentOfParent = Element::find($parent->parent);
                $parentOfParent->key = "../";
                $elements->prepend($parentOfParent);
            }
        }

        $user = $request->user();
        $activeTab = 'elements';

        return view('account.elements', compact('elements', 'user', 'uuid', 'activeTab'));

    }


    public function store(Request $request)
    {
        try {
            switch($request->element_type_id) {
                case 1:
                    $request->validate([
                        'key' => 'required|string|max:255',
                        'passwordEncrypted' => 'required|string',
                        'iv' => 'required|string',
                        'salt' => 'required|string',
                        'hmac' => 'required|string',
                    ]);
                    break;
                case 4:
                    $request->validate([
                        'key' => 'required|string|max:255',
                    ]); 
                    break;
                default:
                    return redirect()->route('account.elements')->with('error', 'Invalid element type.');
            }
        } catch (\Exception $e) {
            return redirect()->route('account.elements')->with('error', 'Error adding element: ' . $e->getMessage());
        }

        // Create the element
        switch($request->element_type_id) {
            case 1:
                $element = new Element();
                $element->uuid = Str::uuid();
                $element->key = $request->key;
                $element->content = $request->passwordEncrypted;
                $element->iv = $request->iv;
                $element->salt = $request->salt;
                $element->hmac = $request->hmac;
                $element->user_id = $request->user()->id;
                $element->element_type_id = 1;
                $element->parent = $request->parent;
                $element->save();
                break;
            case 4: 
                $element = new Element();
                $element->uuid = Str::uuid();
                $element->key = $request->key;
                $element->content = "";
                $element->iv = "";
                $element->salt = "";
                $element->hmac = "";
                $element->user_id = $request->user()->id;
                $element->element_type_id = 4;
                $element->parent = $request->parent;
                $element->save();
                break;
        }

        return redirect()->route('account.elements', ['uuid' => $request->parent]);
    }


    public function delete(Request $request, $uuid)
    {
        $element = Element::find($uuid);
        $element->delete();
        return redirect()->route('account.elements')->with('success', 'Element removed.');;
    }

    public function get(Request $request, $uuid) 
    {
        // We get password data from database (only if it belongs to current user)
        $element = Element::find($uuid);
        if ($element->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // We print it in JSON
        return response()->json($element);
    }



}