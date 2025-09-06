<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeOffRequest;
use App\Http\Requests\UpdateTimeOffRequest;
use App\Models\TimeOff;
use Illuminate\Support\Facades\Auth;

class TimeOffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:provider']);
    }

    public function index()
    {
        $timeOffs = TimeOff::query()
            ->where('provider_id', Auth::id())
            ->orderBy('start_at', 'desc')
            ->paginate(10);

        return view('provider.time_offs.index', compact('timeOffs'));
    }

    public function create()
    {
        $timeOff = new TimeOff();
        return view('provider.time_offs.create', compact('timeOff'));
    }

    public function store(StoreTimeOffRequest $request)
    {
        $data = $request->validated();
        $data['provider_id'] = Auth::id();

        TimeOff::create($data);

        return redirect()
            ->route('provider.time-offs.index')
            ->with('success', 'Time off created successfully.');
    }

    public function edit(TimeOff $timeOff)
    {
        if ($timeOff->provider_id !== Auth::id()) {
            abort(403);
        }

        return view('provider.time_offs.edit', compact('timeOff'));
    }

    public function update(UpdateTimeOffRequest $request, TimeOff $timeOff)
    {
        if ($timeOff->provider_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();
        unset($data['provider_id']);

        $timeOff->update($data);

        return redirect()
            ->route('provider.time-offs.index')
            ->with('success', 'Time off updated successfully.');
    }

    public function destroy(TimeOff $timeOff)
    {
        if ($timeOff->provider_id !== Auth::id()) {
            abort(403);
        }

        $timeOff->delete();

        return redirect()
            ->route('provider.time-offs.index')
            ->with('success', 'Time off deleted successfully.');
    }
}

