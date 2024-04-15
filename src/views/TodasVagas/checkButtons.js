let btnJovemAprendiz = document.querySelector("#btnJovemAprendiz");
let btnEstagio = document.querySelector("#btnEstagio");
let btnClt = document.querySelector("#btnClt");
let btnPj = document.querySelector("#btnPj");

let jaMoficado = false;
let estagioMoficado = false;
let cltMoficado = false;
let pjMoficado = false;

// ======================================================

function JovemAprendiz(){
    if(jaMoficado == false){        
        btnJovemAprendiz.style.backgroundColor = "var(--laranja)";
        btnJovemAprendiz.style.border = "1px solid var(--laranja)"
        btnJovemAprendiz.style.color = "whitesmoke";
        jaMoficado = true;
    }
    else{
        btnJovemAprendiz.style = "initial";
        jaMoficado = false;
    }
}

function Estagio(){
    if(estagioMoficado == false){
        btnEstagio.style.backgroundColor = "var(--laranja)";
        btnEstagio.style.border = "1px solid var(--laranja)"
        btnEstagio.style.color = "whitesmoke";
        estagioMoficado = true;
    }
    else{
        btnEstagio.style = "initial";
        estagioMoficado = false;
    }
}

function Clt(){
    if(cltMoficado == false){
        btnClt.style.backgroundColor = "var(--laranja)";
        btnClt.style.border = "1px solid var(--laranja)"
        btnClt.style.color = "whitesmoke";
        cltMoficado = true;
    }
    else{
        btnClt.style = "initial";
        cltMoficado = false;
    }
}

function Pj(){
    if(pjMoficado == false){
        btnPj.style.backgroundColor = "var(--laranja)";
        btnPj.style.border = "1px solid var(--laranja)"
        btnPj.style.color = "whitesmoke";
        pjMoficado = true;
    }
    else{
        btnPj.style = "initial";
        pjMoficado = false;
    }
}

// ======================================================

btnJovemAprendiz.addEventListener("click", JovemAprendiz);
btnEstagio.addEventListener("click", Estagio);
btnClt.addEventListener("click", Clt);
btnPj.addEventListener("click", Pj);
