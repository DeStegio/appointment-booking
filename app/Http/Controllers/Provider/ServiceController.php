<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:provider']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::where('provider_id', Auth::id())
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('provider.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('provider.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $validated = $request->validated();
        $validated['provider_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active');

        Service::create($validated);

        session()->flash('status', 'Service created successfully.');

        return redirect()->route('provider.services.index');
    }

    /**
     * Display the specified resource.
     */
    // No show method per requirements

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        if ($service->provider_id !== Auth::id()) {
            abort(403);
        }

        return view('provider.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        if ($service->provider_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['provider_id']);

        $service->update($validated);

        session()->flash('status', 'Service updated successfully.');

        return redirect()->route('provider.services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        if ($service->provider_id !== Auth::id()) {
            abort(403);
        }

        $service->delete();

        session()->flash('status', 'Service deleted successfully.');

        return redirect()->route('provider.services.index');
    }
}
