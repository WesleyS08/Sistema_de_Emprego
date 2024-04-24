const cursosPagos = document.querySelector("#cursosPagos");
let scrollPerClickCursoPago = 1110;
let scrollAmountCursoPago = 0;

function SliderScrollLeft(){
    cursosPagos.scrollTo({
        top:0,
        left: (scrollAmountCursoPago -= scrollPerClickCursoPago),
        behavior:"smooth"
    });

    if(scrollAmountCursoPago < 0){   
        scrollAmountCursoPago = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountCursoPago <= cursosPagos.scrollWidth - cursosPagos.clientWidth){
        cursosPagos.scrollTo({
            top:0,
            left: (scrollAmountCursoPago += scrollPerClickCursoPago),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftCursosPagos").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightCursosPagos").addEventListener('click', SliderScrollRight);