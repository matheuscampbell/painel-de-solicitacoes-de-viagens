<?php

namespace App\Enums;

class OrigemFiscalProdutoEnum extends Enum
{
   //<option value="">Selecione</option> <option value="0" selected="">0 - Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8</option> <option value="1">1 - Estrangeira - Importação direta, exceto a indicada no código 6</option> <option value="2">2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7</option> <option value="3">3 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% (quarenta por cento) e inferior ou igual a 70% (setenta por cento)</option> <option value="4">4 - Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam o Decreto-Lei no 288/67, e as Leis nos 8.248/91, 8.387/91, 10.176/01 e 11.484/07</option> <option value="5">5 - Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40% (quarenta por cento)</option> <option value="6">6 - Estrangeira - Importação direta, sem similar nacional, constante em lista de Resolução CAMEX e gás natural</option> <option value="7">7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista de Resolução CAMEX e gás natural</option> <option value="8">8 - Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)</option>
    const NACIONAL_EXCETO_INDICADAS = 0;
    const ESTRANGEIRA_IMPORTACAO_DIRETA_EXCETO_INDICADA = 1;
    const ESTRANGEIRA_ADQUIRIDA_NO_MERCADO_INTERNO_EXCETO_INDICADA = 2;
    const NACIONAL_CONTEUDO_IMPORTACAO_SUPERIOR_40_INFERIOR_70 = 3;
    const NACIONAL_PRODUCAO_CONFORMIDADE_PROCESSOS_PRODUTIVOS_BASICOS = 4;
    const NACIONAL_CONTEUDO_IMPORTACAO_INFERIOR_40 = 5;
    const ESTRANGEIRA_IMPORTACAO_DIRETA_SEM_SIMILAR_NACIONAL = 6;
    const ESTRANGEIRA_ADQUIRIDA_NO_MERCADO_INTERNO_SEM_SIMILAR_NACIONAL = 7;
    const NACIONAL_CONTEUDO_IMPORTACAO_SUPERIOR_70 = 8;

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::NACIONAL_EXCETO_INDICADAS:
                return 'Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8';
            case self::ESTRANGEIRA_IMPORTACAO_DIRETA_EXCETO_INDICADA:
                return 'Estrangeira - Importação direta, exceto a indicada no código 6';
            case self::ESTRANGEIRA_ADQUIRIDA_NO_MERCADO_INTERNO_EXCETO_INDICADA:
                return 'Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7';
            case self::NACIONAL_CONTEUDO_IMPORTACAO_SUPERIOR_40_INFERIOR_70:
                return 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% (quarenta por cento) e inferior ou igual a 70% (setenta por cento)';
            case self::NACIONAL_PRODUCAO_CONFORMIDADE_PROCESSOS_PRODUTIVOS_BASICOS:
                return 'Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam o Decreto-Lei no 288/67, e as Leis nos 8.248/91, 8.387/91, 10.176/01 e 11.484/07';
            case self::NACIONAL_CONTEUDO_IMPORTACAO_INFERIOR_40:
                return 'Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40% (quarenta por cento)';
            case self::ESTRANGEIRA_IMPORTACAO_DIRETA_SEM_SIMILAR_NACIONAL:
                return 'Estrangeira - Importação direta, sem similar nacional, constante em lista de Resolução CAMEX e gás natural';
            case self::ESTRANGEIRA_ADQUIRIDA_NO_MERCADO_INTERNO_SEM_SIMILAR_NACIONAL:
                return 'Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista de Resolução CAMEX e gás natural';
            case self::NACIONAL_CONTEUDO_IMPORTACAO_SUPERIOR_70:
                return 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)';
            default:
                return self::getKeys()[$value] ?? 'N/A';
        }
    }
}
