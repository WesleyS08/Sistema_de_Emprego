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
let verMais = document.querySelector(".verMais");

let slidesMysql = document.querySelectorAll(".slideMysql");
let slidesPhp = document.querySelectorAll(".slidePhp");
let slidesFirebase = document.querySelectorAll(".slideFirebase");
let logosFatec = document.querySelectorAll(".logoFatec");
let slidesJs = document.querySelectorAll(".slideJs");
let slidesHtml = document.querySelectorAll(".slideHtml");
let slidesCss = document.querySelectorAll(".slideCss");

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

        styleElem.innerHTML = ".carrosselInfinito:before, .carrosselInfinito:after{background: linear-gradient(to right, rgba(28,28,28,1) 0%, rgba(28,28,28,0) 100%);}";    
        slidesMysql.forEach((slideMysql) => slideMysql.src = "../../assets/images/logos_parceiros/mysqlWhite.svg");
        slidesPhp.forEach((slidePhp) => slidePhp.src = "../../assets/images/logos_parceiros/phpWhite.svg");
        slidesFirebase.forEach((slideFirebase) => slideFirebase.src = "../../assets/images/logos_parceiros/firebaseWhite.svg");
        logosFatec.forEach((logoFatec) => logoFatec.src = "../../assets/images/logos_parceiros/fatecWhite.png");
        slidesJs.forEach((slideJs) => slideJs.src = "../../assets/images/logos_parceiros/javascriptWhite.svg");
        slidesHtml.forEach((slideHtml) => slideHtml.src = "../../assets/images/logos_parceiros/htmlWhite.svg");
        slidesCss.forEach((slideCss) => slideCss.src = "../../assets/images/logos_parceiros/cssWhite.svg");
        
        verMais.style.color = "black";
        verMais.style.backgroundColor = "whitesmoke";    
        verMais.style.border = "1px solid whitesmoke ";
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

        styleElem.innerHTML = ".carrosselInfinito:before, .carrosselInfinito:after{background: linear-gradient(to right, rgba(245,245,245,1) 0%, rgba(245,245,245,0) 100%);}";    
        slidesMysql.forEach((slideMysql) => slideMysql.src = "../../assets/images/logos_parceiros/mysql.svg");
        slidesPhp.forEach((slidePhp) => slidePhp.src = "../../assets/images/logos_parceiros/php.svg");
        slidesFirebase.forEach((slideFirebase) => slideFirebase.src = "../../assets/images/logos_parceiros/firebase.svg");
        logosFatec.forEach((logoFatec) => logoFatec.src = "../../assets/images/logos_parceiros/fatec.png");
        slidesJs.forEach((slideJs) => slideJs.src = "../../assets/images/logos_parceiros/javascript.svg");
        slidesHtml.forEach((slideHtml) => slideHtml.src = "../../assets/images/logos_parceiros/html.svg");
        slidesCss.forEach((slideCss) => slideCss.src = "../../assets/images/logos_parceiros/css.svg");
    
        verMais.style = "initial";
    }
    finally{
        modo="claro";
    }
}

function AlternarModo(){
    if(modo=="claro"){
        Noturno();
    }else if(modo=="noturno"){
        Claro();
    }
}

document.querySelector(".btnModo").addEventListener("click", AlternarModo);

