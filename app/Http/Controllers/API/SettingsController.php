<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Display application settings.
     */
    public function index()
    {
        try {
            // Get settings from database
            $dbSettings = Setting::all()->keyBy('key');
            
            // Default settings
            $defaultSettings = [
                // General Settings
                'site_name' => config('app.name', 'Derown'),
                'site_description' => 'Leading technology solutions provider',
                'site_keywords' => 'technology, solutions, innovation',
                'contact_email' => 'info@derown.com',
                'contact_phone' => '+1234567890',
                'address' => '123 Business Street, City, Country',

                // Site Settings
                'timezone' => config('app.timezone', 'UTC'),
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'items_per_page' => 10,
                'maintenance_mode' => false,

                // Email Settings
                'mail_driver' => config('mail.default', 'smtp'),
                'mail_host' => config('mail.mailers.smtp.host', 'localhost'),
                'mail_port' => config('mail.mailers.smtp.port', 587),
                'mail_username' => config('mail.mailers.smtp.username', ''),
                'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
                'mail_from_address' => config('mail.from.address', 'noreply@derown.com'),
                'mail_from_name' => config('mail.from.name', 'Derown'),

                // Security Settings
                'session_lifetime' => config('session.lifetime', 120),
                'password_min_length' => 8,
                'require_email_verification' => true,
                'two_factor_enabled' => false,
                'login_attempts' => 5,
                'lockout_duration' => 15,

                // Media Settings
                'max_file_size' => 10240, // KB
                'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx',
                'image_quality' => 85,
                'auto_resize_images' => true,
                'max_image_width' => 1920,
                'max_image_height' => 1080,

                // Notification Settings
                'email_notifications' => true,
                'admin_notifications' => true,
                'user_registration_notification' => true,
                'contact_form_notification' => true,

                // Appearance Settings
                'theme' => 'default',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#64748B',
                'logo_url' => asset('images/logo.png'),
                'favicon_url' => asset('images/favicon.ico'),

                // Advanced Settings
                'cache_enabled' => true,
                'debug_mode' => config('app.debug', false),
                'api_rate_limit' => 60,
                'api_logging' => true,
                'backup_enabled' => true,
                'backup_frequency' => 'daily',
            ];
            
            // Merge database settings with defaults
            $settings = collect($defaultSettings)->map(function ($value, $key) use ($dbSettings) {
                if (isset($dbSettings[$key])) {
                    return $dbSettings[$key]->value;
                }
                return $value;
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        try {
            $settings = $request->all();
            
            foreach ($settings as $key => $value) {
                // Skip non-setting fields
                if ($key === '_token' || $key === '_method') {
                    continue;
                }
                
                // Determine the type of the setting
                $type = 'string';
                if (is_bool($value)) {
                    $type = 'boolean';
                } elseif (is_numeric($value)) {
                    $type = 'number';
                } elseif (is_array($value)) {
                    $type = 'array';
                    $value = json_encode($value);
                }
                
                // Determine the group based on key prefix
                $group = 'general';
                if (strpos($key, 'mail_') === 0) {
                    $group = 'email';
                } elseif (strpos($key, 'site_') === 0) {
                    $group = 'site';
                } elseif (in_array($key, ['timezone', 'date_format', 'time_format', 'items_per_page', 'maintenance_mode'])) {
                    $group = 'site';
                } elseif (in_array($key, ['session_lifetime', 'password_min_length', 'require_email_verification', 'two_factor_enabled', 'login_attempts', 'lockout_duration'])) {
                    $group = 'security';
                } elseif (strpos($key, 'max_') === 0 || strpos($key, 'allowed_') === 0 || strpos($key, 'image_') === 0 || strpos($key, 'auto_') === 0) {
                    $group = 'media';
                } elseif (strpos($key, 'notification') !== false || strpos($key, 'email_') === 0) {
                    $group = 'notifications';
                } elseif (in_array($key, ['theme', 'primary_color', 'secondary_color', 'logo_url', 'favicon_url'])) {
                    $group = 'appearance';
                } elseif (in_array($key, ['cache_enabled', 'debug_mode', 'api_rate_limit', 'api_logging', 'backup_enabled', 'backup_frequency'])) {
                    $group = 'advanced';
                }
                
                // Save the setting
                Setting::set($key, $value, $group, $type);
            }
            
            // Clear settings cache
            Cache::forget('app_settings');
            
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a file for settings.
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240',
                'key' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $key = $request->input('key');
            
            // Determine the storage path based on the key
            $path = 'settings';
            if (strpos($key, 'logo') !== false) {
                $path = 'settings/logos';
            } elseif (strpos($key, 'favicon') !== false) {
                $path = 'settings/favicons';
            } elseif (strpos($key, 'image') !== false || strpos($key, 'photo') !== false) {
                $path = 'settings/images';
            }
            
            // Store the file
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs($path, $filename, 'public');
            $url = asset('storage/' . $filePath);
            
            // Update the setting
            Setting::set($key, $url);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup application settings.
     */
    public function backup()
    {
        try {
            $settings = Setting::all()->toArray();
            $backup = [
                'timestamp' => now()->toIso8601String(),
                'settings' => $settings
            ];
            
            $json = json_encode($backup, JSON_PRETTY_PRINT);
            $filename = 'settings_backup_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response($json)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore application settings from backup.
     */
    public function restore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $backup = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup file'
                ], 422);
            }

            if (!isset($backup['settings'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup format'
                ], 422);
            }

            // Clear existing settings
            Setting::truncate();
            
            // Restore settings from backup
            foreach ($backup['settings'] as $setting) {
                $newSetting = new Setting();
                $newSetting->fill($setting);
                $newSetting->save();
            }
            
            // Clear settings cache
            Cache::forget('app_settings');

            return response()->json([
                'success' => true,
                'message' => 'Settings restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}