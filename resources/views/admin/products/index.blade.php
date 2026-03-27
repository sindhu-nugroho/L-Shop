<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Product
      </h2>
      <a href="{{ route('admin.products.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded">
        Add Product
      </a>
    </div>
  </x-slot>

  <div class="py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach ($products as $product)
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        @if($product->image)
          <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
        @endif

        <div class="p-6">
          <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
          <p class="text-gray-600 dark:text-gray-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
          
          <div class="flex gap-2 mt-4">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">
              Edit
            </a>

            <form method="POST" action="{{ route('admin.products.destroy',$product)}}">
              @csrf @method('DELETE')
              <button onclick="return confirm('Hapus Produk?')"
                class="bg-red-600 text-white px-4 py-2 rounded">
                Delete
              </button>
            </form>

            <button class="bg-green-600 text-white px-4 py-2 rounded">
              Checkout
            </button>
          </div>
        </div>
      </div>
    @endforeach
  </div>


</x-app-layout>