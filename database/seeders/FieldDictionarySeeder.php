<?php

namespace Database\Seeders;

use App\Models\DictionaryTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DictionaryTable::create(['table_name' => 'products', 'field_name' => 'name', 'display_name' => 'Product Name', 'description' => 'Name of the product', 'visibility' => 'All']);
        DictionaryTable::create(['table_name' => 'products', 'field_name' => 'price', 'display_name' => 'Product Price', 'description' => 'Price of the product', 'visibility' => 'All']);
        DictionaryTable::create(['table_name' => 'products', 'field_name' => 'quantity', 'display_name' => 'Stock Quantity', 'description' => 'Number of items in stock', 'visibility' => 'All']);

        DictionaryTable::create(['table_name' => 'categories', 'field_name' => 'name', 'display_name' => 'Category Name', 'description' => 'Name of the category', 'visibility' => 'All']);

        DictionaryTable::create(['table_name' => 'customers', 'field_name' => 'first_name', 'display_name' => 'Customer First Name', 'description' => 'First Name of the customer', 'visibility' => 'All']);
        DictionaryTable::create(['table_name' => 'customers', 'field_name' => 'last_name', 'display_name' => 'Customer Last Name', 'description' => 'Last Name of the customer', 'visibility' => 'All']);
        DictionaryTable::create(['table_name' => 'customers', 'field_name' => 'email', 'display_name' => 'Email Address', 'description' => 'Customer email', 'visibility' => 'All']);
    }
}
