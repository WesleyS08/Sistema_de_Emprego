const sliders = document.querySelector(".carrosselBox");
let scrollPerClick = 1040;
let scrollAmount = 0;

function SliderScrollLeft(){
    sliders.scrollTo({
        top:0,
        left: (scrollAmount -= scrollPerClick),
        behavior:"smooth"
    });

    if(scrollAmount < 0){   
        scrollAmount = 0;
    }
}

function SliderScrollRight(){
    if(scrollAmount <= sliders.scrollWidth - sliders.clientWidth){
        sliders.scrollTo({
            top:0,
            left: (scrollAmount += scrollPerClick),
            behavior:"smooth"
        })
    }
}

document.querySelector(".btnLeftSlider").addEventListener('click', SliderScrollLeft);
document.querySelector(".btnRightSlider").addEventListener('click', SliderScrollRight);