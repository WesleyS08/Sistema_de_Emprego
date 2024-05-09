/////////////////// FORM CANDIDATO ///////////////////

// Validar Email
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formCandidato');
    const inputEmail = form.querySelector('input[name="email"]'); // Deve ser "email"
    const avisoEmail = form.querySelector('small[name="avisoCandidato"]'); // Correção para pequeno aviso

    inputEmail.addEventListener('input', function () {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (regex.test(email)) {
            // Verifica no banco de dados se o email já está registrado
            $.ajax({
                url: 'verificar_email.php', // Altere para a URL correta
                method: 'POST',
                data: { email: email },
                success: function (response) {
                    let resposta;

                    try {
                        resposta = typeof response === 'string' ? JSON.parse(response) : response;

                        if (resposta && resposta.hasOwnProperty('existe')) {
                            if (resposta.existe) {
                                avisoEmail.textContent = "Email já registrado.";
                                avisoEmail.style.color = "red"; 
                                inputEmail.classList.add('error'); // Adiciona classe de erro
                            } else {
                                avisoEmail.textContent = "Email disponível.";
                                avisoEmail.style.color = "green"; 
                                inputEmail.classList.remove('error'); // Remove classe de erro
                            }
                        } else {
                            avisoEmail.textContent = "Resposta inválida do servidor.";
                            avisoEmail.style.color = "red"; 
                            inputEmail.classList.add('error'); 
                        }
                    } catch (e) {
                        console.error('Erro ao processar resposta do servidor:', e);
                        avisoEmail.textContent = "Erro ao processar resposta do servidor.";
                        avisoEmail.style.color = "red"; 
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Erro ao verificar email:', errorThrown);
                    avisoEmail.textContent = "Erro ao verificar email.";
                    avisoEmail.style.color = "red"; 
                    inputEmail.classList.add('error'); 
                }
            });

            // Limpa aviso de erro se o email for válido e disponível
            if (!inputEmail.classList.contains('error')) {
                avisoEmail.textContent = ''; 
            }
        } else {
            avisoEmail.textContent = 'Por favor, insira um email válido.';
            avisoEmail.style.color = "red"; 
            inputEmail.classList.add('error'); 
        }
    });

    form.addEventListener('submit', function (event) {
        const email = inputEmail.value.trim();
        const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

        if (!regex.test(email)) {
            event.preventDefault(); // Impede o envio se o email for inválido
            avisoEmail.textContent = 'Por favor, insira um email válido.';
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formCandidato');
    const inputCPF = form.querySelector('input[name="cpf"]');
    const avisoCPF = form.querySelector('small[name="aviso-cpf"]');

    inputCPF.addEventListener('input', function () {
        const cpf = inputCPF.value.trim();
        if (cpf.length === 11) {
            if (TestaCPF(cpf)) { // Corrigido para verificar validade correta
                console.log('CPF válido. Realizando verificação no banco de dados...');
                avisoCPF.textContent = "CPF válido. Realizando verificação no banco de dados...";
                avisoCPF.style.color = "green"; // Corrigido para ajuste visual
                
                // Realizar a verificação no banco de dados
                $.ajax({
                    url: 'verificar_cpf.php', 
                    method: 'POST',
                    data: { cpf: cpf }, 
                    success: function (response) {
                        try {
                            // Verifica se a resposta é do tipo string e converte para JSON
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response && response.hasOwnProperty('existe')) {
                                if (response.existe) {
                                    console.log('CPF já registrado.');
                                    avisoCPF.textContent = "CPF já registrado.";
                                    avisoCPF.style.color = "red"; 
                                } else {
                                    console.log('CPF disponível.');
                                    avisoCPF.textContent = "CPF disponível.";
                                    avisoCPF.style.color = "green";
                                }
                            } else {
                                console.log('Resposta inválida do servidor.');
                                avisoCPF.textContent = "Resposta inválida do servidor.";
                                avisoCPF.style.color = "red";
                            }
                        } catch (e) {
                            console.error('Erro ao processar resposta do servidor:', e);
                            avisoCPF.textContent = "Erro ao processar resposta do servidor.";
                            avisoCPF.style.color = "red"; 
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Erro ao verificar CPF:', errorThrown);
                        avisoCPF.textContent = "Erro ao verificar CPF.";
                        avisoCPF.style.color = "red"; 
                    }
                });
            } else {
                avisoCPF.textContent = 'CPF inválido';
                avisoCPF.style.color = "red"; 
            }
        } else {
            avisoCPF.textContent = 'O CPF deve conter 11 dígitos';
            avisoCPF.style.color = "red"; 
        }
    });

    form.addEventListener('submit', function (event) {
        const cpf = inputCPF.value.trim();
        if (!TestaCPF(cpf)) {
            avisoCPF.textContent = 'CPF inválido';
            avisoCPF.style.color = "red"; 
            event.preventDefault(); 
        }
    });
});

// Checar se senhas inseridas são iguais, maiores que 6 caracteres e se possuem caracteres especiais para torná-la complexa
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formCandidato');
    const inputSenha = form.querySelector('input[name="senha"]');
    const inputConfirmarSenha = form.querySelector('input[name="confirmaSenha"]');
    const avisoSenha = form.querySelector('small[name="aviso-senha"]');

    // Função para validar se as senhas são iguais
    function senhasSaoIguais(senha, confirmaSenha) {
        return senha === confirmaSenha;
    }

    // Função para validar a complexidade da senha
    function verificaSenhaComplexa(senha) {
        // Verificar se a senha tem pelo menos 6 caracteres
        if (senha.length < 6) {
            return false;
        }

        // Verificar se a senha contém pelo menos uma letra minúscula, uma letra maiúscula, um número e um caractere especial
        const regexComplexa = /(?=^.{8,}$)((?=.*\d)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
        return regexComplexa.test(senha);
    }

    // Adicionar evento de input para verificar se as senhas são iguais
    inputConfirmarSenha.addEventListener('input', function (event) {
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();

        if (!senhasSaoIguais(senha, confirmaSenha)) {
            avisoSenha.textContent = 'As senhas não são iguais';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se as senhas forem iguais
        }
    });

    // Adicionar evento de input para validar a complexidade da senha
    inputSenha.addEventListener('input', function (event) {
        const senha = inputSenha.value.trim();

        if (!verificaSenhaComplexa(senha)) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres, uma letra minúscula, uma letra maiúscula, um número e um caractere especial';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se a senha for complexa
        }
    });

    // Adicionar evento de submit para validar as senhas antes do envio do formulário
    form.addEventListener('submit', function (event) {
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();

        // Validar se as senhas são iguais
        if (!senhasSaoIguais(senha, confirmaSenha)) {
            avisoSenha.textContent = 'As senhas não são iguais';
            event.preventDefault(); // Impedir o envio do formulário se as senhas não forem iguais
            return;
        }

        // Validar se a senha é complexa
        if (!verificaSenhaComplexa(senha)) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres, uma letra minúscula, uma letra maiúscula, um número e um caractere especial';
            event.preventDefault(); // Impedir o envio do formulário se a senha não for complexa
            return;
        }

        // Se todas as validações passarem, permitir o envio do formulário
    });
});

