let btnMostraSenha = document.querySelector("#mostrarSenha");
let olho = document.querySelector("#olho");
let senhaVisivel = false;

function MostrarSenha(){
    if(senhaVisivel==false){
        olho.src="../../assets/images/icones_diversos/openEye.svg";
        senha.type="text";
        senhaVisivel = true;
    }else{
        olho.src="../../assets/images/icones_diversos/closeEye.svg";
        senha.type="password";
        senhaVisivel = false;
    }
}

btnMostraSenha.addEventListener("click", MostrarSenha);