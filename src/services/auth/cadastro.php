<?php
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Autenticação do usuário candidato

    $nomeUsuario = $_POST['nome'];
    $emailUsuario = $_POST['email'];
    $cpfUsuario = $_POST['cpf'];
    $senhaUsuario = $_POST['senha'];

    // Criptografia da senha

    $senhaCriptografada = sha1($senhaUsuario);

    // Verificar se o CPF já existe na tabela de candidatos

    $checkCPF = $_con->prepare("SELECT COUNT(*) AS total FROM tb_candidato WHERE CPF = ?");
    $checkCPF->bind_param("s", $cpfUsuario);
    $checkCPF->execute();
    $cpfExists = $checkCPF->get_result()->fetch_assoc()['total'];

    if ($cpfExists > 0) {
        echo "Erro ao inserir registro: CPF já existe.";        
    } else {
        // Iniciar a transação
        $_con->begin_transaction();

        // Prevenir SQL Injection usando prepared statements
        $stmt1 = $_con->prepare("INSERT INTO tb_pessoas (Nome, Email, Senha) VALUES (?, ?, ?)");
        $stmt1->bind_param("sss", $nomeUsuario, $emailUsuario, $senhaCriptografada);
        
        $stmt2 = $_con->prepare("INSERT INTO tb_candidato (CPF) VALUES (?)");
        $stmt2->bind_param("s", $cpfUsuario);

        // Executar as instruções SQL dentro da transação
        $stmt1->execute();
        $stmt2->execute();

        // Verificar se ambas as inserções foram bem-sucedidas
        if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
            // Confirmar a transação
            $_con->commit();
            echo "Registro inserido com sucesso!";
        } else {
            // Desfazer a transação em caso de erro
            $_con->rollback();
            echo "Erro ao inserir registro. Por favor, tente novamente.";
        }

        // Fechar as declarações preparadas
        $stmt1->close();
        $stmt2->close();
    }
} else {
    $aviso = "Ocorreu um erro ao processar o formulário.";
}
?>
