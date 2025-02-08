<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\DailyInventoryTask;
use App\Models\Product;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    private function isAdminRole($role)
    {
        $adminRoles = ['Master Admin', 'Manager', 'SuperAdmin', 'SiteEditor'];
        return in_array($role, $adminRoles);
    }

    private function assignDailyInventoryTasks($adminId)
    {
        $settings = Admin::first();
        $inventoryItemCount = $settings->daily_inventory_items;
        $costThreshold = $settings->inventory_threshold;

      
        $products = Product::where('dni', false)
            ->where('cost', '>', $costThreshold)
            ->whereNotIn('id', DailyInventoryTask::where('staff_id', $adminId)
                ->whereDate('assigned_at', today())
                ->pluck('product_id'))
            ->orderBy('last_checked_date', 'asc') 
            ->limit($inventoryItemCount)
            ->get();

        foreach ($products as $product) {
            DailyInventoryTask::create([
                'staff_id' => $adminId, 
                'product_id' => $product->id,
                'status' => 'pending'
            ]);

            // Update last checked date
            $product->update(['last_checked_date' => now()]);
        }

    }
 
}
