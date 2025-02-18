<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EnviarTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {

    }


    public function build()
    {
        $usu = Auth::user();
        $this->user = User::where('id',$usu->id);
        $this->pdf = Pdf::loadView('emails.ticket', ['user' => $usu]); // Crea el PDF según el usuario

        $mail = $this->subject('Tu ticket para las Jornadas de Videojuegos')
            ->view('emails.ticket')
            ->attachData($this->pdf->output(), 'ticket.pdf', [
                'mime' => 'application/pdf',
            ]);
        $usu->confirmado = true;
        Mail::to($usu->email)->send($mail);

        return redirect()->route('home');

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enviar Ticket',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
