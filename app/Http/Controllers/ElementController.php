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
       
        $elements_per_page = Configuration::where('user_id', $request->user()->id)->where('parameter', 'elements_per_page')->first();
        if (!$elements_per_page) {
            $elements_per_page = 10;
        } else {
            $elements_per_page = $elements_per_page->value;
        }
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
            ->paginate($elements_per_page);

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
                case 1: // Password
                case 2: // Text
                    $request->validate([
                        'key' => 'required|string|max:255',
                        'passwordEncrypted' => 'required|string',
                        'iv' => 'required|string',
                        'salt' => 'required|string',
                        'hmac' => 'required|string',
                        'iterations' => 'required|integer',
                    ]);
                    break;                
                case 3: // File
                    $request->validate([
                        'key' => 'required|string|max:255',
                        'file' => 'required|file',
                        'file_iv' => 'required|string',
                        'file_salt' => 'required|string',
                        'file_hmac' => 'required|string',
                        'iterations' => 'required|integer',
                    ]);
                    break;
                case 4: // Folder
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

        $element = new Element();
        $element->uuid = Str::uuid();
        $element->key = $request->key;
        $element->user_id = $request->user()->id;
        $element->element_type_id = $request->element_type_id;
        $element->parent = $request->parent;
        $element->iterations = $request->iterations ?? 0;

        switch($request->element_type_id) {
            case 1: // Password
            case 2: // Text
                $element->content = $request->passwordEncrypted;
                $element->iv = $request->iv;
                $element->salt = $request->salt;
                $element->hmac = $request->hmac;
                break;
                
            case 3: // File
                // Store the encrypted file
                $file = $request->file('file');
                $path = $file->storeAs(
                    'private/files/' . $request->user()->id,
                    $element->uuid . '.bin',
                    'local'
                );
                
                // Store file metadata
                $element->file_path = $path;
                $element->file_name = $file->getClientOriginalName();
                $element->file_size = $file->getSize();
                $element->file_mime = $file->getMimeType();
                $element->iv = $request->file_iv;
                $element->salt = $request->file_salt;
                $element->hmac = $request->file_hmac;
                $element->content = ''; // No content for files, stored separately
                break;
                
            case 4: // Folder
                $element->content = "";
                $element->iv = "";
                $element->salt = "";
                $element->hmac = "";
                $element->iterations = 0;
                break;
        }
        
        $element->save();

        return redirect()->route('account.elements', ['uuid' => $request->parent]);
    }


    public function delete(Request $request, $uuid)
    {
        // We delete element data from database (only if it belongs to current user)
        $element = Element::find($uuid);
        if ($element->user_id !== $request->user()->id) {
            $log = new Log();
            $log->user_id = $request->user()->id;
            $log->action = 'delete';
            $log->loggable_type = 'App\\Models\\Element';
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

        // If this is a file element, delete the physical file first
        if ($element->element_type_id === 3 && !empty($element->file_path)) {
            // Delete the file using Laravel's Storage facade
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($element->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($element->file_path);
                
                // Log the file deletion
                $log = new Log();
                $log->user_id = $request->user()->id;
                $log->action = 'delete_file';
                $log->loggable_type = 'App\\Models\\Element';
                $log->loggable_id = $uuid;
                $log->ip_address = $request->ip();
                $log->user_agent = $request->userAgent();
                $log->data = json_encode([
                    'file_path' => $element->file_path,
                    'file_name' => $element->file_name,
                    'file_size' => $element->file_size
                ]);
                $log->save();
            }
        }

        // Delete the database record
        $element->delete();
        
        return redirect()->route('account.elements')->with('success', 'Element removed.');
    }

    /**
     * Download an encrypted file
     */
    public function downloadFile(Request $request, $uuid)
    {
        $element = Element::findOrFail($uuid);
        
        // Check if the element belongs to the current user
        if ($element->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if the element is a file
        if ($element->element_type_id !== 3 || !$element->file_path) {
            abort(404, 'File not found');
        }
        
        // Get the file path
        $path = storage_path('app/' . $element->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found on server');
        }
        
        // Log the file download
        $log = new Log();
        $log->user_id = $request->user()->id;
        $log->action = 'download';
        $log->loggable_type = 'App\\Models\\Element';
        $log->loggable_id = $uuid;
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->data = json_encode([
            'file_name' => $element->file_name,
            'file_size' => $element->file_size,
            'file_mime' => $element->file_mime
        ]);
        $log->save();
        
        // Return the file for download
        return response()->download($path, $element->file_name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $element->file_name . '"',
            'X-File-Name' => $element->file_name,
            'X-File-Size' => $element->file_size,
            'X-File-Mime' => $element->file_mime,
            'X-File-IV' => $element->iv,
            'X-File-Salt' => $element->salt,
            'X-File-Hmac' => $element->hmac,
            'X-File-Iterations' => $element->iterations,
        ]);
    }
    
    /**
     * Get element data (for passwords and text)
     */
    public function get(Request $request, $uuid) 
    {
        $element = Element::findOrFail($uuid);
        
        // Check if the element belongs to the current user
        if ($element->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if the element is a file (should be handled by downloadFile)
        if ($element->element_type_id === 3) {
            abort(400, 'Use the download endpoint for files');
        }

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
        // We return a custom JSON response that matches the client's expectations
        return response()->json([
            'password' => $element->content,
            'iv' => $element->iv,
            'salt' => $element->salt,
            'hmac' => $element->hmac,
            'iterations' => $element->iterations
        ]);
    }



}