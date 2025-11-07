<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Services\Social\SocialProviderService;
use App\Support\SystemLogger;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(Request $request, string $provider)
    {
        // $this->authorize('contenthub.manage');

// dd($provider,$request->all());
        $scopes = SocialProviderService::scopesFor($provider);
        if (!$scopes) abort(404, 'Provider not supported');

        // Add state if you want to tie to user/session (Socialite handles CSRF state internally)
        return Socialite::driver($provider)
            ->scopes($scopes)
            ->with(['prompt' => 'consent']) // for Google-like providers
            ->redirect();
    }

    public function callback(Request $request, string $provider, SocialProviderService $svc)
    {
        // $this->authorize('contenthub.manage');

        // try {
            // dd($request->all(),$provider,$svc);
            $user = Socialite::driver($provider)->stateless()->user();
          

            // save tokens + metadata
            $conn = $svc->storeOrUpdateConnection($provider, $user);
          

            // SystemLogger::log('social_connect', [
            //     'provider' => $provider,
            //     'account_id' => $conn->account_id,
            //     'account_name' => $conn->account_name,
            // ], optional($request->user())->id);

            return redirect()->route('admin.content-hub.connections')
                ->with('status', ucfirst($provider).' connected: '.$conn->account_name);
        // } catch (Throwable $e) {
        //     // SystemLogger::log('connection_error', [
        //     //     'provider' => $provider,
        //     //     'error' => $e->getMessage(),
        //     // ], optional($request->user())->id);

        //     return redirect()->route('admin.content-hub.connections')
        //         ->withErrors(['oauth' => 'Connection failed: '.$e->getMessage()]);
        // }
    }
}
