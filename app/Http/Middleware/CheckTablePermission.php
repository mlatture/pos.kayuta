<?php

namespace App\Http\Middleware;

use App\Models\WhitelistTable;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckTablePermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $table = $request->route('table');
        $user = Auth::user();

        $whitelist = WhitelistTable::where('table_name', $table)->first();
        if ($whitelist) {
            $requiredPermission = $this->getRequiredPermission($request->method());

//            if ($user->permission_level < $requiredPermission) {
//                return redirect('/home')->with('error', 'Unauthorized access');
//            }
        }

        return $next($request);
    }

    private function getRequiredPermission($method): int
    {
        return match ($method) {
            'POST', 'PUT' => 2,
            'DELETE' => 3,
            default => 1,
        };
    }
}
