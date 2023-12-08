<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['B','P','V']);
        $billedDate = $this->faker->dateTimeThisDecade();

        return [
            'customer_id'=>Customer::factory(),
            'amount'=> $this->faker->numberBetween(100,500),
            'status'=> $status,
            'billed_date'=> $billedDate,
            'paid_date'=> $status == 'P' ? $this->faker->dateTimeBetween($billedDate, 'now') : NULL
        ];
    }
}
