<?php

$servidor = "localhost";
$usuario = "root";
$senha = ""; 
$banco = ""; 

// Conexão ao MySQL
$conexao = new mysqli($servidor, $usuario, $senha);


if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}


$sql = file_get_contents('../../docs/Modelos do banco de dados/Script_de_criação.sql');


if ($conexao->multi_query($sql) === TRUE) {
    echo "Script SQL executado com sucesso.";
} else {
    echo "Erro ao executar script SQL: " . $conexao->error;
}

$conexao->close();

?>
