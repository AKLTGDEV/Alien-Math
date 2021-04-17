<?php

use Illuminate\Database\Seeder;
use App\numbersT;
use App\tags;
use App\TagsModel;
use App\UserModel;
use App\users;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create an "Admin" account here
         */
        $admin = new UserModel;
        $admin->name = "Syed Nasim";
        $admin->username = "admin";
        $admin->email = "admin@crowdoubt.net";
        $admin->type = "admin";
        $admin->grade = "S1";
        $admin->level = "3";
        $admin->password = bcrypt("13141314");
        $admin->remember_token = str_random(10);
        users::storebio("admin", "Regular boi");
        users::storetags("admin", [
            "Algebra", "Angles"
        ]);

        $admin->save();


        factory(App\UserModel::class, numbersT::users())->create()->each(function ($u) {
            $nos_tags = count(TagsModel::all());
            $tags_halfmark = floor($nos_tags / 2);
            $tag1 = TagsModel::where('id', rand(1, $tags_halfmark))->first();
            $tag2 = TagsModel::where('id', rand($tags_halfmark + 1, $nos_tags))->first();
            $tag3 = TagsModel::where('id', rand(1, $nos_tags))->first();
            while ($tag3 == $tag1 || $tag3 == $tag2) {
                $tag3 = TagsModel::where('id', rand(1, $nos_tags))->first();
            }

            tags::tagfollower_new($tag1->name);
            tags::tagfollower_new($tag2->name);
            tags::tagfollower_new($tag3->name);

            users::storetags($u->username, [
                $tag1->name,
                $tag2->name,
                $tag3->name
            ]);
        });
    }
}
