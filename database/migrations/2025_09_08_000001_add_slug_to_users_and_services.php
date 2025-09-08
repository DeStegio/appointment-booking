<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Users: add nullable unique slug
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'slug')) {
                $t->string('slug')->nullable()->after('name');
                $t->unique('slug');
            }
        });

        // Services: add nullable unique slug
        Schema::table('services', function (Blueprint $t) {
            if (!Schema::hasColumn('services', 'slug')) {
                $t->string('slug')->nullable()->after('name');
                $t->unique('slug');
            }
        });

        // Backfill slugs for existing providers and services
        $this->backfillUserSlugs();
        $this->backfillServiceSlugs();
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $t) {
            if (Schema::hasColumn('services', 'slug')) {
                $t->dropUnique(['slug']);
                $t->dropColumn('slug');
            }
        });

        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'slug')) {
                $t->dropUnique(['slug']);
                $t->dropColumn('slug');
            }
        });
    }

    private function backfillUserSlugs(): void
    {
        if (!Schema::hasColumn('users', 'slug')) {
            return;
        }

        $providers = DB::table('users')
            ->where('role', 'provider')
            ->whereNull('slug')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        foreach ($providers as $p) {
            $base = Str::slug((string) $p->name);
            if ($base === '') {
                $base = 'provider-' . $p->id;
            }
            $slug = $base;
            $suffix = 2;
            while (DB::table('users')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            DB::table('users')->where('id', $p->id)->update(['slug' => $slug]);
        }
    }

    private function backfillServiceSlugs(): void
    {
        if (!Schema::hasColumn('services', 'slug')) {
            return;
        }

        $services = DB::table('services')
            ->whereNull('slug')
            ->select('id', 'provider_id', 'name')
            ->orderBy('id')
            ->get();

        foreach ($services as $s) {
            $base = Str::slug((string) ($s->provider_id . '-' . $s->name));
            if ($base === '') {
                $base = 'service-' . $s->id;
            }
            $slug = $base;
            $suffix = 2;
            while (DB::table('services')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            DB::table('services')->where('id', $s->id)->update(['slug' => $slug]);
        }
    }
};

