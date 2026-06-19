<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // Koordinat kota asal toko — ganti sesuai lokasi toko
    const TOKO_LAT = -7.2106; // Garut - Balubur Limbangan
    const TOKO_LNG = 107.9101;

    // Tarif per km (Rp). Sesuaikan sesuka hati.
    const TARIF_PER_KM = 150;

    // Minimum ongkir (Rp)
    const ONGKIR_MINIMUM = 5000;

    // Gratis ongkir jika subtotal >= nilai ini. Set 0 untuk nonaktif.
    const FREE_SHIPPING_THRESHOLD = 0;

    /**
     * Tampilkan halaman checkout
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        [$subtotal] = $this->calculateCartTotals($cart);

        return view('checkout.index', compact('cart', 'subtotal'));
    }

    /**
     * Endpoint AJAX: hitung ongkir berdasarkan jarak
     * GET /checkout/shipping?city_id=1&subtotal=150000
     */
    public function getShipping(Request $request)
    {
        $request->validate([
            'city_id'  => 'required|integer|exists:cities,id',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $subtotal = (float) $request->subtotal;

        // Cek gratis ongkir
        if (self::FREE_SHIPPING_THRESHOLD > 0 && $subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return response()->json([
                'success'       => true,
                'free_shipping' => true,
                'message'       => 'Selamat! Kamu mendapat gratis ongkir.',
                'couriers'      => [],
            ]);
        }

        $city    = City::findOrFail($request->city_id);
        $jarakKm = $this->hitungJarak(self::TOKO_LAT, self::TOKO_LNG, $city->lat, $city->lng);

        $biayaDasar = max(
            self::ONGKIR_MINIMUM,
            round($jarakKm * self::TARIF_PER_KM / 1000) * 1000
        );

        $couriers = [
            [
                'courier_code' => 'jne',
                'courier_name' => 'JNE',
                'services' => [
                    ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'multiplier' => 0.8,  'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                    ['service' => 'REG', 'description' => 'Reguler',               'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                    ['service' => 'YES', 'description' => 'Yakin Esok Sampai',     'multiplier' => 2.0,  'etd' => '1 hari'],
                ],
            ],
            [
                'courier_code' => 'pos',
                'courier_name' => 'POS Indonesia',
                'services' => [
                    ['service' => 'Pos Biasa', 'description' => 'Layanan Standar', 'multiplier' => 0.75, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                    ['service' => 'Pos Kilat', 'description' => 'Layanan Kilat',   'multiplier' => 1.1,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                ],
            ],
            [
                'courier_code' => 'tiki',
                'courier_name' => 'TIKI',
                'services' => [
                    ['service' => 'ECO', 'description' => 'Economy Service',    'multiplier' => 0.85, 'etd' => $this->hitungEtd($jarakKm, 'lambat')],
                    ['service' => 'REG', 'description' => 'Regular Service',    'multiplier' => 1.0,  'etd' => $this->hitungEtd($jarakKm, 'normal')],
                    ['service' => 'ONS', 'description' => 'Over Night Service', 'multiplier' => 1.9,  'etd' => '1 hari'],
                ],
            ],
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
            'kota'          => "{$city->name}, {$city->province}",
        ]);
    }

    /**
     * Simpan pesanan
     */
    public function store(Request $request, OrderService $orderService)
    {
        $validated = $request->validate(
            [
                'name'            => 'required|string|max:255',
                'phone'           => 'required|digits_between:10,15',
                'address'         => 'required|string|max:500',
                'city_id'         => 'required|integer|exists:cities,id',
                'courier'         => 'required|string',
                'courier_service' => 'required|string',
                'shipping_cost'   => 'required|numeric|min:0',
            ],
            [
                'phone.digits_between'     => 'Nomor WhatsApp harus 10–15 digit angka.',
                'city_id.required'         => 'Kota tujuan wajib dipilih.',
                'courier_service.required' => 'Layanan pengiriman wajib dipilih.',
            ]
        );

        try {
            $order = $orderService->createOrder(auth()->user(), $validated);

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function calculateCartTotals($cart): array
    {
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $price = $item->price ?? 0;
            if ($item->type === 'rent') {
                $subtotal += $price * $item->quantity * ($item->duration ?? 1);
            } else {
                $subtotal += $price * $item->quantity;
            }
        }
        return [$subtotal];
    }

    /** Haversine formula — jarak dua koordinat dalam KM */
    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /** Estimasi tiba berdasarkan jarak */
    private function hitungEtd(float $jarakKm, string $speed): string
    {
        if ($speed === 'lambat') {
            if ($jarakKm <= 100)  return '1–2 hari';
            if ($jarakKm <= 500)  return '3–4 hari';
            if ($jarakKm <= 1500) return '5–7 hari';
            return '7–14 hari';
        }
        if ($jarakKm <= 100)  return '1 hari';
        if ($jarakKm <= 500)  return '2–3 hari';
        if ($jarakKm <= 1500) return '3–5 hari';
        return '5–7 hari';
    }
}