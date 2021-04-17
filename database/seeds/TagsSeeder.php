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
        /*$tags_test = array(
            "NEET", "JEE", "UPSC", "AIPMT",
            "CBSE", "ISI", "ISC", "physics",
            "chemistry", "math", "KVPY",
            "challenging", "biology",
            "boards", "easy", "random", "english",
            "history", "civics", "economics"
        );*/

        $tags_test = [
            "Algebra", "Angles", "Circles and Composite of Figures", "Fractions",
            "Gap and Difference Strategies", "Model drawing Strategies", "Number Patterns",
            "Number x Value Strategies", "Percentage", "Pie Charts",
            "Remainder and Equal fraction Strategies", "Repeated Identity Strategies",
            "Simultaneous Euations", "Solid Figures and Nets", "Speed", "Unchanged strategies",
            "Volume of Cubes and Cuboids", "Whole Numbers", "Area of Triangles", "Average",
            "Decimals", "Fraction Strategies", "Percentage Strategies", "Rate", "Ratio",
            "Ratio Strategies", "Whole Number Strategies", "Angles and 8-point Compass",
            "Area and Perimeter", "Factors and Multiples", "Line Graphs",
            "Multiplication and Division", "Symmetry", "Time", "Addition and Subtraction",
            "Bar Graphs", "Length", "Mass", "Money", "Parallel and Perpendicular Lines"
        ];

        foreach ($tags_test as $tag) {
            echo "Creating tag: $tag..\n";

            DB::table('tags')->insert([
                'name' => $tag,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            Storage::put("tags/" . md5($tag), "[]");
        }

        //factory(App\TagsModel::class, numbersT::tags() - 4)->create();
        //factory(App\TagsModel::class, numbersT::tags())->create();
    }
}
