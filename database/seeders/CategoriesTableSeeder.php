<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Apparel', 'quick_books_account_name' => 'Apparel Sales', 'account_type' => 'Income', 'notes' => 'Retail clothing'],
            ['name' => 'BBQ/Camping', 'quick_books_account_name' => 'Camping Gear Sales', 'account_type' => 'Income', 'notes' => 'Includes grills, utensils, etc.'],
            ['name' => 'Behind The Counter', 'quick_books_account_name' => 'Concession Sales', 'account_type' => 'Income', 'notes' => 'Items sold from non-self-service areas (e.g., tobacco, ice cream)'],
            ['name' => 'Candy', 'quick_books_account_name' => 'Snack Food Sales', 'account_type' => 'Income', 'notes' => 'Can be grouped with Food if preferred'],
            ['name' => 'Cold Drinks', 'quick_books_account_name' => 'Beverage Sales', 'account_type' => 'Income', 'notes' => 'Non-alcoholic drinks'],
            ['name' => 'Fishing', 'quick_books_account_name' => 'Sporting Goods Sales', 'account_type' => 'Income', 'notes' => 'Bait, tackle, fishing gear'],
            ['name' => 'Food', 'quick_books_account_name' => 'Prepared Food Sales', 'account_type' => 'Income', 'notes' => 'Meals or ready-to-eat food'],
            ['name' => 'Gifts', 'quick_books_account_name' => 'Gift & Souvenir Sales', 'account_type' => 'Income', 'notes' => 'Logo items, keepsakes'],
            ['name' => 'Grocery', 'quick_books_account_name' => 'Grocery Sales', 'account_type' => 'Income', 'notes' => 'Packaged goods, essentials'],
            ['name' => 'Home', 'quick_books_account_name' => 'Household Items Sales', 'account_type' => 'Income', 'notes' => 'Cleaning, small tools, etc.'],
            ['name' => 'Hot Drinks', 'quick_books_account_name' => 'Beverage Sales', 'account_type' => 'Income', 'notes' => 'Can combine with Cold Drinks if desired'],
            ['name' => 'Pool', 'quick_books_account_name' => 'Pool Merchandise/Accessories', 'account_type' => 'Income', 'notes' => 'Goggles, towels, floaties'],
            ['name' => 'RV', 'quick_books_account_name' => 'RV Supplies Sales', 'account_type' => 'Income', 'notes' => 'Hoses, leveling blocks, adapters'],
            ['name' => 'RX', 'quick_books_account_name' => 'Over-the-Counter Medication Sales', 'account_type' => 'Income', 'notes' => 'Mark taxable/nontaxable properly'],
            ['name' => 'Rentals/Services', 'quick_books_account_name' => 'Equipment Rental Income', 'account_type' => 'Income', 'notes' => 'Canoes, carts, etc.'],
            ['name' => 'Toys', 'quick_books_account_name' => 'Toy Sales', 'account_type' => 'Income', 'notes' => 'Games, outdoor toys'],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['name' => $data['name']], $data);
        }

    }
}
