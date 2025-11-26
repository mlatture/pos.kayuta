<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SyndicatedContent;
use Illuminate\Http\Request;

class SyndicatedContentController extends Controller
{
    // GET /api/syndicated-content?status=pending
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $items = SyndicatedContent::where('status', $status)
            ->orderBy('id')
            ->limit(50) // safety limit
            ->get([
                'id',
                'tenant_id',
                'idea_id',
                'channel',
                'title',
                'body_md',
                'meta',
                'status',
                'created_at',
            ]);

        return response()->json($items);
    }

    // PATCH /api/syndicated-content/{id}
    // body: { "status": "processed" } or "deleted", etc.
    public function update(Request $request, SyndicatedContent $content)
    {
        $status = $request->input('status', 'processed');

        $content->status = $status;
        $content->save();

        return response()->json([
            'message' => 'updated',
            'id'      => $content->id,
            'status'  => $content->status,
        ]);
    }

    // OPTIONAL: DELETE /api/syndicated-content/{id}
    public function destroy(SyndicatedContent $content)
    {
        $content->delete();

        return response()->json([
            'message' => 'deleted',
            'id'      => $content->id,
        ]);
    }
}
