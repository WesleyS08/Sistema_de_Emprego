<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['email_session'])) {
    http_response_code(401); // Resposta de erro se não autenticado
    echo json_encode(array('success' => false, 'message' => 'Usuário não autenticado'));
    exit;
}

$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : 0; // Obter o ID da pessoa do POST
$novoTema = isset($_POST['tema']) ? $_POST['tema'] : ''; // Obter o novo tema do POST

// Verifica se o ID da pessoa é válido
if ($idPessoa <= 0) {
    http_response_code(400); // Solicitação inválida
    echo json_encode(array('success' => false, 'message' => 'ID da pessoa inválido'));
    exit;
}

// Atualizar a preferência do tema no banco de dados usando o ID da pessoa
$sql = "UPDATE Tb_Pessoas SET Tema = ? WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $novoTema, $idPessoa); // Vincular tema e ID da pessoa

    if ($stmt->execute()) {
        echo json_encode(array('success' => true, 'message' => 'Tema atualizado com sucesso'));
    } else {
        http_response_code(500); // Erro no servidor
        echo json_encode(array('success' => false, 'message' => 'Erro ao atualizar tema'));
    }

    $stmt->close(); // Fechar a instrução
} else {
    http_response_code(500); // Erro no servidor
    echo json_encode(array('success' => false, 'message' => 'Erro ao preparar a declaração'));
}

$_con->close(); // Fechar a conexão com o banco de dados
?>
