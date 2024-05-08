let visivel = false;

function MostraFiltros(){
    if(visivel==false){
        visivel = true;
        document.querySelector("#iconeFiltro").src= "../../../assets/images/icones_diversos/showVisible.svg";
        document.querySelector(".containerFiltros").style.display = "inline";
    }
    else{        
        visivel = false;
        document.querySelector("#iconeFiltro").src= "../../../assets/images/icones_diversos/showHidden.svg";
        document.querySelector(".containerFiltros").style.display = "none";
        
    }
}

document.querySelector("#mostraFiltros").addEventListener("click", MostraFiltros);