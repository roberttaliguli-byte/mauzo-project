<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Bidhaa;
use App\Models\Mteja;
use App\Models\Masaplaya;
use App\Models\Wafanyakazi;
use App\Models\Matumizi;
use App\Models\Manunuzi;
use App\Models\Mauzo;
use App\Models\Madeni;
use App\Models\Marejesho;
use App\Models\History;
use App\Models\AinaYaMatumizi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Majina halisi ya Watanzania
        $firstNamesMale = ['Juma', 'Mohamed', 'John', 'Robert', 'William', 'James', 'Charles', 'Joseph', 'Thomas', 'David'];
        $firstNamesFemale = ['Maria', 'Amina', 'Grace', 'Sarah', 'Elizabeth', 'Mary', 'Anna', 'Margaret', 'Susan', 'Joyce'];
        $lastNames = ['Mwinyi', 'Kikwete', 'Magufuli', 'Mkapa', 'Nyerere', 'Kambarage', 'Nyanda', 'Mrema', 'Lowassa', 'Pinda'];
        
        $locations = ['Dar es Salaam', 'Mwanza', 'Arusha', 'Dodoma', 'Mbeya', 'Tanga', 'Morogoro', 'Zanzibar', 'Moshi', 'Kigoma'];
        $regions = ['Dar es Salaam', 'Mwanza', 'Arusha', 'Dodoma', 'Mbeya', 'Tanga', 'Morogoro', 'Mjini Magharibi', 'Kilimanjaro', 'Kigoma'];
        
        // Majina ya kampuni za kweli Tanzania
        $companyNames = [
            'Supermarket Yetu', 'Duka la Bidhaa', 'Hardware Jipya', 'Mauzo Makini', 'Biashara Bora', 
            'Kampuni ya Bidhaa', 'Maduka Yetu', 'Uchumi Supermarket', 'Nile Supermarket', 'Shoprite Tanzania'
        ];

        // 1. Create Companies (10) kwa majina halisi
        $companies = [];
        for ($i = 0; $i < 10; $i++) {
            $ownerGender = $i % 2 == 0 ? 'male' : 'female';
            $ownerFirstName = $ownerGender == 'male' ? 
                $firstNamesMale[array_rand($firstNamesMale)] : 
                $firstNamesFemale[array_rand($firstNamesFemale)];
            $ownerLastName = $lastNames[array_rand($lastNames)];
            
            $companies[] = Company::create([
                'company_name' => $companyNames[$i] . ' ' . $locations[$i],
                'owner_name' => $ownerFirstName . ' ' . $ownerLastName,
                'owner_gender' => $ownerGender,
                'owner_dob' => now()->subYears(rand(30, 65))->format('Y-m-d'),
                'location' => $locations[$i],
                'region' => $regions[$i],
                'phone' => '2557' . rand(10000000, 99999999),
                'email' => strtolower(str_replace(' ', '', $companyNames[$i])) . '@example.com',
                'is_verified' => $i < 8,
                'is_user_approved' => $i < 9,
                'package' => $i < 3 ? 'basic' : ($i < 7 ? 'premium' : 'enterprise'),
                'database_name' => 'db_' . strtolower(str_replace(' ', '_', $companyNames[$i])),
                'package_start' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'package_end' => now()->addMonths(rand(6, 24))->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Users (10) - each with a company
        $users = [];
        foreach ($companies as $index => $company) {
            $userFirstName = $firstNamesMale[array_rand($firstNamesMale)];
            $userLastName = $lastNames[array_rand($lastNames)];
            
            $users[] = User::create([
                'name' => $userFirstName . ' ' . $userLastName,
                'email' => 'user' . $company->id . '_' . $index . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'boss',
                'is_approved' => true,
                'company_id' => $company->id,
                'username' => 'user' . $company->id . '_' . $index,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        // admin
User::create([
    'name' => 'System Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'), // Use a strong password
    'role' => 'admin',  // <-- important
    'is_approved' => true,
    'company_id' => null, // Admin doesn't need a company
    'username' => 'admin',
    'email_verified_at' => now(),
    'remember_token' => Str::random(10),
    'created_at' => now(),
    'updated_at' => now(),
]);

        // 4. Seed other tables with company relationships
        foreach ($companies as $company) {
            // Majina ya wateji wa kweli
            $mtejaFirstNames = ['Asha', 'Zainab', 'Fatuma', 'Halima', 'Neema', 'Tumaini', 'Baraka', 'Rajab', 'Hamisi', 'Yusuf'];
            $mtejaLastNames = ['Juma', 'Omar', 'Hassan', 'Ali', 'Khamis', 'Said', 'Kondo', 'Moshi', 'Kato', 'Nassoro'];
            
            // Create Mtejas first (needed for Madenis)
            $mtejas = [];
            for ($j = 0; $j < 10; $j++) {
                $mtejaFirstName = $mtejaFirstNames[array_rand($mtejaFirstNames)];
                $mtejaLastName = $mtejaLastNames[array_rand($mtejaLastNames)];
                
                $mtejas[] = Mteja::create([
                    'jina' => $mtejaFirstName . ' ' . $mtejaLastName,
                    'simu' => '2556' . rand(10000000, 99999999),
                    'barua_pepe' => strtolower($mtejaFirstName) . $j . '_' . $company->id . '@gmail.com',
                    'anapoishi' => 'Street ' . ($j + 1) . ', ' . $company->location,
                    'maelezo' => 'Mteja mwaminifu wa ' . $company->company_name,
                    'company_id' => $company->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Bidhaa za kweli Tanzania
            $bidhaaNames = [
                'Mchele Super', 'Unga wa Ngano', 'Sukari', 'Mafuta ya Kupikia', 'Sabuni ya Kuosha', 
                'Majani ya Chai', 'Kahawa', 'Viatu', 'Mavazi', 'Vifaa vya Nyumbani'
            ];
            $ainaBidhaa = ['Chakula', 'Vyakula', 'Vinywaji', 'Sabuni', 'Viatu', 'Mavazi', 'Vifaa', 'Zana', 'Vyakula', 'Vinywaji'];
            $vipimo = ['kg', 'kg', 'kg', 'lita', 'kopo', 'piece', 'piece', 'piece', 'piece', 'piece'];

            // Create Bidhaas
            $bidhaas = [];
            for ($i = 0; $i < 10; $i++) {
                $bidhaas[] = Bidhaa::create([
                    'company_id' => $company->id,
                    'jina' => $bidhaaNames[$i],
                    'aina' => $ainaBidhaa[$i],
                    'kipimo' => $vipimo[$i],
                    'idadi' => rand(5, 200),
                    'bei_nunua' => rand(800, 25000),
                    'bei_kuuza' => rand(1000, 30000),
                    'expiry' => now()->addDays(rand(60, 365))->format('Y-m-d'),
                    'barcode' => 'BRC' . $company->id . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Majina ya masuplaya
            $masaplayaNames = [
                'Tanzania Breweries', 'Bakhresa Group', 'Mohammed Enterprises', 'Suma JKT', 
                'Tanga Cement', 'TBL', 'Azam Products', 'Sayona Products', 'Kilimanjaro Native', 'Mbeya Cement'
            ];

            // Create Masaplayas
            for ($j = 0; $j < 10; $j++) {
                Masaplaya::create([
                    'jina' => $masaplayaNames[$j],
                    'simu' => '2557' . rand(10000000, 99999999),
                    'barua_pepe' => 'info' . $j . '_' . $company->id . '@' . strtolower(str_replace(' ', '', $masaplayaNames[$j])) . '.com',
                    'anaopoishi' => $locations[array_rand($locations)],
                    'ofisi' => 'Head Office, ' . $locations[array_rand($locations)],
                    'maelezo' => 'Msambazaji mkuu wa ' . $bidhaaNames[$j % count($bidhaaNames)],
                    'company_id' => $company->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Majina ya wafanyakazi - zaidi ya 10 kuepuka kurudia
            $wafanyakaziFirstNames = ['Paul', 'George', 'Richard', 'Edward', 'Michael', 'Daniel', 'Stephen', 'Peter', 'Andrew', 'Mark', 'James', 'Joseph', 'David', 'Christopher', 'Brian'];
            $wafanyakaziLastNames = ['Mabula', 'Kibona', 'Shayo', 'Mollel', 'Kimambo', 'Lyimo', 'Masanja', 'Kaduri', 'Mwantumu', 'Kaseja', 'Mrosso', 'Kadogo', 'Mfinanga', 'Kibiriti', 'Mkumbo'];

            // Create Wafanyakazis - tumia username za kipekee kwa kutumia company_id na index
            for ($j = 0; $j < 10; $j++) {
                $firstName = $wafanyakaziFirstNames[array_rand($wafanyakaziFirstNames)];
                $lastName = $wafanyakaziLastNames[array_rand($wafanyakaziLastNames)];
                
                // Tengeneza username ya kipekee kwa kutumia company_id na index
                $username = 'staff_' . $company->id . '_' . $j;
                
                Wafanyakazi::create([
                    'jina' => $firstName . ' ' . $lastName,
                    'jinsia' => $j % 2 == 0 ? 'male' : 'female',
                    'tarehe_kuzaliwa' => now()->subYears(rand(20, 45))->format('Y-m-d'),
                    'anuani' => 'House No. ' . ($j + 1) . ', ' . $company->location,
                    'simu' => '2557' . rand(10000000, 99999999),
                    'barua_pepe' => $username . '@company.com',
                    'ndugu' => 'Parent ' . ($j + 1),
                    'simu_ndugu' => '2557' . rand(10000000, 99999999),
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'role' => $j < 2 ? 'meneja' : 'mfanyakazi',
                    'company_id' => $company->id,
                    'getini' => $j < 5 ? 'simama' : 'keti',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Aina za matumizi
            $ainaMatumizi = ['Stakabadhi', 'Mafuta ya Gari', 'Mishahara', 'Bidhaa Ofisi', 'Usafiri', 'Lunch', 'Mengineyo', 'Ada za Simu', 'Umeme', 'Maji'];

            // Create Matumizis
            for ($j = 0; $j < 10; $j++) {
                Matumizi::create([
                    'company_id' => $company->id,
                    'aina' => $ainaMatumizi[$j],
                    'maelezo' => 'Malipo ya ' . $ainaMatumizi[$j] . ' mwezi ' . ($j + 1),
                    'gharama' => rand(5000, 100000),
                    'created_at' => now()->subDays($j * 3),
                    'updated_at' => now()->subDays($j * 3),
                ]);
            }

            // Create Aina za Matumizi
            for ($j = 0; $j < 10; $j++) {
                AinaYaMatumizi::create([
                    'jina' => $ainaMatumizi[$j],
                    'maelezo' => 'Aina ya matumizi ya ' . $ainaMatumizi[$j],
                    'rangi' => '#' . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                    'kategoria' => ['msingi', 'zana', 'usafiri', 'nyingine'][$j % 4],
                    'company_id' => $company->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create Histories
            for ($j = 0; $j < 10; $j++) {
                $userName = $wafanyakaziFirstNames[array_rand($wafanyakaziFirstNames)] . ' ' . $wafanyakaziLastNames[array_rand($wafanyakaziLastNames)];
                $actions = ['created', 'updated', 'deleted', 'sold', 'purchased'];
                $action = $actions[array_rand($actions)];
                
                History::create([
                    'user' => $userName,
                    'action' => $action,
                    'details' => 'Amefanya ' . $action . ' kwenye bidhaa: ' . $bidhaaNames[$j],
                    'created_at' => now()->subDays($j),
                    'updated_at' => now()->subDays($j),
                ]);
            }

            // Create Manunuzis for each bidhaa
            foreach ($bidhaas as $bidhaa) {
                for ($j = 0; $j < 2; $j++) {
                    Manunuzi::create([
                        'company_id' => $company->id,
                        'bidhaa_id' => $bidhaa->id,
                        'idadi' => rand(10, 100),
                        'bei' => $bidhaa->bei_nunua,
                        'expiry' => $bidhaa->expiry,
                        'saplaya' => $masaplayaNames[array_rand($masaplayaNames)],
                        'simu' => '2557' . rand(10000000, 99999999),
                        'mengineyo' => 'Manunuzi ya ' . $bidhaa->jina . ' kutoka kwa msambazaji',
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }

            // Create Mauzos for each bidhaa
            foreach ($bidhaas as $bidhaa) {
                for ($j = 0; $j < 3; $j++) {
                    $idadi = rand(1, min(20, $bidhaa->idadi));
                    $bei = $bidhaa->bei_kuuza;
                    $punguzo = rand(0, 500);
                    
                    Mauzo::create([
                        'company_id' => $company->id,
                        'bidhaa_id' => $bidhaa->id,
                        'idadi' => $idadi,
                        'bei' => $bei,
                        'punguzo' => $punguzo,
                        'jumla' => ($idadi * $bei) - $punguzo,
                        'is_debt_repayment' => $j == 0,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ]);
                }
            }

            // Create Madenis for each bidhaa
            foreach ($bidhaas as $bidhaa) {
                for ($j = 0; $j < 2; $j++) {
                    $idadi = rand(1, 15);
                    $bei = $bidhaa->bei_kuuza;
                    $jumla = $idadi * $bei;
                    $baki = rand(0, $jumla);
                    
                    $mteja = $mtejas[array_rand($mtejas)];
                    
                    $madeni = Madeni::create([
                        'bidhaa_id' => $bidhaa->id,
                        'idadi' => $idadi,
                        'bei' => $bei,
                        'jumla' => $jumla,
                        'baki' => $baki,
                        'jina_mkopaji' => $mteja->jina,
                        'simu' => $mteja->simu,
                        'tarehe_malipo' => now()->addDays(rand(7, 60))->format('Y-m-d'),
                        'company_id' => $company->id,
                        'mteja_id' => $mteja->id,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(1, 30)),
                    ]);

                    // Create Marejeshos for each madeni
                    if ($baki > 0) {
                        for ($k = 0; $k < 2; $k++) {
                            $kiasi = rand(1000, $baki / 2);
                            Marejesho::create([
                                'madeni_id' => $madeni->id,
                                'kiasi' => $kiasi,
                                'tarehe' => now()->subDays(rand(1, 15))->format('Y-m-d'),
                                'company_id' => $company->id,
                                'created_at' => now()->subDays(rand(1, 15)),
                                'updated_at' => now()->subDays(rand(1, 15)),
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('📊 DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('================================');
        $this->command->info('🏢 Total Companies: ' . count($companies));
        $this->command->info('👥 Total Users: ' . (count($users) + 1));
        $this->command->info('🔐 Admin Login: admin@example.com / password');
        $this->command->info('👨‍💼 Staff Login: staff_1_0@company.com / password');
        $this->command->info('================================');
    }
}