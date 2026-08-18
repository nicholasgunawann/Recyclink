<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use App\Models\SellerWallet;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;
    protected SellerWallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $sellerRole = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);

        // Create admin user
        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole($adminRole);

        // Create seller user with wallet
        $this->seller = User::factory()->create(['status' => 'active']);
        $this->seller->assignRole($sellerRole);
        $this->wallet = SellerWallet::create([
            'seller_id' => $this->seller->id,
            'balance' => 500000,
        ]);
    }

    public function test_admin_can_view_withdrawals_index_page(): void
    {
        $withdrawal = Withdrawal::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->seller->id,
            'amount' => 100000,
            'net_amount' => 97000,
            'admin_fee' => 3000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Penjual',
            'status' => Withdrawal::STATUS_PENDING,
            'withdrawal_number' => 'WD-20260818-1234',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.withdrawals.index'));

        $response->assertStatus(200);
        $response->assertSee('WD-20260818-1234');
        $response->assertSee('Budi Penjual');
        $response->assertSee('1234567890');
    }

    public function test_admin_can_view_withdrawal_detail_page(): void
    {
        $withdrawal = Withdrawal::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->seller->id,
            'amount' => 100000,
            'net_amount' => 97000,
            'admin_fee' => 3000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Penjual',
            'status' => Withdrawal::STATUS_PENDING,
            'withdrawal_number' => 'WD-20260818-1234',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.withdrawals.show', $withdrawal->id));

        $response->assertStatus(200);
        $response->assertSee('WD-20260818-1234');
        $response->assertSee('BCA');
        $response->assertSee('97.000');
        $response->assertSee('Setujui Penarikan');
    }

    public function test_admin_can_approve_pending_withdrawal(): void
    {
        $withdrawal = Withdrawal::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->seller->id,
            'amount' => 100000,
            'net_amount' => 97000,
            'admin_fee' => 3000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Penjual',
            'status' => Withdrawal::STATUS_PENDING,
            'withdrawal_number' => 'WD-20260818-1234',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.withdrawals.approve', $withdrawal->id));

        $response->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_APPROVED, $withdrawal->fresh()->status);
        $this->assertEquals($this->admin->id, $withdrawal->fresh()->approved_by);
    }

    public function test_admin_can_mark_approved_withdrawal_as_paid(): void
    {
        $withdrawal = Withdrawal::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->seller->id,
            'amount' => 100000,
            'net_amount' => 97000,
            'admin_fee' => 3000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Penjual',
            'status' => Withdrawal::STATUS_APPROVED,
            'withdrawal_number' => 'WD-20260818-1234',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.withdrawals.pay', $withdrawal->id));

        $response->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_PAID, $withdrawal->fresh()->status);
    }

    public function test_admin_can_reject_withdrawal_and_refund_balance_to_seller(): void
    {
        $initialBalance = (float) $this->wallet->balance; // 500,000

        $withdrawal = Withdrawal::create([
            'wallet_id' => $this->wallet->id,
            'user_id' => $this->seller->id,
            'amount' => 100000,
            'net_amount' => 97000,
            'admin_fee' => 3000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Penjual',
            'status' => Withdrawal::STATUS_PENDING,
            'withdrawal_number' => 'WD-20260818-1234',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.withdrawals.reject', $withdrawal->id), [
            'admin_note' => 'Nomor rekening tidak valid.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_REJECTED, $withdrawal->fresh()->status);
        $this->assertEquals('Nomor rekening tidak valid.', $withdrawal->fresh()->admin_note);
        // Verify balance refunded
        $this->assertEquals($initialBalance + 100000, (float) $this->wallet->fresh()->balance);
    }

    public function test_non_admin_cannot_access_admin_withdrawals(): void
    {
        $response = $this->actingAs($this->seller)->get(route('admin.withdrawals.index'));
        $response->assertStatus(403);
    }
}
