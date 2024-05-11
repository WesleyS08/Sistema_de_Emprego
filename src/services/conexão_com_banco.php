<?php

// Chama o script para criar o banco de dados
require_once("criar_banco.php");

$servidor = "localhost";
$usuario = "root";
$senha = ""; // Deixe vazio se não tiver senha definida para o usuário root
$banco = "SIAS";

$_con = mysqli_connect($servidor, $usuario, $senha, $banco);

// Para definir o $pdo

try {
    // Cria uma nova instância da classe PDO
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    
    // Define o modo de erro do PDO como exceção
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define o conjunto de caracteres para UTF-8
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    // Se houver uma exceção ao tentar conectar, exibe uma mensagem de erro
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit();
}

if (!$_con) {
    die("Falha na conexão: " . mysqli_connect_error());
}

//echo "Conexão estabelecida com sucesso.";
?>
