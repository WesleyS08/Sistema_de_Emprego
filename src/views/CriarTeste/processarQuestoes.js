// Função para enviar os dados do formulário via AJAX
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
        emailUsuario: emailUsuario
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

    // Enviar dados via AJAX
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
    })
    .catch(error => {
        console.error('Erro ao enviar dados:', error);
    });
}

// Chamar a função para enviar dados quando o formulário for submetido
document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar submissão padrão do formulário
    enviarDados();
});