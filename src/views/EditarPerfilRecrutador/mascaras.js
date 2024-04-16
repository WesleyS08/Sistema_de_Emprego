const telefone = document.querySelector("#telefone");
const data = document.querySelector("#data");

telefone.addEventListener('keypress', (e) => {
    
    if(isNaN(e.key) && e.key !== '.' && e.key !== ',') e.preventDefault();

    let telefonelength = telefone.value.length

    if(telefonelength === 0){
        telefone.value += "(";
    }

    if(telefonelength === 3){        
        telefone.value += ") ";
    }

    if(telefonelength === 10 ){        
        telefone.value += "-";
    }
})