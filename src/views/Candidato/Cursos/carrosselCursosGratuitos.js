const cursosGratuitos = document.querySelector("#cursosGratuitos");
let scrollPerClickCursoGratuito = 1110;
let scrollAmountCursoGratuito = 0;

function SliderScrollLeft(){
    cursosGratuitos.scrollTo({
        top:0,
        left: (scrollAmountCursoGratuito -= scrollPerClickCursoGratuito),
        behavior:"smooth"
    });

    if(scrollAmountCursoGratuito < 0){   
        scrollAmountCursoGratuito = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmountCursoGratuito <= cursosGratuitos.scrollWidth - cursosGratuitos.clientWidth){
        cursosGratuitos.scrollTo({
            top:0,
            left: (scrollAmountCursoGratuito += scrollPerClickCursoGratuito),
            behavior:"smooth"
        })
    }
}

document.querySelector("#leftCursosGratuitos").addEventListener('click', SliderScrollLeft);
document.querySelector("#rightCursosGratuitos").addEventListener('click', SliderScrollRight);