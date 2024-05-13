let modo = "claro";

let body = document.querySelector("body");
let paragrafos = document.querySelectorAll("p");
let sinopses = document.querySelectorAll(".sinopse");
let dataVagas = document.querySelectorAll(".dataVaga");
let tipoVagas = document.querySelectorAll(".tipoVaga");
let nomeTestes = document.querySelectorAll(".nomeTeste");
let labels = document.querySelectorAll("label");
let smalls = document.querySelectorAll("small");
let titulos2 = document.querySelectorAll("h2");
let titulos3 = document.querySelectorAll("h3");
let articles = document.querySelectorAll("article");
let perfis = document.querySelectorAll(".perfil");
let verMaisBtns = document.querySelectorAll(".verMais");
let labellines = document.querySelectorAll(".labelLine");
let textAreaLabellines = document.querySelectorAll(".textArealabelLine");

let slidesMysql = document.querySelectorAll(".slideMysql");
let slidesPhp = document.querySelectorAll(".slidePhp");
let slidesFirebase = document.querySelectorAll(".slideFirebase");
let logosFatec = document.querySelectorAll(".logoFatec");
let slidesJs = document.querySelectorAll(".slideJs");
let slidesHtml = document.querySelectorAll(".slideHtml");
let slidesCss = document.querySelectorAll(".slideCss");

let inputs = document.querySelectorAll("input[type='text']");
let inputsNumber = document.querySelectorAll("input[type='number']");
let selects = document.querySelectorAll("select");
let texteareas = document.querySelectorAll("textarea");

let perguntas = document.querySelectorAll(".pergunta");
let alternativas = document.querySelectorAll(".alternativa");
let numQuestoes = document.querySelectorAll(".numQuestao");

let divTituloDigitaveis = document.querySelectorAll("#divTituloDigitavelTodos");
let piscas = document.querySelectorAll(".pisca");

let infos = document.querySelectorAll(".infos");
let divIconeENomes = document.querySelectorAll(".divIconeENome");
let iconesVaga = document.querySelectorAll(".iconeVaga");
let divsAcessos = document.querySelectorAll(".divAcessos");
let nomeAreas = document.querySelectorAll(".nomeArea");
let legends = document.querySelectorAll("legend");

let backImgs = document.querySelectorAll(".backImg");
let nomeFiltros = document.querySelectorAll(".nomeFiltro");

var styleElem = document.head.appendChild(document.createElement("style"));

