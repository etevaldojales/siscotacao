<tbody>
    @if($cotacoes)
        @foreach($cotacoes as $cotacao)
            <tr data-id="{{ $cotacao->id }}" style="background-color: {{ $loop->index % 2 == 0 ? '#FOF8FF' : '#DCDCDC' }}">
                <?php 
                if($cotacao->status == 1 && $cotacao->status_envio == 1) {
                    $vrdisabled = "";
                }
                else {
                    $vrdisabled = "disabled";
                }
                ?>
                <td>{{ $cotacao->numero }}</td>
                <td>{{ Carbon\Carbon::parse($cotacao->encerramento)->format('d/m/Y') }}</td>
                <td>{{ \App\Helpers\Helper::getStatusCotacao($cotacao->status) }}</td>
                <td>{{ \App\Helpers\Helper::getStatusEnvioCotacao($cotacao->status_envio) }}</td>
                <td>
                    <nobr>
                        <button class="btn btn-sm btn-warning btnEdit" {{ $vrdisabled }}>Editar</button>
                        <button class="btn btn-sm btn-primary btnAddItens" {{ $vrdisabled }}>Adicionar Ítens</button>
                        <button type="button" class="btn btn-sm btn-success" id="btnEnviaCot" {{ $vrdisabled }}>Enviar</button>
                    </nobr>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="5" style="text-align: center">Nenhuma cotação cadastrada</td>
        </tr>
    @endif
</tbody>
