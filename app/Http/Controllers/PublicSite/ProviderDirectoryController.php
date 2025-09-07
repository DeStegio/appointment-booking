<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderDirectoryController extends Controller
{
    public function index(Request $r)
    {
        $q = trim($r->input('q', ''));
        $providers = User::providers()->active()
            ->when($q, function ($qq) use ($q) {
                return $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('public/providers/index', compact('providers', 'q'));
    }

    public function show(User $provider)
    {
        abort_unless(strcasecmp((string) $provider->role, 'provider') === 0, 404);
        abort_unless((bool) $provider->is_active === true, 404);
        $services = $provider->services()->orderBy('name')->paginate(10);

        return view('public/providers/show', compact('provider', 'services'));
    }
}
