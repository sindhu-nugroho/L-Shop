<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Checkout Monitor
      </h2>
    </div>
  </x-slot>

  <div class="py-6">
    <table class="min-w-full bg-white dark:bg-gray-800">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b text-black dark:text-white">Order ID</th>
          <th class="py-2 px-4 border-b text-black dark:text-white">User</th>
          <th class="py-2 px-4 border-b text-black dark:text-white">Total</th>
          <th class="py-2 px-4 border-b text-black dark:text-white">Status</th>
          <th class="py-2 px-4 border-b text-black dark:text-white">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
        <tr>
          <td class="py-2 px-4 border-b">#{{ $order->id }}</td>
          <td class="py-2 px-4 border-b">{{ $order->user->name }}</td>
          <td class="py-2 px-4 border-b">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
          <td class="py-2 px-4 border-b">{{ ucfirst($order->status) }}</td>
          <td class="py-2 px-4 border-b">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">View</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-app-layout>