<table class="table table-bordered" id="productGrid">
    <thead>
        <tr style="background-color: #D3D3D3;">
            <th>Código</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Status</th>
            <th>Categoria</th>
            <th>CNPJ Comprador</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($produtos) && $produtos->count() > 0)

            @foreach($produtos as $produto)
                <tr style="background-color: {{ $loop->iteration % 2 == 0 ? "#DCDCDC" : "" }};">
                    <td>{{ $produto->codigo }}</td>
                    <td>{{ $produto->name }}</td>
                    <td>{{ $produto->description }}</td>
                    <td>{{ $produto->status }}</td>
                    <td>{{ $produto->categoria ? $produto->categoria->nome : '' }}</td>
                    <td>{{ $produto->cnpj_comprador ? $produto->cnpj_comprador : '' }}</td>
                    <td>
                        <nobr>
                        <button class="btn btn-sm btn-primary btn-edit" 
                            data-id="{{ $produto->id }}" 
                            data-codigo="{{ $produto->codigo }}"
                            data-name="{{ $produto->name }}" 
                            data-description="{{ $produto->description }}" 
                            data-status="{{ $produto->status }}"
                            data-category_id="{{ $produto->category_id }}"
                            data-cnpj_comprador="{{ $produto->cnpj_comprador }}"
                            data-cnpj_comprador_display="{{ $produto->cnpj_comprador }}"
                            data-marcas="{{ $produto->marcas->pluck('id')->implode(',') }}">
                            Editar
                        </button>
                        @if(auth()->user()->isAdmin())
                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $produto->id }}">
                            Excluir
                        </button>
                        @endif
                        </nobr>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="text-center">Nenhum produto cadastrado.</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Pagination Links -->
<div>
    {{ $produtos->links() }}
</div>
