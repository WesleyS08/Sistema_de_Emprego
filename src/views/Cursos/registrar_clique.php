<?php
error_reporting(0); // Desativa a exibição de erros
header('Content-Type: application/json'); 

include "../../services/conexão_com_banco.php";

// Obtendo os dados da requisição
$data = json_decode(file_get_contents('php://input'), true);
$cursoID = $data['cursoID'];

// Atualizando o contador de cliques
$sql = "UPDATE Tb_Cursos SET cliques = cliques + 1 WHERE Id_Cursos = ?";
$stmt = $_con->prepare($sql);
$stmt->bind_param("i", $cursoID);

$response = array();
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

$stmt->close();
$_con->close();

// Enviando resposta
echo json_encode($response);
?>
