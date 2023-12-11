<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "id" => Uuid::uuid4(),
            "zip_code" => $this->faker->numberBetween(19000000, 19999999),
            "complement" =>$this->faker->sentence(4),
            "number" => $this->faker->numberBetween(1, 9999)
        ];
    }
}
