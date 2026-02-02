<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RollbackNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete seeded data

        // Get all news records
        $images = DB::table('news')->get();

        foreach ($images as $image) {
            DB::table('news')
                ->where('id', $image->id)
                ->update([
                    'thumbnail' => 'uploads/news/photo_2026-01-28_18-23-14.jpg'
                ]);
        }
    }
}
