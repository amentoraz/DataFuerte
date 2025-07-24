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
        return view('configuration.index', compact('configuration', 'user'));
    }

    public function store(Request $request)
    {

        try {
            // Obtenemos iterations y lo validamos como integer
            $request->validate([
                'iterations' => 'required|integer|min:100000',
            ]);
            
            // If not exists, create a new configuration
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

            return redirect()->route('account.elements')->with('success', 'Configuration saved successfully');
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Error saving configuration: ' . $e->getMessage());
        }
    }
}