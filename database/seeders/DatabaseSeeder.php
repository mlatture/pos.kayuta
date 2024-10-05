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
//        $this->call(ActivityLogSeeder::class);
//        $this->call(CampsitePicsSeeder::class);
//        $this->call(CustomersTableSeeder::class);
//        $this->call(HikingAndMountainBikingMapsSeeder::class);
//        $this->call(KayutaThemeSongsSeeder::class);
//        $this->call(KayutaVideosSeeder::class);
//        $this->call(NotificationsSeeder::class);
//        $this->call(ProductVendorsSeeder::class);
//        $this->call(ReviewsSeeder::class);
//        $this->call(TaleOfKayutasTableSeeder::class);
//        $this->call(VirtualTourOfBearsSeeder::class);
//        $this->call(VirtualTourOfSitesSeeder::class);


        $this->call(ActivityLogSeeder::class);
//        $this->call(AdminCartSeeder::class);
        $this->call(AdminRolesSeeder::class);
//        $this->call(AdminsSeeder::class);
//        $this->call(AmenitiesSeeder::class);
//        $this->call(AttachmentsSeeder::class);
//        $this->call(BlogsSeeder::class);
//        $this->call(BusinessSettingsSeeder::class);
//        $this->call(CacheLocksSeeder::class);
//        $this->call(CacheSeeder::class);
//        $this->call(CampgroundMapsSeeder::class);
//        $this->call(CampgroundPoliciesSeeder::class);
//        $this->call(CampingSeasonsSeeder::class);
//        $this->call(CampsitePicsSeeder::class);
//        $this->call(CardsOnFilesSeeder::class);
//        $this->call(CartReservationsSeeder::class);
//        $this->call(CategoriesSeeder::class);
//        $this->call(ChattingsSeeder::class);
//        $this->call(ContactsSeeder::class);
//        $this->call(ContentsSeeder::class);
//        $this->call(CouponsSeeder::class);
//        $this->call(CurrenciesTableSeeder::class);
//        $this->call(CustomersTableSeeder::class);
//        //$this->call(DictionaryTablesSeeder::class);
//        $this->call(DirectionsTableSeeder::class);
//        $this->call(DraftsSeeder::class);
//        $this->call(ElectricSeeder::class);
//        $this->call(EmergencyContactsSeeder::class);
//        $this->call(EventsTableSeeder::class);
//        $this->call(FieldDictionarySeeder::class);
//        $this->call(GiftCardsSeeder::class);
//        $this->call(HearAboutUsSeeder::class);
//        $this->call(HelpTopicsSeeder::class);
//        $this->call(HikingAndMountainBikingMapsSeeder::class);
//        $this->call(InfosTableSeeder::class);
//        $this->call(KayutaThemeSongsSeeder::class);
//        $this->call(KayutaVideosSeeder::class);
//        $this->call(LiabilityWaiverSeeder::class);
//        $this->call(LocalAreaAttractionsSeeder::class);
//        $this->call(MenuItemsTableSeeder::class);
//        $this->call(NewsLettersTableSeeder::class);
//        $this->call(NotificationsSeeder::class);
//        $this->call(OrderItemsSeeder::class);
//        $this->call(OrdersSeeder::class);
//        $this->call(OrganizationsSeeder::class);
//        $this->call(PagesSeeder::class);
//        $this->call(PaymentBillsSeeder::class);
//        $this->call(paymentsSeeder::class);
//        $this->call(PosPaymentsSeeder::class);
//        $this->call(ProductsSeeder::class);
//        $this->call(ProductVendorsSeeder::class);
//        $this->call(QuicklinksSeeder::class);
//        $this->call(RateTiersTableSeeder::class);
//        $this->call(ReadingsTableSeeder::class);
//        $this->call(ReceiptsTableSeeder::class);
//        $this->call(ReservationsTableSeeder::class);
//        $this->call(ReviewsSeeder::class);
//        $this->call(SearchFunctionsSeeder::class);
//        $this->call(SeasonalWaitingListsSeeder::class);
//        $this->call(SeasonsTableSeeder::class);
//        $this->call(SettingsSeeder::class);
//        $this->call(SiteClassesTableSeeder::class);
//        $this->call(SiteHookupsTableSeeder::class);
//        $this->call(SiteRigtypesTableSeeder::class);
        $this->call(SitesTableSeeder::class);
