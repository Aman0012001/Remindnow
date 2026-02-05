<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Festival;
use App\Models\FestivalDescription;
use Carbon\Carbon;

class FestivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample festivals
        $festivals = [
            [
                'name' => 'Holi',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'is_active' => 1,
                'description' => 'The festival of colors and spring.'
            ],
            [
                'name' => 'Diwali',
                'date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'is_active' => 1,
                'description' => 'The festival of lights.'
            ],
            [
                'name' => 'Ganesh Chaturthi',
                'date' => Carbon::now()->addDays(15)->format('Y-m-d') . ', ' . Carbon::now()->addDays(16)->format('Y-m-d'),
                'is_active' => 1,
                'description' => 'Celebrating the birth of Lord Ganesha.'
            ],
            [
                'name' => 'Navratri',
                'date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                'is_active' => 1,
                'description' => 'Nine nights of dance and worship.'
            ],
            [
                'name' => 'Janmashtami',
                'date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'is_active' => 1,
                'description' => 'Celebrating the birth of Lord Krishna.'
            ]
        ];

        foreach ($festivals as $data) {
            $festival = Festival::create([
                'name' => $data['name'],
                'date' => $data['date'],
                'is_active' => $data['is_active'],
                'states' => json_encode([1, 2, 3])
            ]);

            FestivalDescription::create([
                'parent_id' => $festival->id,
                'language_id' => 1, // Assuming common language ID
                'description' => $data['description']
            ]);
        }
    }
}
