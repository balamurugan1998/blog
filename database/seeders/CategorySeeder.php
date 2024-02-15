<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Master Category Array
        $category = [
            ['category'=>'Fashion'],
            ['category'=>'Lifestyle'],
            ['category'=>'Business Blogs'],
            ['category'=>'Food'],
            ['category'=>'Travel'],
            ['category'=>'Fitness'],
            ['category'=>'Affiliate Blog'],
            ['category'=>'Finance'],
            ['category'=>'News'],
            ['category'=>'Case Studies'],
            ['category'=>'Music'],
            ['category'=>'Parenting'],
            ['category'=>'Beauty'],
            ['category'=>'Movies'],
            ['category'=>'Gaming'],
            ['category'=>'Atrs'],
            ['category'=>'DIY'],
            ['category'=>'Comparisons'],
            ['category'=>'Checklists'],
            ['category'=>'Personal Blogs']
        ];

        // Looping and Inserting Array's Category into Category Table
        foreach($category as $cat){
            if(Category::where('category',$cat['category'])->doesntExist()){
                Category::create($cat);
            }
        }
    }
}
