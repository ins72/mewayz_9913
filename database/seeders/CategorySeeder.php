<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Digital Products', 'slug' => 'digital-products', 'description' => 'Digital downloads, software, and courses'],
            ['name' => 'Courses', 'slug' => 'courses', 'description' => 'Online courses and educational content'],
            ['name' => 'Templates', 'slug' => 'templates', 'description' => 'Design templates and digital assets'],
            ['name' => 'Services', 'slug' => 'services', 'description' => 'Professional services and consulting'],
            ['name' => 'Merchandise', 'slug' => 'merchandise', 'description' => 'Physical products and branded items'],
            ['name' => 'Subscriptions', 'slug' => 'subscriptions', 'description' => 'Monthly and yearly subscription plans'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
