/////////////////// FORM CANDIDATO ///////////////////

// Validar Email
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCandidato');
    const inputEmail = form.querySelector('input[name="email"]');
    const avisos = form.querySelectorAll('small[name="aviso"]');

    inputEmail.addEventListener('input', function() {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (regex.test(email)) {
            avisos[1].textContent = ''; // email válido, limpa mensagem de aviso
            inputEmail.classList.remove('error');
        } else {
            avisos[1].textContent = 'Por favor, insira um email válido.';
            inputEmail.classList.add('error');
        }
    });

    form.addEventListener('submit', function(event) {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (!regex.test(email)) {
            event.preventDefault(); // Impede o envio do formulário se o email for inválido
            avisos[1].textContent = 'Por favor, insira um email válido.';
            inputEmail.classList.add('error');
        }
    });
});

// Validar CPF

function TestaCPF(strCPF) {
    var Soma;
    var Resto;
    Soma = 0;
    if (strCPF == "00000000000") return false;

    for (i = 1; i <= 9; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10))) return false;

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11))) return false;
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCandidato');
    const inputCPF = form.querySelector('input[name="cpf"]');
    const avisoCPF = form.querySelector('small[name="aviso-cpf"]');

    inputCPF.addEventListener('input', function() {
        const cpf = inputCPF.value.trim();
        if (cpf.length === 11) {
            if (!TestaCPF(cpf)) {
                avisoCPF.textContent = 'CPF inválido';
            } else {
                avisoCPF.textContent = '';
            }
        } else {
            avisoCPF.textContent = 'O CPF deve conter 11 dígitos';
        }
    });

    form.addEventListener('submit', function(event) {
        const cpf = inputCPF.value.trim();
        if (!TestaCPF(cpf)) {
            avisoCPF.textContent = 'CPF inválido';
            event.preventDefault(); // Se o CPF for inválido, ele irá impedir o envio do formulário.
        }
    });
});

// Checar se senhas inseridas são iguais, maiores que 6 caracteres e se possuem caracteres especiais para torná-la complexa

// Variável global para rastrear se as senhas são iguais
let senhasIguaisCandidato = true;

function senhasSaoIguais(senha, contrasenha) {
    // Verificar se as senhas são diferentes
    senhasIguaisCandidato = contrasenha === senha;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCandidato');
    const inputSenha = form.querySelector('input[name="senha"]');
    const inputConfirmarSenha = form.querySelector('input[name="confirmaSenha"]');
    const avisoSenha = form.querySelector('small[name="aviso-senha"]');

    inputConfirmarSenha.addEventListener('input', function(event) { 
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();
       
        senhasSaoIguais(senha, confirmaSenha);

        if (!senhasIguaisCandidato) { 
            avisoSenha.textContent = 'As senhas não são iguais';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se as senhas forem iguais
        }
    });

    form.addEventListener('submit', function(event) {
        if (!senhasIguaisCandidato) {
            event.preventDefault(); // Impedir o envio do formulário se as senhas não forem iguais
        }
    });

    inputSenha.addEventListener('input', function(event) {
        const senha = inputSenha.value.trim();
        const senhaLength = senha.length;

        // console.log("Senha Length: ", senhaLength);

        // Verificar se a senha tem pelo menos 6 caracteres
        if (senhaLength < 6) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres';
            return;
        }

        // Verificar se a senha é complexa usando regex
        const regexComplexa = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/;
        if (!regexComplexa.test(senha)) {
            avisoSenha.textContent = 'A senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial';
            return;
        }

        // Limpar aviso se a senha atender aos critérios
        avisoSenha.textContent = '';
    });
});

/////////////////// FORM EMPRESA ///////////////////

// Validar Email
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formRecrutador');
    const inputEmail = form.querySelector('input[name="email"]');
    const avisos = form.querySelectorAll('small[name="aviso"]');

    inputEmail.addEventListener('input', function() {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (regex.test(email)) {
            avisos[1].textContent = ''; // email válido, limpa mensagem de aviso
            inputEmail.classList.remove('error');
        } else {
            avisos[1].textContent = 'Por favor, insira um email válido.';
            inputEmail.classList.add('error');
        }
    });

    form.addEventListener('submit', function(event) {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (!regex.test(email)) {
            event.preventDefault(); // Impede o envio do formulário se o email for inválido
            avisos[1].textContent = 'Por favor, insira um email válido.';
            inputEmail.classList.add('error');
        }
    });
});

// Validar CNPJ
function validarCNPJ(cnpj) {
 
    cnpj = cnpj.replace(/[^\d]+/g,'');
 
    if(cnpj == '') return false;
     
    if (cnpj.length != 14)
        return false;
 
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" || 
        cnpj == "11111111111111" || 
        cnpj == "22222222222222" || 
        cnpj == "33333333333333" || 
        cnpj == "44444444444444" || 
        cnpj == "55555555555555" || 
        cnpj == "66666666666666" || 
        cnpj == "77777777777777" || 
        cnpj == "88888888888888" || 
        cnpj == "99999999999999")
        return false;
         
    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;
           
    return true;    
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formRecrutador');
    const inputCNPJ = form.querySelector('input[name="cnpj"]');
    const avisoCNPJ = form.querySelector('small[name="aviso-cnpj"]');

    inputCNPJ.addEventListener('input', function() {
        const CNPJ = inputCNPJ.value.trim();
        if (CNPJ.length === 14) {
            if (!validarCNPJ(CNPJ)) {
                avisoCNPJ.textContent = 'CNPJ inválido';
            } else {
                avisoCNPJ.textContent = '';
            }
        } else {
            avisoCNPJ.textContent = 'O CNPJ deve conter 14 dígitos';
        }
    });

    form.addEventListener('submit', function(event) {
        const CNPJ = inputCNPJ.value.trim(); 
        if (!validarCNPJ(CNPJ)) {
            avisoCNPJ.textContent = 'CNPJ Inválido';
            event.preventDefault(); 
        }
    });
});

// Variável global para rastrear se as senhas são iguais, se possuem mais de 6 caracteres e se possuem caracteres especiais
let senhasIguaisRecrutador = true;

function senhasSaoIguaisRecrutador(senha, contrasenha) {
    // Verificar se as senhas são diferentes
    senhasIguaisRecrutador = contrasenha === senha;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formRecrutador');
    const inputSenha = form.querySelector('input[name="senha"]');
    const inputConfirmarSenha = form.querySelector('input[name="confirmaSenha"]');
    const avisoSenha = form.querySelector('small[name="aviso-senha"]');

    inputConfirmarSenha.addEventListener('input', function(event) { 
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();
       
        senhasSaoIguaisRecrutador(senha, confirmaSenha);

        if (!senhasIguaisRecrutador) { 
            avisoSenha.textContent = 'As senhas não são iguais';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se as senhas forem iguais
        }
    });

    inputSenha.addEventListener('input', function(event) {
        const senha = inputSenha.value.trim();
        const senhaLength = senha.length;

        // console.log("Senha Length: ", senhaLength);

        // Verificar se a senha tem pelo menos 6 caracteres
        if (senhaLength < 6) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres';
            return;
        }

        // Verificar se a senha é complexa usando regex
        const regexComplexa = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/;
        if (!regexComplexa.test(senha)) {
            avisoSenha.textContent = 'A senha deve conter pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial';
            return;
        }

        // Limpar aviso se a senha atender aos critérios
        avisoSenha.textContent = '';
    });

    form.addEventListener('submit', function(event) {
        if (!senhasIguaisRecrutador) {
            event.preventDefault(); // Impedir o envio do formulário se as senhas não forem iguais
        }
    });
});
