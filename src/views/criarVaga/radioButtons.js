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

// ======================================================

function MeioPeriodo(){
    btnMeioPeriodo.style.backgroundColor = "var(--laranja)";
    btnMeioPeriodo.style.color = "whitesmoke";
    btnMeioPeriodo.style.transform = "scale(1.06)";

    btnIntegral.style = "initial";
}

function Integral(){
    btnIntegral.style.backgroundColor = "var(--laranja)";
    btnIntegral.style.color = "whitesmoke";
    btnIntegral.style.transform = "scale(1.06)";

    btnMeioPeriodo.style = "initial";
}

// ======================================================

function Remoto(){
    btnRemoto.style.backgroundColor = "var(--laranja)";
    btnRemoto.style.color = "whitesmoke";
    btnRemoto.style.transform = "scale(1.06)";

    btnPresencial.style = "initial";
}

function Presencial(){
    btnPresencial.style.backgroundColor = "var(--laranja)";
    btnPresencial.style.color = "whitesmoke";
    btnPresencial.style.transform = "scale(1.06)";

    btnRemoto.style = "initial";
}

// ======================================================

function JovemAprendiz(){
    btnJovemAprendiz.style.backgroundColor = "var(--laranja)";
    btnJovemAprendiz.style.color = "whitesmoke";
    btnJovemAprendiz.style.transform = "scale(1.06)";

    btnEstagio.style = "initial";
    btnClt.style = "initial";
    btnPj.style = "initial";
}

function Estagio(){
    btnEstagio.style.backgroundColor = "var(--laranja)";
    btnEstagio.style.color = "whitesmoke";
    btnEstagio.style.transform = "scale(1.06)";

    btnJovemAprendiz.style = "initial";
    btnClt.style = "initial";
    btnPj.style = "initial";
}

function Clt(){
    btnClt.style.backgroundColor = "var(--laranja)";
    btnClt.style.color = "whitesmoke";
    btnClt.style.transform = "scale(1.06)";

    btnJovemAprendiz.style = "initial";
    btnEstagio.style = "initial";
    btnPj.style = "initial";
}

function Pj(){
    btnPj.style.backgroundColor = "var(--laranja)";
    btnPj.style.color = "whitesmoke";
    btnPj.style.transform = "scale(1.06)";

    btnJovemAprendiz.style = "initial";
    btnClt.style = "initial";
    btnEstagio.style = "initial";
}

function Medio(){
    btnMedio.style.backgroundColor = "var(--laranja)";
    btnMedio.style.color = "whitesmoke";
    btnMedio.style.transform = "scale(1.06)";

    btnTecnico.style = "initial";
    btnSuperior.style = "initial";
}

function Tecnico(){
    btnTecnico.style.backgroundColor = "var(--laranja)";
    btnTecnico.style.color = "whitesmoke";
    btnTecnico.style.transform = "scale(1.06)";

    btnMedio.style = "initial";
    btnSuperior.style = "initial";
}

function Superior(){
    btnSuperior.style.backgroundColor = "var(--laranja)";
    btnSuperior.style.color = "whitesmoke";
    btnSuperior.style.transform = "scale(1.06)";

    btnMedio.style = "initial";
    btnTecnico.style = "initial";
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


