export default function (valor, mascara) {
    // Remove caracteres não numéricos do valor
    let valorFormatado = '';
    valor = valor.replace(/\D/g, '');
    let i = mascara.length - 1;
    let j = valor.length - 1;

    while (i >= 0) {
        if (mascara[i] === '#') {
            if (j >= 0) {
                valorFormatado = valor[j] + valorFormatado;
                j--;
            } else {
                valorFormatado = '0' + valorFormatado; // Preenche com zero se não houver mais dígitos no valor
            }
        } else {
            valorFormatado = mascara[i] + valorFormatado; // Mantém o caractere da máscara
        }
        i--;
    }

    return valorFormatado;
}
