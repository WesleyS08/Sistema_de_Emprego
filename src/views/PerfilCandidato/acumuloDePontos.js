let numPontos = document.getElementsByName("numPontos");
let progressoPontos = document.getElementsByName("progressoPontos");

function AcumulaPontos(){
    for(let i=0; i<numPontos.length; i++){
        progressoPontos[i].style.width = `${numPontos[i].textContent}px`;
    }
}

AcumulaPontos();