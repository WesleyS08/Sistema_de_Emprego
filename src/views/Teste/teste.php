<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

// Verificar se a sessão não está ativada
if (!isset($_SESSION['email_session']) && !isset($_SESSION['google_session'])) {
    // Redirecionar o usuário para a página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Recuperar o ID do questionário da URL
$id_questionario = $_GET['id'];

// Selecionar as questões do banco de dados com base no ID do questionário
$sql = "SELECT q.Id_Questao, q.Enunciado, a.Id_Alternativa, a.Texto
        FROM Tb_Questoes q
        INNER JOIN Tb_Alternativas a ON q.Id_Questao = a.Tb_Questoes_Id_Questao
        ORDER BY q.Id_Questao, a.Id_Alternativa";

$sql = "SELECT Nome, Nivel FROM Tb_Questionarios WHERE Id_Questionario = $id_questionario";

$result = $_con->query($sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $nomeQuestionario = $row['Nome'];
    $nivelQuestionario = $row['Nivel'];
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
        <a class="btnVoltar" href="../PreparaTeste/preparaTeste.php">
            <img src="../../assets/images/icones_diversos/backWhite.svg">                 
            <h2>Sair</h2>
        </a>
        <div style="display: flex;">
            <h2 id="minutos">30</h2>
            <h2>:</h2>
            <h2 id="segundos">00</h2> 
        </div>
        <button class="btnModo" id="btnModoTeste"><img src="../../../imagens/moon.svg"></button> 
    </nav>
    <form id="respostas" method="post" action="../../../src/services/Testes/processarTeste.php?id=<?php echo $id_questionario; ?>">
        <div class="containerTeste">
            <div class="contentTeste">
                <div class="divTitulo">                
                    <h2><?php echo $nomeQuestionario ?></h2>
                    <div class="divSubtitulo">
                        <p>Por:&nbsp;</p>
                        <p id="autorTeste">Microsoft</p>
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
                                        echo "<div><input type='radio' name='respostas[$idQuestao]' value='" . $alternativa["Id_Alternativa"] . "'><label for=''>" . $alternativa["Texto"] . "</label></div>";
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