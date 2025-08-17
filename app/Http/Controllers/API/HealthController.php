<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Check the health status of the application
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $status = 'ok';
        $message = 'Server is running';
        $dbStatus = 'ok';
        
        // Check database connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $status = 'error';
            $dbStatus = 'error';
            $message = 'Database connection failed';
        }
        
        return response()->json([
            'status' => $status,
            'message' => $message,
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => $dbStatus,
                'app' => 'ok',
            ],
            'environment' => config('app.env'),
            'version' => config('app.version', '1.0.0'),
        ]);
    }
}