<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['cow', 'گائے', 'Cow', '🐄'], ['buffalo', 'بھینس', 'Buffalo', '🐃'], ['goat', 'بکری', 'Goat', '🐐'], ['sheep', 'بھیڑ', 'Sheep', '🐑']] as [$k, $ur, $en, $i]) {
            Category::updateOrCreate(['key' => $k], ['name_ur' => $ur, 'name_en' => $en, 'icon' => $i]);
        }
    }
}
