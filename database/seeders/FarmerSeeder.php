<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\FarmerActivity;
use App\Models\FarmerProduction;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipalities = ['Hagonoy', 'Digos City', 'Matanao', 'Padada', 'Santa Cruz', 'Bansalan', 'Kiblawan', 'Sulop'];
        $barangays = [
            'Hagonoy' => ['Guihing', 'Aplaya', 'Balutakay', 'Poblacion', 'Sacol', 'San Isidro'],
            'Digos City' => ['Aplaya', 'Binaton', 'Colorado', 'Dulangan', 'Matti', 'Rizal'],
            'Matanao' => ['Asbang', 'Cogon', 'Kibao', 'Manga', 'Poblacion', 'Tibongbong'],
            'Padada' => ['Almond', 'Lower Limonzo', 'Poblacion', 'Northern Paligue', 'Southern Paligue'],
            'Santa Cruz' => ['Astorga', 'Bato', 'Coronon', 'Inawayan', 'Poblacion', 'Zone 1'],
            'Bansalan' => ['Anonang', 'Doblado', 'Kinuskusan', 'Mabuhay', 'Poblacion'],
            'Kiblawan' => ['Bagong Negro', 'Balasiao', 'Bunot', 'Poblacion'],
            'Sulop' => ['Climaco', 'Harada Butai', 'Katipunan', 'Poblacion', 'Waterfall'],
        ];

        $soilTypes = ['Loam', 'Clay', 'Sandy Loam', 'Volcanic Alluvial'];

        $sampleFarmers = [
            ['first_name' => 'Juan', 'middle_name' => 'Santos', 'last_name' => 'Dela Cruz', 'contact' => '09171112233'],
            ['first_name' => 'Pedro', 'middle_name' => 'Reyes', 'last_name' => 'Alcantara', 'contact' => '09182223344'],
            ['first_name' => 'Maria', 'middle_name' => 'Clara', 'last_name' => 'Bautista', 'contact' => '09193334455'],
            ['first_name' => 'Antonio', 'middle_name' => 'Luna', 'last_name' => 'Gonzales', 'contact' => '09204445566'],
            ['first_name' => 'Francisco', 'middle_name' => 'Baltazar', 'last_name' => 'Mendoza', 'contact' => '09215556677'],
            ['first_name' => 'Andres', 'middle_name' => 'Bonifacio', 'last_name' => 'Villanueva', 'contact' => '09226667788'],
            ['first_name' => 'Gregorio', 'middle_name' => 'Del Pilar', 'last_name' => 'Soriano', 'contact' => '09237778899'],
            ['first_name' => 'Jose', 'middle_name' => 'Protacio', 'last_name' => 'Rizal', 'contact' => '09248889900'],
            ['first_name' => 'Melchora', 'middle_name' => 'Aquino', 'last_name' => 'Fernandez', 'contact' => '09259990011'],
            ['first_name' => 'Gabriela', 'middle_name' => 'Silang', 'last_name' => 'Navarro', 'contact' => '09260001122'],
            ['first_name' => 'Emilio', 'middle_name' => 'Aguinaldo', 'last_name' => 'Castillo', 'contact' => '09271112233'],
            ['first_name' => 'Apolinario', 'middle_name' => 'Mabini', 'last_name' => 'Santiago', 'contact' => '09282223344'],
            ['first_name' => 'Marcelo', 'middle_name' => 'Del Pilar', 'last_name' => 'Ramos', 'contact' => '09293334455'],
            ['first_name' => 'Juan', 'middle_name' => 'Luna', 'last_name' => 'Aquino', 'contact' => '09304445566'],
            ['first_name' => 'Teresa', 'middle_name' => 'Magbanua', 'last_name' => 'Flores', 'contact' => '09315556677'],
            ['first_name' => 'Trinidad', 'middle_name' => 'Tecson', 'last_name' => 'Valenzuela', 'contact' => '09326667788'],
            ['first_name' => 'Vicente', 'middle_name' => 'Abad', 'last_name' => 'Torres', 'contact' => '09337778899'],
            ['first_name' => 'Mariano', 'middle_name' => 'Ponce', 'last_name' => 'Guerrero', 'contact' => '09348889900'],
            ['first_name' => 'Diego', 'middle_name' => 'Silang', 'last_name' => 'Cruz', 'contact' => '09359990011'],
            ['first_name' => 'Artemio', 'middle_name' => 'Ricarte', 'last_name' => 'Corpuz', 'contact' => '09360001122'],
        ];

        foreach ($sampleFarmers as $idx => $sf) {
            $mun = $municipalities[$idx % count($municipalities)];
            $brgList = $barangays[$mun];
            $brg = $brgList[$idx % count($brgList)];

            $farmerCode = 'FAR-2026-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);

            $farmer = Farmer::create([
                'farmer_code' => $farmerCode,
                'first_name' => $sf['first_name'],
                'middle_name' => $sf['middle_name'],
                'last_name' => $sf['last_name'],
                'contact_number' => $sf['contact'],
                'email' => strtolower($sf['first_name']) . '.' . strtolower($sf['last_name']) . '@davaosugarplanters.com',
                'address' => "Purok " . (($idx % 5) + 1) . ", Sitio Salutillo",
                'barangay' => $brg,
                'municipality' => $mun,
                'province' => 'Davao del Sur',
                'status' => 'Active',
            ]);

            // Create 1 to 2 Farms per Farmer
            $farmCount = ($idx % 3 === 0) ? 2 : 1;
            for ($f = 1; $f <= $farmCount; $f++) {
                $farmSize = round(1.5 + ($idx * 0.4) + ($f * 0.5), 2);
                $soil = $soilTypes[($idx + $f) % count($soilTypes)];

                $farm = Farm::create([
                    'farmer_id' => $farmer->id,
                    'farm_name' => "{$sf['last_name']} Sugarcane Farm Plot {$f}",
                    'location' => "Sitio " . ($f === 1 ? 'Central' : 'North') . ", Brgy. {$brg}, {$mun}",
                    'farm_size' => $farmSize,
                    'farm_size_unit' => 'Hectares',
                    'soil_type' => $soil,
                    'status' => 'Active',
                ]);

                // Create Production Records across Crop Years
                $cropYears = ['2023-2024', '2024-2025', '2025-2026'];
                foreach ($cropYears as $cyIdx => $cy) {
                    $estYield = round($farmSize * 65.0, 2); // ~65 Metric Tons per Hectare
                    $actYield = ($cyIdx < 2) ? round($estYield * (0.92 + ($idx % 5) * 0.03), 2) : null;
                    $status = ($cyIdx < 2) ? 'Completed' : 'Growing';

                    FarmerProduction::create([
                        'farmer_id' => $farmer->id,
                        'farm_id' => $farm->id,
                        'crop_year' => $cy,
                        'planting_date' => date('Y-m-d', strtotime("-".(3 - $cyIdx)." years +2 months")),
                        'harvest_date' => ($cyIdx < 2) ? date('Y-m-d', strtotime("-".(3 - $cyIdx)." years +12 months")) : null,
                        'estimated_yield' => $estYield,
                        'actual_yield' => $actYield,
                        'status' => $status,
                    ]);
                }
            }

            // Create Initial Farmer Activity Logs
            FarmerActivity::create([
                'farmer_id' => $farmer->id,
                'activity_type' => 'Farmer Registered',
                'description' => "Initial registration of planter {$farmer->full_name} under code {$farmer->farmer_code}.",
                'activity_date' => now()->subMonths(6),
                'created_by' => 'System Admin',
            ]);

            FarmerActivity::create([
                'farmer_id' => $farmer->id,
                'activity_type' => 'Planting',
                'description' => "Logged planting season for crop year 2025-2026 across registered farms.",
                'activity_date' => now()->subMonths(2),
                'created_by' => 'Field Inspector',
            ]);
        }
    }
}

