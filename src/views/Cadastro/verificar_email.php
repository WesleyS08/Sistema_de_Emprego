<?php
include_once "../../services/conexão_com_banco.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $_con->prepare('SELECT COUNT(*) FROM Tb_Pessoas WHERE Email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    echo json_encode(['existe' => $count > 0]);
    exit;
}
?>
