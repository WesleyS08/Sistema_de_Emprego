const minhasVagas = document.querySelector("#minhasVagas");
let scrollPerClickMinhaVaga = 1040;
let scrollAmountMinhaVaga = 0;

function SliderScrollLeft(){
    minhasVagas.scrollTo({
        top:0,
        left: (scrollAmountMinhaVaga -= scrollPerClickMinhaVaga),
        behavior:"smooth"
    });

    if(scrollAmountMinhaVaga < 0){   
        scrollAmountMinhaVaga = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountMinhaVaga <= minhasVagas.scrollWidth - minhasVagas.clientWidth){
        minhasVagas.scrollTo({
            top:0,
            left: (scrollAmountMinhaVaga += scrollPerClickMinhaVaga),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftMinhasVagas").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightMinhasVagas").addEventListener('click', SliderScrollRight);