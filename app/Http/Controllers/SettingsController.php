<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
class SettingsController extends Controller
{
    public function assignDailyInventoryTasks($staff_id)
    {
        $inventoryItemCount = Admin::first()->daily_inventory_items;
        $costThreshold = Admin::first()->inventory_threshold;

        $products = Product::where('dni', false)
            ->where('cost', '>', $costThreshold)
            ->where(function ($query) {
                $query->whereNull('last_checked_date')
                    ->orWhereDate('last_checked_date', '<', now());
            })
            ->orderBy('last_checked_date', 'asc')
            ->limit($inventoryItemCount)
            ->get();

        foreach ($products as $product) {
            DailyInventoryTask::creat([
                'staff_id' => $staff_id,
                'product_id' => $product->id,
                'status' => 'pending',
            ]);

            $product->update(['last_checked_date' => now()]);

        }

        return response()->json(['success' => true, 'tasks_assigned' => $products->count()]);

            
    }

    public function updateInventory(Request $request, $task_id)
    {
        $task = DailyInventoryTask::findOrFail($task_id);
        $product = $task->product;

        $oldQuantity = $product->quantity;
        $newQuantity = $request->updated_quantity;

        $product->update([
            'stock' => $newQuantity,
        ]);

        InventoryLog::create([
            'product_id' => $product->id,
            'staff_id' => $task->staff_id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'reason' => $request->reason, 'Routine Check',
        ]);

        $task->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Inventory updated successfully');
    }
}
