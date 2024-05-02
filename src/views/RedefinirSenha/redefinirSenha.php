<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/verificacoes.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/login.css">    
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>
<body>
    <nav>
        <a  href="../../../index.php">
            <img id="logo" src="../../assets/images/logos_empresa/logo_sias.png" alt="Logo da Empresa">
        </a>
    </nav>

    <div class="container">
        <div class="content">
            <h1>Redefina sua senha</h1>
            <p>Escolha uma nova senha para proteger sua conta</p>
            
            <?php
            include "../../services/conexão_com_banco.php";


            // Obter o token da URL
            $token = isset($_GET['token']) ? trim($_GET['token']) : '';

            if ($token) {
                // Verificar se o token é válido
                $sql = "SELECT Id_Pessoas, Codigo_Created_At FROM Tb_Pessoas WHERE Note = ?";
                $stmt = $_con->prepare($sql);
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Verificar se o token não expirou (validade de 24 horas)
                    $user = $result->fetch_assoc();
                    $tokenCreatedAt = new DateTime($user['Codigo_Created_At']);
                    $now = new DateTime();
                    
                    $interval = $now->diff($tokenCreatedAt);

                    if ($interval->d < 1) {
                        // O token é válido e não expirou
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            // Receber as senhas do formulário
                            $novaSenha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
                            $confirmaSenha = isset($_POST['confirmaSenha']) ? trim($_POST['confirmaSenha']) : '';

                            if ($novaSenha === $confirmaSenha) {
                                // Hash da nova senha
                                $novaSenhaHasheada = sha1($novaSenha);

                                // Atualizar a senha no banco de dados
                                $sql = "UPDATE Tb_Pessoas SET Senha = ?, Note = NULL, Codigo_Created_At = NULL WHERE Id_Pessoas = ?";
                                $stmt = $_con->prepare($sql);
                                $stmt->bind_param("si", $novaSenhaHasheada, $user['Id_Pessoas']);
                                $stmt->execute();

                                echo "Senha redefinida com sucesso. Agora você pode fazer login.";
                            } else {
                                echo "As senhas não conferem. Por favor, tente novamente.";
                            }
                        } else {
                            // Exibir o formulário para redefinir a senha
                            echo "<form class='formVerificacao' method='POST'>";
                            echo "  <div class='containerInput'>";
                            echo "    <div class='contentInput'>";
                            echo "      <input class='inputAnimado' id='senha' name='senha' type='password' required>";
                            echo "      <div class='labelLine'>Nova Senha</div>";                        
                            echo "      <div class='labelLine' id='mostrarSenha'>";
                            echo "        <img id='olho' src='../../assets/images/icones_diversos/closeEye.svg'>";
                            echo "      </div>";
                            echo "    </div>";
                            echo "  </div>"; 
                            echo "  <div class='containerInput'>";
                            echo "    <div class='contentInput'>";
                            echo "      <input class='inputAnimado' id='confirmaSenha' name='confirmaSenha' type='password' required>";
                            echo "      <div class='labelLine'>Confirmar Senha</div>";
                            echo "    </div>";
                            echo "  </div>";
                            echo "  <input type='submit' value='Salvar' class='btnLogin' id='btnLaranja'>";
                            echo "</form>";
                        }
                    } else {
                        echo "O link para redefinir a senha expirou. Por favor, solicite um novo.";
                    }
                } else {
                    echo "Token inválido. Por favor, verifique o link recebido.";
                }
            } else {
                echo "Token não fornecido.";
            }
            ?>
        </div>
    </div>

    <script src="mostrarSenha.js"></script>
</body>
</html>
