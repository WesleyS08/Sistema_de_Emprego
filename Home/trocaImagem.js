let runningGuy = document.querySelector(".runningGuy");

function TrocarParaPreto(){
    runningGuy.src = "../imagens/runningBlack.svg";
}

function TrocarParaBranco(){
    runningGuy.src = "../imagens/runningWhite.svg";
}

document.querySelector("#trocaImg").addEventListener("mouseover", TrocarParaPreto);
document.querySelector("#trocaImg").addEventListener("mouseout", TrocarParaBranco);