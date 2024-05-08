let questoesAdicionadas = document.querySelector(".questoesAdicionadas");
let acumula = 2;
function AdicionarQuestao(){
    let articleQuestao = document.createElement("div");
    let divPergunta = document.createElement("div");
    let numQuestao = document.createElement("p");
    let inputSimples = document.createElement("input");
    let divAlternativas = document.createElement("div");
    
    articleQuestao.className = "articleQuestao";    
    divPergunta.className = "divPergunta";    
    numQuestao.className = "numQuestao";       
    numQuestao.name = "numQuestao";
    numQuestao = document.createTextNode(`${acumula}.`);   
    inputSimples.className = "inputSimples";
    inputSimples.placeholder = "Pergunta";
    divAlternativas.className = "divAlternativas";
    
    let divRadio = [];
    let inputRadio = [];
    let inputResposta = [];

    for(let i=0; i<6; i++){
        divRadio[i] = document.createElement("div");
        inputRadio[i] = document.createElement("input");
        inputResposta[i] = document.createElement("input");

        divRadio[i].className = "divRadio";
        inputRadio[i].type = "radio";
        inputRadio[i].name = `questao${acumula}`;
        inputResposta[i].className = "inputSimples";
        inputResposta[i].placeholder = "Resposta";
    }

    questoesAdicionadas.appendChild(articleQuestao);
    articleQuestao.appendChild(divPergunta);    
    articleQuestao.appendChild(divAlternativas);
    divPergunta.appendChild(numQuestao);
    divPergunta.appendChild(inputSimples);

    for(let i=0; i<6; i++){
        divAlternativas.appendChild(divRadio[i]);
        divRadio[i].appendChild(inputRadio[i]);
        divRadio[i].appendChild(inputResposta[i]);
    }

    acumula++;
}

document.querySelector("#btnAdicionar").addEventListener("click", AdicionarQuestao);