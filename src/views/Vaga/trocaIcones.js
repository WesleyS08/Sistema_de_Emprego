let tipoProfissional = document.querySelector("#tipoProfissional");
let imgTipoProfissional = document.querySelector("#imgTipoProfissional");

let modalidade = document.querySelector("#modalidade");
let imgModalidade = document.querySelector("#imgModalidade");

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

trocaProfissional();
trocaModalidade();