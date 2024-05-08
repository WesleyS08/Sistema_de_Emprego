let btnMeioPeriodo = document.querySelector("#btnMeioPeriodo");
let btnIntegral = document.querySelector("#btnIntegral");

let btnRemoto = document.querySelector("#btnRemoto");
let btnPresencial = document.querySelector("#btnPresencial");

let btnJovemAprendiz = document.querySelector("#btnJovemAprendiz");
let btnEstagio = document.querySelector("#btnEstagio");
let btnClt = document.querySelector("#btnClt");
let btnPj = document.querySelector("#btnPj");

let btnMedio = document.querySelector("#btnMedio");
let btnTecnico = document.querySelector("#btnTecnico");
let btnSuperior = document.querySelector("#btnSuperior");

let btnAberto = document.querySelector("#btnAberto");
let btnbtnEncerrado = document.querySelector("btnEncerrado");

// ======================================================

function MeioPeriodo(){
    btnMeioPeriodo.style.backgroundColor = "var(--laranja)";
    btnMeioPeriodo.style.color = "whitesmoke";

    btnIntegral.style = "initial";
}

function Integral(){
    btnIntegral.style.backgroundColor = "var(--laranja)";
    btnIntegral.style.color = "whitesmoke";

    btnMeioPeriodo.style = "initial";
}

// ======================================================

function Remoto(){
    btnRemoto.style.backgroundColor = "var(--laranja)";
    btnRemoto.style.color = "whitesmoke";

    btnPresencial.style = "initial";
}

function Presencial(){
    btnPresencial.style.backgroundColor = "var(--laranja)";
    btnPresencial.style.color = "whitesmoke";

    btnRemoto.style = "initial";
}

// ======================================================

function JovemAprendiz(){
    btnJovemAprendiz.style.backgroundColor = "var(--laranja)";
    btnJovemAprendiz.style.color = "whitesmoke";

    btnEstagio.style = "initial";
    btnClt.style = "initial";
    btnPj.style = "initial";
}

function Estagio(){
    btnEstagio.style.backgroundColor = "var(--laranja)";
    btnEstagio.style.color = "whitesmoke";

    btnJovemAprendiz.style = "initial";
    btnClt.style = "initial";
    btnPj.style = "initial";
}

function Clt(){
    btnClt.style.backgroundColor = "var(--laranja)";
    btnClt.style.color = "whitesmoke";

    btnJovemAprendiz.style = "initial";
    btnEstagio.style = "initial";
    btnPj.style = "initial";
}

function Pj(){
    btnPj.style.backgroundColor = "var(--laranja)";
    btnPj.style.color = "whitesmoke";

    btnJovemAprendiz.style = "initial";
    btnClt.style = "initial";
    btnEstagio.style = "initial";
}

function Medio(){
    btnMedio.style.backgroundColor = "var(--laranja)";
    btnMedio.style.color = "whitesmoke";

    btnTecnico.style = "initial";
    btnSuperior.style = "initial";
}

function Tecnico(){
    btnTecnico.style.backgroundColor = "var(--laranja)";
    btnTecnico.style.color = "whitesmoke";

    btnMedio.style = "initial";
    btnSuperior.style = "initial";
}

function Superior(){
    btnSuperior.style.backgroundColor = "var(--laranja)";
    btnSuperior.style.color = "whitesmoke";

    btnMedio.style = "initial";
    btnTecnico.style = "initial";
}

// ======================================================

function Aberto(){
    btnAberto.style.backgroundColor = "var(--laranja)";
    btnAberto.style.color = "whitesmoke";

    btnEncerrado.style = "initial";
}

function Encerrado(){
    btnEncerrado.style.backgroundColor = "var(--laranja)";
    btnEncerrado.style.color = "whitesmoke";

    btnAberto.style = "initial";
}

// ======================================================

btnMeioPeriodo.addEventListener("click", MeioPeriodo);
btnIntegral.addEventListener("click", Integral);

btnRemoto.addEventListener("click", Remoto);
btnPresencial.addEventListener("click", Presencial);

btnJovemAprendiz.addEventListener("click", JovemAprendiz);
btnEstagio.addEventListener("click", Estagio);
btnClt.addEventListener("click", Clt);
btnPj.addEventListener("click", Pj);

btnMedio.addEventListener("click", Medio);
btnTecnico.addEventListener("click", Tecnico);
btnSuperior.addEventListener("click", Superior);

btnAberto.addEventListener("click", Aberto);
btnEncerrado.addEventListener("click", Encerrado);


