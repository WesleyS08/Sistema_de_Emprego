const titulo = document.querySelector("#tituloAutomatico");
const text1 = "eu futuro está aqui!";
const interval = 140; // variável do tempo de digitação

// método para fazer o título autodigitável
function showText(titulo, text, interval){
    let char = text1.split("").reverse(); // esta variável está recebendo um array de todos caracteres do texto (ao inverso)
    
    // função responsável pela inserção de letras no título   
    let typer = setInterval(() => {

        // este if verifica se todos os caractéres já foram digitados (pois eles são removidos do array a medida que são exibidos)
        if(!char.length){
            return clearInterval(typer); // Encerrando a função
        }

        let next = char.pop(); // variável recebendo e removendo a última letra do array
        titulo.innerHTML += next; // adicionando a última letra ao título

    }, interval)
}

showText(titulo, text1, interval);