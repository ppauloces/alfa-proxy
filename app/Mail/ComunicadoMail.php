<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComunicadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nomeUsuario;

    public function __construct(string $nomeUsuario)
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Atualização AlfaProxy – Estabilidade e Liberação de Compras',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comunicado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
