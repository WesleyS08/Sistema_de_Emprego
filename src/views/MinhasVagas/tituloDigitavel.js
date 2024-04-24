const titulo = document.querySelector("#tituloAutomatico");
const text1 = "inhas vagas"
const interval = 150; // variável do tempo de digitação

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