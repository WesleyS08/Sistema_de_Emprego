let questoesAdicionadas = document.querySelector(".questoesAdicionadas");
let acumula = 2;

function AdicionarQuestao(){
    
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
    if(modo==="noturno"){
        numQuestao.style.color="whitesmoke";
    }
    else if(modo==="claro"){
        numQuestao.style.color="black";
    }

    ponto.classList.add("ponto");           
    ponto.classList.add("noturno");   
    ponto.textContent=".";
    if(modo==="noturno"){
        ponto.style.color="whitesmoke";
    }
    else if(modo==="claro"){
        ponto.style.color="black";
    }

    inputSimples.classList.add("inputSimples");    
    inputSimples.classList.add("noturno");
    if(modo==="noturno"){
        inputSimples.style.color="whitesmoke";
    }
    else if(modo==="claro"){
        inputSimples.style.color="black";
    }

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
        if(modo==="noturno"){
            inputResposta[i].style.color="whitesmoke";
        }
        else if(modo==="claro"){
            inputResposta[i].style.color="black";
        }
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

document.querySelector("#btnAdicionar").addEventListener("click", AdicionarQuestao);