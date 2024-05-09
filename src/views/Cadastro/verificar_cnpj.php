<?php
include_once "../../services/conexão_com_banco.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj = $_POST['cnpj'];

    $stmt = $_con->prepare('SELECT COUNT(*) FROM Tb_Empresa WHERE CNPJ = ?');
    $stmt->bind_param('s', $cnpj);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    echo json_encode(['existe' => $count > 0]);
    exit;
}
?>
