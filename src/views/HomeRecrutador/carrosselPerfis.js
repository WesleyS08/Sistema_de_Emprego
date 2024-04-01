const sliderPerfil = document.querySelector("#carrosselPerfis");
let scrollPerClickPerfil = 1000;
let scrollAmountPerfil = 0;

function SliderScrollLeft(){
    sliderPerfil.scrollTo({
        top:0,
        left: (scrollAmountPerfil -= scrollPerClickPerfil),
        behavior:"smooth"
    });

    if(scrollAmountPerfil < 0){   
        scrollAmountPerfil = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountPerfil <= sliderPerfil.scrollWidth - sliderPerfil.clientWidth){
        sliderPerfil.scrollTo({
            top:0,
            left: (scrollAmountPerfil += scrollPerClickPerfil),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftPerfis").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightPerfis").addEventListener('click', SliderScrollRight);