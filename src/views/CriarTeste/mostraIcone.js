let fotoDePerfil = document.querySelector(".divImgTeste");

function MostrarIcone(){
    document.querySelector(".divIconeEditar").style.display="flex";
}

function EsconderIcone(){
    document.querySelector(".divIconeEditar").style.display="none";
}

fotoDePerfil.addEventListener("mouseover", MostrarIcone);
fotoDePerfil.addEventListener("mouseout", EsconderIcone);