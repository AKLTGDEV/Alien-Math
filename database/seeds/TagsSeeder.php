<?php

use Illuminate\Database\Seeder;
use App\numbersT;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags_test = array(
            "NEET", "JEE", "UPSC", "AIPMT",
            "CBSE", "ISI", "ISC", "physics",
            "chemistry", "math", "KVPY",
            "challenging", "biology",
            "boards", "easy", "random", "english",
            "history", "civics", "economics"
        );

        foreach ($tags_test as $tag) {
            DB::table('tags')->insert([
                'name' => $tag,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            Storage::put("tags/" . md5($tag), "[]");
        }
        factory(App\TagsModel::class, numbersT::tags()-4)->create();

        //factory(App\TagsModel::class, numbersT::tags())->create();
    }
}
