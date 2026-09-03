<?php

namespace App\Console\Commands;

use App\Models\LiveLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BackfillLiveLocationAddresses extends Command
{
    protected $signature = 'location:backfill-addresses {--hours=0} {--batch=100} {--limit=0}';
    protected $description = 'Backfill missing alamat lokasi pada data live location menggunakan reverse geocoding OpenStreetMap.';

    public function handle()
    {
        $hours = (int) $this->option('hours');
        $batch = max(10, (int) $this->option('batch'));
        $limit = (int) $this->option('limit');

        $query = LiveLocation::query()
            ->where(function ($sub) {
                $sub->whereNull('lokasi')
                    ->orWhereRaw("lokasi REGEXP '^-?[0-9]+(\\.[0-9]+)?,[[:space:]]*-?[0-9]+(\\.[0-9]+)?$'");
            });

        if ($hours > 0) {
            $query->where('tracked_at', '>=', now()->subHours($hours));
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Tidak ada record yang perlu dibackfill.');
            return 0;
        }

        $this->info("Mulai backfill {$total} record lokasi...");

        $processed = 0;
        $failed = 0;

        $query->orderBy('tracked_at', 'asc')
            ->chunk($batch, function ($locations) use (&$processed, &$failed) {
                foreach ($locations as $location) {
                    $address = $this->reverseGeocode($location->latitude, $location->longitude);
                    if ($address) {
                        $location->lokasi = $address;
                        $location->save();
                        $processed++;
                        $this->line("[OK] {$location->id} => {$address}");
                    } else {
                        $failed++;
                        $this->warn("[SKIP] {$location->id} - reverse geocoding gagal");
                    }
                }
            });

        $this->info("Selesai: {$processed} record berhasil dibackfill, {$failed} gagal.");

        return 0;
    }

    private function reverseGeocode($lat, $lng)
    {
        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'addressdetails' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $address = $data['address'] ?? [];
            $parts = [];

            if (!empty($address['house_number'])) {
                $parts[] = trim(($address['road'] ?? '') . ' ' . $address['house_number']);
            } elseif (!empty($address['road'])) {
                $parts[] = $address['road'];
            }

            foreach (['suburb', 'neighbourhood', 'village', 'hamlet', 'town', 'city_district', 'city', 'county', 'state'] as $key) {
                if (!empty($address[$key]) && !in_array($address[$key], $parts, true)) {
                    $parts[] = $address[$key];
                }
            }

            $formatted = implode(', ', array_filter($parts));
            return $formatted ?: ($data['display_name'] ?? null);
        } catch (\Exception $e) {
            Log::warning('Reverse geocoding failed for backfill: ' . $e->getMessage());
            return null;
        }
    }
}
