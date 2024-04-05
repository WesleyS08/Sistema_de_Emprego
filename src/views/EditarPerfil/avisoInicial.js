let sobreMim = document.querySelector("#sobreMim");

function verificaTexto(){
    if(sobreMim.textContent==""){
        document.querySelector(".divAvisoInicial").style.display="flex";
    }else{        
        document.querySelector(".divAvisoInicial").style.display="none";
    }
}

verificaTexto();