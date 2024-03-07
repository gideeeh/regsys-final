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
        ]);
    
        $service = Service::create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'],
        ]);
    
        return redirect()->route('appointments.services')->with('success', 'Service added successfully!');
    }

    public function update(Request $request, $serviceId)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'update_description' => 'nullable|string',
        ]);

        $service = Service::findOrFail($serviceId);
        $service->update([
            'service_name' => $validated['service_name'],
            'description' => $validated['update_description'],
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
