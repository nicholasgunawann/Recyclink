<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'phone_number'      => fake()->numerify('08##########'),
            'avatar'            => null,
            'status'            => User::STATUS_ACTIVE,
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => User::STATUS_SUSPENDED,
        ]);
    }

    public function asSeller(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('seller');

            \App\Models\SellerProfile::factory()->create(['user_id' => $user->id]);
            \App\Models\SellerWallet::create(['seller_id' => $user->id]);
        });
    }

    public function asBuyer(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('buyer');

            \App\Models\BuyerProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
