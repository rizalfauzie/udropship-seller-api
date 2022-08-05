<?php

namespace App\Providers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {

            $token = $request->bearerToken();

            if (empty($token)) {
                return null;
            }

            $vendor = Vendor::whereRaw("MD5(CONCAT(vendor_id,'$',email)) = ?", $token)
                ->where('enable_api', 1)
                ->where('status', 'A')
                ->first();

            if ($vendor && $vendor->vendor_id) {
                return $vendor;
            }

            return null;
        });
    }
}
