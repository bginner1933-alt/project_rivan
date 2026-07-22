<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.base_url');
    }

    /**
     * Hitung ongkir ke kota tujuan (Starter: hanya JNE).
     * RajaOngkir Starter pakai city_id, bukan district.
     *
     * @param  int|string  $destinationCityId  — rajaongkir city_id
     * @param  int         $weightGram
     * @return array       ['success' => bool, 'couriers' => [...], 'message' => '...']
     */
    public function getCost(int|string $destinationCityId, int $weightGram = 1000): array
    {
        // Origin: sesuaikan dengan kota toko kamu (contoh: Jakarta Pusat = 152)
        $originCityId = config('services.rajaongkir.origin_city_id', 152);

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post("{$this->baseUrl}/cost", [
                'origin'      => $originCityId,
                'destination' => $destinationCityId,
                'weight'      => $weightGram,
                'courier'     => 'jne', // Starter hanya support JNE
            ]);

            $body = $response->json();

            if (
                ! $response->successful() ||
                ! isset($body['rajaongkir']['results']) ||
                empty($body['rajaongkir']['results'])
            ) {
                $msg = $body['rajaongkir']['status']['description'] ?? 'Gagal mendapatkan ongkir.';
                return ['success' => false, 'message' => $msg];
            }

            $couriers = collect($body['rajaongkir']['results'])->map(function ($courier) {
                $services = collect($courier['costs'])->map(fn($svc) => [
                    'service'     => $svc['service'],
                    'description' => $svc['description'],
                    'cost'        => $svc['cost'][0]['value'],
                    'etd'         => $svc['cost'][0]['etd'],
                    'etd_label'   => $this->formatEtd($svc['cost'][0]['etd']),
                ])->values()->all();

                return [
                    'courier_code' => $courier['code'],
                    'courier_name' => $courier['name'],
                    'services'     => $services,
                ];
            })->values()->all();

            return ['success' => true, 'couriers' => $couriers];

        } catch (\Throwable $e) {
            Log::error('RajaOngkir error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi ke RajaOngkir bermasalah.'];
        }
    }

    private function formatEtd(string $etd): string
    {
        $etd = trim($etd);
        if ($etd === '' || $etd === '-') return 'Estimasi tidak tersedia';
        // e.g. "1-2" → "1-2 hari"
        return $etd . ' hari';
    }
}