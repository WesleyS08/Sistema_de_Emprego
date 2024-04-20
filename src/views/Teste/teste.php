<?php
include "../../services/conexão_com_banco.php";

// Recuperar o ID do questionário da URL
$id_questionario = $_GET['id'];

// Selecionar as questões do banco de dados com base no ID do questionário
$sql = "SELECT q.Id_Questao, q.Enunciado, a.Id_Alternativa, a.Texto
        FROM Tb_Questoes q
        INNER JOIN Tb_Alternativas a ON q.Id_Questao = a.Tb_Questoes_Id_Questao
        ORDER BY q.Id_Questao, a.Id_Alternativa";
        
$result = $_con->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="teste.css">
</head>
<body>
    <nav>
        <a href="../PreparaTeste/preparaTeste.html"><h2><</h2><h3>Sair</h3></a>
        <div style="display: flex;">
            <h2 id="minutos">30</h2>
            <h2>:</h2>
            <h2 id="segundos">00</h2>
        </div>
    </nav>
    <div class="containerTeste">
        <div class="contentTeste">
            <div class="divTitulo">                
                <h2>Python para Análise de Dados</h2>
                <div class="divSubtitulo">
                    <p>Por:&nbsp;</p>
                    <p id="autorTeste">Microsoft</p>
                </div>
                <div class="divSubtitulo">
                    <p>Nível:&nbsp;</p>
                    <p id="nivelTeste">Intermediário</p>
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
                                ORDER BY q.Id_Questao, a.Id_Alternativa";

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

                            // Aqui as questões deverão ser exibidas
                            foreach ($questoes as $idQuestao => $questao) {
                                echo "<article class='articleQuestao'>";
                                echo "<div class='divPergunta'>";
                                echo "<p name='numQuestao' class='numQuestao'>" . $idQuestao . "</p>";
                                echo "<p>.</p>";
                                echo "<p name='pergunta' class='pergunta'>" . $questao["Enunciado"] . "</p>";
                                echo "</div>";
                                echo "<div class='divAlternativas'>";
                                foreach ($questao["Alternativas"] as $alternativa) {
                                    echo "<div><input type='radio' name='questao_" . $idQuestao . "' value='" . $alternativa["Id_Alternativa"] . "'><label for=''>" . $alternativa["Texto"] . "</label></div>";
                                }
                                echo "</div>";
                                echo "</article>";
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
    <iframe width="110" height="100" src="https://www.myinstants.com/instant/ram-tchum-2-4173/embed/" frameborder="0" scrolling="no"></iframe>
    <script src="cronometro.js"></script> 
    <script src="contagemQuestoes.js"></script>
    <script src="finalizarTeste.js"></script>
</body>
</html>