let minutos = document.querySelector("#minutos");
let segundos = document.querySelector("#segundos");

function ContagemRegressiva(segundos, minutos){
    
    let cronometro = setInterval(() =>{
        
        if(minutos.textContent == "00" && segundos.textContent == "00"){
            alert("Tempo esgotado!");
            return clearInterval(cronometro);
        }
        else if(segundos.textContent == "00"){

            segundos.innerText = "59";

            let minutoAtual = Number(document.querySelector("#minutos").textContent);
            minutoAtual--;
            if(minutoAtual>9){                
                minutos.innerHTML = `${minutoAtual}`;
            }else{
                minutos.innerHTML = `0${minutoAtual}`;
            }            
        }
        else{            
            let segundoAtual = Number(document.querySelector("#segundos").textContent);
            segundoAtual--;
            if(segundoAtual>9){                
                segundos.innerHTML = `${segundoAtual}`;
            }else{
                segundos.innerHTML = `0${segundoAtual}`;
            }
        }
    }, 1000)
}

ContagemRegressiva(segundos, minutos);