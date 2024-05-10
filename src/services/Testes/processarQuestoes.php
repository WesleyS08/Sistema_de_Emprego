<?php
// Inclua o arquivo de conexão com o banco de dados
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    $emailUsuario = $_SESSION['email_session'];

    // Consultar o banco de dados para obter o ID da empresa com base no email
    // Substitua "sua_tabela_empresa" pelo nome real da tabela que contém as informações da empresa
    $query = "SELECT Id_Empresa FROM Tb_Empresa WHERE Email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$emailUsuario]);
    $idEmpresa = $stmt->fetchColumn();

    // Verificar se a consulta foi bem-sucedida
    if (!$idEmpresa) {
        // Se não encontrar a empresa, redirecione para a página de login
        header("Location: ../Login/login.html");
        exit;
    }
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}

