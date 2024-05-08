const certificacoes = document.querySelector("#certificacoes");
let scrollPerClickCertificacoes = 1110;
let scrollAmountCertificacoes = 0;

function SliderScrollLeft(){
    certificacoes.scrollTo({
        top:0,
        left: (scrollAmountCertificacoes -= scrollPerClickCertificacoes),
        behavior:"smooth"
    });

    if(scrollAmountCertificacoes < 0){   
        scrollAmountCertificacoes = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountCertificacoes <= certificacoes.scrollWidth - certificacoes.clientWidth){
        certificacoes.scrollTo({
            top:0,
            left: (scrollAmountCertificacoes += scrollPerClickCertificacoes),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftCertificacoes").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightCertificacoes").addEventListener('click', SliderScrollRight);