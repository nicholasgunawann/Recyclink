<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Checkout | DompetX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <style>
        body { background-color: #f3f4f6; }
        .dompetx-brand { color: #1e3a8a; } /* Blue brand for payment gateway */
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <!-- Header Gateway -->
        <div class="bg-blue-900 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                <span class="text-white font-bold text-xl tracking-wide">DompetX</span>
            </div>
            <span class="text-blue-200 text-xs font-medium px-2 py-1 bg-blue-800 rounded-full border border-blue-700">SANDBOX MODE</span>
        </div>

        <div class="p-6">
            <div class="text-center mb-6">
                <p class="text-gray-500 text-sm mb-1">Total Pembayaran</p>
                <h1 class="text-4xl font-extrabold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h1>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Merchant</span>
                    <span class="font-semibold text-gray-900">Recyclink</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Order ID</span>
                    <span class="font-mono text-sm font-semibold text-gray-900">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Metode</span>
                    <span class="font-semibold text-gray-900 uppercase">{{ request()->query('method', 'E-Wallet') }}</span>
                </div>
            </div>

            <!-- Formulir Dummy -->
            <form action="{{ route('buyer.dompetx.process', $order->id) }}" method="POST">
                @csrf
                <input type="hidden" name="method" value="{{ request()->query('method', 'ewallet') }}">
                
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-900">Simulasi Pembayaran</p>
                            <p class="text-xs text-blue-700 mt-1">Ini adalah halaman demo. Klik tombol di bawah untuk mensimulasikan pembayaran berhasil.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 flex justify-center items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Simulasikan Pembayaran Sukses
                </button>
                
                <a href="{{ route('buyer.orders.payment.create', $order->id) }}" class="block text-center mt-4 text-sm text-gray-500 hover:text-gray-700 font-medium">
                    Batalkan & Kembali
                </a>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
