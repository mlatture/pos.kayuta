<?php

namespace Database\Seeders;

use App\Models\WhitelistTable;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(AdminsSeeder::class);
        $this->call(AdminRolesSeeder::class);
        $this->call(ProductVendorsTableSeeder::class);
        $this->call(OrganizationsSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(ActivityLogSeeder::class);
        $this->call(CampgroundMapsSeeder::class);
        $this->call(CampgroundPoliciesSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(BusinessSettingsSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(DirectionsTableSeeder::class);
        $this->call(GiftCardsTableSeeder::class);
        $this->call(HikingAndMountainBikingMapsSeeder::class);
        $this->call(InfosTableSeeder::class);
        $this->call(RateTiersTableSeeder::class);
        $this->call(KayutaThemeSongsSeeder::class);
        $this->call(KayutaVideosSeeder::class);
        $this->call(LiabilityWaiverSeeder::class);
        $this->call(LocalAreaAttractionsSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(NewsLettersTableSeeder::class);
        $this->call(ReadingsTableSeeder::class);
        $this->call(ReceiptsTableSeeder::class);
        $this->call(SeasonsTableSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(SiteClassesTableSeeder::class);
        $this->call(SiteHookupsTableSeeder::class);
        $this->call(SiteRigtypesTableSeeder::class);
        $this->call(SocialMediasTableSeeder::class);
        $this->call(SubmenuItemsTableSeeder::class);
        $this->call(TaleOfKayutasTableSeeder::class);
        $this->call(SitesTableSeeder::class);

        $this->call(TableWhitelistSeeder::class);
        $this->call(FieldDictionarySeeder::class);
        $this->call(RateTiersTableSeeder::class);
    }
}
