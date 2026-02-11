<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacaoCotacaoFornecedorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $nomeFornecedor;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param string $link
     * @param string $nomeFornecedor
     * @param mixed $pdf
     * @return void
     */
    public function __construct($link, $nomeFornecedor, $pdf = null)
    {
        $this->link = $link; 
        $this->nomeFornecedor = $nomeFornecedor;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject('Aprovação da Cotação')
                    ->view('emails.confirmacao_cotacao_fornecedor')
                    ->with([
                        'link' => $this->link,
                        'nomeFornecedor' => $this->nomeFornecedor,
                    ]);

        if ($this->pdf) {
            $email->attachData($this->pdf, 'cotacao.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
