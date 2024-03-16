let email = document.querySelector("#email");
let senha = document.querySelector("#senha");
let mensagemEmail = "O email deve ser inserido!";
let mensagemSenha = "A senha deve ser inserida!";

function ExibirAviso(){
    if(email.value==""){
        document.getElementsByName("aviso")[0].innerText = mensagemEmail;
    }
    if(senha.value==""){
        document.getElementsByName("aviso")[1].innerText = mensagemSenha;
    }
}

function LimparAvisoEmail(){
    document.getElementsByName("aviso")[0].innerText = "";
}

function LimparAvisoSenha(){
    document.getElementsByName("aviso")[1].innerText = "";
}

document.querySelector(".btnLogin").addEventListener("click", ExibirAviso);
document.querySelector("#email").addEventListener("click", LimparAvisoEmail)
document.querySelector("#senha").addEventListener("click", LimparAvisoSenha);