<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        ShopItem::updateOrCreate(
            ['name' => 'Golden Aura Border'],
            [
                'description' => 'A radiant golden border for your status avatar. Symbol of purity.',
                'price_soul_points' => 50000,
                'category' => 'border',
                'asset_path' => 'border-gold-aura',
            ]
        );

        ShopItem::updateOrCreate(
            ['name' => 'The Shadow Sovereign Title'],
            [
                'description' => 'A legendary title that glows in the dark. Grants a +2% WIS bonus.',
                'price_soul_points' => 100000,
                'category' => 'title',
                'asset_path' => 'title-sovereign',
            ]
        );

        ShopItem::updateOrCreate(
            ['name' => 'Emerald Green Name'],
            [
                'description' => 'Change your name display color to a calming emerald green.',
                'price_soul_points' => 25000,
                'category' => 'name_color',
                'asset_path' => 'color-emerald',
            ]
        );
    }
}
