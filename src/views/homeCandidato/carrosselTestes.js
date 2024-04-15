const carrosselTestes = document.querySelector("#carrosselTestes");
let scrollPerClickTeste = 1040;
let scrollAmountTeste = 0;

function SliderScrollLeft(){
    carrosselTestes.scrollTo({
        top:0,
        left: (scrollAmountTeste -= scrollPerClickTeste),
        behavior:"smooth"
    });

    if(scrollAmountTeste < 0){   
        scrollAmountTeste = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountTeste <= carrosselTestes.scrollWidth - carrosselTestes.clientWidth){
        carrosselTestes.scrollTo({
            top:0,
            left: (scrollAmountTeste += scrollPerClickTeste),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftTestes").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightTestes").addEventListener('click', SliderScrollRight);