<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\ApiKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApiChannelController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id'            => ['required','integer'],
            'channel_id'             => ['required','integer'],
            'code'                   => ['required','string','max:32','alpha_dash','unique:booking_channels,code'],
            'name'                   => ['required','string','max:255','unique:booking_channels,name'],
            'sandbox'                => ['sometimes','boolean'],
            'rate_limit_per_minute'  => ['sometimes','integer','min:1'],
            'rate_burst_per_minute'  => ['sometimes','integer','min:1'],
        ]);
    
        if ($validator->fails()) {
            return redirect()->to(route('admin.business-settings.index') . '#apiChannels')
                ->withErrors($validator)
                ->withInput();
        }
    
        $data   = $validator->validated();
        $now    = now();
        $adminId= optional($request->user())->id;
    
        try {
            $result = DB::transaction(function () use ($data, $now, $adminId) {
                [$plain, $hash] = $this->generateUniqueApiKeyHash();
    
                $id = DB::table('booking_channels')->insertGetId([
                    'property_id'            => $data['property_id'],
                    'channel_id'             => $data['channel_id'],
                    'code'                   => $data['code'],
                    'name'                   => $data['name'],
                    'api_key_hash'           => $hash,
                    'status'                 => 'active',
                    'sandbox'                => (bool)($data['sandbox'] ?? false),
                    'last_used_at'           => null,
                    'auto_disabled'          => false,
                    'rate_limit_per_minute'  => $data['rate_limit_per_minute'] ?? 100,
                    'rate_burst_per_minute'  => $data['rate_burst_per_minute'] ?? 300,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ]);
    
                DB::table('channel_system_logs')->insert([
                    'admin_id'         => $adminId,
                    'transaction_type' => 'key_create',
                    'status'           => 'success',
                    'payload_snippet'  => json_encode(['booking_channel_id' => $id, 'code' => $data['code']]),
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
    
                return ['id' => $id, 'plain' => $plain];
            });
    
            return redirect()
                ->to(route('admin.business-settings.index') . '#apiChannels')
                ->with('created_key_plain', $result['plain']);
    
        } catch (QueryException $e) {
            $this->logSystemFailure($adminId, ['error' => $this->shortError($e->getMessage()), 'code' => $e->getCode()]);
            return redirect()->to(route('admin.business-settings.index') . '#apiChannels')
                ->withInput()
                ->withErrors(['general' => 'We could not create the channel key due to a database constraint. Please review the inputs and try again.']);
        } catch (Throwable $e) {
            $this->logSystemFailure($adminId, ['error' => $this->shortError($e->getMessage())]);
            return redirect()->to(route('admin.business-settings.index') . '#apiChannels')
                ->withInput()
                ->withErrors(['general' => 'Unexpected error while creating the channel key. Please try again.']);
        }
    }
    
    /**
     * Generate a unique API key + hash, retrying on rare unique collisions.
     *
     * @return array{string,string} [plain, hash]
     * @throws \RuntimeException
     */
    private function generateUniqueApiKeyHash(): array
    {
        $maxAttempts = 5;
    
        for ($i = 0; $i < $maxAttempts; $i++) {
            $plain = ApiKeys::generate();
            $hash  = ApiKeys::hash($plain);
    
            // Quick existence check to avoid hitting the unique constraint
            $exists = DB::table('booking_channels')->where('api_key_hash', $hash)->exists();
            if (!$exists) {
                return [$plain, $hash];
            }
        }
    
        throw new \RuntimeException('Failed to generate a unique API key. Please try again.');
    }
    
    /**
     * Log a failure in channel_system_logs (outside transaction).
     */
    private function logSystemFailure(?int $adminId, array $details): void
    {
        try {
            DB::table('channel_system_logs')->insert([
                'admin_id'         => $adminId,
                'transaction_type' => 'key_create',
                'status'           => 'failure',
                'payload_snippet'  => json_encode($details),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } catch (Throwable $ignored) {
            // Swallow logging errors to avoid masking the original failure.
        }
    }

    public function rotate(Request $request, int $id)
    {
        $plain = ApiKeys::generate();
        $hash  = ApiKeys::hash($plain);

        DB::table('booking_channels')->where('id',$id)->update([
            'api_key_hash' => $hash,
            'updated_at' => now(),
        ]);

        Cache::forget("api:key:".$hash); // new value cached on first use
        DB::table('channel_system_logs')->insert([
            'admin_id'        => optional($request->user())->id,
            'transaction_type'=> 'key_rotate',
            'status'          => 'success',
            'payload_snippet' => json_encode(['booking_channel_id' => $id]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->to(route('admin.business-settings.index') . '#' . 'apiChannels')->with('rotated_key_plain', $plain);
    }

    public function revoke(Request $request, int $id)
    {
        $row = DB::table('booking_channels')->where('id',$id)->first();
        if ($row) {
            DB::table('booking_channels')->where('id',$id)->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);
            Cache::forget("api:key:".$row->api_key_hash);
        }
        DB::table('channel_system_logs')->insert([
            'admin_id'        => optional($request->user())->id,
            'transaction_type'=> 'key_revoke',
            'status'          => 'success',
            'payload_snippet' => json_encode(['booking_channel_id' => $id]),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return redirect()->to(route('admin.business-settings.index') . '#' . 'apiChannels')->with('message','Key revoked.');
    }
    
    /**
     * Keep error messages short and safe for storage/display.
     */
    private function shortError(string $message, int $limit = 300): string
    {
        $trim = trim($message);
        return mb_strimwidth($trim, 0, $limit, '…', 'UTF-8');
    }
}