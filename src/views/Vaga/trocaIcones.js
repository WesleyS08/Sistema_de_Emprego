let tipoProfissional = document.querySelector("#tipoProfissional");
let imgTipoProfissional = document.querySelector("#imgTipoProfissional");

let modalidade = document.querySelector("#modalidade");
let imgModalidade = document.querySelector("#imgModalidade");

let jornada = document.querySelector("#jornada");
let imgJornada = document.querySelector("#imgJornada");

let nivel = document.querySelector("#nivel");
let imgNivel = document.querySelector("#imgNivel");

function trocaProfissional(){
    if (tipoProfissional.textContent.includes("Estágio") || tipoProfissional.textContent.includes("Jovem Aprendiz")) {
        imgTipoProfissional.src = "../../assets/images/icones_vaga/estudanteSemFundo.svg";
    }
    if(tipoProfissional.textContent.includes("CLT")){
        imgTipoProfissional.src="../../assets/images/icones_vaga/cltSemFundo.svg";
    }
    if(tipoProfissional.textContent.includes("PJ")){
        imgTipoProfissional.src="../../assets/images/icones_vaga/pjSemFundo.svg";
    }
}

function trocaModalidade(){
    if(modalidade.textContent.includes("Presencial")){
        imgModalidade.src="../../assets/images/icones_vaga/iconePresencial.svg";
    }
    if(modalidade.textContent.includes("Remoto")){
        imgModalidade.src="../../assets/images/icones_vaga/iconeRemoto.svg";
    }
}

function trocaJornada(){
    if(jornada.textContent.includes("Tempo integral")){
        imgJornada.src="../../assets/images/icones_vaga/iconeTempoIntegral.svg";
    }
    if(jornada.textContent.includes("Meio período")){
        imgJornada.src="../../assets/images/icones_vaga/iconeMeioPeriodo.svg";
    }
}

function trocaNivel(){
    if(nivel.textContent.includes("Médio")){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelMedio.svg";
    }
    if(nivel.textContent.includes("Técnico")){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelTecnico.svg";
    }
    if(nivel.textContent.includes("Superior")){
        imgNivel.src="../../assets/images/icones_vaga/iconeNivelSuperior.svg";
    }
}

trocaProfissional();
trocaModalidade();
trocaJornada();
trocaNivel();