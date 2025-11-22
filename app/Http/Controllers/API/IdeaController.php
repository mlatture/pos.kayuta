<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContentIdea;
use App\Models\FeedbackLog;
use Illuminate\Http\Request;
// use App\Services\AI\ContentService; // abhi optional, replace me later
use App\Jobs\GeneratePostsFromIdeaJob;

class IdeaController extends Controller
{
    // GET /api/ideas
    public function index(Request $request)
    {
        // $tenantId = auth()->user()->tenant_id ?? 1;
        
              $tenantId = auth('admin')->user()->tenant_id ?? 1;  


        $ideas = ContentIdea::where('tenant_id', $tenantId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($ideas);
    }

    // POST /api/ideas/{idea}/approve
    public function approve(ContentIdea $idea)
    {
        $this->authorizeTenant($idea->tenant_id);

        $idea->update(['status' => 'approved']);

        FeedbackLog::create([
            'tenant_id' => $idea->tenant_id,
            'source'    => 'idea',
            'source_id' => $idea->id,
            'action'    => 'approve',
            'weight'    => 2,
        ]);

         GeneratePostsFromIdeaJob::dispatch($idea->id);

        // TODO: yahan baad me GeneratePostsFromIdea job dispatch karna hai
        // dispatch(new GeneratePostsFromIdea($idea->id));

        return response()->json(['status' => 'ok']);
    }

    // POST /api/ideas/{idea}/replace
    public function replace(ContentIdea $idea/*, ContentService $contentService*/)
    {
        $this->authorizeTenant($idea->tenant_id);

        FeedbackLog::create([
            'tenant_id' => $idea->tenant_id,
            'source'    => 'idea',
            'source_id' => $idea->id,
            'action'    => 'replace',
            'weight'    => -1,
        ]);

        // Abhi simple version: sirf delete kar do.
        // Baad me AI se new idea generate kara lenge.
        $idea->delete();

        return response()->json(['status' => 'ok']);
    }

    // DELETE /api/ideas/{idea}
    public function destroy(ContentIdea $idea)
    {
        $this->authorizeTenant($idea->tenant_id);

        FeedbackLog::create([
            'tenant_id' => $idea->tenant_id,
            'source'    => 'idea',
            'source_id' => $idea->id,
            'action'    => 'delete',
            'weight'    => -2,
        ]);

        $idea->delete();

        return response()->json(['status' => 'ok']);
    }

    // protected function authorizeTenant(int $tenantId)
    // {
    //     $currentTenantId = auth()->user()->tenant_id ?? 1;
    //     if ($tenantId !== $currentTenantId) {
    //         abort(403, 'Unauthorized tenant access');
    //     }
    // }
    
    protected function authorizeTenant(int $tenantId)
{
    // Yahan bhi admin guard use karo:
    $currentTenantId = auth('admin')->user()->tenant_id ?? 1;
    if ($tenantId !== $currentTenantId) {
        abort(403, 'Unauthorized tenant access');
    }
}

}
