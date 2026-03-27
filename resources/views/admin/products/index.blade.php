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

            <button class="pay-button bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-bold transition" 
                    data-price="{{ $product->price }}" 
                    data-name="{{ $product->name }}">
              Beli Sekarang
            </button>

            <form method="POST" action="{{ route('admin.products.destroy',$product)}}">
              @csrf @method('DELETE')
              <button onclick="return confirm('Hapus Produk?')"
                class="bg-red-600 text-white px-3 py-1 rounded">
                Delete
              </button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
  <script type="text/javascript">
    const payButtons = document.querySelectorAll('.pay-button');
    
    payButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data price & name dari atribut tombol yang diklik
            const price = this.getAttribute('data-price');
            const name = this.getAttribute('data-name');

            // Request Token ke Controller Laravel Anda
            fetch('/admin/get-snap-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token keamanan bawaan Laravel
                },
                body: JSON.stringify({ 
                    price: price,
                    item_name: name 
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                
                // Eksekusi Popup Midtrans menggunakan token yang didapat
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) { 
                        alert("Pembayaran Berhasil!"); 
                        window.location.reload(); 
                    },
                    onPending: function(result) { 
                        alert("Selesaikan pembayaran Anda di gerai/bank."); 
                    },
                    onError: function(result) { 
                        alert("Pembayaran Gagal."); 
                    }
                });
            })
            .catch(err => {
                console.error('Fetch Error:', err);
                alert('Gagal menghubungi server pembayaran.');
            });
        });
    });
  </script>
</x-app-layout>
