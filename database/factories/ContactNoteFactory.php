<?php

namespace Database\Factories;



use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactNote>
 */
class ContactNoteFactory extends Factory
{
    protected $model = ContactNote::class;

    public function definition(): array
    {
        return [
            'contact_id' => Contact::inRandomOrder()->first()?->id, // ✅ use existing
            'user_id'    => User::inRandomOrder()->first()?->id,    // ✅ use existing
            'note'       => '<p>' . fake()->paragraph(3) . '</p>',  // ✅ simple string
        ];
    }
}
