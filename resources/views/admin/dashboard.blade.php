<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Dashboard Admin
    </h2>
  </x-slot>

  <div class="p-6">
    <p>Halo, {{ Auth::user()->name }}!</p>
    <p>Role: {{ Auth::user()->role }}</p>
  </div>
</x-app-layout>