<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteListing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoListingSeeder extends Seeder
{
    // ponytail: flat array is sufficient — no factory needed for fixed demo data
    private array $listings = [
        [
            'title'           => 'Limbah Kardus Bekas Grade A - Ready Stock Banyak',
            'category_name'   => 'Kertas (Kardus, kertas)',
            'price_per_unit'  => 1200, 'unit' => 'kg', 'min_order' => 50, 'quantity' => 5000,
            'waste_condition' => 'used', 'city' => 'Jakarta', 'province' => 'DKI Jakarta',
            'address'         => 'Jl. Industri Raya No. 45, Kawasan Industri Pulogadung',
            'description'     => "Limbah kardus OCC Grade A dalam jumlah besar. Bersih, kering, sudah dipres.\n\nMinimum pickup: 50 kg. Bisa COD area Jakarta. Negosiasi harga >1 ton.",
        ],
        [
            'title'           => 'Plastik PET Botol Bening - Bersih & Kering',
            'category_name'   => 'Plastik',
            'price_per_unit'  => 2500, 'unit' => 'kg', 'min_order' => 100, 'quantity' => 3000,
            'waste_condition' => 'scrap', 'city' => 'Surabaya', 'province' => 'Jawa Timur',
            'address'         => 'Jl. Raya Kali Rungkut No. 10, Surabaya',
            'description'     => "Plastik PET botol bening bekas minuman mineral. Sudah dicuci, dipisah dari tutup & label.\n✓ Bebas kontaminasi ✓ Flake siap proses ✓ Moisture <1%",
        ],
        [
            'title'           => 'Besi Tua Campuran - Ton Timbang di Tempat',
            'category_name'   => 'Logam',
            'price_per_unit'  => 3800, 'unit' => 'kg', 'min_order' => 500, 'quantity' => 20000,
            'waste_condition' => 'scrap', 'city' => 'Bandung', 'province' => 'Jawa Barat',
            'address'         => 'Jl. Soekarno Hatta No. 123, Bandung',
            'description'     => "Besi tua campuran dari sisa konstruksi dan pabrik. Besi profil, plat, cor, pipa.\nMoU untuk kontrak panjang tersedia.",
        ],
        [
            'title'           => 'Limbah Tembaga Kabel Stripping - Kadar Tinggi',
            'category_name'   => 'Logam',
            'price_per_unit'  => 72000, 'unit' => 'kg', 'min_order' => 10, 'quantity' => 500,
            'waste_condition' => 'scrap', 'city' => 'Semarang', 'province' => 'Jawa Tengah',
            'address'         => 'Jl. Raya Kaligawe No. 45, Semarang',
            'description'     => "Tembaga dari stripping kabel industri. Kadar >95%. Tersedia sertifikat analisa.",
        ],
        [
            'title'           => 'Serbuk Kayu Halus - Biomassa Energi',
            'category_name'   => 'Lainnya (Kayu, keramik, kaca dll)',
            'price_per_unit'  => 400, 'unit' => 'kg', 'min_order' => 1000, 'quantity' => 50000,
            'waste_condition' => 'used', 'city' => 'Medan', 'province' => 'Sumatera Utara',
            'address'         => 'Jl. Gatot Subroto No. 200, Medan',
            'description'     => "Serbuk kayu halus dari mesin CNC & gergaji furniture. Cocok untuk biomassa, briket, media tanam jamur.",
        ],
        [
            'title'           => 'Minyak Jelantah (UCO) - Food Grade',
            'category_name'   => 'Cairan/Minyak',
            'price_per_unit'  => 8500, 'unit' => 'liter', 'min_order' => 200, 'quantity' => 10000,
            'waste_condition' => 'used', 'city' => 'Jakarta', 'province' => 'DKI Jakarta',
            'address'         => 'Jl. Pluit Selatan No. 8, Jakarta Utara',
            'description'     => "UCO dari restoran & industri makanan. FFA <3%, Moisture <1%. Supplier untuk biodiesel & oleokimia.",
        ],
        [
            'title'           => 'Ban Bekas Truk & Alat Berat',
            'category_name'   => 'Lainnya (Kayu, keramik, kaca dll)',
            'price_per_unit'  => 35000, 'unit' => 'pcs', 'min_order' => 20, 'quantity' => 500,
            'waste_condition' => 'scrap', 'city' => 'Bekasi', 'province' => 'Jawa Barat',
            'address'         => 'Kawasan Industri MM2100, Bekasi',
            'description'     => "Ban truk dan alat berat berbagai ukuran (11R22.5, 12R22.5, OTR). Cocok untuk daur ulang karet.",
        ],
        [
            'title'           => 'Aluminium Kaleng Cacah - Siap Lebur',
            'category_name'   => 'Logam',
            'price_per_unit'  => 14500, 'unit' => 'kg', 'min_order' => 200, 'quantity' => 8000,
            'waste_condition' => 'scrap', 'city' => 'Tangerang', 'province' => 'Banten',
            'address'         => 'Jl. Raya Serang KM 12, Tangerang',
            'description'     => "UBC (Used Beverage Can) aluminium sudah dicacah. Bersih, density optimal. Laporan analisis tersedia.",
        ],
    ];

    public function run(): void
    {
        $demoSeller = User::where('email', 'seller@recyclink.id')->first();
        if (! $demoSeller) {
            $this->command->warn('Demo seller not found. Run AdminUserSeeder first.');
            return;
        }

        foreach ($this->listings as $i => $data) {
            $category = WasteCategory::where('category_name', $data['category_name'])->first();
            if (! $category) {
                $this->command->warn("Category '{$data['category_name']}' not found, skipping.");
                continue;
            }

            $slug = Str::slug($data['title']) . '-' . ($i + 1);
            unset($data['category_name']);

            WasteListing::updateOrCreate(['slug' => $slug], array_merge($data, [
                'seller_id'           => $demoSeller->id,
                'category_id'         => $category->id,
                'slug'                => $slug,
                'availability_status' => 'available',
                'verification_status' => 'approved',
                'view_count'          => fake()->numberBetween(10, 300),
                'published_at'        => now()->subDays(rand(1, 60)),
            ]));

            $this->command->info("✓ {$data['title']}");
        }
    }
}
