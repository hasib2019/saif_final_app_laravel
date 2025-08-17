<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PressRelease;
use App\Models\Partner;
use App\Models\HeroSlide;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        try {
            $stats = [
                'overview' => [
                    'total_users' => User::count(),
                    'total_products' => Product::count(),
                    'total_categories' => ProductCategory::count(),
                    'total_news' => PressRelease::count(),
                    'total_partners' => Partner::count(),
                    'total_hero_slides' => HeroSlide::count(),
                    'total_contact_submissions' => FormSubmission::count(),
                ],
                'recent_activity' => [
                    'new_users_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
                    'new_products_this_month' => Product::whereMonth('created_at', Carbon::now()->month)->count(),
                    'new_news_this_month' => PressRelease::whereMonth('created_at', Carbon::now()->month)->count(),
                    'new_submissions_this_month' => FormSubmission::whereMonth('created_at', Carbon::now()->month)->count(),
                ],
                'content_status' => [
                    'active_products' => Product::where('is_active', true)->count(),
                    'inactive_products' => Product::where('is_active', false)->count(),
                    'active_news' => PressRelease::where('is_active', true)->count(),
                    'inactive_news' => PressRelease::where('is_active', false)->count(),
                    'active_partners' => Partner::where('is_active', true)->count(),
                    'inactive_partners' => Partner::where('is_active', false)->count(),
                ],
                'monthly_trends' => [
                    'users' => $this->getMonthlyTrends(User::class),
                    'products' => $this->getMonthlyTrends(Product::class),
                    'news' => $this->getMonthlyTrends(PressRelease::class),
                    'submissions' => $this->getMonthlyTrends(FormSubmission::class),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function recentActivities()
    {
        try {
            $activities = [];

            // Recent users
            $recentUsers = User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']);
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'type' => 'user',
                    'action' => 'created',
                    'description' => "New user registered: {$user->name}",
                    'created_at' => $user->created_at,
                    'data' => $user
                ];
            }

            // Recent products
            $recentProducts = Product::latest()->take(5)->get(['id', 'name', 'created_at']);
            foreach ($recentProducts as $product) {
                $activities[] = [
                    'type' => 'product',
                    'action' => 'created',
                    'description' => "New product added: {$product->name['en']}",
                    'created_at' => $product->created_at,
                    'data' => $product
                ];
            }

            // Recent news
            $recentNews = PressRelease::latest()->take(5)->get(['id', 'title', 'created_at']);
            foreach ($recentNews as $news) {
                $activities[] = [
                    'type' => 'news',
                    'action' => 'created',
                    'description' => "New article published: {$news->title['en']}",
                    'created_at' => $news->created_at,
                    'data' => $news
                ];
            }

            // Sort by created_at desc
            usort($activities, function($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });

            return response()->json([
                'success' => true,
                'data' => array_slice($activities, 0, 10)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system information
     */
    public function systemInfo()
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database_type' => config('database.default'),
                'storage_used' => $this->getStorageUsage(),
                'cache_status' => $this->getCacheStatus(),
                'queue_status' => $this->getQueueStatus(),
            ];

            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch system information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly trends for a model
     */
    private function getMonthlyTrends($model)
    {
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = $model::whereYear('created_at', $date->year)
                          ->whereMonth('created_at', $date->month)
                          ->count();
            $trends[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        return $trends;
    }

    /**
     * Get storage usage
     */
    private function getStorageUsage()
    {
        try {
            $storagePath = storage_path('app/public');
            if (is_dir($storagePath)) {
                $size = $this->getDirSize($storagePath);
                return $this->formatBytes($size);
            }
            return '0 B';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get directory size
     */
    private function getDirSize($directory)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Get cache status
     */
    private function getCacheStatus()
    {
        try {
            return cache()->has('test_key') ? 'Active' : 'Inactive';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get queue status
     */
    private function getQueueStatus()
    {
        try {
            return 'Active'; // Simplified for now
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}