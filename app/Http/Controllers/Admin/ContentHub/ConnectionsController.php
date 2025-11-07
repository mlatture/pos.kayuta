<?php

namespace App\Http\Controllers\Admin\ContentHub;

use App\Http\Controllers\Controller;
use App\Models\ContentHub\SocialConnection;
use App\Support\SystemLogger;
use Illuminate\Http\Request;

class ConnectionsController extends Controller
{
    public function index()
    {
        // $this->authorize('contenthub.manage');
        $connections = SocialConnection::query()->orderBy('channel')->get();
        return view('admin.content_hub.connections', compact('connections'));
    }

    public function disconnect(Request $request, int $id)
    {
        // $this->authorize('contenthub.manage');

        $conn = SocialConnection::findOrFail($id);
        $label = $conn->channel.' / '.$conn->account_name;
        $conn->delete();

        // SystemLogger::log('social_disconnect', [
        //     'provider' => $conn->channel,
        //     'account_id' => $conn->account_id,
        //     'account_name' => $conn->account_name,
        // ], optional($request->user())->id);

        return back()->with('status', 'Disconnected: '.$label);
    }
}
