<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'message' => fake()->paragraph(3, 5),
            'user_id' => null, // Guest submission by default
        ];
    }

    /**
     * Indicate that the contact is from an authenticated user.
     */
    public function fromAuthenticatedUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the contact is from a specific user.
     */
    public function fromUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the contact is from a guest user.
     */
    public function fromGuest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * Indicate that the contact has a long message.
     */
    public function withLongMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => fake()->paragraphs(5, true),
        ]);
    }

    /**
     * Indicate that the contact has special characters.
     */
    public function withSpecialCharacters(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'José María O\'Connor',
            'message' => 'This message contains special characters: áéíóú ñ & < > " \'',
        ]);
    }
} 