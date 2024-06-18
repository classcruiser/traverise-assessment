<?php

namespace Database\Seeders;

use App\Events\TenantCreated;
use App\Models\Booking\Location;
use App\Models\Booking\Room;
use App\Models\Booking\RoomInfo;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // create super admin
        $super = User::create([
            'name' => 'Administrator',
            'email' => 'admin@traverise.com',
            'password' => Hash::make('password'),
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create role
        $super_admin = Role::create(['name' => 'Super Admin']);
        
        // create permissions
        $permissions_names = [
            'add booking', 'edit booking', 'view booking', 'delete booking',
            'add payment', 'edit payment', 'view payment', 'delete payment', 'confirm payment',
            'add guest', 'edit guest', 'view guest', 'delete guest',
            'add room', 'edit room', 'delete room',
        ];

        $permissions = collect($permissions_names)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        $permissions_tenant = [
            'add addon', 'add automated email', 'add booking',
            'add camp', 'add document', 'add guest', 'add notes', 'add payment',
            'add room', 'add setting', 'add user', 'confirm payment', 'delete addon', 'delete automated email',
            'delete booking', 'delete guest', 'delete guest room', 'delete payment', 'delete room', 'delete setting',
            'delete user', 'download PDF', 'edit addon', 'edit automated email', 'edit booking', 'edit camp',
            'edit document', 'edit guest','edit guest room', 'edit payment', 'edit prices', 'edit profile',
            'edit room', 'edit user', 'export bookings', 'manage agent', 'manage appearances', 'manage blacklist',
            'manage roles', 'manage special offer', 'manage special package', 'manage voucher', 'save setting', 'search room',
            'see prices', 'send payment confirmation', 'update room', 'view booking', 'view daily report', 'view general report',
            'view guest', 'view income report', 'view monthly report', 'view payment', 'view report', 'view yearly report'
        ];

        $permissions_tenants = collect($permissions_tenant)->map(function ($perm) {
            return ['name' => $perm, 'guard_name' => 'tenant'];
        });

        Permission::insert($permissions->toArray());
        Permission::insert($permissions_tenants->toArray());

        // insert countries
        DB::table('country_codes')->insert([
            ['cc_iso' => 'BGD', 'cc_iso2' => 'BD', 'country_name' => 'Bangladesh', 'phone_code' => '880'],
            ['cc_iso' => 'BEL', 'cc_iso2' => 'BE', 'country_name' => 'Belgium', 'phone_code' => '32'],
            ['cc_iso' => 'BFA', 'cc_iso2' => 'BF', 'country_name' => 'Burkina Faso', 'phone_code' => '226'],
            ['cc_iso' => 'BGR', 'cc_iso2' => 'BG', 'country_name' => 'Bulgaria', 'phone_code' => '359'],
            ['cc_iso' => 'BIH', 'cc_iso2' => 'BA', 'country_name' => 'Bosnia and Herzegovina', 'phone_code' => '387'],
            ['cc_iso' => 'BRB', 'cc_iso2' => 'BB', 'country_name' => 'Barbados', 'phone_code' => '1-246'],
            ['cc_iso' => 'WLF', 'cc_iso2' => 'WF', 'country_name' => 'Wallis and Futuna', 'phone_code' => '681'],
            ['cc_iso' => 'BLM', 'cc_iso2' => 'BL', 'country_name' => 'Saint Barthelemy', 'phone_code' => '590'],
            ['cc_iso' => 'BMU', 'cc_iso2' => 'BM', 'country_name' => 'Bermuda', 'phone_code' => '1-441'],
            ['cc_iso' => 'BRN', 'cc_iso2' => 'BN', 'country_name' => 'Brunei', 'phone_code' => '673'],
            ['cc_iso' => 'BOL', 'cc_iso2' => 'BO', 'country_name' => 'Bolivia', 'phone_code' => '591'],
            ['cc_iso' => 'BHR', 'cc_iso2' => 'BH', 'country_name' => 'Bahrain', 'phone_code' => '973'],
            ['cc_iso' => 'BDI', 'cc_iso2' => 'BI', 'country_name' => 'Burundi', 'phone_code' => '257'],
            ['cc_iso' => 'BEN', 'cc_iso2' => 'BJ', 'country_name' => 'Benin', 'phone_code' => '229'],
            ['cc_iso' => 'BTN', 'cc_iso2' => 'BT', 'country_name' => 'Bhutan', 'phone_code' => '975'],
            ['cc_iso' => 'JAM', 'cc_iso2' => 'JM', 'country_name' => 'Jamaica', 'phone_code' => '+1-876'],
            ['cc_iso' => 'BVT', 'cc_iso2' => 'BV', 'country_name' => 'Bouvet Island', 'phone_code' => ''],
            ['cc_iso' => 'BWA', 'cc_iso2' => 'BW', 'country_name' => 'Botswana', 'phone_code' => '267'],
            ['cc_iso' => 'WSM', 'cc_iso2' => 'WS', 'country_name' => 'Samoa', 'phone_code' => '685'],
            ['cc_iso' => 'BES', 'cc_iso2' => 'BQ', 'country_name' => 'Bonaire, Saint Eustatius and Saba ', 'phone_code' => '599'],
            ['cc_iso' => 'BRA', 'cc_iso2' => 'BR', 'country_name' => 'Brazil', 'phone_code' => '55'],
            ['cc_iso' => 'BHS', 'cc_iso2' => 'BS', 'country_name' => 'Bahamas', 'phone_code' => '1-242'],
            ['cc_iso' => 'JEY', 'cc_iso2' => 'JE', 'country_name' => 'Jersey', 'phone_code' => '+44-1534'],
            ['cc_iso' => 'BLR', 'cc_iso2' => 'BY', 'country_name' => 'Belarus', 'phone_code' => '375'],
            ['cc_iso' => 'BLZ', 'cc_iso2' => 'BZ', 'country_name' => 'Belize', 'phone_code' => '501'],
            ['cc_iso' => 'RUS', 'cc_iso2' => 'RU', 'country_name' => 'Russia', 'phone_code' => '7'],
            ['cc_iso' => 'RWA', 'cc_iso2' => 'RW', 'country_name' => 'Rwanda', 'phone_code' => '250'],
            ['cc_iso' => 'SRB', 'cc_iso2' => 'RS', 'country_name' => 'Serbia', 'phone_code' => '381'],
            ['cc_iso' => 'TLS', 'cc_iso2' => 'TL', 'country_name' => 'East Timor', 'phone_code' => '670'],
            ['cc_iso' => 'REU', 'cc_iso2' => 'RE', 'country_name' => 'Reunion', 'phone_code' => '262'],
            ['cc_iso' => 'TKM', 'cc_iso2' => 'TM', 'country_name' => 'Turkmenistan', 'phone_code' => '993'],
            ['cc_iso' => 'TJK', 'cc_iso2' => 'TJ', 'country_name' => 'Tajikistan', 'phone_code' => '992'],
            ['cc_iso' => 'ROU', 'cc_iso2' => 'RO', 'country_name' => 'Romania', 'phone_code' => '40'],
            ['cc_iso' => 'TKL', 'cc_iso2' => 'TK', 'country_name' => 'Tokelau', 'phone_code' => '690'],
            ['cc_iso' => 'GNB', 'cc_iso2' => 'GW', 'country_name' => 'Guinea-Bissau', 'phone_code' => '245'],
            ['cc_iso' => 'GUM', 'cc_iso2' => 'GU', 'country_name' => 'Guam', 'phone_code' => '+1-671'],
            ['cc_iso' => 'GTM', 'cc_iso2' => 'GT', 'country_name' => 'Guatemala', 'phone_code' => '502'],
            ['cc_iso' => 'SGS', 'cc_iso2' => 'GS', 'country_name' => 'South Georgia and the South Sandwich Islands', 'phone_code' => ''],
            ['cc_iso' => 'GRC', 'cc_iso2' => 'GR', 'country_name' => 'Greece', 'phone_code' => '30'],
            ['cc_iso' => 'GNQ', 'cc_iso2' => 'GQ', 'country_name' => 'Equatorial Guinea', 'phone_code' => '240'],
            ['cc_iso' => 'GLP', 'cc_iso2' => 'GP', 'country_name' => 'Guadeloupe', 'phone_code' => '590'],
            ['cc_iso' => 'JPN', 'cc_iso2' => 'JP', 'country_name' => 'Japan', 'phone_code' => '81'],
            ['cc_iso' => 'GUY', 'cc_iso2' => 'GY', 'country_name' => 'Guyana', 'phone_code' => '592'],
            ['cc_iso' => 'GGY', 'cc_iso2' => 'GG', 'country_name' => 'Guernsey', 'phone_code' => '+44-1481'],
            ['cc_iso' => 'GUF', 'cc_iso2' => 'GF', 'country_name' => 'French Guiana', 'phone_code' => '594'],
            ['cc_iso' => 'GEO', 'cc_iso2' => 'GE', 'country_name' => 'Georgia', 'phone_code' => '995'],
            ['cc_iso' => 'GRD', 'cc_iso2' => 'GD', 'country_name' => 'Grenada', 'phone_code' => '+1-473'],
            ['cc_iso' => 'GBR', 'cc_iso2' => 'GB', 'country_name' => 'United Kingdom', 'phone_code' => '44'],
            ['cc_iso' => 'GAB', 'cc_iso2' => 'GA', 'country_name' => 'Gabon', 'phone_code' => '241'],
            ['cc_iso' => 'SLV', 'cc_iso2' => 'SV', 'country_name' => 'El Salvador', 'phone_code' => '503'],
            ['cc_iso' => 'GIN', 'cc_iso2' => 'GN', 'country_name' => 'Guinea', 'phone_code' => '224'],
            ['cc_iso' => 'GMB', 'cc_iso2' => 'GM', 'country_name' => 'Gambia', 'phone_code' => '220'],
            ['cc_iso' => 'GRL', 'cc_iso2' => 'GL', 'country_name' => 'Greenland', 'phone_code' => '299'],
            ['cc_iso' => 'GIB', 'cc_iso2' => 'GI', 'country_name' => 'Gibraltar', 'phone_code' => '350'],
            ['cc_iso' => 'GHA', 'cc_iso2' => 'GH', 'country_name' => 'Ghana', 'phone_code' => '233'],
            ['cc_iso' => 'OMN', 'cc_iso2' => 'OM', 'country_name' => 'Oman', 'phone_code' => '968'],
            ['cc_iso' => 'TUN', 'cc_iso2' => 'TN', 'country_name' => 'Tunisia', 'phone_code' => '216'],
            ['cc_iso' => 'JOR', 'cc_iso2' => 'JO', 'country_name' => 'Jordan', 'phone_code' => '962'],
            ['cc_iso' => 'HRV', 'cc_iso2' => 'HR', 'country_name' => 'Croatia', 'phone_code' => '385'],
            ['cc_iso' => 'HTI', 'cc_iso2' => 'HT', 'country_name' => 'Haiti', 'phone_code' => '509'],
            ['cc_iso' => 'HUN', 'cc_iso2' => 'HU', 'country_name' => 'Hungary', 'phone_code' => '36'],
            ['cc_iso' => 'HKG', 'cc_iso2' => 'HK', 'country_name' => 'Hong Kong', 'phone_code' => '852'],
            ['cc_iso' => 'HND', 'cc_iso2' => 'HN', 'country_name' => 'Honduras', 'phone_code' => '504'],
            ['cc_iso' => 'HMD', 'cc_iso2' => 'HM', 'country_name' => 'Heard Island and McDonald Islands', 'phone_code' => ' '],
            ['cc_iso' => 'VEN', 'cc_iso2' => 'VE', 'country_name' => 'Venezuela', 'phone_code' => '58'],
            ['cc_iso' => 'PRI', 'cc_iso2' => 'PR', 'country_name' => 'Puerto Rico', 'phone_code' => '1-787'],
            ['cc_iso' => 'PSE', 'cc_iso2' => 'PS', 'country_name' => 'Palestinian Territory', 'phone_code' => '970'],
            ['cc_iso' => 'PLW', 'cc_iso2' => 'PW', 'country_name' => 'Palau', 'phone_code' => '680'],
            ['cc_iso' => 'PRT', 'cc_iso2' => 'PT', 'country_name' => 'Portugal', 'phone_code' => '351'],
            ['cc_iso' => 'SJM', 'cc_iso2' => 'SJ', 'country_name' => 'Svalbard and Jan Mayen', 'phone_code' => '47'],
            ['cc_iso' => 'PRY', 'cc_iso2' => 'PY', 'country_name' => 'Paraguay', 'phone_code' => '595'],
            ['cc_iso' => 'IRQ', 'cc_iso2' => 'IQ', 'country_name' => 'Iraq', 'phone_code' => '964'],
            ['cc_iso' => 'PAN', 'cc_iso2' => 'PA', 'country_name' => 'Panama', 'phone_code' => '507'],
            ['cc_iso' => 'PYF', 'cc_iso2' => 'PF', 'country_name' => 'French Polynesia', 'phone_code' => '689'],
            ['cc_iso' => 'PNG', 'cc_iso2' => 'PG', 'country_name' => 'Papua New Guinea', 'phone_code' => '675'],
            ['cc_iso' => 'PER', 'cc_iso2' => 'PE', 'country_name' => 'Peru', 'phone_code' => '51'],
            ['cc_iso' => 'PAK', 'cc_iso2' => 'PK', 'country_name' => 'Pakistan', 'phone_code' => '92'],
            ['cc_iso' => 'PHL', 'cc_iso2' => 'PH', 'country_name' => 'Philippines', 'phone_code' => '63'],
            ['cc_iso' => 'PCN', 'cc_iso2' => 'PN', 'country_name' => 'Pitcairn', 'phone_code' => '870'],
            ['cc_iso' => 'POL', 'cc_iso2' => 'PL', 'country_name' => 'Poland', 'phone_code' => '48'],
            ['cc_iso' => 'SPM', 'cc_iso2' => 'PM', 'country_name' => 'Saint Pierre and Miquelon', 'phone_code' => '508'],
            ['cc_iso' => 'ZMB', 'cc_iso2' => 'ZM', 'country_name' => 'Zambia', 'phone_code' => '260'],
            ['cc_iso' => 'ESH', 'cc_iso2' => 'EH', 'country_name' => 'Western Sahara', 'phone_code' => '212'],
            ['cc_iso' => 'EST', 'cc_iso2' => 'EE', 'country_name' => 'Estonia', 'phone_code' => '372'],
            ['cc_iso' => 'EGY', 'cc_iso2' => 'EG', 'country_name' => 'Egypt', 'phone_code' => '20'],
            ['cc_iso' => 'ZAF', 'cc_iso2' => 'ZA', 'country_name' => 'South Africa', 'phone_code' => '27'],
            ['cc_iso' => 'ECU', 'cc_iso2' => 'EC', 'country_name' => 'Ecuador', 'phone_code' => '593'],
            ['cc_iso' => 'ITA', 'cc_iso2' => 'IT', 'country_name' => 'Italy', 'phone_code' => '39'],
            ['cc_iso' => 'VNM', 'cc_iso2' => 'VN', 'country_name' => 'Vietnam', 'phone_code' => '84'],
            ['cc_iso' => 'SLB', 'cc_iso2' => 'SB', 'country_name' => 'Solomon Islands', 'phone_code' => '677'],
            ['cc_iso' => 'ETH', 'cc_iso2' => 'ET', 'country_name' => 'Ethiopia', 'phone_code' => '251'],
            ['cc_iso' => 'SOM', 'cc_iso2' => 'SO', 'country_name' => 'Somalia', 'phone_code' => '252'],
            ['cc_iso' => 'ZWE', 'cc_iso2' => 'ZW', 'country_name' => 'Zimbabwe', 'phone_code' => '263'],
            ['cc_iso' => 'SAU', 'cc_iso2' => 'SA', 'country_name' => 'Saudi Arabia', 'phone_code' => '966'],
            ['cc_iso' => 'ESP', 'cc_iso2' => 'ES', 'country_name' => 'Spain', 'phone_code' => '34'],
            ['cc_iso' => 'ERI', 'cc_iso2' => 'ER', 'country_name' => 'Eritrea', 'phone_code' => '291'],
            ['cc_iso' => 'MNE', 'cc_iso2' => 'ME', 'country_name' => 'Montenegro', 'phone_code' => '382'],
            ['cc_iso' => 'MDA', 'cc_iso2' => 'MD', 'country_name' => 'Moldova', 'phone_code' => '373'],
            ['cc_iso' => 'MDG', 'cc_iso2' => 'MG', 'country_name' => 'Madagascar', 'phone_code' => '261'],
            ['cc_iso' => 'MAF', 'cc_iso2' => 'MF', 'country_name' => 'Saint Martin', 'phone_code' => '590'],
            ['cc_iso' => 'MAR', 'cc_iso2' => 'MA', 'country_name' => 'Morocco', 'phone_code' => '212'],
            ['cc_iso' => 'MCO', 'cc_iso2' => 'MC', 'country_name' => 'Monaco', 'phone_code' => '377'],
            ['cc_iso' => 'UZB', 'cc_iso2' => 'UZ', 'country_name' => 'Uzbekistan', 'phone_code' => '998'],
            ['cc_iso' => 'MMR', 'cc_iso2' => 'MM', 'country_name' => 'Myanmar', 'phone_code' => '95'],
            ['cc_iso' => 'MLI', 'cc_iso2' => 'ML', 'country_name' => 'Mali', 'phone_code' => '223'],
            ['cc_iso' => 'MAC', 'cc_iso2' => 'MO', 'country_name' => 'Macao', 'phone_code' => '853'],
            ['cc_iso' => 'MNG', 'cc_iso2' => 'MN', 'country_name' => 'Mongolia', 'phone_code' => '976'],
            ['cc_iso' => 'MHL', 'cc_iso2' => 'MH', 'country_name' => 'Marshall Islands', 'phone_code' => '692'],
            ['cc_iso' => 'MKD', 'cc_iso2' => 'MK', 'country_name' => 'Macedonia', 'phone_code' => '389'],
            ['cc_iso' => 'MUS', 'cc_iso2' => 'MU', 'country_name' => 'Mauritius', 'phone_code' => '230'],
            ['cc_iso' => 'MLT', 'cc_iso2' => 'MT', 'country_name' => 'Malta', 'phone_code' => '356'],
            ['cc_iso' => 'MWI', 'cc_iso2' => 'MW', 'country_name' => 'Malawi', 'phone_code' => '265'],
            ['cc_iso' => 'MDV', 'cc_iso2' => 'MV', 'country_name' => 'Maldives', 'phone_code' => '960'],
            ['cc_iso' => 'MTQ', 'cc_iso2' => 'MQ', 'country_name' => 'Martinique', 'phone_code' => '596'],
            ['cc_iso' => 'MNP', 'cc_iso2' => 'MP', 'country_name' => 'Northern Mariana Islands', 'phone_code' => '1-670'],
            ['cc_iso' => 'MSR', 'cc_iso2' => 'MS', 'country_name' => 'Montserrat', 'phone_code' => '1-664'],
            ['cc_iso' => 'MRT', 'cc_iso2' => 'MR', 'country_name' => 'Mauritania', 'phone_code' => '222'],
            ['cc_iso' => 'IMN', 'cc_iso2' => 'IM', 'country_name' => 'Isle of Man', 'phone_code' => '+44-1624'],
            ['cc_iso' => 'UGA', 'cc_iso2' => 'UG', 'country_name' => 'Uganda', 'phone_code' => '256'],
            ['cc_iso' => 'TZA', 'cc_iso2' => 'TZ', 'country_name' => 'Tanzania', 'phone_code' => '191'],
            ['cc_iso' => 'MYS', 'cc_iso2' => 'MY', 'country_name' => 'Malaysia', 'phone_code' => '60'],
            ['cc_iso' => 'MEX', 'cc_iso2' => 'MX', 'country_name' => 'Mexico', 'phone_code' => '52'],
            ['cc_iso' => 'ISR', 'cc_iso2' => 'IL', 'country_name' => 'Israel', 'phone_code' => '972'],
            ['cc_iso' => 'FRA', 'cc_iso2' => 'FR', 'country_name' => 'France', 'phone_code' => '33'],
            ['cc_iso' => 'IOT', 'cc_iso2' => 'IO', 'country_name' => 'British Indian Ocean Territory', 'phone_code' => '246'],
            ['cc_iso' => 'SHN', 'cc_iso2' => 'SH', 'country_name' => 'Saint Helena', 'phone_code' => '290'],
            ['cc_iso' => 'FIN', 'cc_iso2' => 'FI', 'country_name' => 'Finland', 'phone_code' => '358'],
            ['cc_iso' => 'FJI', 'cc_iso2' => 'FJ', 'country_name' => 'Fiji', 'phone_code' => '679'],
            ['cc_iso' => 'FLK', 'cc_iso2' => 'FK', 'country_name' => 'Falkland Islands', 'phone_code' => '500'],
            ['cc_iso' => 'FSM', 'cc_iso2' => 'FM', 'country_name' => 'Micronesia', 'phone_code' => '691'],
            ['cc_iso' => 'FRO', 'cc_iso2' => 'FO', 'country_name' => 'Faroe Islands', 'phone_code' => '298'],
            ['cc_iso' => 'NIC', 'cc_iso2' => 'NI', 'country_name' => 'Nicaragua', 'phone_code' => '505'],
            ['cc_iso' => 'NLD', 'cc_iso2' => 'NL', 'country_name' => 'Netherlands', 'phone_code' => '31'],
            ['cc_iso' => 'NOR', 'cc_iso2' => 'NO', 'country_name' => 'Norway', 'phone_code' => '47'],
            ['cc_iso' => 'NAM', 'cc_iso2' => 'NA', 'country_name' => 'Namibia', 'phone_code' => '264'],
            ['cc_iso' => 'VUT', 'cc_iso2' => 'VU', 'country_name' => 'Vanuatu', 'phone_code' => '678'],
            ['cc_iso' => 'NCL', 'cc_iso2' => 'NC', 'country_name' => 'New Caledonia', 'phone_code' => '687'],
            ['cc_iso' => 'NER', 'cc_iso2' => 'NE', 'country_name' => 'Niger', 'phone_code' => '227'],
            ['cc_iso' => 'NFK', 'cc_iso2' => 'NF', 'country_name' => 'Norfolk Island', 'phone_code' => '672'],
            ['cc_iso' => 'NGA', 'cc_iso2' => 'NG', 'country_name' => 'Nigeria', 'phone_code' => '234'],
            ['cc_iso' => 'NZL', 'cc_iso2' => 'NZ', 'country_name' => 'New Zealand', 'phone_code' => '64'],
            ['cc_iso' => 'NPL', 'cc_iso2' => 'NP', 'country_name' => 'Nepal', 'phone_code' => '977'],
            ['cc_iso' => 'NRU', 'cc_iso2' => 'NR', 'country_name' => 'Nauru', 'phone_code' => '674'],
            ['cc_iso' => 'NIU', 'cc_iso2' => 'NU', 'country_name' => 'Niue', 'phone_code' => '683'],
            ['cc_iso' => 'COK', 'cc_iso2' => 'CK', 'country_name' => 'Cook Islands', 'phone_code' => '682'],
            ['cc_iso' => 'XKX', 'cc_iso2' => 'XK', 'country_name' => 'Kosovo', 'phone_code' => ''],
            ['cc_iso' => 'CIV', 'cc_iso2' => 'CI', 'country_name' => 'Ivory Coast', 'phone_code' => '225'],
            ['cc_iso' => 'CHE', 'cc_iso2' => 'CH', 'country_name' => 'Switzerland', 'phone_code' => '41'],
            ['cc_iso' => 'COL', 'cc_iso2' => 'CO', 'country_name' => 'Colombia', 'phone_code' => '57'],
            ['cc_iso' => 'CHN', 'cc_iso2' => 'CN', 'country_name' => 'China', 'phone_code' => '86'],
            ['cc_iso' => 'CMR', 'cc_iso2' => 'CM', 'country_name' => 'Cameroon', 'phone_code' => '237'],
            ['cc_iso' => 'CHL', 'cc_iso2' => 'CL', 'country_name' => 'Chile', 'phone_code' => '56'],
            ['cc_iso' => 'CCK', 'cc_iso2' => 'CC', 'country_name' => 'Cocos Islands', 'phone_code' => '61'],
            ['cc_iso' => 'CAN', 'cc_iso2' => 'CA', 'country_name' => 'Canada', 'phone_code' => '1'],
            ['cc_iso' => 'COG', 'cc_iso2' => 'CG', 'country_name' => 'Republic of the Congo', 'phone_code' => '242'],
            ['cc_iso' => 'CAF', 'cc_iso2' => 'CF', 'country_name' => 'Central African Republic', 'phone_code' => '236'],
            ['cc_iso' => 'COD', 'cc_iso2' => 'CD', 'country_name' => 'Democratic Republic of the Congo', 'phone_code' => '243'],
            ['cc_iso' => 'CZE', 'cc_iso2' => 'CZ', 'country_name' => 'Czech Republic', 'phone_code' => '420'],
            ['cc_iso' => 'CYP', 'cc_iso2' => 'CY', 'country_name' => 'Cyprus', 'phone_code' => '357'],
            ['cc_iso' => 'CXR', 'cc_iso2' => 'CX', 'country_name' => 'Christmas Island', 'phone_code' => '61'],
            ['cc_iso' => 'CRI', 'cc_iso2' => 'CR', 'country_name' => 'Costa Rica', 'phone_code' => '506'],
            ['cc_iso' => 'CUW', 'cc_iso2' => 'CW', 'country_name' => 'Curacao', 'phone_code' => '599'],
            ['cc_iso' => 'CPV', 'cc_iso2' => 'CV', 'country_name' => 'Cape Verde', 'phone_code' => '238'],
            ['cc_iso' => 'CUB', 'cc_iso2' => 'CU', 'country_name' => 'Cuba', 'phone_code' => '53'],
            ['cc_iso' => 'SWZ', 'cc_iso2' => 'SZ', 'country_name' => 'Swaziland', 'phone_code' => '268'],
            ['cc_iso' => 'SYR', 'cc_iso2' => 'SY', 'country_name' => 'Syria', 'phone_code' => '963'],
            ['cc_iso' => 'SXM', 'cc_iso2' => 'SX', 'country_name' => 'Sint Maarten', 'phone_code' => '599'],
            ['cc_iso' => 'KGZ', 'cc_iso2' => 'KG', 'country_name' => 'Kyrgyzstan', 'phone_code' => '996'],
            ['cc_iso' => 'KEN', 'cc_iso2' => 'KE', 'country_name' => 'Kenya', 'phone_code' => '254'],
            ['cc_iso' => 'SSD', 'cc_iso2' => 'SS', 'country_name' => 'South Sudan', 'phone_code' => '211'],
            ['cc_iso' => 'SUR', 'cc_iso2' => 'SR', 'country_name' => 'Suriname', 'phone_code' => '597'],
            ['cc_iso' => 'KIR', 'cc_iso2' => 'KI', 'country_name' => 'Kiribati', 'phone_code' => '686'],
            ['cc_iso' => 'KHM', 'cc_iso2' => 'KH', 'country_name' => 'Cambodia', 'phone_code' => '855'],
            ['cc_iso' => 'KNA', 'cc_iso2' => 'KN', 'country_name' => 'Saint Kitts and Nevis', 'phone_code' => '1-869'],
            ['cc_iso' => 'COM', 'cc_iso2' => 'KM', 'country_name' => 'Comoros', 'phone_code' => '269'],
            ['cc_iso' => 'STP', 'cc_iso2' => 'ST', 'country_name' => 'Sao Tome and Principe', 'phone_code' => '239'],
            ['cc_iso' => 'SVK', 'cc_iso2' => 'SK', 'country_name' => 'Slovakia', 'phone_code' => '421'],
            ['cc_iso' => 'KOR', 'cc_iso2' => 'KR', 'country_name' => 'South Korea', 'phone_code' => '82'],
            ['cc_iso' => 'SVN', 'cc_iso2' => 'SI', 'country_name' => 'Slovenia', 'phone_code' => '386'],
            ['cc_iso' => 'PRK', 'cc_iso2' => 'KP', 'country_name' => 'North Korea', 'phone_code' => '850'],
            ['cc_iso' => 'KWT', 'cc_iso2' => 'KW', 'country_name' => 'Kuwait', 'phone_code' => '965'],
            ['cc_iso' => 'SEN', 'cc_iso2' => 'SN', 'country_name' => 'Senegal', 'phone_code' => '221'],
            ['cc_iso' => 'SMR', 'cc_iso2' => 'SM', 'country_name' => 'San Marino', 'phone_code' => '378'],
            ['cc_iso' => 'SLE', 'cc_iso2' => 'SL', 'country_name' => 'Sierra Leone', 'phone_code' => '232'],
            ['cc_iso' => 'SYC', 'cc_iso2' => 'SC', 'country_name' => 'Seychelles', 'phone_code' => '248'],
            ['cc_iso' => 'KAZ', 'cc_iso2' => 'KZ', 'country_name' => 'Kazakhstan', 'phone_code' => '7'],
            ['cc_iso' => 'CYM', 'cc_iso2' => 'KY', 'country_name' => 'Cayman Islands', 'phone_code' => '1-345'],
            ['cc_iso' => 'SGP', 'cc_iso2' => 'SG', 'country_name' => 'Singapore', 'phone_code' => '65'],
            ['cc_iso' => 'SWE', 'cc_iso2' => 'SE', 'country_name' => 'Sweden', 'phone_code' => '46'],
            ['cc_iso' => 'SDN', 'cc_iso2' => 'SD', 'country_name' => 'Sudan', 'phone_code' => '249'],
            ['cc_iso' => 'DOM', 'cc_iso2' => 'DO', 'country_name' => 'Dominican Republic', 'phone_code' => '1-809'],
            ['cc_iso' => 'DMA', 'cc_iso2' => 'DM', 'country_name' => 'Dominica', 'phone_code' => '1-767'],
            ['cc_iso' => 'DJI', 'cc_iso2' => 'DJ', 'country_name' => 'Djibouti', 'phone_code' => '253'],
            ['cc_iso' => 'DNK', 'cc_iso2' => 'DK', 'country_name' => 'Denmark', 'phone_code' => '45'],
            ['cc_iso' => 'VGB', 'cc_iso2' => 'VG', 'country_name' => 'British Virgin Islands', 'phone_code' => '1-284'],
            ['cc_iso' => 'DEU', 'cc_iso2' => 'DE', 'country_name' => 'Germany', 'phone_code' => '49'],
            ['cc_iso' => 'YEM', 'cc_iso2' => 'YE', 'country_name' => 'Yemen', 'phone_code' => '967'],
            ['cc_iso' => 'DZA', 'cc_iso2' => 'DZ', 'country_name' => 'Algeria', 'phone_code' => '213'],
            ['cc_iso' => 'USA', 'cc_iso2' => 'US', 'country_name' => 'United States', 'phone_code' => '1'],
            ['cc_iso' => 'URY', 'cc_iso2' => 'UY', 'country_name' => 'Uruguay', 'phone_code' => '598'],
            ['cc_iso' => 'MYT', 'cc_iso2' => 'YT', 'country_name' => 'Mayotte', 'phone_code' => '262'],
            ['cc_iso' => 'UMI', 'cc_iso2' => 'UM', 'country_name' => 'United States Minor Outlying Islands', 'phone_code' => '1'],
            ['cc_iso' => 'LBN', 'cc_iso2' => 'LB', 'country_name' => 'Lebanon', 'phone_code' => '961'],
            ['cc_iso' => 'LCA', 'cc_iso2' => 'LC', 'country_name' => 'Saint Lucia', 'phone_code' => '1-758'],
            ['cc_iso' => 'LAO', 'cc_iso2' => 'LA', 'country_name' => 'Laos', 'phone_code' => '856'],
            ['cc_iso' => 'TUV', 'cc_iso2' => 'TV', 'country_name' => 'Tuvalu', 'phone_code' => '688'],
            ['cc_iso' => 'TWN', 'cc_iso2' => 'TW', 'country_name' => 'Taiwan', 'phone_code' => '886'],
            ['cc_iso' => 'TTO', 'cc_iso2' => 'TT', 'country_name' => 'Trinidad and Tobago', 'phone_code' => '1-868'],
            ['cc_iso' => 'TUR', 'cc_iso2' => 'TR', 'country_name' => 'Turkey', 'phone_code' => '90'],
            ['cc_iso' => 'LKA', 'cc_iso2' => 'LK', 'country_name' => 'Sri Lanka', 'phone_code' => '94'],
            ['cc_iso' => 'LIE', 'cc_iso2' => 'LI', 'country_name' => 'Liechtenstein', 'phone_code' => '423'],
            ['cc_iso' => 'LVA', 'cc_iso2' => 'LV', 'country_name' => 'Latvia', 'phone_code' => '371'],
            ['cc_iso' => 'TON', 'cc_iso2' => 'TO', 'country_name' => 'Tonga', 'phone_code' => '676'],
            ['cc_iso' => 'LTU', 'cc_iso2' => 'LT', 'country_name' => 'Lithuania', 'phone_code' => '370'],
            ['cc_iso' => 'LUX', 'cc_iso2' => 'LU', 'country_name' => 'Luxembourg', 'phone_code' => '352'],
            ['cc_iso' => 'LBR', 'cc_iso2' => 'LR', 'country_name' => 'Liberia', 'phone_code' => '231'],
            ['cc_iso' => 'LSO', 'cc_iso2' => 'LS', 'country_name' => 'Lesotho', 'phone_code' => '266'],
            ['cc_iso' => 'THA', 'cc_iso2' => 'TH', 'country_name' => 'Thailand', 'phone_code' => '66'],
            ['cc_iso' => 'ATF', 'cc_iso2' => 'TF', 'country_name' => 'French Southern Territories', 'phone_code' => ''],
            ['cc_iso' => 'TGO', 'cc_iso2' => 'TG', 'country_name' => 'Togo', 'phone_code' => '228'],
            ['cc_iso' => 'TCD', 'cc_iso2' => 'TD', 'country_name' => 'Chad', 'phone_code' => '235'],
            ['cc_iso' => 'TCA', 'cc_iso2' => 'TC', 'country_name' => 'Turks and Caicos Islands', 'phone_code' => '1-649'],
            ['cc_iso' => 'LBY', 'cc_iso2' => 'LY', 'country_name' => 'Libya', 'phone_code' => '218'],
            ['cc_iso' => 'VAT', 'cc_iso2' => 'VA', 'country_name' => 'Vatican', 'phone_code' => '379'],
            ['cc_iso' => 'VCT', 'cc_iso2' => 'VC', 'country_name' => 'Saint Vincent and the Grenadines', 'phone_code' => '1-784'],
            ['cc_iso' => 'ARE', 'cc_iso2' => 'AE', 'country_name' => 'United Arab Emirates', 'phone_code' => '971'],
            ['cc_iso' => 'AND', 'cc_iso2' => 'AD', 'country_name' => 'Andorra', 'phone_code' => '376'],
            ['cc_iso' => 'ATG', 'cc_iso2' => 'AG', 'country_name' => 'Antigua and Barbuda', 'phone_code' => '1-268'],
            ['cc_iso' => 'AFG', 'cc_iso2' => 'AF', 'country_name' => 'Afghanistan', 'phone_code' => '93'],
            ['cc_iso' => 'AIA', 'cc_iso2' => 'AI', 'country_name' => 'Anguilla', 'phone_code' => '1-264'],
            ['cc_iso' => 'VIR', 'cc_iso2' => 'VI', 'country_name' => 'U.S. Virgin Islands', 'phone_code' => '1-340'],
            ['cc_iso' => 'ISL', 'cc_iso2' => 'IS', 'country_name' => 'Iceland', 'phone_code' => '354'],
            ['cc_iso' => 'IRN', 'cc_iso2' => 'IR', 'country_name' => 'Iran', 'phone_code' => '98'],
            ['cc_iso' => 'ARM', 'cc_iso2' => 'AM', 'country_name' => 'Armenia', 'phone_code' => '374'],
            ['cc_iso' => 'ALB', 'cc_iso2' => 'AL', 'country_name' => 'Albania', 'phone_code' => '355'],
            ['cc_iso' => 'AGO', 'cc_iso2' => 'AO', 'country_name' => 'Angola', 'phone_code' => '244'],
            ['cc_iso' => 'ATA', 'cc_iso2' => 'AQ', 'country_name' => 'Antarctica', 'phone_code' => ''],
            ['cc_iso' => 'ASM', 'cc_iso2' => 'AS', 'country_name' => 'American Samoa', 'phone_code' => '1-684'],
            ['cc_iso' => 'ARG', 'cc_iso2' => 'AR', 'country_name' => 'Argentina', 'phone_code' => '54'],
            ['cc_iso' => 'AUS', 'cc_iso2' => 'AU', 'country_name' => 'Australia', 'phone_code' => '61'],
            ['cc_iso' => 'AUT', 'cc_iso2' => 'AT', 'country_name' => 'Austria', 'phone_code' => '43'],
            ['cc_iso' => 'ABW', 'cc_iso2' => 'AW', 'country_name' => 'Aruba', 'phone_code' => '297'],
            ['cc_iso' => 'IND', 'cc_iso2' => 'IN', 'country_name' => 'India', 'phone_code' => '91'],
            ['cc_iso' => 'ALA', 'cc_iso2' => 'AX', 'country_name' => 'Aland Islands', 'phone_code' => '358-18'],
            ['cc_iso' => 'AZE', 'cc_iso2' => 'AZ', 'country_name' => 'Azerbaijan', 'phone_code' => '994'],
            ['cc_iso' => 'IRL', 'cc_iso2' => 'IE', 'country_name' => 'Ireland', 'phone_code' => '353'],
            ['cc_iso' => 'IDN', 'cc_iso2' => 'ID', 'country_name' => 'Indonesia', 'phone_code' => '62'],
            ['cc_iso' => 'UKR', 'cc_iso2' => 'UA', 'country_name' => 'Ukraine', 'phone_code' => '380'],
            ['cc_iso' => 'QAT', 'cc_iso2' => 'QA', 'country_name' => 'Qatar', 'phone_code' => '974'],
            ['cc_iso' => 'MOZ', 'cc_iso2' => 'MZ', 'country_name' => 'Mozambique', 'phone_code' => '258'],
        ]);

        // create demo tenant
        $tenant = Tenant::create([
            'id' => 'demo',
            'plan' => 'premium',
            'first_name' => 'Admin',
            'last_name' => 'Istrator',
            'company' => 'Traverise',
            'email' => 'admin@traverise.com',
            'phone' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'zip' => null,
            'country' => 'DE',
            'is_active' => 1,
            'stripe_account_id' => 'acct_1MlDNjDBdj2euGhq',
            'stripe_onboarding_process' => 0,
        ]);

        $super = [
            'email' => 'admin@traverise.com',
            'password' => Hash::make('password')
        ];

        TenantCreated::dispatch($tenant, $super);

        // default camp
        $camp = Location::create([
            'tenant_id' => $tenant->id,
            'name' => 'Demo camp',
            'short_name' => 'DEMO',
            'abbr' => 'DEMO',
            'contact_email' => 'demo@localhost',
            'phone' => 123456,
            'price_type' => 'guest',
            'allow_pending' => 1,
            'duration_discount' => 0,
            'enable_deposit' => 1,
            'deposit_type' => 'percent',
            'deposit_value' => 20,
            'deposit_due' => 1,
            'service' => 'Lorem ipsum',
            'minimum_checkin' => date('Y-m-d'),
            'minimum_nights' => 1,
            'admin_visible' => 1,
            'active' => 1,
            'has_arrival_rule' => 0,
        ]);

        $room = Room::create([
            'tenant_id' => $tenant->id,
            'location_id' => $camp->id,
            'name' => 'Double Room',
            'availability' => 'auto',
            'room_short_description' => 'Lorem ipsum',
            'room_description' => 'Lorem ipsum',
            'inclusions' => 'Lorem ipsum',
            'room_type' => 'Shared',
            'capacity' => 4,
            'bed_type' => json_encode(['Double', 'Twin']),
            'bathroom_type' => 'Private',
            'default_price' => 19.99,
            'min_nights' => 1,
            'min_guest' => 1,
            'allow_private' => 1,
            'force_private' => 0,
            'allow_pending' => 1,
            'empty_fee_low' => 10,
            'empty_fee_main' => 15,
            'empty_fee_peak' => 20,
            'active' => 1,
            'admin_active' => 1,
            'calendar_visibility' => 1,
            'sort' => 1,
            'cal_sort' => 1,
        ]);

        RoomInfo::create([
            'room_id' => $room->id,
            'name' => 'Double Room 1',
            'beds' => 2,
        ]);

        RoomInfo::create([
            'room_id' => $room->id,
            'name' => 'Double Room 2',
            'beds' => 2,
        ]);
    }
}
