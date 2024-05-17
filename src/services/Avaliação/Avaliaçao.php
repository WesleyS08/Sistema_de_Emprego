<?php
include "../conexão_com_banco.php";
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['email_session']) && !isset($_SESSION['google_session'])) {
    echo "Usuário não autenticado.";
    exit;
}

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $idPessoa = isset($_POST['idPessoa']) ? $_POST['idPessoa'] : '';
    $nota = isset($_POST['estrelas']) ? $_POST['estrelas'] : '';
    $texto = isset($_POST['opiniao']) ? $_POST['opiniao'] : '';

    // Validar os dados recebidos
    if (empty($email) || empty($idPessoa) || empty($nota) || empty($texto)) {
        echo "Todos os campos são obrigatórios.";
        exit;
    }

    // Inserir os dados na tabela de avaliações
    $sql = "INSERT INTO Tb_Avaliacoes (Tb_Pessoas_Id, Nota, Texto, Data_Avaliacao) VALUES (?, ?, ?, CURRENT_TIMESTAMP())";
    $stmt = $_con->prepare($sql);
    
    if ($stmt) {
        // Vincular os parâmetros à consulta
        $stmt->bind_param("iis", $idPessoa, $nota, $texto);
        
        // Executar a declaração
        if ($stmt->execute()) {
            echo "Avaliação salva com sucesso.";
        } else {
            echo "Erro ao salvar a avaliação.";
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>
