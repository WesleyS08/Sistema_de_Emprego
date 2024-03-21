<?php

// Chama o script para criar o banco de dados
require_once("criar_banco.php");

$servidor = "localhost";
$usuario = "root";
$senha = ""; // Deixe vazio se não tiver senha definida para o usuário root
$banco = "SIAS";

$_con = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$_con) {
    die("Falha na conexão: " . mysqli_connect_error());
}

echo "Conexão estabelecida com sucesso.";
?>
