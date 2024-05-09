<?php
include_once "../../services/conexão_com_banco.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = $_POST['cpf'];

    $stmt = $_con->prepare('SELECT COUNT(*) FROM Tb_Candidato WHERE CPF = ?');
    $stmt->bind_param('s', $cpf);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    echo json_encode(['existe' => $count > 0]);
    exit;
}
?>