/////////////////// FORM EMPRESA ///////////////////

// Validar Email
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRecrutador');
    const inputEmail = document.getElementById('emailRecrutador'); // Usando ID para garantir a referência correta
    const avisoEmail = form.querySelector('small[name="avisoEmailRecrutador"]'); // Para exibir mensagens de erro/aviso


    const regex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/; // Regex para validar emails

    inputEmail.addEventListener('input', function () {
        const email = inputEmail.value.trim();

        if (regex.test(email)) {
            avisoEmail.textContent = ''; // Limpa mensagem de erro se o email for válido
            inputEmail.classList.remove('error');

            // Verifica se o email já está registrado no banco de dados
            $.ajax({
                url: 'verificar_email_recrutador.php', // Endpoint para verificar unicidade do email
                method: 'POST',
                data: { email: email },
                success: function (response) {
                    let resposta;
                    try {
                        resposta = typeof response === 'string' ? JSON.parse(response) : response;

                        if (resposta && resposta.hasOwnProperty('existe')) {
                            if (resposta.existe) {
                                avisoEmail.textContent = "Email já registrado.";
                                avisoEmail.style.color = "red"; 
                                inputEmail.classList.add('error'); 
                            } else {
                                avisoEmail.textContent = "Email disponível.";
                                avisoEmail.style.color = "green"; 
                                inputEmail.classList.remove('error'); 
                            }
                        } else {
                            avisoEmail.textContent = "Resposta inválida do servidor.";
                            avisoEmail.style.color = "red"; 
                            inputEmail.classList.add('error'); 
                        }
                    } catch (e) {
                        console.error('Erro ao processar resposta do servidor:', e);
                        avisoEmail.textContent = "Erro ao processar resposta do servidor.";
                        avisoEmail.style.color = "red"; 
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Erro ao verificar email:', errorThrown);
                    avisoEmail.textContent = "Erro ao verificar email.";
                    avisoEmail.style.color = "red"; 
                    inputEmail.classList.add('error'); 
                }
            });

        } else {
            avisoEmail.textContent = 'Por favor, insira um email válido.';
            avisoEmail.style.color = "red"; 
            inputEmail.classList.add('error'); 
        }
    });

    form.addEventListener('submit', function (event) {
        const email = inputEmail.value.trim();

        if (!regex.test(email)) {
            event.preventDefault(); // Impede o envio do formulário se o email for inválido
            avisoEmail.textContent = 'Por favor, insira um email válido.';
            avisoEmail.style.color = "red"; 
            inputEmail.classList.add('error'); 
        }
    });
});
// Validar CNPJ
function validarCNPJ(cnpj) {

    cnpj = cnpj.replace(/[^\d]+/g, '');

    if (cnpj == '') return false;

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
    numeros = cnpj.substring(0, tamanho);
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
    numeros = cnpj.substring(0, tamanho);
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
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRecrutador');
    const inputCNPJ = form.querySelector('input[name="cnpj"]');
    const avisoCNPJ = form.querySelector('small[name="aviso-cnpj"]');

    inputCNPJ.addEventListener('input', function () {
        const CNPJ = inputCNPJ.value.trim();
        if (CNPJ.length === 14) {
            avisoCNPJ.textContent = validarCNPJ(CNPJ) ? '' : 'CNPJ inválido';
            if (validarCNPJ(CNPJ)) {
                console.log('CNPJ válido. Realizando verificação no banco de dados...');
                avisoCNPJ.textContent = "CNPJ válido. Realizando verificação no banco de dados..." 
                // Realizar a verificação no banco de dados
                $.ajax({
                    url: 'verificar_cnpj.php', // Substitua pelo seu URL de verificação
                    method: 'POST',
                    data: { cnpj: CNPJ },
                    success: function (response) {
                        console.log('Resposta do servidor:', response);
                        // Verifica se a resposta é do tipo string
                        if (typeof response === 'string') {
                            // Converte a resposta para objeto JSON
                            response = JSON.parse(response);
                        }

                        if (response && response.hasOwnProperty('existe')) {
                            // Verifica se 'existe' é verdadeiro
                            if (response.existe) {
                                console.log('CNPJ já registrado.');
                                avisoCNPJ.textContent = "CNPJ já registrado.";
                            } else {
                                console.log('CNPJ disponível.');
                                avisoCNPJ.textContent = ("CNPJ disponível.")
                                avisoCNPJ.style.color = "green";
                            }
                        } else {
                            console.log('Resposta inválida do servidor.');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Erro ao verificar CNPJ:', errorThrown);
                    }
                });
            }
        } else {
            avisoCNPJ.textContent = 'O CNPJ deve conter 14 dígitos';
            avisoCNPJ.style.color = "red";
        }
    });

    form.addEventListener('submit', function (event) {
        const CNPJ = inputCNPJ.value.trim();
        if (CNPJ.length === 14 && !validarCNPJ(CNPJ)) {
            avisoCNPJ.textContent = 'CNPJ Inválido';
            event.preventDefault();
            return;
        }
    });
});


