<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\UpsellingItem;

class UpsellingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // 1. Ambil Data Restoran (Pastikan ID-nya benar)
        $r209 = Restaurant::where('code', '209')->first();
        $njr  = Restaurant::where('code', 'NJR')->first(); // Pastikan kodenya njr (atau NJR sesuai db anda)
        $xfh  = Restaurant::where('code', 'XFH')->first();
        $cha  = Restaurant::where('code', 'CHA')->first();
        $voda = Restaurant::where('code', 'VODA')->first();
        $jm   = Restaurant::where('code', 'JM')->first();

        $items = [];

        // --- MENU UNTUK 209 DINING ---
        if ($r209) {
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'food', 'name' => 'Mushroom Soup'];
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'food', 'name' => 'Iga Bakar'];
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'food', 'name' => 'Tex Mex'];
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 flavors'];
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'beverage', 'name' => 'Promo Wine by Glass B1G1'];
            $items[] = ['restaurant_id' => $r209->id, 'type' => 'beverage', 'name' => 'Upselling Mocktail of The Month'];
        }

        // --- MENU UNTUK NAGANO ---
        if ($njr) {
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'food', 'name' => 'Ishiyaki Mixed Salmon Rice'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'food', 'name' => 'Ishiyaki Mixed Beef Rice'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'food', 'name' => 'Ishiyaki Mixed Chicken Rice'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'beverage', 'name' => 'Mocktail by Nagano (Matcha Tart)'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'beverage', 'name' => 'Special Upselling All Sake by Bottle'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 flavors'];
            $items[] = ['restaurant_id' => $njr->id, 'type' => 'beverage', 'name' => 'Chandon Brut by Bottle'];
        }

        // --- MENU UNTUK XIANG FU HAI ---
        if ($xfh) {
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'BBQ Iberico Pork with Osmanthus Honey'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'Sizzling Short Ribs with Truffle Sauce'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'Flambe 8 Tresure Chicken with Dried Seafood'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'Empurau Fish'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'Signature Lobster in X.O. Sauce'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'food', 'name' => 'Two Treasures of Harmony'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 Flavors'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'beverage', 'name' => 'Promo Wine by Bottle (All Wine by Bottle)'];
            $items[] = ['restaurant_id' => $xfh->id, 'type' => 'beverage', 'name' => 'Special Upselling Beer (Tsing Tao)'];
        }

        if ($cha) {
            $items[] = ['restaurant_id' => $cha->id, 'type' => 'food', 'name' => 'Picanha Com Sal Groso'];
            $items[] = ['restaurant_id' => $cha->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 Flavors'];
            $items[] = ['restaurant_id' => $cha->id, 'type' => 'beverage', 'name' => 'Monkey Soulder by Glass 100++'];
            $items[] = ['restaurant_id' => $cha->id, 'type' => 'beverage', 'name' => 'Chandon Brut by Bottle'];
            $items[] = ['restaurant_id' => $cha->id, 'type' => 'beverage', 'name' => 'Upselling Wine by Bottle'];
        }

        if ($voda) {
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'food', 'name' => 'Indian Butter Chicken'];
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'food', 'name' => 'Lamb Biryani'];
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'food', 'name' => 'Cowboy Butcher'];
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'beverage', 'name' => 'Belgium Chocolate'];
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 Flavors'];
            $items[] = ['restaurant_id' => $voda->id, 'type' => 'beverage', 'name' => 'Cocktail of The Month'];
        }

        if ($jm) {
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'food', 'name' => 'Coffee Bean Bread'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'food', 'name' => 'Danish Cheese Mushroom'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'food', 'name' => 'Tiramisu Croissant'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'food', 'name' => 'Dalgona Bomboloni Croissant'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'food', 'name' => 'Choc Brownise Mousse Cake'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'beverage', 'name' => 'Bacha Coffee 4 Flavors'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'beverage', 'name' => 'Latte Series'];
            $items[] = ['restaurant_id' => $jm->id, 'type' => 'beverage', 'name' => 'Upselling Mocktail of The Month'];
        }

        // Eksekusi Insert
        foreach ($items as $item) {
            UpsellingItem::firstOrCreate([
                'restaurant_id' => $item['restaurant_id'],
                'name' => $item['name']
            ], [
                'type' => $item['type']
            ]);
        }
    }
}
