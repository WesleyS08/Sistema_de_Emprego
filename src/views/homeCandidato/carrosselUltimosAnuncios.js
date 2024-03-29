const ultimosAnuncios = document.querySelector("#ultimosAnuncios");
let scrollPerClickAnuncio = 1040;
let scrollAmountAnuncio = 0;

function SliderScrollLeft(){
    ultimosAnuncios.scrollTo({
        top:0,
        left: (scrollAmountAnuncio -= scrollPerClickAnuncio),
        behavior:"smooth"
    });

    if(scrollAmountAnuncio < 0){   
        scrollAmountAnuncio = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountAnuncio <= ultimosAnuncios.scrollWidth - ultimosAnuncios.clientWidth){
        ultimosAnuncios.scrollTo({
            top:0,
            left: (scrollAmountAnuncio += scrollPerClickAnuncio),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftUltimosAnuncios").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightUltimosAnuncios").addEventListener('click', SliderScrollRight);