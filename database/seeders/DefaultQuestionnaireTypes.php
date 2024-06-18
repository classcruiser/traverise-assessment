<?php

namespace Database\Seeders;

use App\Models\Booking\QuestionnaireType;
use Illuminate\Database\Seeder;

class DefaultQuestionnaireTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'dropdown'],
            ['name' => 'input'],
            ['name' => 'radio'],
            ['name' => 'checkbox']
        ];

        QuestionnaireType::insert($types);
    }
}
