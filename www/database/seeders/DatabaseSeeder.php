<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\AccessControl\Database\Seeders\AclDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AclDatabaseSeeder::class,
        ]);
    }
}
