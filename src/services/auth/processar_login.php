<?php
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    
    if (!empty($email) && !empty($senha)) {
       
        $senha_sha1 = sha1($senha);

       
        $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha_sha1'";
        $result = mysqli_query($_con, $sql);

        
        if (mysqli_num_rows($result) > 0) {
            
            header("Location: pagina_de_sucesso.php");
            exit;
        } else {
          
            $aviso = "Usuário não encontrado ou senha incorreta. Por favor, tente novamente.";
        }
    } else {
        
        $aviso = "Por favor, preencha todos os campos.";
    }
} else {
    
    $aviso = "Ocorreu um erro ao processar o formulário.";
}

header("Location: login.php?aviso=" . urlencode($aviso));
exit;
?>
