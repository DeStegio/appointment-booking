<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderScheduleRequest;
use App\Http\Requests\UpdateProviderScheduleRequest;
use App\Models\ProviderSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderScheduleController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providerId = Auth::id();
        $schedules = ProviderSchedule::query()
            ->where('provider_id', $providerId)
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->paginate(10);

        return view('provider.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schedule = new ProviderSchedule();
        return view('provider.schedules.create', compact('schedule'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProviderScheduleRequest $request)
    {
        $data = $request->validated();
        $data['provider_id'] = Auth::id();

        ProviderSchedule::create($data);

        return redirect()
            ->route('provider.schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProviderSchedule $providerSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProviderSchedule $providerSchedule)
    {
        if ($providerSchedule->provider_id !== Auth::id()) {
            abort(403);
        }

        $schedule = $providerSchedule;
        return view('provider.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProviderScheduleRequest $request, ProviderSchedule $providerSchedule)
    {
        if ($providerSchedule->provider_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();
        // Ensure provider_id cannot be changed via update
        unset($data['provider_id']);

        $providerSchedule->update($data);

        return redirect()
            ->route('provider.schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProviderSchedule $providerSchedule)
    {
        if ($providerSchedule->provider_id !== Auth::id()) {
            abort(403);
        }

        $providerSchedule->delete();

        return redirect()
            ->route('provider.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
