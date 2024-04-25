// Função para pegar o Id do questionário vindo da url, serve para redirecionar após o fim da contagem do cronometro â página anterior.
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

// Obter o ID do questionário da URL
var questionarioId = getParameterByName('id');

// Verificar se o ID do questionário foi encontrado na URL
if (questionarioId !== null) {
    // No momento não há nada a ser adicionado aqui

} else {
    console.error('ID do questionário não encontrado na URL.');
}

let minutos = document.querySelector("#minutos");
let segundos = document.querySelector("#segundos");

function ContagemRegressiva(segundos, minutos) {

    let cronometro = setInterval(() => {

        if (minutos.textContent === "00" && segundos.textContent === "00") {

            clearInterval(cronometro);

            alert("Tempo esgotado!");

            // Redireciona para a página desejada após o tempo esgotado, técnicamente seria a página de resultado que pode ser adicionada futuramente (ou não)
            window.location.href = "../PreparaTeste/preparaTeste.php?id=" + questionarioId;

            return;

        } else if (segundos.textContent === "00") {
            segundos.innerText = "59";

            let minutoAtual = Number(minutos.textContent);
            minutoAtual--;

            if (minutoAtual > 9) {                
                minutos.innerHTML = `${minutoAtual}`;

            } else {
                minutos.innerHTML = `0${minutoAtual}`;

            }
        } else {
            let segundoAtual = Number(segundos.textContent);

            segundoAtual--;

            if (segundoAtual > 9) {
                segundos.innerHTML = `${segundoAtual}`;

            } else {
                segundos.innerHTML = `0${segundoAtual}`;

            }
        }
    }, 1000);
}

ContagemRegressiva(segundos, minutos);