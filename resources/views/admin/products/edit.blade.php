<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Edit Product
    </h2>
  </x-slot>

  <div class="p-6 max-w-xl">
    <form method="POST" enctype="multipart/form-data" 
        action="{{ route('admin.products.update', $product->id) }}">

      @csrf 
      @method('PUT')

      <input type="text" name="name"
        value="{{ $product->name}}"
        class="w-full mb-4 rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        
      <input type="number" name="price"
        value="{{ $product->price }}" 
        class="w-full mb-4 rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        
      <textarea name="description"
        class="w-full mb-4 rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        {{ $product->description }}
      </textarea>

      <input type="file" name="image" class="mb-3">

      <button class="bg-green-600 text-white px-4 py-2 rounded">
        Update
      </button>

    </form>
  </div>
</x-app-layout>