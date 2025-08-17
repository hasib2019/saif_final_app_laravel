<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallRbacSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Role-Based Access Control system with modules and menu items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing Role-Based Access Control System...');

        // Check if migrations exist
        $this->info('Checking migrations...');
        $moduleMigrationExists = DB::table('migrations')
            ->where('migration', 'like', '%create_modules_table%')
            ->exists();
            
        $menuItemMigrationExists = DB::table('migrations')
            ->where('migration', 'like', '%create_menu_items_table%')
            ->exists();
            
        $menuItemPermissionsMigrationExists = DB::table('migrations')
            ->where('migration', 'like', '%create_menu_item_permissions_table%')
            ->exists();

        if (!$moduleMigrationExists || !$menuItemMigrationExists || !$menuItemPermissionsMigrationExists) {
            $this->info('Running migrations...');
            Artisan::call('migrate');
            $this->info(Artisan::output());
        } else {
            $this->info('Migrations already exist.');
        }

        // Run the seeder
        $this->info('Seeding roles, permissions, modules, and menu items...');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->info(Artisan::output());

        // Clear cache
        $this->info('Clearing cache...');
        Artisan::call('cache:clear');
        $this->info('Route cache clearing...');
        Artisan::call('route:clear');
        $this->info('Config cache clearing...');
        Artisan::call('config:clear');
        $this->info('View cache clearing...');
        Artisan::call('view:clear');

        $this->info('RBAC system installed successfully!');
        $this->info('You can now access the system with the following roles:');
        $this->info('- admin: Full access to all features');
        $this->info('- editor: Access to content management and products');
        $this->info('- viewer: Limited access to dashboard and form submissions');
        
        return Command::SUCCESS;
    }
}