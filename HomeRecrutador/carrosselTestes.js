const sliderTeste = document.querySelector("#carrosselTestes");
let scrollPerClickTeste = 800;
let scrollAmountTeste = 0;

function SliderScrollLeft(){
    sliderTeste.scrollTo({
        top:0,
        left: (scrollAmountTeste -= scrollPerClickTeste),
        behavior:"smooth"
    });

    if(scrollAmountTeste < 0){   
        scrollAmountTeste = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountTeste <= sliderTeste.scrollWidth - sliderTeste.clientWidth){
        sliderTeste.scrollTo({
            top:0,
            left: (scrollAmountTeste += scrollPerClickTeste),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftTestes").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightTestes").addEventListener('click', SliderScrollRight);