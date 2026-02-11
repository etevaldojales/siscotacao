<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Usuario;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Barryvdh\Debugbar\Facades\Debugbar;

class ProdutoUploadController extends Controller
{
    private function maskCnpj($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj); // Remove anything that is not a digit
        if (strlen($cnpj) !== 14) {
            return $cnpj; // Return as is if length is not 14 digits
        }
        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }

    public function showUploadForm()
    {
        return view('produto_upload');
    }

    public function getCategoriesByCnpj(Request $request)
    {

        $cnpj = $request->cnpj_comprador;

        if (!$cnpj) {
            return response()->json(['error' => 'CNPJ comprador is required'], 400);
        }

        // Optionally mask the CNPJ to match the stored format
        $maskedCnpj = $this->maskCnpj($cnpj);

        $categories = Categoria::where('cnpj_comprador', $maskedCnpj)->get(['id', 'nome']);

        return response()->json($categories);
    }

    public function upload(Request $request)
    {
        $dados = $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        //$file = $request->file('excel_file');
        $msg = 'Produtos importados com sucesso';

        if ($request->hasFile('excel_file') && $request->file('excel_file')->isValid()) {
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);

            if (empty($data) || empty($data[0])) {
                return response()->json(['error' => 'Excel file is empty or invalid'], 400);
            }

            $rows = $data[0];

            DB::beginTransaction();

            try {
                foreach ($rows as $row) {

                    $maskedCnpj = $this->maskCnpj($row[6] ?? '');
                    $category = Categoria::where('descricao', $row[4] ?? '')->where('cnpj_comprador', '=', $maskedCnpj)->first();

                    if(!$category) {
                        // If category does not exist, create it
                        $category = Categoria::create([
                            'nome' => $row[4] ?? '',
                            'descricao' => $row[4] ?? '',
                            'cnpj_comprador' => $maskedCnpj,
                            'user_id' => 1 // Assuming user_id is 1 for simplicity
                        ]);
                    }
                
                    $produto = Produto::create([
                        'codigo' => $row[2] ?? 0,
                        'name' => $row[3] ?? '',
                        'description' => $row[4] ?? null,
                        'category_id' => $category ? $category->id : null,
                        'cnpj_comprador' => $maskedCnpj,
                        'price' => 0,
                        'stock' => 0,
                        'status' => 'Ativo',
                        'user_id' => 1
                    ]);
                   
                }

                DB::commit(); // Commit to save changes to the database

                return response()->json(['success' => $msg]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Falha ao importar produtos: ' . $e->getMessage()], 500);
            }
        } else {
            return redirect()->back()->with('error', 'Arquivo não encontrado ou inválido.');
        }
    }
}
