<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CotacaoFornecedorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $nomeFornecedor;

    /**
     * Create a new message instance.
     *
     * @param string $link
     * @return void
     */
    public function __construct($link, $nomeFornecedor)
    {
        $this->link = $link; 
        $this->nomeFornecedor = $nomeFornecedor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nova Cotação')
                    ->view('emails.cotacao_fornecedor')
                    ->with([
                        'link' => $this->link,
                        'nomeFornecedor' => $this->nomeFornecedor,
                    ]);
    }
}
