let sobre = document.querySelector("#sobre");

function verificaTexto(){
    if(sobre.textContent==""){
        document.querySelector(".divAvisoInicial").style.display="flex";
    }else{        
        document.querySelector(".divAvisoInicial").style.display="none";
    }
}

verificaTexto();