<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato') {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'candidato') {
    $emailUsuario = $_SESSION['google_session'];
} else {

    header("Location: ../Login/login.html");
    exit;
}

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Recuperar o ID do questionário da URL
$id_questionario = $_GET['id'];

// Selecionar os dados do questionário e o nome da empresa relacionada
$sql = "SELECT q.Nome, q.Nivel, q.Tempo, e.Nome_da_Empresa 
        FROM Tb_Questionarios q
        JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
        JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
        WHERE q.Id_Questionario = $id_questionario";

$result = $_con->query($sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $nomeQuestionario = $row['Nome'];
    $nivelQuestionario = $row['Nivel'];
    $tempo = $row['Tempo'];
    $nomeEmpresa = $row['Nome_da_Empresa'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/teste.css">
</head>
<body>
    <nav id="navFixa">          
        <a id="btnVoltarTeste" href="../PreparaTeste/preparaTeste.php">
            <img src="../../assets/images/icones_diversos/backWhite.svg">
        </a>
        <div style="display: flex;">
            <h4 id="minutos"><?php echo $tempo ?></h4>
            <h4>:</h4>
            <h4 id="segundos">00</h4> 
        </div>
        <button class="btnModo" id="btnModoTeste"><img src="../../../imagens/moon.svg"></button> 
    </nav>
    <form id="respostas" method="post" action="../../../src/services/Testes/processarTeste.php?id=<?php echo $id_questionario; ?>">
        <div class="containerTeste">
            <div class="contentTeste">
                <div class="divTitulo">                
                    <h2><?php echo $nomeQuestionario ?></h2>
                    <div class="divSubtitulo">
                        <p>Criado por:&nbsp;</p>
                        <p id="autorTeste"><?php echo $nomeEmpresa ?></p>
                    </div>
                    <div class="divSubtitulo">
                        <p>Nível:&nbsp;</p>
                        <p id="nivelTeste"><?php echo $nivelQuestionario ?></p>
                    </div>
                </div>
                <div class="divQuestoes">
                    <?php                    
                        // Verifica se o parâmetro 'id' foi passado na URL
                        if(isset($_GET['id'])) {
                            $id_questionario = $_GET['id'];

                            // Select no SQL para puxar as questões com base no id do questionário
                            $sql = "SELECT q.Id_Questao, q.Enunciado, a.Id_Alternativa, a.Texto
                            FROM Tb_Questoes q
                            INNER JOIN Tb_Alternativas a ON q.Id_Questao = a.Tb_Questoes_Id_Questao
                            WHERE q.Id_Questionario = $id_questionario
                            ORDER BY RAND(), q.Id_Questao, a.Id_Alternativa";

                            $result = $_con->query($sql);

                            $questoes = array();

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {   
                                    // Se a questão ainda não existe no array, uma nova entrada vai ser criada para ela
                                    if (!isset($questoes[$row["Id_Questao"]])) {
                                        $questoes[$row["Id_Questao"]] = array(
                                            "Enunciado" => $row["Enunciado"],
                                            "Alternativas" => array()
                                        );
                                    }
                                    
                                    // Adiciona a alternativa ao array de alternativas da questão
                                    $questoes[$row["Id_Questao"]]["Alternativas"][] = array(
                                        "Id_Alternativa" => $row["Id_Alternativa"],
                                        "Texto" => $row["Texto"]
                                    );
                                }

                                $contador = 0;

                                // Aqui as questões deverão ser exibidas
                                foreach ($questoes as $idQuestao => $questao) {
                                    echo "<div class='articleQuestao'>";
                                    echo "<div class='divPergunta'>";
                                    echo "<p name='numQuestao' class='numQuestao'>" . $idQuestao . "</p>";
                                    echo "<p>.</p>";
                                    echo "<p name='pergunta' class='pergunta'>" . $questao["Enunciado"] . "</p>";
                                    echo "</div>";
                                    echo "<div class='divAlternativas'>";
                                    // Nomeie todos os inputs com o mesmo nome, indicando a questão
                                    foreach ($questao["Alternativas"] as $alternativa) {
                                        echo "<div><input type='radio' name='respostas[$idQuestao]' value='" . $alternativa["Id_Alternativa"] . "'><label for=''  class='alternativa'>" . $alternativa["Texto"] . "</label></div>";
                                    }
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else { // Caso não encontre nada, não irá retornar obviamente nada, e irá exibir a mensagem de erro.
                                echo "Nenhuma questão encontrada.";
                            }
                        } else {
                            echo "ID do questionário não especificado na URL.";
                        }
                    ?>
                    <div class="divButton">
                        <button id="finalizar">Finalizar teste</button>
                    </div> 
                </div>          
            </div>
        </div>
    </form>
    <script src="cronometro.js"></script>    
    <script src="../../../modoNoturno.js"></script>
    <script src="contagemQuestoes.js"></script>
    <script src="finalizarTeste.js"></script>
</body>
</html>