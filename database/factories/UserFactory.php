<?php

namespace Database\Factories;

use App\Models\User;
use App\Utils\Cpf;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "id" => Uuid::uuid4(),
            "fullname" => $this->faker->name,
            "birthdate" =>$this->faker->date('Y-m-d', '2005-01-01'),
            "password" => Hash::make("minhasenhasecreta"),
            "document" => Cpf::generate(),
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
