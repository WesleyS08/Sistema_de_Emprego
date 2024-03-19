const titulo = document.querySelector("#tituloAutomatico");
const nomeUsuario = "Pedro";
const interval = 150; // variável do tempo de digitação

function DeterminaHorario(){
    let hora = new Date().getHours().toString().padStart(2, '0')
    if(hora<12){
        return "om dia";
    }
    else if(hora<18){
        return "oa tarde";
    }
    else{
        return "oa noite"
    }
}


let text1 = `${DeterminaHorario()} ${nomeUsuario}!`;

function showText(titulo, text1, interval){
    let char = text1.split("").reverse();
      
    let typer = setInterval(() => {
        if(!char.length){
            return clearInterval(typer);
        }

        let next = char.pop();
        titulo.innerHTML += next;

    }, interval)
}

showText(titulo, text1, interval);