let modo = "claro";
let body = document.querySelector("body");
let paragrafos = document.querySelectorAll("p");
let labels = document.querySelectorAll("label");
let smalls = document.querySelectorAll("small");
let titulos2 = document.querySelectorAll("h2");
let titulos3 = document.querySelectorAll("h3");
let articles = document.querySelectorAll("article");
let perfis = document.querySelectorAll(".perfil");

function Noturno(){
    body.style.backgroundColor = "#1C1C1C";
    paragrafos.forEach((p) => p.style.color="whitesmoke");
    labels.forEach((label) => label.style.color="whitesmoke");
    smalls.forEach((small) => small.style.color="silver");
    titulos2.forEach((h2) => h2.style.color="whitesmoke");
    titulos3.forEach((h3) => h3.style.color="whitesmoke");
    articles.forEach((article) => article.style.boxShadow="none");
    perfis.forEach((perfil) => perfil.style.background="none");
    modo="noturno";
}

function Claro(){
    body.style.backgroundColor = "whitesmoke";
    paragrafos.forEach((p) => p.style.color="black");
    smalls.forEach((small) => small.style.color="black");
    titulos2.forEach((h2) => h2.style.color="black");
    titulos3.forEach((h3) => h3.style.color="black");
    articles.forEach((article) => article.style.boxShadow="4px 4px 4px silver");
    perfis.forEach((perfil) => perfil.style.background="#eeeeee");
    
    
    
    modo="claro";
}

function AlternarModo(){
    if(modo=="claro"){
        Noturno();
    }else if(modo=="noturno"){
        Claro();
    }
}

document.querySelector(".btnModo").addEventListener("click", AlternarModo);


