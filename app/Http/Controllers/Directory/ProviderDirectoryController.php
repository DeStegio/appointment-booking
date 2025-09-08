<?php

namespace App\Http\Controllers\Directory;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProviderDirectoryController extends Controller
{
    public function index(Request $r)
    {
        $q = trim($r->string('q')->toString());

        $providers = User::query()
            ->where('role', 'provider')
            ->where('is_active', true);

        if ($q !== '') {
            $providers->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%")
                   ->orWhereExists(function ($sub) use ($q) {
                       $sub->selectRaw(1)
                           ->from('services')
                           ->whereColumn('services.provider_id', 'users.id')
                           ->where('services.is_active', true)
                           ->where('services.name', 'like', "%$q%");
                   });
            });
        }

        $providers = $providers
            ->withCount(['services' => function ($q) { $q->where('is_active', true); }])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('directory.providers.index', [
            'providers' => $providers,
        ]);
    }

    public function show(User $provider, Request $r, AvailabilityService $availability)
    {
        if (!(bool) $provider->is_active || strcasecmp((string) $provider->role, 'provider') !== 0) {
            abort(404);
        }

        $services = Service::query()
            ->where('provider_id', $provider->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedService = null;
        if ($r->filled('service')) {
            $serviceSlug = (string) $r->input('service');
            $selectedService = $services->firstWhere('slug', $serviceSlug);
        } else {
            $selectedService = $services->first();
        }

        $date = Carbon::parse($r->input('date', now()->toDateString()))->toDateString();
        $slots = [];
        if ($selectedService) {
            $slots = $availability->getSlots($provider->id, $selectedService->id, $date);
        }

        // Attach only active services to the provider relation for the view
        $provider->setRelation('services', $services);

        return view('directory.providers.show', [
            'provider' => $provider,
            'services' => $services,
            'service'  => $selectedService,
            'date'     => $date,
            'slots'    => $slots,
        ]);
    }

    public function slots(User $provider, Service $service, Request $r, AvailabilityService $availability)
    {
        if ((int) $service->provider_id !== (int) $provider->id) {
            abort(404);
        }
        if (!(bool) $provider->is_active || !(bool) $service->is_active) {
            abort(404);
        }

        $date = Carbon::parse($r->input('date', now()->toDateString()))->toDateString();
        $slots = $availability->getSlots($provider->id, $service->id, $date);

        return response()->json(['date' => $date, 'slots' => $slots]);
    }
}
