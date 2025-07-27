<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Support\Str;

class ConfigurationController extends Controller
{

    public function index(Request $request)
    {
        $configuration = Configuration::where('user_id', $request->user()->id)->get();
        $user = $request->user();
        $activeTab = 'configuration';

        // Check if installed already
        $installed = Configuration::where('user_id', $request->user()->id)->where('parameter', 'installed')->first();

        // Number of iterations
        $iterations = Configuration::where('user_id', $request->user()->id)->where('parameter', 'iterations')->first();

        // Number of elements per page
        $elements_per_page = Configuration::where('user_id', $request->user()->id)->where('parameter', 'elements_per_page')->first();

        return view('configuration.index', compact('configuration', 'user', 'activeTab', 'installed', 
            'iterations', 'elements_per_page'));
    }

    public function store(Request $request)
    {

        try {
            // Obtenemos iterations y lo validamos como integer
            $request->validate([
                'iterations' => 'required|integer|min:100000',
                'elements_per_page' => 'required|integer|min:1',
            ]);
            
            // If not exists, create a new iterations entry
            $existingIterations = Configuration::where('user_id', $request->user()->id)->where('parameter', 'iterations')->first();
            if (!$existingIterations) {
                $configuration = new Configuration();
                $configuration->user_id = $request->user()->id;
                $configuration->parameter = 'iterations';
                $configuration->value = $request->iterations;
                $configuration->save();
            } else {
                $existingIterations->value = $request->iterations;
                $existingIterations->save();
            }

            // If not exists, create a new pagination entry
            $existingPagination = Configuration::where('user_id', $request->user()->id)->where('parameter', 'elements_per_page')->first();
            if (!$existingPagination) {
                $configuration = new Configuration();
                $configuration->user_id = $request->user()->id;
                $configuration->parameter = 'elements_per_page';
                $configuration->value = $request->elements_per_page;
                $configuration->save();
            } else {
                $existingPagination->value = $request->elements_per_page;
                $existingPagination->save();
            }

            // Now for the "installed" parameter, we need to set it to 1
            $existingInstalled = Configuration::where('user_id', $request->user()->id)->where('parameter', 'installed')->first();
            if (!$existingInstalled) {
                $installed = new Configuration();
                $installed->user_id = $request->user()->id;
                $installed->parameter = 'installed';
                $installed->value = 1;
                $installed->save();
            } else {
                $existingInstalled->value = 1;
                $existingInstalled->save();
            }

            if (!$existingInstalled) {
                return redirect()->route('account.elements')->with('success', 'Configuration saved successfully. You may now begin to use the platform.');
            } else {
                return redirect()->route('configuration.index')->with('success', 'Configuration saved successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Error saving configuration: ' . $e->getMessage());
        }
    }
}