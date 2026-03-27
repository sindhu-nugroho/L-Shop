<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Checkout Monitor
      </h2>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-700/40">
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">ID</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">User</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">Produk</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">Qty</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">Total</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">Status Bayar</th>
            <th class="py-3 px-4 border-b text-left text-black dark:text-white">Waktu Checkout</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">#{{ $order->id }}</td>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">{{ $order->user->name ?? '-' }}</td>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">{{ $order->product_name ?? '-' }}</td>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">{{ $order->quantity ?? 1 }}</td>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
              <td class="py-3 px-4 border-b">
                @if ($order->status === 'paid')
                  <span class="inline-flex rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">Paid</span>
                @elseif ($order->status === 'failed')
                  <span class="inline-flex rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold">Failed</span>
                @else
                  <span class="inline-flex rounded-full bg-yellow-100 text-yellow-700 px-3 py-1 text-xs font-semibold">Pending</span>
                @endif
              </td>
              <td class="py-3 px-4 border-b text-gray-800 dark:text-gray-100">{{ $order->created_at?->format('d M Y H:i') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-6 px-4 text-center text-gray-600 dark:text-gray-300">
                Belum ada data checkout.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>