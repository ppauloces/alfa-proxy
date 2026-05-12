<?php

namespace App\Mail;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProxyRecyclingWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nomeUsuario;
    public Stock $stock;
    public \Carbon\Carbon $recicladaEm;

    public function __construct(string $nomeUsuario, Stock $stock, \Carbon\Carbon $recicladaEm)
    {
        $this->nomeUsuario = $nomeUsuario;
        $this->stock = $stock;
        $this->recicladaEm = $recicladaEm;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AlfaProxy – Sua proxy sera reciclada em 24h',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proxy-recycling-warning',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
