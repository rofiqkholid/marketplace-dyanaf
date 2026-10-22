<x-mail::message>
# Halo, {{ $order->customer_name }}

Terima kasih atas pesanan Anda di **Dyanaf Creative**. Pembayaran Anda telah kami terima dan terverifikasi secara otomatis.

Pesanan Anda saat ini sedang dalam proses antrian pengerjaan / penyiapan oleh tim kami.

---

### Rincian Pesanan

<div style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 13px; line-height: 1.8; color: #1e293b; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; margin: 15px 0;">
<strong>Nomor Pesanan:</strong> {{ $order->order_number }}<br>
<strong>Item / Layanan:</strong> {{ $order->package_name ? $order->item_name . ' (' . $order->package_name . ')' : $order->item_name }}<br>
<strong>Total Pembayaran:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}<br>
<strong>Status Pembayaran:</strong> Lunas (Paid)<br>
<strong>Metode:</strong> {{ strtoupper($order->bank ? $order->bank . ' VIRTUAL ACCOUNT' : ($order->payment_type ?? 'QRIS')) }}<br>
<strong>Waktu Transaksi:</strong> {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB
</div>

---

Anda dapat memantau status pengerjaan atau mengakses aset/akun pesanan Anda kapan saja melalui tautan pelacakan berikut:

<x-mail::button :url="$trackingUrl" color="primary">
Cek Progres Pesanan
</x-mail::button>

Jika tombol di atas tidak dapat diklik, Anda juga dapat menyalin tautan berikut ke browser:  
<span style="font-family: ui-monospace, monospace; font-size: 12px;">[{{ $trackingUrl }}]({{ $trackingUrl }})</span>

Jika Anda memiliki pertanyaan seputar pesanan ini, silakan hubungi kami via WhatsApp di [0858-8172-1193](https://wa.me/6285881721193).

Salam hangat,<br>
**Dyanaf Creative Team**
</x-mail::message>
