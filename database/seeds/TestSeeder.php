<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Model\Users;
use App\Model\Material;
use App\Model\Tags;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $materials = factory(Material::class)
            ->states('public')
            ->create()
            ->each( function($m) {
                for($i = 0; $i < 2; $i++) {
                    $tagId = factory(Tags::class)->create()->id;
                    $m->tags()->attach($tagId, ['user_id' => $m->user_id]);
                }
            });
        $materials = factory(Material::class)
            ->states('private')
            ->create()
            ->each( function($m) {
                for($i = 0; $i < 2; $i++) {
                    $tagId = factory(Tags::class)->create()->id;
                    $m->tags()->attach($tagId, ['user_id' => $m->user_id]);
                }
            });
    }
}