//        $this->call(SocialMediasTableSeeder::class);
//        $this->call(SubmenuItemsTableSeeder::class);
//        $this->call(TableWhitelistSeeder::class);
//        $this->call(TaleOfKayutasTableSeeder::class);
//        $this->call(TaxTypesSeeder::class);
//        $this->call(ThemeWeekendsSeeder::class);
//        $this->call(TrailerTongueWeightsSeeder::class);
//        $this->call(TransactionsSeeder::class);
//        $this->call(TranslationsSeeder::class);
//        $this->call(UnitRateSeeder::class);
//        $this->call(UserSeeder::class);
//        $this->call(VirtualTourOfBearsSeeder::class);
//        $this->call(VirtualTourOfSitesSeeder::class);
//        $this->call(WhitelistTablesSeeder::class);

//        $this->call(AdminsSeeder::class);
//        $this->call(AdminRolesSeeder::class);
//        $this->call(OrganizationsSeeder::class);
//        $this->call(ProductsSeeder::class);
//        $this->call(ActivityLogSeeder::class);
//        $this->call(CampgroundMapsSeeder::class);
//        $this->call(CampgroundPoliciesSeeder::class);
//        $this->call(CategoriesSeeder::class);
//        $this->call(BusinessSettingsSeeder::class);
//        $this->call(CurrenciesTableSeeder::class);
//        $this->call(CustomersTableSeeder::class);
//        $this->call(DirectionsTableSeeder::class);
//        $this->call(HikingAndMountainBikingMapsSeeder::class);
//        $this->call(InfosTableSeeder::class);
//        $this->call(RateTiersTableSeeder::class);
//        $this->call(KayutaThemeSongsSeeder::class);
//        $this->call(KayutaVideosSeeder::class);
//        $this->call(LiabilityWaiverSeeder::class);
//        $this->call(LocalAreaAttractionsSeeder::class);
//        $this->call(MenuItemsTableSeeder::class);
//        $this->call(NewsLettersTableSeeder::class);
//        $this->call(ReadingsTableSeeder::class);
//        $this->call(ReceiptsTableSeeder::class);
//        $this->call(SeasonsTableSeeder::class);
//        $this->call(UserSeeder::class);
//        $this->call(SettingsSeeder::class);
//        $this->call(SiteClassesTableSeeder::class);
//        $this->call(SiteHookupsTableSeeder::class);
//        $this->call(SiteRigtypesTableSeeder::class);
//        $this->call(SocialMediasTableSeeder::class);
//        $this->call(SubmenuItemsTableSeeder::class);
//        $this->call(TaleOfKayutasTableSeeder::class);
//        $this->call(SitesTableSeeder::class);
//
//        $this->call(TableWhitelistSeeder::class);
//        $this->call(FieldDictionarySeeder::class);
//        $this->call(RateTiersTableSeeder::class);
//
//        $this->call(AdminCartSeeder::class);
//        $this->call(AmenitiesSeeder::class);
//        $this->call(AttachmentsSeeder::class);
//        $this->call(BlogsSeeder::class);
//        $this->call(CacheSeeder::class);
//        $this->call(CacheLocksSeeder::class);
//        $this->call(CampingSeasonsSeeder::class);
//        $this->call(CampsitePicsSeeder::class);
//        $this->call(CardsOnFilesSeeder::class);
//        $this->call(CartReservationsSeeder::class);
//        $this->call(ChattingsSeeder::class);
//        $this->call(ContactsSeeder::class);
//        $this->call(ContentsSeeder::class);
//        $this->call(CouponsSeeder::class);
//        $this->call(DictionaryTablesSeeder::class);
//        $this->call(DraftsSeeder::class);
//        $this->call(ElectricSeeder::class);
//        $this->call(EmergencyContactsSeeder::class);
//        $this->call(GiftCardsSeeder::class);
//        $this->call(HearAboutUsSeeder::class);
//        $this->call(HelpTopicsSeeder::class);
//        $this->call(NotificationsSeeder::class);
//        $this->call(OrderItemsSeeder::class);
//        $this->call(OrdersSeeder::class);
//        $this->call(PagesSeeder::class);
//        $this->call(PaymentBillsSeeder::class);
//        $this->call(paymentsSeeder::class);
//        $this->call(PosPaymentsSeeder::class);
//        $this->call(ProductVendorsSeeder::class);
//        $this->call(QuicklinksSeeder::class);
//        $this->call(ReservationsTableSeeder::class);
//        $this->call(ReviewsSeeder::class);
//        $this->call(SeasonalWaitingListsSeeder::class);
//        $this->call(TaxTypesSeeder::class);
//        $this->call(ThemeWeekendsSeeder::class);
//        $this->call(TrailerTongueWeightsSeeder::class);
//        $this->call(TranslationsSeeder::class);
//        $this->call(UnitRateSeeder::class);
//        $this->call(VirtualTourOfBearsSeeder::class);
//        $this->call(VirtualTourOfSitesSeeder::class);
//        $this->call(WhitelistTablesSeeder::class);
    }
}
