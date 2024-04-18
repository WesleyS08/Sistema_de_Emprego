let tipoProfissional = document.querySelector("#tipoProfissional");
let imgTipoProfissional = document.querySelector("#imgTipoProfissional");

let modalidade = document.querySelector("#modalidade");
let imgModalidade = document.querySelector("#imgModalidade");

let jornada = document.querySelector("#jornada");
let imgJornada = document.querySelector("#imgJornada");

let nivel = document.querySelector("#nivel");
let imgNivel = document.querySelector("#imgNivel");

function trocaProfissional(){
    if(tipoProfissional.textContent == "Estágio" || tipoProfissional.textContent == "Jovem Aprendiz"){
        imgTipoProfissional.src="../../assets/images/icones_vaga/estudanteSemFundo.svg";
    }
    if(tipoProfissional.textContent == "CLT"){
        imgTipoProfissional.src="../../assets/images/icones_vaga/cltSemFundo.svg";
    }
    if(tipoProfissional.textContent == "PJ"){
        imgTipoProfissional.src="../../assets/images/icones_vaga/pjSemFundo.svg";
    }
}

function trocaModalidade(){
    if(modalidade.textContent == "Presencial"){
        imgModalidade.src="../../assets/images/icones_vaga/iconePresencial.svg";
    }
    if(modalidade.textContent == "Remoto"){
        imgModalidade.src="../../assets/images/icones_vaga/iconeRemoto.svg";
    }
}

function trocaJornada(){
    if(jornada.textContent == "Tempo integral"){
        imgJornada.src="../../assets/images/icones_vaga/iconeTempoIntegral.svg";
    }
    if(jornada.textContent == "Meio período"){
        imgJornada.src="../../assets/images/icones_vaga/iconeMeioPeriodo.svg";
    }
}

function trocaNivel(){
    if(nivel.textContent == "Ensino Médio"){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelMedio.svg";
    }
    if(nivel.textContent == "Ensino Técnico"){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelTecnico.svg";
    }
    if(nivel.textContent == "Ensino Superior"){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelSuperior.svg";
    }
}

trocaProfissional();
trocaModalidade();
trocaJornada();
trocaNivel();