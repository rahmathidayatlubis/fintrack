<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default categories (global, user_id = null)
        $existing = Category::whereNull('user_id')->count();
        if ($existing === 0) {
            foreach (Category::getDefaults() as $cat) {
                Category::create(array_merge($cat, ['user_id' => null]));
            }
        }
    }
}