function Noturno(){
    
    try{
        body.style.backgroundColor = "#1C1C1C";
        paragrafos.forEach((p) => p.style.color="silver");        
        nomeTestes.forEach((nomeTeste) => nomeTeste.style.color="whitesmoke");
        smalls.forEach((small) => small.style.color="silver");
        titulos2.forEach((h2) => h2.style.color="whitesmoke");
        titulos3.forEach((h3) => h3.style.color="whitesmoke");    
        articles.forEach((article) => article.style.border="none");
        articles.forEach((article) => article.style.boxShadow="0px 0px 4px silver");
        perfis.forEach((perfil) => perfil.style.background="none");    
        sinopses.forEach((sinopse) => sinopse.style.color="whitesmoke");   
        dataVagas.forEach((dataVaga) => dataVaga.style.color="silver");    
        tipoVagas.forEach((tipoVaga) => tipoVaga.style.color="silver");            
        labellines.forEach((labelLine) => labelLine.style.backgroundColor="#1C1C1C");            
        textAreaLabellines.forEach((textAreaLabelline) => textAreaLabelline.style.backgroundColor="#1C1C1C");  

        styleElem.innerHTML = ".carrosselInfinito:before, .carrosselInfinito:after{background: linear-gradient(to right, rgba(28,28,28,1) 0%, rgba(28,28,28,0) 100%);}";    
        slidesMysql.forEach((slideMysql) => slideMysql.src = "../../assets/images/logos_parceiros/mysqlWhite.svg");
        slidesPhp.forEach((slidePhp) => slidePhp.src = "../../assets/images/logos_parceiros/phpWhite.svg");
        slidesFirebase.forEach((slideFirebase) => slideFirebase.src = "../../assets/images/logos_parceiros/firebaseWhite.svg");
        logosFatec.forEach((logoFatec) => logoFatec.src = "../../assets/images/logos_parceiros/fatecWhite.png");
        slidesJs.forEach((slideJs) => slideJs.src = "../../assets/images/logos_parceiros/javascriptWhite.svg");
        slidesHtml.forEach((slideHtml) => slideHtml.src = "../../assets/images/logos_parceiros/htmlWhite.svg");
        slidesCss.forEach((slideCss) => slideCss.src = "../../assets/images/logos_parceiros/cssWhite.svg");
        
        inputs.forEach((input) => input.style.color="white");        
        inputsNumber.forEach((inputNumber) => inputNumber.style.color="white");        
        selects.forEach((select) => select.style.color="whitesmoke");       
        texteareas.forEach((textarea) => textarea.style.color="whitesmoke");
        
        perguntas.forEach((pergunta) => pergunta.style.color="whitesmoke");            
        alternativas.forEach((alternativa) => alternativa.style.color="whitesmoke");        
        numQuestoes.forEach((numQuestao) => numQuestao.style.color="whitesmoke");           

        infos.forEach((info) => info.style.color="whitesmoke");        
        divIconeENomes.forEach((divIconeENome) => divIconeENome.style.color="whitesmoke");        
        iconesVaga.forEach((iconeVaga) => iconeVaga.colors="primary:#f5f5f5,secondary:#c76f16");   
        divsAcessos.forEach((divAcesso) => divAcesso.children[0].src="../../assets/images/icones_diversos/peopleWhite.svg")
        nomeAreas.forEach((nomeArea) => nomeArea.style.color="whitesmoke");
        legends.forEach((legend) => legend.style.backgroundColor="#1C1C1C");

        verMaisBtns.forEach((verMais) => verMais.style.color="black");
        verMaisBtns.forEach((verMais) => verMais.style.border="none");        
        verMaisBtns.forEach((verMais) => verMais.style.backgroundColor="whitesmoke");

        backImgs.forEach((backImg) => backImg.src="../../assets/images/icones_diversos/backWhite.svg");        
        nomeFiltros.forEach((nomeFiltro) => nomeFiltro.style.color="whitesmoke");        
        
        divTituloDigitaveis.forEach((divTituloDigitavelTodos) => divTituloDigitavelTodos.style.color="whitesmoke");
        piscas.forEach((pisca) => pisca.style.backgroundColor="whitesmoke");

        const elementosNoturnos = document.querySelectorAll('.noturno');
        elementosNoturnos.forEach(element => {
            element.style.color = 'whitesmoke';
        });
    }
    finally{       
        modo="noturno";
    }
}

