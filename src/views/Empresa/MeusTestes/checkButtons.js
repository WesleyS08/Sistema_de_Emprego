let btnBasico = document.querySelector("#btnBasico");
let btnIntermediario = document.querySelector("#btnIntermediario");
let btnExperiente = document.querySelector("#btnExperiente");

let basicoMoficado = false;
let interMoficado = false;
let expMoficado = false;

// ======================================================

function Basico(){
    if(basicoMoficado == false){        
        btnBasico.style.backgroundColor = "var(--laranja)";
        btnBasico.style.border = "1px solid var(--laranja)"
        btnBasico.style.color = "whitesmoke";
        basicoMoficado = true;
    }
    else{
        btnBasico.style = "initial";
        basicoMoficado = false;
    }
}

function Intermediario(){
    if(interMoficado == false){
        btnIntermediario.style.backgroundColor = "var(--laranja)";
        btnIntermediario.style.border = "1px solid var(--laranja)"
        btnIntermediario.style.color = "whitesmoke";
        interMoficado = true;
    }
    else{
        btnIntermediario.style = "initial";
        interMoficado = false;
    }
}

function Experiente(){
    if(expMoficado == false){
        btnExperiente.style.backgroundColor = "var(--laranja)";
        btnExperiente.style.border = "1px solid var(--laranja)"
        btnExperiente.style.color = "whitesmoke";
        expMoficado = true;
    }
    else{
        btnExperiente.style = "initial";
        expMoficado = false;
    }
}

// ======================================================

btnBasico.addEventListener("click", Basico);
btnIntermediario.addEventListener("click", Intermediario);
btnExperiente.addEventListener("click", Experiente);
