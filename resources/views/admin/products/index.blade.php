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
          <h3 class="text-lg font-semibold text-black dark:text-white">{{ $product->name }}</h3>
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

            <form method="POST" action="{{ route('admin.checkout.store', $product->id) }}" class="checkout-form">
              @csrf
              <input
                type="number"
                name="quantity"
                min="1"
                value="1"
                class="w-20 rounded border-gray-300 text-sm"
              >
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Checkout
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
    const checkoutForms = document.querySelectorAll('.checkout-form');

    async function updatePaymentStatus(monitorId, status) {
      await fetch('{{ route('admin.payment.status.update') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ monitor_id: monitorId, status })
      });
    }

    checkoutForms.forEach((form) => {
      form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Memproses...';

        try {
          const response = await fetch(form.action, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              quantity: form.querySelector('input[name="quantity"]').value
            })
          });

          const data = await response.json();

          if (!response.ok || !data.snap_token) {
            throw new Error(data.message || 'Gagal memulai transaksi.');
          }

          window.snap.pay(data.snap_token, {
            onSuccess: async function() {
              await updatePaymentStatus(data.monitor_id, 'paid');
              window.location.reload();
            },
            onPending: async function() {
              await updatePaymentStatus(data.monitor_id, 'pending');
              window.location.reload();
            },
            onError: async function() {
              await updatePaymentStatus(data.monitor_id, 'failed');
              window.location.reload();
            },
            onClose: async function() {
              await updatePaymentStatus(data.monitor_id, 'failed');
              window.location.reload();
            }
          });
        } catch (error) {
          alert(error.message || 'Gagal menghubungi server pembayaran.');
          submitButton.disabled = false;
          submitButton.textContent = 'Checkout';
        }
      });
    });
  </script>
</x-app-layout>