function Claro(){
    try{
        body.style.backgroundColor = "whitesmoke";   
        paragrafos.forEach((p) => p.style.color="black");
        smalls.forEach((small) => small.style.color="black");
        titulos2.forEach((h2) => h2.style.color="black");
        titulos3.forEach((h3) => h3.style.color="black");    
        articles.forEach((article) => article.style.border="1px solid silver");
        articles.forEach((article) => article.style.boxShadow="4px 4px 4px silver");
        perfis.forEach((perfil) => perfil.style.background="#eeeeee");    
        dataVagas.forEach((dataVaga) => dataVaga.style.color="black");    
        tipoVagas.forEach((tipoVaga) => tipoVaga.style.color="black");
        sinopses.forEach((sinopse) => sinopse.style.color="whitesmoke");
        
        labellines.forEach((labelLine) => labelLine.style.backgroundColor="#f5f5f5");  
        textAreaLabellines.forEach((textAreaLabelline) => textAreaLabelline.style.backgroundColor="#f5f5f5");  

        styleElem.innerHTML = ".carrosselInfinito:before, .carrosselInfinito:after{background: linear-gradient(to right, rgba(245,245,245,1) 0%, rgba(245,245,245,0) 100%);}";    
        slidesMysql.forEach((slideMysql) => slideMysql.src = "../../assets/images/logos_parceiros/mysql.svg");
        slidesPhp.forEach((slidePhp) => slidePhp.src = "../../assets/images/logos_parceiros/php.svg");
        slidesFirebase.forEach((slideFirebase) => slideFirebase.src = "../../assets/images/logos_parceiros/firebase.svg");
        logosFatec.forEach((logoFatec) => logoFatec.src = "../../assets/images/logos_parceiros/fatec.png");
        slidesJs.forEach((slideJs) => slideJs.src = "../../assets/images/logos_parceiros/javascript.svg");
        slidesHtml.forEach((slideHtml) => slideHtml.src = "../../assets/images/logos_parceiros/html.svg");
        slidesCss.forEach((slideCss) => slideCss.src = "../../assets/images/logos_parceiros/css.svg");
        
        inputs.forEach((input) => input.style.color="black");   
        inputsNumber.forEach((inputNumber) => inputNumber.style.color="black");        
        selects.forEach((select) => select.style.color="black");
        texteareas.forEach((textarea) => textarea.style.color="black");
        
        perguntas.forEach((pergunta) => pergunta.style.color="black");            
        alternativas.forEach((alternativa) => alternativa.style.color="black");          
        numQuestoes.forEach((numQuestao) => numQuestao.style.color="black");    
        
        infos.forEach((info) => info.style.color="black");
        divIconeENomes.forEach((divIconeENome) => divIconeENome.style.color="black");
        iconesVaga.forEach((iconeVaga) => iconeVaga.colors="primary:#242424,secondary:#c74b16");
        divsAcessos.forEach((divAcesso) => divAcesso.children[0].src="../../assets/images/icones_diversos/people.svg")
        nomeAreas.forEach((nomeArea) => nomeArea.style.color="black");
        legends.forEach((legend) => legend.style.backgroundColor="whitesmoke");

        verMaisBtns.forEach((verMais) => verMais.style="initial");
        
        backImgs.forEach((backImg) => backImg.src="../../assets/images/icones_diversos/back.svg");        
        nomeFiltros.forEach((nomeFiltro) => nomeFiltro.style.color="black");

        divTituloDigitaveis.forEach((divTituloDigitavelTodos) => divTituloDigitavelTodos.style.color="black");
        piscas.forEach((pisca) => pisca.style.backgroundColor="black");

        const elementosNoturnos = document.querySelectorAll('.noturno');
        elementosNoturnos.forEach(element => {
            element.style.color = 'black';
        });
    }
    finally{
        modo="claro";
    }
}

// Passamos o tema Salvo no banco de dados, não mexam nisso 
if (typeof temaDoBancoDeDados !== 'undefined') {
    if (temaDoBancoDeDados === "noturno") {
        Noturno(); 
    } else {
        Claro(); 
    }
} else {
    Claro(); // Ou Noturno, conforme necessário
}

function AlternarModo() {
    if (modo === "claro") {
        Noturno();
        //localStorage.setItem('modoNoturnoAtivo', true);

        salvarTemaNoBancoDeDados("noturno", function() {
            // Callback chamado após o tema ser salvo com sucesso
            // Atualizar o modo após o tema ser salvo
            modo = "noturno";
        });
    } else if (modo === "noturno") {
        Claro();        
        //localStorage.setItem('modoNoturnoAtivo', false);
        salvarTemaNoBancoDeDados("claro", function() {
            // Callback chamado após o tema ser salvo com sucesso
            // Atualizar o modo após o tema ser salvo
            modo = "claro";
        });
    }
}

//function carregarPreferenciaModoNoturno() {
//    const modoNoturnoAtivo = localStorage.getItem('modoNoturnoAtivo') === 'true';
//    if (modoNoturnoAtivo) {
//      Noturno(); // Função para ativar o modo noturno
//    }
//  }

//carregarPreferenciaModoNoturno()
document.querySelector(".btnModo").addEventListener("click", AlternarModo);
