<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../services/conexão_com_banco.php";
header('Content-Type: application/json');

try {
    $email = isset($_POST['email']) ? trim(urldecode($_POST['email'])) : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Formato de e-mail inválido.");
    }

    $sql = "SELECT COUNT(*) AS count FROM Tb_Pessoas WHERE Email = ?";
    $stmt = $_con->prepare($sql);

    if (!$stmt) {
        throw new Exception("Falha ao preparar a consulta.");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Falha ao obter resultados.");
    }

    $row = $result->fetch_assoc();
    $existe = $row['count'] > 0;

    echo json_encode([
        'existe' => $existe,
        'mensagem' => $existe ? 'E-mail registrado' : 'E-mail não encontrado'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'existe' => false,
        'erro' => $e->getMessage()
    ]);
} finally {
    if ($stmt) {
        $stmt->close();
    }
    if ($_con) {
        $_con->close();
    }
}
?>