let numQuestao = document.getElementsByName("numQuestao");

function ContarQuestoes(numQuestao){
    for(let i=0;i<numQuestao.length;i++){
        numQuestao[i].innerHTML = i+1;
    }
}

ContarQuestoes(numQuestao);