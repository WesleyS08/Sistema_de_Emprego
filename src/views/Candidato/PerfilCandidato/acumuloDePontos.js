let numPontos = document.getElementsByClassName("numPontos");
let progressoPontos = document.getElementsByClassName("progresso");

function AcumulaPontos() {
    for (let i = 0; i < numPontos.length; i++) {
        progressoPontos[i].style.width = `${numPontos[i].textContent}px`;
    }
}

AcumulaPontos();