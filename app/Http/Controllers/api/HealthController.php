<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HealthController extends Controller
{
    /**
     * Check the health of the application and its core dependencies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $status = 'UP';
        $checks = [];

        // 1. Database Check
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'UP',
                'message' => 'Connection successful',
            ];
        } catch (\Exception $e) {
            $status = 'DOWN';
            $checks['database'] = [
                'status' => 'DOWN',
                'message' => 'Could not connect: ' . $e->getMessage(),
            ];
        }

        // 2. Storage Check
        $checks['storage'] = [
            'status' => is_writable(storage_path()) ? 'UP' : 'DOWN',
            'message' => is_writable(storage_path()) ? 'Storage is writable' : 'Storage is not writable',
        ];
        if ($checks['storage']['status'] === 'DOWN') {
            $status = 'DEGRADED';
        }

        // 3. Environment Info
        $checks['environment'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => Carbon::now()->toDateTimeString(),
            'timezone' => config('app.timezone'),
        ];

        return response()->json([
            'status' => $status,
            'timestamp' => Carbon::now()->toIso8601String(),
            'checks' => $checks
        ], $status === 'UP' ? 200 : 500);
    }
}
