<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidNotification extends Mailable
{
    use Queueable, SerializesModels;

    public \App\Models\Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pembayaran & Detail Pesanan #' . $this->order->order_number . ' - Dyanaf Creative',
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('support@dyanaf.id', 'Dyanaf Creative Support'),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.paid',
            with: [
                'order' => $this->order,
                'trackingUrl' => route('tracking-order', ['order_number' => $this->order->order_number]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
