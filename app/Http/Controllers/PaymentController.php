<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use App\Models\product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function checkout(Request $request, product $product): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $total = (int) $product->price * $quantity;

        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORD-' . strtoupper(uniqid());

        $monitor = Monitor::create([
            'user_id' => auth()->id(),
            'order_id' => $orderId,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'total' => $total,
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'item_details' => [[
                'id' => (string) $product->id,
                'price' => (int) $product->price,
                'quantity' => $quantity,
                'name' => $product->name,
            ]],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snapToken,
                'monitor_id' => $monitor->id,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            $monitor->update(['status' => 'failed']);

            return response()->json([
                'message' => 'Gagal membuat transaksi pembayaran.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSnapToken(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'ORD-' . rand(), // Menambahkan prefix agar order_id lebih unik
                'gross_amount' => (int)$request->price, // Pastikan harga berupa integer
            ],
            'customer_details' => [
                'first_name' => 'Nama Pembeli',
                'email' => 'customer@example.com',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monitor_id' => ['required', 'integer', 'exists:monitors,id'],
            'status' => ['required', 'in:pending,paid,failed'],
        ]);

        $monitor = Monitor::where('id', $validated['monitor_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $monitor->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Status pembayaran diperbarui.',
        ]);
    }

    public function notification(Request $request): JsonResponse
    {
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $orderId = $notification->order_id;

            $status = 'pending';

            if ($transactionStatus === 'capture') {
                $status = $fraudStatus === 'accept' ? 'paid' : 'pending';
            } elseif ($transactionStatus === 'settlement') {
                $status = 'paid';
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
                $status = 'failed';
            } elseif ($transactionStatus === 'pending') {
                $status = 'pending';
            }

            $monitor = Monitor::where('order_id', $orderId)->first();

            if (!$monitor) {
                return response()->json(['message' => 'Order tidak ditemukan.'], 404);
            }

            $monitor->update(['status' => $status]);

            return response()->json(['message' => 'Notifikasi diproses.']);
        } catch (\Throwable $e) {
            Log::error('Midtrans notification error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['message' => 'Gagal memproses notifikasi.'], 500);
        }
    }
}