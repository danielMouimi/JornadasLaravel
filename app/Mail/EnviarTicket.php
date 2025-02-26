<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class EnviarTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdf;

    public function __construct($user)
    {
        $this->user = $user;
        $this->pdf = Pdf::loadView('emails.ticket', ['user' => $user]); // Genera el PDF
    }

    public function build()
    {
        return $this->subject('Tu ticket para las Jornadas de Videojuegos')
            ->view('emails.ticket')
            ->attachData($this->pdf->output(), 'ticket.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
