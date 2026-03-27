<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Dashboard User
    </h2>
  </x-slot>

  <div class="p-6 space-y-6">
    <div>
      <p>Halo, {{ Auth::user()->name }}!</p>
      <p>Role: {{ Auth::user()->role }}</p>
    </div>

    @if (session('success'))
      <div class="rounded border border-green-300 bg-green-50 p-3 text-green-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse ($products as $product)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
          @endif

          <div class="p-5 space-y-2">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $product->description }}</p>
            <p class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

            <form method="POST" action="{{ route('checkout.store', $product->id) }}" class="checkout-form flex items-center gap-2 pt-2">
              @csrf
              <input
                type="number"
                name="quantity"
                min="1"
                value="1"
                class="w-20 rounded border-gray-300 text-sm"
              >
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Checkout
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="col-span-full rounded border border-dashed border-gray-300 p-4 text-gray-600 dark:text-gray-300">
          Belum ada produk tersedia.
        </div>
      @endforelse
    </div>
  </div>

  <script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
  <script>
    const checkoutForms = document.querySelectorAll('.checkout-form');

    async function updatePaymentStatus(monitorId, status) {
      await fetch('{{ route('payment.status.update') }}', {
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
          alert(error.message || 'Terjadi kesalahan saat checkout.');
          submitButton.disabled = false;
          submitButton.textContent = 'Checkout';
        }
      });
    });
  </script>
</x-app-layout>