function finalizarTeste() {
    // Verifica se todas as perguntas foram respondidas
    var perguntas = document.querySelectorAll('.articleQuestao');
    var todasRespondidas = true;
    var perguntasNaoRespondidas = [];

    perguntas.forEach(function(pergunta, index) {
        var alternativas = pergunta.querySelectorAll('input[type="radio"]');
        var respondida = false;
        for (var i = 0; i < alternativas.length; i++) {
            if (alternativas[i].checked) {
                respondida = true;
                break;
            }
        }
        if (!respondida) {
            todasRespondidas = false;
            perguntasNaoRespondidas.push(index + 1); // Adiciona o número da pergunta não respondida
        }
    });

    if (todasRespondidas) {
        // Confirmação antes de finalizar o teste
        var confirmar = confirm("Deseja finalizar o teste?");
        if (confirmar) {
            // Se confirmar, permitir que o formulário seja enviado
            return true;
        } else {
            // Se cancelar, não permitir que o formulário seja enviado
            return false;
        }
    } else {
        // Exibe uma mensagem de erro
        alert("Por favor, responda todas as perguntas antes de finalizar o teste.");

        // Exibe no console quantas perguntas estão sem resposta e quais são elas
        console.log("Total de perguntas:", perguntas.length);
        console.log("Perguntas não respondidas:", perguntasNaoRespondidas.join(", "));

        // Retorna false para impedir que o formulário seja enviado
        return false;
    }
}
