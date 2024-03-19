const sliderAnuncio = document.querySelector("#carrosselAnuncios");
let scrollPerClickAnuncio = 800;
let scrollAmountAnuncio = 0;

function SliderScrollLeft(){
    sliderAnuncio.scrollTo({
        top:0,
        left: (scrollAmountAnuncio -= scrollPerClickAnuncio),
        behavior:"smooth"
    });

    if(scrollAmountAnuncio < 0){   
        scrollAmountAnuncio = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountAnuncio <= sliderAnuncio.scrollWidth - sliderAnuncio.clientWidth){
        sliderAnuncio.scrollTo({
            top:0,
            left: (scrollAmountAnuncio += scrollPerClickAnuncio),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftAnuncios").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightAnuncios").addEventListener('click', SliderScrollRight);