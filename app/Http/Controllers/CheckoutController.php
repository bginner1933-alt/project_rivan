<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;

class CheckoutController extends Controller
{
    const TOKO_LAT = -7.2106;
    const TOKO_LNG = 107.9101;
    const TARIF_PER_KM        = 150;
    const ONGKIR_MINIMUM      = 5000;
    const FREE_SHIPPING_THRESHOLD = 0; // 0 = nonaktif

    public function index(Request $request)
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        [$subtotal] = $this->calculateCartTotals($cart);
        $provinces  = Province::orderBy('name')->get();

        return view('checkout.index', compact('cart', 'subtotal', 'provinces'));
    }

    /** AJAX: ambil kota berdasarkan provinsi */
    public function getCities(Request $request)
    {
        $request->validate(['province_code' => 'required|string']);

        $cities = City::where('province_code', $request->province_code)
                      ->orderBy('name')
                      ->get(['code', 'name']);

        return response()->json($cities);
    }

    /** AJAX: ambil kecamatan berdasarkan kota */
    public function getDistricts(Request $request)
    {
        $request->validate(['city_code' => 'required|string']);

        $districts = District::where('city_code', $request->city_code)
                             ->orderBy('name')
                             ->get(['code', 'name']);

        return response()->json($districts);
    }

    /** AJAX: hitung ongkir berdasarkan kecamatan */
    public function getShipping(Request $request)
    {
        $request->validate([
            'district_code' => 'required|string',
            'subtotal'      => 'required|numeric|min:0',
        ]);

        $subtotal = (float) $request->subtotal;

        if (self::FREE_SHIPPING_THRESHOLD > 0 && $subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return response()->json([
                'success'       => true,
                'free_shipping' => true,
                'message'       => 'Selamat! Kamu mendapat gratis ongkir.',
                'couriers'      => [],
            ]);
        }

        $district = District::with('city')->where('code', $request->district_code)->firstOrFail();

        // Koordinat kecamatan — pakai lat/lng jika tersedia, fallback ke kota
        // Sesudah — pakai koordinat dari tabel cities kita sendiri
        $lat = null;
        $lng = null;

        // Coba ambil dari tabel cities (laravolt) jika ada kolom koordinat
        if (isset($district->city->latitude) && $district->city->latitude) {
            $lat = $district->city->latitude;
            $lng = $district->city->longitude;
        }

        // Fallback: cari di tabel cities yang kita buat sebelumnya
        if (!$lat) {
            $ourCity = \App\Models\City::where('name', 'like', '%' . $district->city->name . '%')->first();
            if ($ourCity) {
                $lat = $ourCity->lat;
                $lng = $ourCity->lng;
            }
        }

        // Last resort: koordinat pusat Indonesia
        if (!$lat) {
            $lat = -2.5;
            $lng = 118.0;
        }

        $jarakKm    = $this->hitungJarak(self::TOKO_LAT, self::TOKO_LNG, $lat, $lng);
        $biayaDasar = max(self::ONGKIR_MINIMUM, round($jarakKm * self::TARIF_PER_KM / 1000) * 1000);

        $couriers = [
            ['courier_code' => 'jne',  'courier_name' => 'JNE', 'services' => [
                ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'multiplier' => 0.8,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'REG', 'description' => 'Reguler',               'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'YES', 'description' => 'Yakin Esok Sampai',     'multiplier' => 2.0,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'pos',  'courier_name' => 'POS Indonesia', 'services' => [
                ['service' => 'Pos Biasa', 'description' => 'Layanan Standar', 'multiplier' => 0.75, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Pos Kilat', 'description' => 'Layanan Kilat',   'multiplier' => 1.1,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
            ['courier_code' => 'tiki', 'courier_name' => 'TIKI', 'services' => [
                ['service' => 'ECO', 'description' => 'Economy Service',    'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'REG', 'description' => 'Regular Service',    'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'ONS', 'description' => 'Over Night Service', 'multiplier' => 1.9,  'etd' => '1 hari'],
            ]],
        ];

        $result = [];
        foreach ($couriers as $group) {
            $services = [];
            foreach ($group['services'] as $svc) {
                $cost = max(self::ONGKIR_MINIMUM, (int) round($biayaDasar * $svc['multiplier'] / 1000) * 1000);
                $services[] = [
                    'code'        => $group['courier_code'],
                    'service'     => $svc['service'],
                    'description' => $svc['description'],
                    'cost'        => $cost,
                    'etd_label'   => $svc['etd'],
                ];
            }
            usort($services, fn($a, $b) => $a['cost'] - $b['cost']);
            $result[] = [
                'courier_code' => $group['courier_code'],
                'courier_name' => $group['courier_name'],
                'services'     => $services,
            ];
        }

        return response()->json([
            'success'       => true,
            'free_shipping' => false,
            'couriers'      => $result,
            'jarak_km'      => round($jarakKm),
            'lokasi'        => "{$district->name}, {$district->city->name}",
        ]);
    }

    public function store(Request $request, OrderService $orderService)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|digits_between:10,15',
            'address'         => 'required|string|max:500',
            'province_code'   => 'required|string',
            'city_code'       => 'required|string',
            'district_code'   => 'required|string',
            'courier'         => 'required|string',
            'courier_service' => 'required|string',
            'shipping_cost'   => 'required|numeric|min:0',
        ], [
            'phone.digits_between'     => 'Nomor WhatsApp harus 10–15 digit angka.',
            'province_code.required'   => 'Provinsi wajib dipilih.',
            'city_code.required'       => 'Kota wajib dipilih.',
            'district_code.required'   => 'Kecamatan wajib dipilih.',
            'courier_service.required' => 'Layanan pengiriman wajib dipilih.',
        ]);

        try {
            $order = $orderService->createOrder(auth()->user(), $validated);
            return redirect()->route('orders.show', $order->id)
                             ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function calculateCartTotals($cart): array
    {
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $price = $item->price ?? 0;
            $subtotal += $item->type === 'rent'
                ? $price * $item->quantity * ($item->duration ?? 1)
                : $price * $item->quantity;
        }
        return [$subtotal];
    }

    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function hitungEtd(float $jarakKm, string $speed): string
    {
        if ($speed === 'lambat') {
            if ($jarakKm <= 50)   return '1 hari';
            if ($jarakKm <= 200)  return '2–3 hari';
            if ($jarakKm <= 600)  return '3–5 hari';
            if ($jarakKm <= 1500) return '5–7 hari';
            return '7–14 hari';
        }
        if ($jarakKm <= 50)   return 'Hari ini';
        if ($jarakKm <= 200)  return '1 hari';
        if ($jarakKm <= 600)  return '2–3 hari';
        if ($jarakKm <= 1500) return '3–5 hari';
        return '5–7 hari';
    }
}