// Variável global para rastrear se as senhas são iguais, se possuem mais de 6 caracteres e se possuem caracteres especiais
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRecrutador');
    const inputSenha = form.querySelector('input[name="senha"]');
    const inputConfirmarSenha = form.querySelector('input[name="confirmaSenha"]');
    const avisoSenha = form.querySelector('small[name="aviso-senha"]');
    let senhasIguaisRecrutador = true;

    // Função para validar se as senhas são iguais
    function senhasSaoIguais(senha, confirmaSenha) {
        return senha === confirmaSenha;
    }

    // Função para validar a complexidade da senha
    function verificaSenhaComplexa(senha) {
        // Verificar se a senha tem pelo menos 6 caracteres
        if (senha.length < 6) {
            return false;
        }

        // Verificar se a senha contém pelo menos uma letra minúscula, uma letra maiúscula, um número e um caractere especial
        const regexComplexa = /(?=^.{8,}$)((?=.*\d)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
        return regexComplexa.test(senha);
    }

    // Adicionar evento de input para verificar se as senhas são iguais
    inputConfirmarSenha.addEventListener('input', function (event) {
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();

        senhasIguaisRecrutador = senhasSaoIguais(senha, confirmaSenha);

        if (!senhasIguaisRecrutador) {
            avisoSenha.textContent = 'As senhas não são iguais';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se as senhas forem iguais
        }
    });

    // Adicionar evento de input para validar a complexidade da senha
    inputSenha.addEventListener('input', function (event) {
        const senha = inputSenha.value.trim();

        // Verificar se a senha atende aos critérios de complexidade
        if (!verificaSenhaComplexa(senha)) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres, uma letra minúscula, uma letra maiúscula, um número e um caractere especial';
        } else {
            avisoSenha.textContent = ''; // Limpar aviso se a senha atender aos critérios
        }
    });

    // Adicionar evento de submit para validar as senhas antes do envio do formulário
    form.addEventListener('submit', function (event) {
        const senha = inputSenha.value.trim();
        const confirmaSenha = inputConfirmarSenha.value.trim();

        // Validar se as senhas são iguais
        if (!senhasIguaisRecrutador) {
            avisoSenha.textContent = 'As senhas não são iguais';
            event.preventDefault(); // Impedir o envio do formulário se as senhas não forem iguais
            return;
        }

        // Validar se a senha é complexa
        if (!verificaSenhaComplexa(senha)) {
            avisoSenha.textContent = 'A senha deve ter pelo menos 6 caracteres, uma letra minúscula, uma letra maiúscula, um número e um caractere especial';
            event.preventDefault(); // Impedir o envio do formulário se a senha não for complexa
            return;
        }

        // Se todas as validações passarem, permitir o envio do formulário
    });
});