let umaEstrela = document.querySelector("#umaEstrela");
let duasEstrelas = document.querySelector("#duasEstrelas");
let tresEstrelas = document.querySelector("#tresEstrelas");
let quatroEstrelas = document.querySelector("#quatroEstrelas");
let cincoEstrelas = document.querySelector("#cincoEstrelas");

function AdicionaUma(){

    umaEstrela.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    
    duasEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    tresEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    quatroEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    cincoEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"

    return true;
}

function AdicionaDuas(){

    umaEstrela.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    duasEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"

    tresEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    quatroEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    cincoEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
}

function AdicionaTres(){

    umaEstrela.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    duasEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    tresEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"

    quatroEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
    cincoEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
}

function AdicionaQuatro(){

    umaEstrela.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    duasEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    tresEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    quatroEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"

    cincoEstrelas.src="../../assets/images/icones_diversos/orangeStar.svg"
}

function AdicionaCinco(){

    umaEstrela.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    duasEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    tresEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    quatroEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
    cincoEstrelas.src="../../assets/images/icones_diversos/orangeStar-fill.svg"
}

umaEstrela.addEventListener("click", AdicionaUma);
duasEstrelas.addEventListener("click", AdicionaDuas);
tresEstrelas.addEventListener("click", AdicionaTres);
quatroEstrelas.addEventListener("click", AdicionaQuatro);
cincoEstrelas.addEventListener("click", AdicionaCinco);