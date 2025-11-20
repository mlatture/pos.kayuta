<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\ContentService;
use App\Models\Tenant; // however you represent tenants

class GenerateIdeasCommand extends Command
{
    protected $signature = 'ideas:generate {tenant_id?}';
    protected $description = 'Generate weekly content ideas for one or all tenants';

    public function handle(ContentService $contentService)
    {
      $tenantId = auth('admin')->user()->tenant_id;  
        

        if ($tenantId) {
            $contentService->generateIdeasForTenant($tenantId);
        } else {
            Tenant::query()->active()->chunk(50, function ($tenants) use ($contentService) {
                foreach ($tenants as $tenant) {
                    $this->info("Generating ideas for tenant {$tenant->id}");
                    $contentService->generateIdeasForTenant($tenant->id);
                }
            });
        }

        return Command::SUCCESS;
    }
}
