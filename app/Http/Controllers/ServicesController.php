<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index() 
    {
        $services = Service::paginate(10)->withQueryString();
        return view('admin.services', [
            'services' => $services,
        ]);
        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_instructions' => 'nullable|string',
            'allowedFileExtension' => 'nullable|array',
            'allowedFileExtension.*' => 'nullable|string',
            'max-size' => 'nullable|numeric',
        ]);

        $requireUpload = $request->requireUpload;

        if ($requireUpload==false) {
            $validated['allowedFileExtension'] = null;
            $validated['max-size'] = null;
        }
    
        $service = Service::create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'],
            'service_instructions' => $validated['service_instructions'],
            'requireUpload' => $requireUpload,
            'allowed_file_extensions' => $validated['allowedFileExtension'],
            'max_file_size' => $validated['max-size'],
        ]);
    
        return redirect()->route('appointments.services')->with('success', 'Service added successfully!');
    }

    public function update(Request $request, $serviceId)
    {
        $validated = $request->validate([
            'update_service_name' => 'required|string|max:255',
            'update_description' => 'nullable|string',
            'update_service_instructions' => 'nullable|string',
            'update_allowedFileExtension' => 'nullable|array',
            'update_allowedFileExtension.*' => 'nullable|string',
            'update_max-size' => 'nullable|numeric',
        ]);

        $requireUpload = $request->updateRequireUpload;

        if ($requireUpload==false) {
            $validated['update_allowedFileExtension'] = null;
            $validated['update_max-size'] = null;
        }

        $service = Service::findOrFail($serviceId);
        $service->update([
            'service_name' => $validated['update_service_name'],
            'description' => $validated['update_description'],
            'service_instructions' => $validated['update_service_instructions'],
            'requireUpload' => $requireUpload,
            'allowed_file_extensions' => $validated['update_allowedFileExtension'],
            'max_file_size' => $validated['update_max-size'],
        ]);

        return redirect()->route('appointments.services')->with('success', 'Service updated successfully!');
    }

    public function delete($id)
    {
        $service = Service::find($id);
        if($service)
        {
            $service->delete();
            return redirect()->back()->with('success','Service has been deleted.');
        }
        else {
            return redirect()->back()->with('error','Service not found');
        }
    }

    public function all_services_json()
    {
        $services = Service::all();
        return response()->json($services);
    }
}
