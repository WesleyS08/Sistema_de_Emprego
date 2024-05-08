let modo = "claro";
let body = document.querySelector("body");
let paragrafos = document.querySelectorAll("p");
let smalls = document.querySelectorAll("small");
let titulos2 = document.querySelectorAll("h2");
let titulos3 = document.querySelectorAll("h3");
let labels = document.querySelectorAll("label");
let divs = document.querySelectorAll("div");
let inputTexts = document.querySelectorAll("input[type=text]");
let textAreas = document.querySelectorAll("textarea");
let inputButtons = document.querySelectorAll("input[type=button]");

function Noturno(){
    body.style.backgroundColor = "#1C1C1C";
    paragrafos.forEach((p) => p.style.color="lavender");
    titulos2.forEach((h2) => h2.style.color="lavender");
    titulos3.forEach((h3) => h3.style.color="lavender");
    labels.forEach((label) => label.style.color="lavender");
    divs.forEach((div) => div.style.background="#1C1C1C");
    inputTexts.forEach((inputText) => inputText.style.color="lavender");
    textAreas.forEach((textarea) => textarea.style.color="lavender");
    inputButtons.forEach((inputButton) => inputButton.style.background="lavender");
    inputButtons.forEach((inputButton) => inputButton.style.border="1px solid lavender");
    inputButtons.forEach((inputButton) => inputButton.style.color="#1C1C1C");
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


