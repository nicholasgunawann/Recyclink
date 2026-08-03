<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Complaint;
use App\Models\WasteListing;
use App\Models\WasteCategory;
use Spatie\Permission\Models\Role;

class DisputeFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ponytail: seed roles if spatie permission is used
        Role::findOrCreate('admin');
        Role::findOrCreate('seller');
        Role::findOrCreate('buyer');
    }

    public function test_dispute_flow_from_buyer_seller_to_admin()
    {
        // 1. Setup buyer, seller, admin
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('admin');

        $seller = User::factory()->create(['status' => 'active']);
        $seller->assignRole('seller');
        $seller->sellerProfile()->create([
            'business_name' => 'Limbah Jaya',
            'phone_number' => '08123456789',
            'address' => 'Jl. Industri',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'verification_status' => 'approved',
        ]);

        $buyer = User::factory()->create(['status' => 'active']);
        $buyer->assignRole('buyer');

        $category = WasteCategory::create(['category_name' => 'Plastik', 'slug' => 'plastik']);
        $listing = WasteListing::create([
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Botol PET Bulk',
            'slug' => 'botol-pet-bulk',
            'quantity' => 100,
            'unit' => 'kg',
            'price_per_unit' => 5000,
            'address' => 'Jl. Merdeka No 10',
            'city' => 'Jakarta',
            'description' => 'Limbah botol plastik berkondisi baik',
            'verification_status' => WasteListing::VERIFICATION_APPROVED,
            'availability_status' => WasteListing::AVAILABILITY_AVAILABLE,
        ]);

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_status' => Order::STATUS_PAID,
            'subtotal' => 50000,
            'platform_fee' => 2500,
            'shipping_cost' => 10000,
            'total_amount' => 62500,
        ]);

        $order->items()->create([
            'listing_id' => $listing->id,
            'waste_name_snapshot' => $listing->title,
            'quantity' => 10,
            'unit' => $listing->unit,
            'price_per_unit_snapshot' => $listing->price_per_unit,
            'subtotal' => 50000,
        ]);

        // 2. Buyer files a complaint
        $response = $this->actingAs($buyer)->post(route('buyer.complaints.store', $order->id), [
            'subject' => 'Barang Rusak',
            'complaint_type' => 'damaged_item',
            'description' => 'Jumlah botol pecah dan tidak sesuai spesifikasi.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complaints', [
            'order_id' => $order->id,
            'complainant_id' => $buyer->id,
            'respondent_id' => $seller->id,
            'status' => Complaint::STATUS_OPEN,
        ]);

        $this->assertEquals(Order::STATUS_DISPUTED, $order->fresh()->order_status);

        $complaint = Complaint::where('order_id', $order->id)->first();

        // 3. Buyer & Seller message in resolution center
        $r1 = $this->actingAs($buyer)->post(route('buyer.complaints.messages.store', $complaint->id), [
            'message' => 'Tolong refund atau ganti rugi.',
        ]);
        $this->assertEquals(1, \App\Models\ComplaintMessage::count(), 'Buyer message count failed');

        $this->actingAs($seller)->post(route('seller.complaints.messages.store', $complaint->id), [
            'message' => 'Mohon dikirimkan foto bukti kerusakannya.',
        ]);

        $this->assertEquals(2, \App\Models\ComplaintMessage::count());

        // 4. Admin processes complaint
        $this->actingAs($admin)->patch(route('admin.complaints.process', $complaint->id));
        $this->assertEquals(Complaint::STATUS_UNDER_REVIEW, $complaint->fresh()->status);

        // 5. Admin resolves complaint (Buyer wins)
        $this->actingAs($admin)->patch(route('admin.complaints.resolve', $complaint->id), [
            'resolution_note' => 'Bukti kerugian pembeli tervalidasi, dana dikembalikan.',
        ]);

        $this->assertEquals(Complaint::STATUS_RESOLVED, $complaint->fresh()->status);
        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->order_status);

        // 6. Seller files an appeal
        \Illuminate\Support\Facades\Storage::fake('public');
        $resAppeal = $this->actingAs($seller)->post(route('seller.complaints.appeal.store', $complaint->id), [
            'appeal_reason' => 'Saya memiliki foto penimbangan sebelum dikirim.',
            'appeal_evidence' => \Illuminate\Http\UploadedFile::fake()->create('evidence.jpg', 500, 'image/jpeg'),
        ]);
        $resAppeal->assertSessionHasNoErrors();

        $this->assertEquals(Complaint::STATUS_APPEALED, $complaint->fresh()->status);

        // 7. Admin accepts appeal (Seller wins)
        $this->actingAs($admin)->patch(route('admin.complaints.appeal.accept', $complaint->id), [
            'appeal_resolution_note' => 'Bukti seller valid.',
        ]);

        $this->assertEquals(Complaint::STATUS_REJECTED, $complaint->fresh()->status);
        $this->assertEquals(Order::STATUS_COMPLETED, $order->fresh()->order_status);
    }
}
