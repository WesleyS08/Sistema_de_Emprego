let btnEsq = document.querySelector("#btnEsquerda");
let btnDir = document.querySelector("#btnDireita");
let formCandidato = document.querySelector("#formCandidato");
let formRecrutador = document.querySelector("#formRecrutador");

function formEsquerda(){
    btnEsq.style.background = "var(--laranja)";
    btnDir.style.background = "var(--laranjaBranco)";

    formCandidato.style.left = "0px";
    formRecrutador.style.left = "600px";
}

function formDireita(){
    btnDir.style.background = "var(--laranja)";
    btnEsq.style.background = "var(--laranjaBranco)";

    formCandidato.style.left = "-600px";
    formRecrutador.style.left = "0px"; 
}

function IniciarFocandoBtn(){  
    formEsquerda();
}


IniciarFocandoBtn();
btnEsq.addEventListener("click", formEsquerda);
btnDir.addEventListener("click", formDireita);