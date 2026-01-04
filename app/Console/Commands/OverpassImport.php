<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Models\Consumer;

class OverpassImport extends Command
{
    const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';

    protected $signature = 'app:OverpassImport';

    protected $description = 'Імпорт тестових координат з overpass-api';

    public function handle()
    {
        foreach ([
                    /*
                    "Барський район",
                    "Бершадський район",
                    "Вінницький район",
                    "Гайсинський район",
                    "Жмеринський район",
                    "Іллінецький район",
                    "Калинівський район",
                    "Козятинський район",
                    "Крижопільський район",
                    "Липовецький район",
                    "Літинський район",
                    "Могилів-Подільський район",
                    "Мурованокуриловецький район",
                    "Немирівський район",
                    "Оратівський район",
                    "Піщанський район",
                    "Погребищенський район",
                    "Теплицький район",
                    "Томашпільський район",
                    "Тростянецький район",
                    "Тульчинський район",
                    "Тиврівський район",
                    "Хмільницький район",
                    "Чернівецький район",
                    "Чечельницький район",
                    "Шаргородський район",
                    "Ямпільський район",
                    */
                     "Вінниця",
                 ] as $areaName) {
            $locations = $this->getLocations($areaName);

            $this->info($areaName . ' -> ' . count($locations));

            foreach ($locations as $location) {
                if (!isset($location['center']['lat']) || !isset($location['center']['lon'])) {
                    continue;
                }

                if (!(($location['center']['lat'] >= 48.06 && $location['center']['lat'] <= 49.88) && ($location['center']['lon'] >= 27.31 && $location['center']['lon'] <= 30.05))) {
                    continue;
                }

                $fields = [
                    'street' => trim($location['tags']['addr:street'] ?? ''),
                    'housenumber' => trim($location['tags']['addr:housenumber'] ?? ''),
                    'lat' => $location['center']['lat'],
                    'lon' => $location['center']['lon']
                ];

                if (empty($fields['street'])) {
                    $fields['street'] = 'Вулиця fake ім. ' . fake()->streetName();
                    $fields['housenumber'] = fake()->buildingNumber();
                }

                if (!empty($location['tags']['building']) && $location['tags']['building'] == 'apartments') {
                    $apartments = rand(10, 100);

                    for ($apartment = 1; $apartment <= $apartments; $apartment++) {
                        $fields['apartment'] = $apartment;

                        Consumer::create($fields);
                    }
                } else {
                    Consumer::create($fields);
                }
            }
        }
    }

    private function getLocations($areaName = ''): array
    {
        $query = <<<OVERPASS
[out:json][timeout:60];
area(3600071241)->.vinnitsia;
area["name"="{$areaName}"](area.vinnitsia)->.targetDistrict;
(
  way["building"](area.targetDistrict);
  relation["building"](area.targetDistrict);
);
out tags center;
OVERPASS;

        try {
            $response = Http::timeout(30)
                ->retry(3, 500)
                ->asForm()
                ->post(self::OVERPASS_URL, [
                    'data' => $query,
                ]);

            if (!$response->successful()) {
                return [];
            }

            return $response->json('elements', []);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

        return [];
    }
}
