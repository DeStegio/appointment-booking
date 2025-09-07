<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->input('q', ''));
        $providers = User::providers()
            ->when($q !== '', function ($qq) use ($q) {
                return $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.providers.index', compact('providers', 'q'));
    }

    public function toggle(User $provider)
    {
        abort_unless(strcasecmp((string) $provider->role, 'provider') === 0, 404);
        $provider->is_active = !$provider->is_active;
        $provider->save();
        return back()->with('status', 'Provider ' . ($provider->is_active ? 'enabled' : 'disabled'));
    }
}

