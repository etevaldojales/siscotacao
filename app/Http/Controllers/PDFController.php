<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Services\PDFService;

class PDFController extends Controller
{
    protected $pdfService;

    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate PDF for a given Pedido.
     *
     * @param Pedido $pedido
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePedidoPDF(Pedido $pedido)
    {
        return $this->pdfService->generatePedidoPDF($pedido);
    }
}
