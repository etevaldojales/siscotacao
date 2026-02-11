<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Cotacao;
//use Barryvdh\DomPDF\Facade\PDF;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFService
{
    /**
     * Generate PDF for a given Pedido.
     *
     * @param Pedido $pedido
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePedidoPDF(Pedido $pedido)
    {
        // Load the PDF view with pedido data
        $pdf = Pdf::loadView('pedidos.pdf', ['pedido' => $pedido]);

        return $pdf;
    }

    /**
     * Generate PDF for a given Cotacao.
     *
     * @param \stdClass $cotacao
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateCotacaoPDF(\stdClass $cotacao)
    {
        // Load the PDF view with cotacao data
        $pdf = Pdf::loadView('cotacao.pdf', ['cotacao' => $cotacao]);

        return $pdf;
    }

    /**
     * Generate PDF for a given Cotacao.
     *
     * @param Cotacao $cotacao
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePDF(Cotacao $cotacao)
    {
        // Load the PDF view with cotacao data
        $pdf = Pdf::loadView('cotacao.pdf', ['cotacao' => $cotacao]);

        return $pdf;
    }

}
