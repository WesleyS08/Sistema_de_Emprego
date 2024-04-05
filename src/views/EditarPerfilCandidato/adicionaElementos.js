let habilidade = document.querySelector("#habilidade");
let habilidadesAdicionadas = document.querySelector("#habilidadesAdicionadas");

let curso = document.querySelector("#curso");
let cursosAdicionados = document.querySelector("#cursosAdicionados");

let experiencia = document.querySelector("#experiencia");
let experienciasAdicionadas = document.querySelector("#experienciasAdicionadas");

function AdicionaHabilidade(){
    if(habilidade.value==""){
        document.querySelector("#avisoHabilidades").innerHTML="Preencha o campo!";
    }
    else{
        let novaHabilidade = document.createElement("li");
        novaHabilidade.innerHTML = habilidade.value;
        habilidadesAdicionadas.appendChild(novaHabilidade)
        habilidade.value = "";
        document.querySelector("#avisoHabilidades").innerHTML="";
    }
}

function AdicionaCurso(){
    if(curso.value==""){
        document.querySelector("#avisoCursos").innerHTML="Preencha o campo!";
    }
    else{
        let novoCurso = document.createElement("li");
        novoCurso.innerHTML = curso.value;
        cursosAdicionados.appendChild(novoCurso)
        curso.value = "";
        document.querySelector("#avisoCursos").innerHTML="";
    }
}

function AdicionaExperiencia(){
    if(experiencia.value==""){
        document.querySelector("#avisoExperiencias").innerHTML="Preencha o campo!";
    }
    else{
        let novaExperiencia = document.createElement("li");
        novaExperiencia.innerHTML = experiencia.value;
        experienciasAdicionadas.appendChild(novaExperiencia)
        experiencia.value = "";
        document.querySelector("#avisoExperiencias").innerHTML="";
    }
}

document.querySelector("#adicionaHabilidade").addEventListener("click", AdicionaHabilidade);
document.querySelector("#adicionaCurso").addEventListener("click", AdicionaCurso);
document.querySelector("#adicionaExperiencia").addEventListener("click", AdicionaExperiencia);
