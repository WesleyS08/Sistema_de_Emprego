<?php
// Processar questionário que o candidato realizou
include "../../services/conexão_com_banco.php";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber as respostas enviadas pelo formulário
    $respostas = $_POST['respostas']; // O 'respostas' é o nome do form enviado

    $pontuacaoTotal = 0; // Aqui vamos armazenar a pontuação do candidato

    // Loop através das respostas enviadas
    foreach ($respostas as $idQuestao => $idAlternativa) {
        // Consultar o banco de dados para obter a alternativa correta
        $sql = "SELECT Correta FROM Tb_Alternativas WHERE Id_Alternativa = $idAlternativa";
        $result = $_con->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verificar se a alternativa escolhida pelo candidato está correta
            if ($row['Correta'] == 1) {
                // Se estiver correta, adicionar um ponto à pontuação total
                $pontuacaoTotal++;
            }
        }
    }

// Recuperar o CPF do candidato usando o email armazenado na tabela Tb_Pessoas
session_start();
$email_candidato = $_SESSION['email_session']; // Supondo que 'email_session' seja o nome da variável de sessão que armazena o email do candidato

// Consultar o banco de dados para obter o CPF correspondente ao email
$sql = "SELECT c.CPF 
        FROM Tb_Pessoas p
        INNER JOIN Tb_Candidato c ON p.Id_Pessoas = c.Tb_Pessoas_Id
        WHERE p.Email = '$email_candidato'";
$result = $_con->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cpf_candidato = $row['CPF'];

    // Atualizar a tabela de resultados com a pontuação total
    $id_questionario = $_GET['id']; // Obtém o ID do questionário da URL
    $data_do_questionario = date('Y-m-d H:i:s'); // Obtém a data e hora atual

    $sql = "INSERT INTO Tb_Resultados (Tb_Questionarios_ID, Tb_Candidato_CPF, Nota, Quantidade_de_Acertos, Data_do_Questionarios) 
            VALUES ('$id_questionario', '$cpf_candidato', '$pontuacaoTotal', '$pontuacaoTotal', '$data_do_questionario')";

    if ($_con->query($sql) === TRUE) {
        // Redirecionar o usuário para alguma página de sucesso ou resultados
        header("Location: ../../views/ResultadosQuestionario/paginaResultado.php?pontuacaoTotal=$pontuacaoTotal");
        exit();
    } else {
        echo "Erro ao atualizar os resultados: " . $_con->error;
    }
} else {
    echo "CPF não encontrado para o email: $email_candidato";
}
}

