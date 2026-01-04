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
                     'Вінниця',
                 ] as $areaName) {
            $locations = $this->getLocations($areaName);

            $this->info($areaName . ' -> ' . count($locations));

            foreach ($locations as $location) {
                if (!isset($location['center']['lat']) || !isset($location['center']['lon'])) {
                    continue;
                }

                Consumer::create([
                    'street' => trim($location['tags']['addr:street']),
                    'housenumber' => trim($location['tags']['addr:housenumber']),
                    'lat' => $location['center']['lat'],
                    'lon' => $location['center']['lon']
                ]);
            }
        }
    }

    private function getLocations($areaName = ''): array
    {
        $query = <<<OVERPASS
[out:json][timeout:120];
area["name"="{$areaName}"]->.searchArea;
(
  way["building"]["addr:housenumber"]["addr:street"](area.searchArea);
  relation["building"]["addr:housenumber"]["addr:street"](area.searchArea);
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
