/**
 * Aplica uma máscara a uma string.
 *
 * @param {any} valor - A string a ser formatada.
 * @param {string} pattern - O padrão da máscara, onde '#' será substituído por caracteres numéricos.
 * @returns {string} - A string formatada.
 */
export default function (valor = '', pattern = '') {
    // Convertendo o valor para string e removendo caracteres não numéricos
    const valorNumerico = String(valor).replace(/\D/g, '');
    let resultado = '';
    let indiceValor = 0;

    // Iterando sobre o padrão para criar a string formatada
    for (let i = 0; i < pattern.length; i++) {
        if (indiceValor >= valorNumerico.length) break;

        if (pattern[i] === '#') {
            resultado += valorNumerico[indiceValor] || '';
            indiceValor++;
        } else {
            resultado += pattern[i];
        }
    }
    return resultado.substring(0, pattern.length)
}
