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
    // Get all news records
    $newsItems = DB::table('news')->get();

    foreach ($newsItems as $news) {
        // Update the news thumbnail
        DB::table('news')
            ->where('id', $news->id)
            ->update([
                'thumbnail' => 'photo_2026-01-14_15-57-46'
            ]);

        $newsDetails = DB::table('news_details')
            ->where('news_id', $news->id)
            ->get();

        foreach ($newsDetails as $detail) {
            DB::table('news_details_images')
                ->insert([
                    'image' => 'photo_2026-01-14_15-57-46','news_details_id' => $detail->id,'created_at' => now(), 'updated_at' => now()
                ]);
        }
    }
}

}
