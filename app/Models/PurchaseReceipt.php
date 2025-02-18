<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $orderDetails; // Aquí guardamos los detalles de la compra

    /**
     * Create a new message instance.
     *
     * @param  $orderDetails
     * @return void
     */
    public function __construct($orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Comprobante de compra')
            ->view('emails.purchaseReceipt') // Vista que usaremos para el correo
            ->with([
                'payerName' => $this->orderDetails->payer_name,
                'amount' => $this->orderDetails->amount,
                'transactionId' => $this->orderDetails->transaction_id,
            ]);
    }
}
