<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $part_number = strtoupper($this->faker->randomLetter);
        $part_number .= strtoupper($this->faker->randomLetter);
        $part_number .= $this->faker->numberBetween(100,999);
        $part_number .= '-';
        $part_number .= $this->faker->randomNumber(1);
        $part_number .= strtoupper($this->faker->randomLetter);
        $part_number .= $this->faker->numberBetween(10,99);

        $lot_number = $this->faker->date('dmy', 'now');
        $lot_number .= $this->faker->numberBetween(100000,999999);

        return [
            'part_number' => $part_number,
            'lot_number' => $lot_number,
            'quantity' => $this->faker->numberBetween(15, 999)
        ];
    }
}
