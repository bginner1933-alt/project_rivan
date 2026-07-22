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
    
    const ONGKIR_MINIMUM      = 5000;
    const ONGKIR_DALAM_PROVINSI = 12000;
    const ONGKIR_LUAR_PROVINSI  = 25000;

    const TARIF_BERAT_PER_KG = 3000;
    const FREE_SHIPPING_THRESHOLD = 0; 

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 🔥 UBAH BARIS INI: Tambahkan eager loading 'items.product' agar object product tidak bernilai null
        $cart = \App\Models\Cart::with(['items.product'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        [$subtotal, $totalWeight] = $this->calculateCartTotals($cart);

        $totalWeightKg = (int) ceil($totalWeight / 1000);
        $biayaBerat    = $totalWeightKg * self::TARIF_BERAT_PER_KG;

        $provinces = Province::orderBy('name')->get();

        return view('checkout.index', compact(
            'cart', 'subtotal', 'provinces',
            'totalWeight', 'totalWeightKg', 'biayaBerat'
        ));
    }

    public function getCities(Request $request)
    {
        $request->validate(['province_code' => 'required|string']);
        return response()->json(
            City::where('province_code', $request->province_code)
                ->orderBy('name')->get(['code', 'name'])
        );
    }

    public function getDistricts(Request $request)
    {
        $request->validate(['city_code' => 'required|string']);
        return response()->json(
            District::where('city_code', $request->city_code)
                ->orderBy('name')->get(['code', 'name'])
        );
    }
    
    /** AJAX: hitung ongkir via RajaOngkir + biaya berat terpisah secara hybrid database */
    public function getShipping(Request $request)
    {
        $request->validate([
            'district_code' => 'required|string',
            'subtotal'      => 'required|numeric|min:0',
            'weight'        => 'required|numeric|min:1',
        ]);

        $subtotal      = (float) $request->subtotal;
        $totalWeightG  = (float) $request->weight;
        $totalWeightKg = (int) ceil($totalWeightG / 1000);
        $biayaBerat    = $totalWeightKg * self::TARIF_BERAT_PER_KG;

        if (self::FREE_SHIPPING_THRESHOLD > 0 && $subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return response()->json([
                'success'       => true,
                'free_shipping' => true,
                'message'       => 'Selamat! Kamu mendapat gratis ongkir.',
                'weight_kg'     => $totalWeightKg,
                'biaya_berat'   => $biayaBerat,
                'couriers'      => [],
            ]);
        }

        $district = District::with('city')->where('code', $request->district_code)->first();
        if (!$district) {
            return response()->json(['success' => false, 'message' => 'Kecamatan tidak ditemukan.']);
        }

        // Hitung jarak dari toko ke kecamatan tujuan
        $lat = $district->latitude  ?? $district->city->latitude  ?? null;
        $lng = $district->longitude ?? $district->city->longitude ?? null;

        // Fallback: cari koordinat dari tabel cities kita
        if (!$lat) {
            $ourCity = \App\Models\City::where('name', 'like', '%' . $district->city->name . '%')->first();
            if ($ourCity) { $lat = $ourCity->lat; $lng = $ourCity->lng; }
        }

        // Last resort: koordinat pusat Indonesia
        if (!$lat) { $lat = -2.5; $lng = 118.0; }

        $jarakKm    = $this->hitungJarak(self::TOKO_LAT, self::TOKO_LNG, $lat, $lng);
        $biayaDasar = max(self::ONGKIR_MINIMUM ?? 5000, round($jarakKm * 150 / 1000) * 1000);

        // ── 17 Ekspedisi ──────────────────────────────────────────
        $courierList = [
            ['courier_code' => 'jne',      'courier_name' => 'JNE',             'services' => [
                ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis',  'multiplier' => 0.8,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'REG', 'description' => 'Reguler',                'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'YES', 'description' => 'Yakin Esok Sampai',      'multiplier' => 2.0,  'etd' => '1 hari'],
                ['service' => 'SPS', 'description' => 'Super Speed',            'multiplier' => 2.5,  'etd' => 'Hari ini'],
            ]],
            ['courier_code' => 'pos',      'courier_name' => 'POS Indonesia',   'services' => [
                ['service' => 'Pos Biasa',    'description' => 'Layanan Standar',   'multiplier' => 0.75, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Pos Kilat',    'description' => 'Layanan Kilat',     'multiplier' => 1.1,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Express Next', 'description' => 'Express Next Day',  'multiplier' => 1.8,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'tiki',     'courier_name' => 'TIKI',            'services' => [
                ['service' => 'ECO', 'description' => 'Economy Service',        'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'REG', 'description' => 'Regular Service',        'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'ONS', 'description' => 'Over Night Service',     'multiplier' => 1.9,  'etd' => '1 hari'],
                ['service' => 'SDS', 'description' => 'Same Day Service',       'multiplier' => 2.3,  'etd' => 'Hari ini'],
            ]],
            ['courier_code' => 'sicepat',  'courier_name' => 'SiCepat',         'services' => [
                ['service' => 'GOKIL', 'description' => 'Layanan Ekonomis',     'multiplier' => 0.8,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'REG',   'description' => 'Reguler',              'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'BEST',  'description' => 'Besok Sampai Tujuan', 'multiplier' => 1.1,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'jnt',      'courier_name' => 'J&T Express',     'services' => [
                ['service' => 'EZ',    'description' => 'J&T EZ',              'multiplier' => 0.9,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'SUPER', 'description' => 'J&T Super',           'multiplier' => 1.8,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'anteraja', 'courier_name' => 'Anteraja',        'services' => [
                ['service' => 'Reguler',  'description' => 'Layanan Reguler',  'multiplier' => 0.9,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Next Day', 'description' => 'Layanan Next Day', 'multiplier' => 1.7,  'etd' => '1 hari'],
                ['service' => 'Same Day', 'description' => 'Layanan Same Day', 'multiplier' => 2.2,  'etd' => 'Hari ini'],
            ]],
            ['courier_code' => 'wahana',   'courier_name' => 'Wahana',          'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.5,  'etd' => '1–2 hari'],
            ]],
            ['courier_code' => 'ninja',    'courier_name' => 'Ninja Express',   'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.9,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.6,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'lion',     'courier_name' => 'Lion Parcel',     'services' => [
                ['service' => 'REGPACK',  'description' => 'Regular Package',  'multiplier' => 0.95, 'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'JAGOPACK', 'description' => 'Jago Package',     'multiplier' => 1.3,  'etd' => '1–2 hari'],
                ['service' => 'ONEPACK',  'description' => 'One Day Package',  'multiplier' => 1.8,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'ide',      'courier_name' => 'ID Express',      'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.5,  'etd' => '1–2 hari'],
            ]],
            ['courier_code' => 'sap',      'courier_name' => 'SAP Express',     'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.9,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.6,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'ncs',      'courier_name' => 'NCS',             'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.8,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.4,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
            ['courier_code' => 'rex',      'courier_name' => 'REX',             'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
            ['courier_code' => 'rpx',      'courier_name' => 'RPX',             'services' => [
                ['service' => 'RDD', 'description' => 'Regular Door to Door',  'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ['service' => 'MDD', 'description' => 'Master Door to Door',   'multiplier' => 1.5,  'etd' => '1–2 hari'],
                ['service' => 'PDD', 'description' => 'Priority Door to Door', 'multiplier' => 2.0,  'etd' => '1 hari'],
            ]],
            ['courier_code' => 'star',     'courier_name' => 'Star Cargo',      'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.75, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.2,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
            ['courier_code' => 'indah',    'courier_name' => 'Indah Logistik',  'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.75, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.3,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
            ['courier_code' => 'pahala',   'courier_name' => 'Pahala Kencana',  'services' => [
                ['service' => 'Reguler', 'description' => 'Layanan Reguler',   'multiplier' => 0.7,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                ['service' => 'Express', 'description' => 'Layanan Express',   'multiplier' => 1.2,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
            ]],
        ];

        // ── Build result ──────────────────────────────────────────
        $result = [];
        foreach ($courierList as $group) {
            $services = [];
            foreach ($group['services'] as $svc) {
                $ongkirBase = max(5000, (int) round($biayaDasar * $svc['multiplier'] / 1000) * 1000);
                $services[] = [
                    'service'     => $svc['service'],
                    'description' => $svc['description'],
                    'cost'        => $ongkirBase,
                    'biaya_berat' => $biayaBerat,
                    'total_cost'  => $ongkirBase + $biayaBerat,
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
            'weight_kg'     => $totalWeightKg,
            'biaya_berat'   => $biayaBerat,
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
            'weight_cost'     => 'required|numeric|min:0',
        ]);

        $validated['payment_method'] = 'midtrans';

        try {
            $order = $orderService->createOrder(auth()->user(), $validated);
            return redirect()->route('orders.show', $order->id);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    private function calculateCartTotals($cart): array
    {
        $subtotal = 0;
        $totalWeight = 0;

        foreach ($cart->items as $item) {
            $price    = $item->price ?? 0;
            $quantity = $item->quantity;

            $subtotal += $item->type === 'rent'
                ? $price * $quantity * ($item->duration ?? 1)
                : $price * $quantity;

            // 🔥 PENGAMAN: Cek kolom 'weight' atau 'berat'. Jika 0/null, paksa isi 1000 gr (1 kg)
            $productWeight = $item->product->weight ?? $item->product->berat ?? 0; 
            if ($productWeight <= 0) {
                $productWeight = 1000; 
            }

            $totalWeight += ($productWeight * $quantity);
        }

        return [$subtotal, $totalWeight];
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
        if ($jarakKm <= 50)   return '1–2 hari';
        if ($jarakKm <= 200)  return '2–3 hari';
        if ($jarakKm <= 600)  return '3–5 hari';
        if ($jarakKm <= 1500) return '5–7 hari';
        return '7–14 hari';
    }
    // normal
    if ($jarakKm <= 50)   return 'Hari ini';
    if ($jarakKm <= 200)  return '1 hari';
    if ($jarakKm <= 600)  return '2–3 hari';
    if ($jarakKm <= 1500) return '3–5 hari';
    return '5–7 hari';
}
}