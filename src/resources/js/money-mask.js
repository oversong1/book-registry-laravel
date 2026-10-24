// Mascara de dinheiro no formato brasileiro: 1234,56 -> 1.234,56
// Vale para todo <input data-mask="money">.

// Formata o que foi digitado: so digitos e uma virgula, pontos nos milhares, ate 2 casas
function formatMoney(texto) {
    // Tira tudo que nao for digito ou virgula (inclusive os pontos antigos)
    let limpo = texto.replace(/[^\d,]/g, '');

    // Separa a parte inteira dos centavos (so a primeira virgula conta)
    const [inteiro, ...resto] = limpo.split(',');
    let centavos = resto.join('').slice(0, 2); // no maximo 2 casas

    // Tira zeros a esquerda ("007" -> "7") e poe o ponto a cada 3 digitos, da direita pra esquerda
    let parteInteira = inteiro.replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Se digitou a virgula, ela aparece mesmo sem centavos ainda ("12," enquanto digita)
    return limpo.includes(',') ? `${parteInteira},${centavos}` : parteInteira;
}

// Converte "1.234,56" no formato que o servidor espera: "1234.56"
function toServer(texto) {
    return texto.replace(/\./g, '').replace(',', '.');
}

document.querySelectorAll('input[data-mask="money"]').forEach((input) => {
    // A cada tecla, reformata o campo
    input.addEventListener('input', () => {
        input.value = formatMoney(input.value);
    });

    // Antes de enviar o formulario, troca pelo formato do servidor
    input.form?.addEventListener('submit', () => {
        input.value = toServer(input.value);
    });
});
