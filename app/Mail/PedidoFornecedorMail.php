<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoFornecedorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Pedido $pedido
     * @param string $pdf
     * @return void
     */
    public function __construct($pedido, $pdf)
    {
        $this->pedido = $pedido;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Pedido #' . $this->pedido->numero)
                    ->view('emails.pedido_fornecedor')
                    ->attachData($this->pdf, 'pedido_' . $this->pedido->numero . '.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'pedido' => $this->pedido,
                    ]);
    }
}
