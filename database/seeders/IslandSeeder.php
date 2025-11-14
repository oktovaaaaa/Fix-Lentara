<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Island;

class IslandSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name'   => 'Pulau Sumatera',
                'slug'   => 'sumatera',
                'place_label' => 'Danau Toba',
                'title'  => 'PULAU',
                'subtitle' => 'SUMATERA',
                'short_description' => 'Pulau dengan Danau Toba, budaya Batak, dan banyak destinasi alam.',
                'image_url' => 'https://images.unsplash.com/photo-1601058497548-f247dfe349d6?auto=format&fit=crop&q=80&w=1170',
                'order'  => 1,
            ],
            [
                'name'   => 'Pulau Jawa',
                'slug'   => 'jawa',
                'place_label' => 'Candi Prambanan',
                'title'  => 'PULAU',
                'subtitle' => 'JAWA',
                'short_description' => 'Pusat budaya dan ekonomi Indonesia, dengan banyak candi & kota besar.',
                'image_url' => 'https://images.unsplash.com/photo-1733039898491-b4f469c6cd1a?auto=format&fit=crop&q=80&w=1170',
                'order'  => 2,
            ],
            [
                'name'   => 'Pulau Kalimantan',
                'slug'   => 'kalimantan',
                'place_label' => 'Masyarakat Dayak',
                'title'  => 'PULAU',
                'subtitle' => 'KALIMANTAN',
                'short_description' => 'Pulau dengan hutan tropis luas dan budaya Dayak.',
                'image_url' => 'https://images.unsplash.com/flagged/photo-1564134204899-4adebaf1adb3?auto=format&fit=crop&q=80&w=735',
                'order'  => 3,
            ],
            [
                'name'   => 'Pulau Sulawesi',
                'slug'   => 'sulawesi',
                'place_label' => 'Monumen Yesus Memberkati',
                'title'  => 'PULAU',
                'subtitle' => 'SULAWESI',
                'short_description' => 'Pulau dengan garis pantai unik dan budaya Toraja, Minahasa, dll.',
                'image_url' => 'https://images.unsplash.com/photo-1612091508912-2136973784c3?auto=format&fit=crop&q=80&w=1167',
                'order'  => 4,
            ],
            [
                'name'   => 'Sunda Kecil (Bali & Nusa Tenggara)',
                'slug'   => 'bali',
                'place_label' => 'Masyarakat Bali',
                'title'  => 'SUNDA KECIL',
                'subtitle' => 'Bali  & Nusa Tenggara',
                'short_description' => 'Pulau wisata dunia dengan pantai, budaya, dan pura yang ikonik.',
                'image_url' => 'https://images.unsplash.com/photo-1741272689174-f7f03b09a0ab?auto=format&fit=crop&q=80&w=1173',
                'order'  => 5,
            ],

            [
                'name'   => 'Pulau Papua',
                'slug'   => 'papua',
                'place_label' => 'Raja Ampat',
                'title'  => 'PAPUA & MALUKU',
                'subtitle' => 'PAPUA',
                'short_description' => 'Pulau paling timur dengan Raja Ampat dan pegunungan tinggi.',
                'image_url' => 'https://images.unsplash.com/photo-1703769605297-cc74106244d9?auto=format&fit=crop&q=80&w=1184',
                'order'  => 6,
            ],
        ];

        foreach ($data as $item) {
            Island::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
