<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrecoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                     => $this->id,
            'produto_id'             => $this->produto_id,
            'variacao_id'            => $this->variacao_id,
            'canal_de_venda_id'      => $this->canal_de_venda_id,
            'preco'                  => $this->preco,
            'preco_de_custo'         => $this->preco_de_custo,
            'ativo'                  => $this->ativo,
            'preco_promocao'         => $this->preco_promocao,
            'data_inicio_promocao'   => $this->data_inicio_promocao,
            'data_fim_promocao'      => $this->data_fim_promocao,
            'ativo_promocao'         => $this->ativo_promocao,
            'preco_atual'            => $this->preco_atual,
            'porcentagem_desconto'   => $this->porcentagem_desconto,
            'status_atual_promocao'  => $this->status_atual_da_promocao,
        ];
    }
}
