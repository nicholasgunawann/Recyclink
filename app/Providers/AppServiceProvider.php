<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\WasteListing;
use App\Policies\WasteListingPolicy;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Models\Complaint;
use App\Policies\ComplaintPolicy;
use App\Models\EducationContent;
use App\Policies\EducationContentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::policy(WasteListing::class, WasteListingPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Complaint::class, ComplaintPolicy::class);
        Gate::policy(EducationContent::class, EducationContentPolicy::class);
    }
}
