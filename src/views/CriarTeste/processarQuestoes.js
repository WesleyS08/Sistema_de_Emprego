function enviarDados() {
    // Reunir dados do formulário
    let formData = {
        titulo: document.getElementById('titulo').value,
        nivel: document.getElementById('nivel').value,
        duracao: document.getElementById('duracao').value,
        competencias: document.getElementById('competencias').value,
        area: document.getElementById('area').value,
        data: document.getElementById('data').value,
        questionario: {
            titulo: document.getElementById('titulo').value,
            nivel: document.getElementById('nivel').value,
            duracao: document.getElementById('duracao').value,
            competencias: document.getElementById('competencias').value,
            area: document.getElementById('area').value,
            data: document.getElementById('data').value,
            questoes: []
        },
        emailUsuario: emailUsuario,
        idPessoa: document.getElementById('idPessoa').value // Obtém o valor do campo idPessoa
    };

    // Reunir dados das questões dinâmicas
    let questoes = document.querySelectorAll('.articleQuestao');
    questoes.forEach((questao, index) => {
        let pergunta = questao.querySelector('.divPergunta input').value;
        let alternativas = [];
        let respostas = questao.querySelectorAll('.divAlternativas .divRadio');
        respostas.forEach(resposta => {
            let textoResposta = resposta.querySelector('.inputSimples').value;
            let correta = resposta.querySelector('input[type="radio"]').checked;
            alternativas.push({ resposta: textoResposta, correta: correta });
        });
        formData.questionario.questoes.push({ pergunta: pergunta, alternativas: alternativas });
    });

    console.log(formData);

    // Enviar dados do formulário via AJAX
    fetch('../../services/Testes/processarQuestoes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
    })
        .then(response => response.json())
        .then(data => {
            // Tratar resposta do servidor, se necessário
            console.log(data);
            // Enviar a imagem agora
            enviarImagem(formData.idPessoa);
        })
        .catch(error => {
            console.error('Erro ao enviar dados:', error);
        });
}

// Função para enviar apenas a imagem via AJAX
function enviarImagem(idPessoa) {
    // Cria um novo FormData para a imagem
    let formDataIMG = new FormData();
    let imagem = document.getElementById('inputImagem').files[0];
    formDataIMG.append('inputImagem', imagem);

    formDataIMG.append('idPessoa', idPessoa); // Usando o idPessoa passado como parâmetro

    // Envia a imagem via AJAX
    fetch('salvar_imagem.php', {
        method: 'POST',
        body: formDataIMG,
    })
        .then(response => {
            if (response.ok) {
                return response.json(); // Retorna a resposta do servidor para o próximo then
            } else {
                throw new Error('Erro ao enviar imagem');
            }
        })
        .then(data => {
            // Trata a resposta do servidor
            console.log(data);
            if (data.hasOwnProperty('success')) {
                // Se a chave 'success' existir, exibe uma mensagem de sucesso
                alert(data.success);
            } else if (data.hasOwnProperty('error')) {
                // Se a chave 'error' existir, exibe uma mensagem de erro
                alert(data.error);
            } else {
                // Se a resposta não contiver nem 'success' nem 'error', exibe uma mensagem genérica de erro
                throw new Error('Resposta inválida do servidor');
            }
        })
        .catch(error => {
            // Captura e exibe qualquer erro ocorrido durante o processo de envio da imagem
            console.error('Erro ao enviar imagem:', error);
        });
}

// Chamar a função para enviar dados quando o formulário for submetido
document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault(); // Evitar submissão padrão do formulário
    enviarDados();
});
