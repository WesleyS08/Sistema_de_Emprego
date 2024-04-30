let btnMostraSenhaCandidato = document.querySelector("#mostrarSenhaCandidato");
let btnMostraSenhaRecrutador = document.querySelector("#mostrarSenhaRecrutador");
let senhaCandidato = document.querySelector("#senhaCandidato");
let senhaRecrutador = document.querySelector("#senhaRecrutador");
let olhoCandidato = document.querySelector("#olhoCandidato");
let olhoRecrutador = document.querySelector("#olhoRecrutador");
let senhaVisivelCandidato = false;
let senhaVisivelRecrutador = false;

function MostrarSenhaCandidato(){
    if(senhaVisivelCandidato==false){
        olhoCandidato.src="../../assets/images/icones_diversos/closeEye.svg";
        senhaCandidato.type="text";
        senhaVisivelCandidato = true;
    }else{
        olhoCandidato.src="../../assets/images/icones_diversos/openEye.svg";
        senhaCandidato.type="password";
        senhaVisivelCandidato = false;
    }
}

function MostrarSenhaRecrutador(){
    if(senhaVisivelRecrutador==false){
        senhaRecrutador.src="../../assets/images/icones_diversos/closeEye.svg";
        senhaRecrutador.type="text";
        senhaVisivelRecrutador = true;
    }else{
        senhaRecrutador.src="../../assets/images/icones_diversos/openEye.svg";
        senhaRecrutador.type="password";
        senhaVisivelRecrutador = false;
    }
}

btnMostraSenhaCandidato.addEventListener("click", MostrarSenhaCandidato);
btnMostraSenhaRecrutador.addEventListener("click", MostrarSenhaRecrutador);