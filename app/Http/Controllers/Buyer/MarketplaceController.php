<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    protected MarketplaceService $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    // Marketplace index – static view (no backend yet)
    public function index(Request $request)
    {
        return view('pages.MarketplaceLimbah.index');
    }

    // Marketplace detail – static view using route param for static lookup
    public function show($id)
    {
        $listings = [
            1 => (object)[ 'id' => 1,  'title' => 'Styrofoam / EPS Bekas – Bongkar Gudang', 'categoryLabel' => 'Plastik', 'city' => 'Surakarta', 'price' => 3000, 'unit' => 'kg', 'stock' => 8000, 'desc' => 'Styrofoam / EPS bekas dari pembongkaran gudang. Kondisi bersih, siap dikirim dalam jumlah besar. Cocok untuk daur ulang dan produksi ulang bahan bangunan.', 'seller' => 'CV Maju Jaya', 'kondisi' => 'Bekas layak pakai', 'moq' => '500 kg', 'image' => 'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=600&q=80' ],
            2 => (object)[ 'id' => 2,  'title' => 'Sekam Padi – Bahan Bakar Boiler', 'categoryLabel' => 'Limbah Organik', 'city' => 'Madiun', 'price' => 800, 'unit' => 'kg', 'stock' => 100000, 'desc' => 'Sekam padi kering hasil penggilingan. Kalori tinggi, cocok sebagai bahan bakar boiler industri atau campuran pakan ternak.', 'seller' => 'UD Tani Makmur', 'kondisi' => 'Kering & bersih', 'moq' => '1000 kg', 'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' ],
            3 => (object)[ 'id' => 3,  'title' => 'E-Waste Kabel & PCB Campuran', 'categoryLabel' => 'Elektronik', 'city' => 'Tangerang', 'price' => 18000, 'unit' => 'kg', 'stock' => 3000, 'desc' => 'Limbah elektronik berupa kabel campuran dan PCB bekas. Mengandung tembaga, aluminium, dan logam berharga lainnya.', 'seller' => 'PT Recycle Tech', 'kondisi' => 'Campuran', 'moq' => '100 kg', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80' ],
            4 => (object)[ 'id' => 4,  'title' => 'Kain Perca Katun – Sisa Konveksi', 'categoryLabel' => 'Tekstil & Kain', 'city' => 'Solo', 'price' => 1500, 'unit' => 'kg', 'stock' => 5000, 'desc' => 'Sisa kain perca katun dari konveksi pakaian. Berwarna-warni, cocok untuk kerajinan, keset, atau bahan isian.', 'seller' => 'Konveksi Solo Jaya', 'kondisi' => 'Sisa produksi', 'moq' => '50 kg', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' ],
            5 => (object)[ 'id' => 5,  'title' => 'Kardus & Karton Bekas Pabrik – Grade A', 'categoryLabel' => 'Kertas & Karton', 'city' => 'Yogyakarta', 'price' => 2200, 'unit' => 'kg', 'stock' => 20000, 'desc' => 'Kardus dan karton bekas dari pabrik. Grade A – kondisi masih kuat dan layak press. Tersedia dalam jumlah besar dan konsisten.', 'seller' => 'PT Karton Nusantara', 'kondisi' => 'Grade A', 'moq' => '200 kg', 'image' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&q=80' ],
            6 => (object)[ 'id' => 6,  'title' => 'Plastik PET Botol Bersih Siap Cacah', 'categoryLabel' => 'Plastik PET', 'city' => 'Depok', 'price' => 5500, 'unit' => 'kg', 'stock' => 15000, 'desc' => 'Plastik PET dari botol minuman yang telah dipilah dan dibersihkan. Siap masuk mesin pencacah. Tidak ada campuran sampah lain.', 'seller' => 'Bank Sampah Depok', 'kondisi' => 'Bersih terpilah', 'moq' => '300 kg', 'image' => 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=600&q=80' ],
            7 => (object)[ 'id' => 7,  'title' => 'Besi Tua Campuran – Ton Timbang di Tempat', 'categoryLabel' => 'Besi & Baja', 'city' => 'Medan', 'price' => 3800, 'unit' => 'kg', 'stock' => 30000, 'desc' => 'Besi tua campuran dari bongkaran bangunan dan mesin pabrik. Timbang di tempat. Harga bisa negosiasi untuk volume besar.', 'seller' => 'Besi Tua Sumatera', 'kondisi' => 'Campuran bongkaran', 'moq' => '1000 kg', 'image' => 'https://images.unsplash.com/photo-1558346547-4439467bd1d5?w=600&q=80' ],
            8 => (object)[ 'id' => 8,  'title' => 'Limbah Tembaga Kabel Stripping – Kadar Tinggi', 'categoryLabel' => 'Tembaga', 'city' => 'Bekasi', 'price' => 72000, 'unit' => 'kg', 'stock' => 2000, 'desc' => 'Tembaga murni hasil stripping kabel industri. Kadar tinggi, cocok untuk peleburan ulang. Tersedia sertifikat kadar.', 'seller' => 'PT Metal Prima', 'kondisi' => 'Siap lebur', 'moq' => '50 kg', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' ],
            9 => (object)[ 'id' => 9,  'title' => 'Serbuk Kayu Halus – Biomassa Energi', 'categoryLabel' => 'Kayu & Biomassa', 'city' => 'Bandung', 'price' => 400, 'unit' => 'kg', 'stock' => 50000, 'desc' => 'Serbuk kayu halus dari industri meubel dan pengergajian. Kering, kadar air rendah. Ideal untuk briket atau bahan bakar biomassa.', 'seller' => 'UD Kayu Lestari', 'kondisi' => 'Kering', 'moq' => '2000 kg', 'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' ],
            10 => (object)[ 'id' => 10, 'title' => 'Aluminium Kaleng Cacah – Siap Lebur', 'categoryLabel' => 'Aluminium', 'city' => 'Tangerang', 'price' => 14500, 'unit' => 'kg', 'stock' => 8000, 'desc' => 'Kaleng aluminium bekas minuman yang sudah dicacah dan dibersihkan. Siap masuk tungku peleburan. Kemurnian tinggi.', 'seller' => 'CV Aluminium Utama', 'kondisi' => 'Cacah bersih', 'moq' => '200 kg', 'image' => 'https://images.unsplash.com/photo-1558346547-4439467bd1d5?w=600&q=80' ],
            11 => (object)[ 'id' => 11, 'title' => 'Minyak Jelantah (UCO) – Food Grade', 'categoryLabel' => 'Minyak', 'city' => 'Bandung', 'price' => 8500, 'unit' => 'liter', 'stock' => 10000, 'desc' => 'Minyak jelantah dari restoran dan industri makanan. Sudah disaring, cocok untuk produksi biodiesel atau sabun industri.', 'seller' => 'CV Energi Hijau', 'kondisi' => 'Tersaring', 'moq' => '200 liter', 'image' => 'https://images.unsplash.com/photo-1510498468133-c97f0e0dcdbe?w=600&q=80' ],
            12 => (object)[ 'id' => 12, 'title' => 'Limbah Kardus Press – Grade B', 'categoryLabel' => 'Kardus', 'city' => 'Medan', 'price' => 2800, 'unit' => 'kg', 'stock' => 25000, 'desc' => 'Kardus bekas dalam kondisi press dan terikat rapi. Grade B dengan sedikit campuran kertas. Siap kirim ke pabrik kertas.', 'seller' => 'Pengepul Medan Baru', 'kondisi' => 'Press & ikat', 'moq' => '500 kg', 'image' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&q=80' ],
            13 => (object)[ 'id' => 13, 'title' => 'Ban Bekas Truk & Alat Berat', 'categoryLabel' => 'Ban Bekas', 'city' => 'Surabaya', 'price' => 35000, 'unit' => 'pcs', 'stock' => 500, 'desc' => 'Ban bekas truk dan alat berat. Kondisi layak retreading atau untuk crumb rubber. Ukuran bervariasi, tersedia pilihan.', 'seller' => 'UD Ban Jaya', 'kondisi' => 'Layak retreading', 'moq' => '10 pcs', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' ],
            14 => (object)[ 'id' => 14, 'title' => 'Serbuk Besi (Iron Powder) Sisa Produksi', 'categoryLabel' => 'Logam', 'city' => 'Cikarang', 'price' => 6200, 'unit' => 'kg', 'stock' => 12000, 'desc' => 'Serbuk besi sisa mesin gerinda pabrik. Kemurnian besi tinggi, cocok untuk industri metalurgi dan magnet.', 'seller' => 'PT Cikarang Metal', 'kondisi' => 'Serbuk halus', 'moq' => '100 kg', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&q=80' ],
            15 => (object)[ 'id' => 15, 'title' => 'Limbah Kaca Pecah – Industri Botol', 'categoryLabel' => 'Kaca', 'city' => 'Semarang', 'price' => 1200, 'unit' => 'kg', 'stock' => 40000, 'desc' => 'Pecahan kaca dari industri botol dan kemasan. Bersih tanpa campuran keramik. Siap masuk dapur peleburan kaca.', 'seller' => 'CV Kaca Semarang', 'kondisi' => 'Bersih', 'moq' => '500 kg', 'image' => 'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=600&q=80' ],
            16 => (object)[ 'id' => 16, 'title' => 'Ampas Tebu (Bagasse) – Kering', 'categoryLabel' => 'Limbah Organik', 'city' => 'Malang', 'price' => 600, 'unit' => 'kg', 'stock' => 80000, 'desc' => 'Ampas tebu kering dari pabrik gula. Nilai kalori baik untuk bahan bakar boiler. Tersedia dalam karung atau curah.', 'seller' => 'PG Malang Baru', 'kondisi' => 'Kering curah', 'moq' => '3000 kg', 'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80' ],
            17 => (object)[ 'id' => 17, 'title' => 'Kulit Imitasi Sisa Jahit – Lembaran', 'categoryLabel' => 'Tekstil', 'city' => 'Sidoarjo', 'price' => 3500, 'unit' => 'kg', 'stock' => 3000, 'desc' => 'Sisa lembaran kulit imitasi (PU leather) dari pabrik tas dan sepatu. Warna bervariasi, bisa untuk kerajinan kecil atau sol sepatu.', 'seller' => 'Pabrik Tas Sidoarjo', 'kondisi' => 'Sisa potong', 'moq' => '50 kg', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' ],
            18 => (object)[ 'id' => 18, 'title' => 'Plastik HDPE Giling – Natural / Putih', 'categoryLabel' => 'Plastik HDPE', 'city' => 'Bogor', 'price' => 7800, 'unit' => 'kg', 'stock' => 18000, 'desc' => 'Plastik HDPE giling dari botol susu dan jerigen. Warna natural/putih bersih, kemurnian tinggi. Siap masuk extruder.', 'seller' => 'CV Plastik Bogor', 'kondisi' => 'Giling bersih', 'moq' => '200 kg', 'image' => 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=600&q=80' ],
        ];
        
        $listing = $listings[$id] ?? $listings[1];
        
        return view('pages.MarketplaceLimbah.show', compact('listing'));
    }
}
