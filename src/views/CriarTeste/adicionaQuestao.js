let questoesAdicionadas = document.querySelector(".questoesAdicionadas");
let acumula = 2;

function validarPalavras(palavra) {
    return new Promise((resolve, reject) => {
        console.log('Enviando solicitação de validação de palavras:', palavra);
        $.ajax({
            url: "verificar-palavra.php",
            type: "POST",
            data: { palavra: palavra },
            success: function (response) {
                try {
                    const resultado = JSON.parse(response);
                    resolve(resultado);
                } catch (e) {
                    reject("Erro ao processar resposta do servidor.");
                }
            },
            error: function () {
                reject("Erro ao verificar palavra. Tente novamente.");
            },
        });
    });
}

function AdicionarQuestao() {
    // Verificar se a pergunta anterior está vazia
    if (acumula > 2) {
        let perguntasAnteriores = document.querySelectorAll(".divPergunta");
        let ultimaPergunta = perguntasAnteriores[perguntasAnteriores.length - 1];
        let inputPerguntaAnterior = ultimaPergunta.querySelector(".inputSimples");
        if (inputPerguntaAnterior.value.trim() === "") {
            alert("Por favor, preencha a pergunta anterior antes de adicionar uma nova.");
            return;
        }
    }

    // Verificar se pelo menos uma opção de resposta foi selecionada na pergunta atual
    let opcoesResposta = document.querySelectorAll(".articleQuestao:last-of-type .divRadio input[type='radio']");
    let algumaOpcaoSelecionada = Array.from(opcoesResposta).some(opcao => opcao.checked);
    if (!algumaOpcaoSelecionada) {
        alert("Por favor, selecione pelo menos uma opção de resposta antes de adicionar uma nova pergunta.");
        return;
    }

    // Verificar se todos os campos da pergunta atual estão preenchidos
    let inputSimplesAtual = document.querySelectorAll(".articleQuestao:last-of-type .inputSimples");
    let todasPreenchidas = true;
    inputSimplesAtual.forEach(function(input) {
        if (input.value.trim() === "") {
            todasPreenchidas = false;
            return;
        }
    });
    if (!todasPreenchidas) {
        alert("Por favor, preencha todos os campos da pergunta atual antes de adicionar uma nova.");
        return;
    }

    // Obter palavras da pergunta atual
    let perguntaAtual = {};
    perguntaAtual.titulo = inputSimplesAtual[0].value.trim();
    perguntaAtual.alternativas = [];
    let inputRespostas = document.querySelectorAll(".articleQuestao:last-of-type .divAlternativas .inputSimples");
    inputRespostas.forEach(function(input) {
        perguntaAtual.alternativas.push(input.value.trim());
    });
    console.log("Pergunta Atual:", perguntaAtual);

    // Enviar palavras da pergunta para validação
    validarPalavras(perguntaAtual.titulo).then(response => {
        if (response.proibido) {
            alert("A palavra '" + perguntaAtual.titulo + "' é proibida.");
        } else if (!response.existe) {
            alert("A palavra '" + perguntaAtual.titulo + "' não existe.");
        } else {
            adicionarPergunta(perguntaAtual);
        }
    }).catch(error => {
        alert(error);
    });
}

function adicionarPergunta(pergunta) {
    // Adicionar nova pergunta
    let articleQuestao = document.createElement("div");
    let divPergunta = document.createElement("div");
    let numQuestao = document.createElement("p");
    let ponto = document.createElement("p");
    let inputSimples = document.createElement("input");
    let divAlternativas = document.createElement("div");

    articleQuestao.className = "articleQuestao";    
    divPergunta.className = "divPergunta";

    numQuestao.classList.add('numQuestao');
    numQuestao.classList.add('noturno');
    numQuestao.textContent=`${acumula}`;

    ponto.classList.add("ponto");           
    ponto.classList.add("noturno");   
    ponto.textContent=".";

    inputSimples.classList.add("inputSimples");    
    inputSimples.classList.add("noturno");

    inputSimples.placeholder = "Pergunta";    
    inputSimples.type = "text";

    divAlternativas.className = "divAlternativas";

    let divRadio = [];
    let inputRadio = [];
    let inputResposta = [];

    for(let i=0; i<5; i++){
        divRadio[i] = document.createElement("div");
        inputRadio[i] = document.createElement("input");
        inputResposta[i] = document.createElement("input");

        divRadio[i].className = "divRadio";
        inputRadio[i].type = "radio";
        inputRadio[i].name = `questao${acumula}`;
        inputResposta[i].classList.add("inputSimples");    
        inputResposta[i].classList.add("noturno");
        inputResposta[i].placeholder = "Resposta";
    }

    questoesAdicionadas.appendChild(articleQuestao);
    articleQuestao.appendChild(divPergunta);    
    articleQuestao.appendChild(divAlternativas);
    divPergunta.appendChild(numQuestao);    
    divPergunta.appendChild(ponto);
    divPergunta.appendChild(inputSimples);

    for(let i=0; i<5; i++){
        divAlternativas.appendChild(divRadio[i]);
        divRadio[i].appendChild(inputRadio[i]);
        divRadio[i].appendChild(inputResposta[i]);
    }
    acumula++;
}

function validarEnvioFormulario(event) {
    let perguntas = document.querySelectorAll(".articleQuestao");
    for (let i = 0; i < perguntas.length; i++) {
        let opcoesResposta = perguntas[i].querySelectorAll(".divRadio input[type='radio']");
        let algumaOpcaoSelecionada = Array.from(opcoesResposta).some(opcao => opcao.checked);
        if (!algumaOpcaoSelecionada) {
            alert("Por favor, selecione pelo menos uma opção de resposta para a pergunta " + (i + 1));
            event.preventDefault(); // Bloquear envio do formulário
            return;
        }
    }
}

document.querySelector("#btnAdicionar").addEventListener("click", AdicionarQuestao);
document.querySelector("#formulario").addEventListener("submit", validarEnvioFormulario);
