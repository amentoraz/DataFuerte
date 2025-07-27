<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Element;
use App\Models\Configuration;
use Illuminate\Support\Str;
use App\Models\Log;
use Illuminate\Support\Facades\DB; 

class ElementController extends Controller
{

    public function index(Request $request, $uuid = "0")
    {        
       
        // Retrieve all elements related with current user
        $elements = Element::where('user_id', $request->user()->id)
            ->where('parent', $uuid)
            ->orderBy('key', 'asc')
            ->select('*') // Select all existing columns
            ->selectSub(function ($query) {
                $query->select(DB::raw('count(*) > 0'))
                    ->from('elements as children')
                    ->whereColumn('children.parent', 'elements.uuid');
            }, 'has_children') // Add a new column 'has_children'
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

        // Current directory
        $currentDirUuid = $uuid;        
        $currentDirectory = "";
        while ($currentDirUuid != 0) {
            $currentDirectory = Element::find($currentDirUuid)->key."/".$currentDirectory;
            $currentDirUuid = Element::find($currentDirUuid)->parent;
        }
        $currentDirectory = "/".$currentDirectory;


        // Get iterations from configuration
        $iterations = Configuration::where('user_id', $request->user()->id)->where('parameter', 'iterations')->first()->value;

        // Get user data
        $user = $request->user();

        // Get active tab
        $activeTab = 'elements';

        return view('account.elements', compact('elements', 'user', 'uuid', 'activeTab', 'iterations', 'currentDirectory'));

    }


    public function store(Request $request)
    {
   
        try {
            switch($request->element_type_id) {
                case 1:
                case 2:
                    $request->validate([
                        'key' => 'required|string|max:255',
                        'passwordEncrypted' => 'required|string',
                        'iv' => 'required|string',
                        'salt' => 'required|string',
                        'hmac' => 'required|string',
                        'iterations' => 'required|integer',
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
//dd($e);            
            return redirect()->route('account.elements')->with('error', 'Error adding element: ' . $e->getMessage());
        }

        // Create the element
        switch($request->element_type_id) {
            case 1:
            case 2:
                $element = new Element();
                $element->uuid = Str::uuid();
                $element->key = $request->key;
                $element->content = $request->passwordEncrypted;
                $element->iv = $request->iv;
                $element->salt = $request->salt;
                $element->hmac = $request->hmac;
                $element->user_id = $request->user()->id;
                $element->element_type_id = $request->element_type_id;
                $element->parent = $request->parent;
                $element->iterations = $request->iterations;
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
                $element->iterations = 0;
                $element->save();
                break;
        }

        return redirect()->route('account.elements', ['uuid' => $request->parent]);
    }


    public function delete(Request $request, $uuid)
    {
        // We delete element data from database (only if it belongs to current user)
        $element = Element::find($uuid);
        if ($element->user_id !== $request->user()->id) {

            $log->user_id = $request->user()->id;
            $log->action = 'delete';
            $log->loggable_type = 'App\Models\Element';
            $log->loggable_id = $uuid;
            $logData = [
                'message' => "Unauthorized deletion of element $uuid",
                'element_id' => $uuid,
            ];
            $log->data = json_encode($logData); // Convert the array to a JSON string
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->severity = 'WARNING';
            $log->save();

            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $element = Element::find($uuid);
        $element->delete();
        return redirect()->route('account.elements')->with('success', 'Element removed.');;
    }

    public function get(Request $request, $uuid) 
    {

        // We log the access to the element
        $log = new Log();
        $log->user_id = $request->user()->id;
        $log->action = 'get';
        $log->loggable_type = 'App\Models\Element';
        $log->loggable_id = $uuid;
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->save();

        // We get password data from database (only if it belongs to current user)
        $element = Element::find($uuid);
        if ($element->user_id !== $request->user()->id) {

            $log->user_id = $request->user()->id;
            $log->action = 'get';
            $log->loggable_type = 'App\Models\Element';
            $log->loggable_id = $uuid;
            $logData = [
                'message' => "Unauthorized access to element $uuid",
                'element_id' => $uuid,
            ];
            $log->data = json_encode($logData); // Convert the array to a JSON string
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->severity = 'WARNING';
            $log->save();


            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // We print it in JSON
        return response()->json($element);
    }



}