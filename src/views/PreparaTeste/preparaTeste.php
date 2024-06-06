<?php
include "../../services/conexão_com_banco.php";
session_start();

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario'])) {
    $emailUsuario = $_SESSION['google_session'];
} else {
}
//Exibição de Erros Web
ini_set("display_errors", "1");
error_reporting(E_ALL);

// Primeira consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

// Verifique se a preparação da declaração foi bem-sucedida
if ($stmt) {
    // Vincule o parâmetro ao placeholder na consulta
    $stmt->bind_param("s", $emailUsuario);
    // Execute a declaração
    $stmt->execute();
    // Obtenha o resultado da consulta
    $result = $stmt->get_result();
    // Verifique se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Obtenha o ID da pessoa e se ela está verificada
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];
    } else {
        // Trate o caso em que nenhum resultado é retornado
    }
    $stmt->close();
}

$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

// Verifique se a preparação foi bem-sucedida
if ($stmt) {
    // Execute a query com o parâmetro
    $stmt->bind_param('i', $idPessoa); // Vincula o parâmetro
    $stmt->execute();

    // Obter resultado usando o método correto
    $result = $stmt->get_result(); // Obtenha o resultado como mysqli_result
    if ($result) {
        $row = $result->fetch_assoc(); // Obter a linha como array associativo
        if ($row && isset($row['Tema'])) {
            $tema = $row['Tema'];
        } else {
            $tema = null; // No caso de não haver resultado
        }
    } else {
        $tema = null; // Se o resultado for nulo
    }
} else {
    die("Erro ao preparar a query.");
}


// Verificar se o ID do questionário foi fornecido na URL
if (isset($_GET['id'])) {
    $id_questionario = $_GET['id'];

    // Verificar se o usuário já respondeu ao questionário
    $sql = "SELECT * FROM Tb_Resultados WHERE Tb_Questionarios_ID = ? AND Tb_Candidato_CPF IN (SELECT CPF FROM Tb_Candidato WHERE Tb_Pessoas_Id = ?)";
    $stmt = $_con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $id_questionario, $idPessoa);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Usuário já respondeu ao questionário
            $jaRespondeu = true;
        } else {
            // Usuário não respondeu o questionário ainda
            $jaRespondeu = false;
        }
        $stmt->close();
    } else {
        die("Erro ao preparar a consulta.");
    }

    // Contar quantas questões estão associadas a um questionário
    $sql = "SELECT COUNT(*) AS total_questoes FROM Tb_Questoes WHERE Id_Questionario = $id_questionario";
    $result = $_con->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $totalQuestoes = $row['total_questoes'];
    } else {
        echo "Nenhuma questão encontrada para este questionário.";
    }

    // Consulta para obter os detalhes do questionário com base no ID
    $sql = "SELECT q.Nome, q.Area, q.DataQuestionario, q.Nivel, q.Descricao, q.Tempo, q.ImagemQuestionario, e.Nome_da_Empresa
            FROM Tb_Questionarios q
            JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
            JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
            WHERE q.Id_Questionario = $id_questionario";
    $result = $_con->query($sql);

    if ($result->num_rows > 0) {
        // Extrair os dados do questionário
        $row = $result->fetch_assoc();
        $nomeQuestionario = $row['Nome'];
        $areaQuestionario = $row['Area'];
        $descricaoQuestionario = $row['Descricao'];
        $dataQuestionario = $row['DataQuestionario'];
        $nivelQuestionario = $row['Nivel'];
        $duracaoTeste = $row['Tempo'];
        $nomeEmpresa = $row['Nome_da_Empresa'];
        $imagemQuestionario = $row['ImagemQuestionario'];

        ?>

        <!DOCTYPE html>
        <html lang="pt-br">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Teste</title>
            <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
            <link rel="stylesheet" type="text/css" href="preparaTeste.css">
        </head>

        <body>

            <nav>
                <input type="checkbox" id="check">
                <label for="check" class="menuBtn">
                    <img src="../../../imagens/menu.svg">
                </label>
                <a href="../homeCandidato/homeCandidato.php"><img id="logo"
                        src="../../assets/images/logos_empresa/logo_sias.png"></a>
                <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
                <ul>
                    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
                    <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
                    <li><a href="../Cursos/cursos.php">Cursos</a></li>
                    <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
                </ul>
            </nav>
            <div class="containerTeste">
                <article class="articleTeste">
                    <header>
                        <h2 id="nomeTeste"><?php echo $nomeQuestionario; ?></h2>
                        <div>
                            <p>Competências:&nbsp;</p>
                            <p id="areaTeste"><?php echo $areaQuestionario; ?></p>
                        </div>
                        <div>
                            <p>Data do teste:&nbsp;</p>
                            <p id="dataTeste"><?php echo date("d/m/Y", strtotime($dataQuestionario)); ?></p>
                        </div>

                    </header>
                    <section class="sectionPrincipal">
                        <div class="divImgteste">
                            <?php
                            // Verifica se a imagem do questionário está definida
                            if (!empty($imagemQuestionario)) {
                                // Mostra a imagem do questionário
                                echo '<img src="' . $imagemQuestionario . '" alt="Imagem do Questionário">';
                            } else {
                                // Caso a imagem não esteja definida, devemos mostrar uma imagem padrão, que no momento, não existe, então, só tem um echo mesmo :)
                                echo 'Imagem não disponível';
                            }
                            ?>
                        </div>
                        <div class="divInformacoes">
                            <div>
                                <div class="divLabels"><label class="infos">Por: </label><label class="infos"
                                        id="autorTeste"><?php echo $nomeEmpresa ?></label></div>
                                <div class="divLabels"><label class="infos">Nível: </label><label class="infos"
                                        id="nivelTeste"><?php echo $nivelQuestionario ?></label></div>
                                <div class="divLabels"><label class="infos">Duração: </label><label class="infos"
                                        id="duracaoTeste"><?php echo $duracaoTeste . " minutos"; ?></label></div>
                                <div class="divLabels"><label class="infos">Questões: </label><label class="infos"
                                        id="quantidadeQuestoes"><?php echo $totalQuestoes ?></label></div>
                            </div>
                        </div>
                    </section>
                    <section class="sectionButton">
                        <?php if ($jaRespondeu): ?>
                            <p class="jaRespondeu">Você já respondeu a este questionário.</p>
                            <button class="disabled" disabled>Iniciar</button>
                        <?php else: ?>
                            <a href="../Teste/teste.php?id=<?php echo $id_questionario; ?>">
                                <button>Iniciar</button>
                            </a>
                        <?php endif; ?>
                    </section>
                </article>
            </div>
            <div class="statusTeste">
                <label>Teste realizado.</label>
                <label>Pontuação obtida:</label>
                <label id="pontuacao">35/50</label>
            </div>
            </article>
            </div>
            <footer>
                <a>Política de Privacidade</a>
                <a href="../NossoContato/nossoContato.html">Nosso contato</a>
                <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
                <p class="sinopse">SIAS 2024</p>
            </footer>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

            <script>
                // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
                var temaDoBancoDeDados = "<?php echo $tema; ?>";
            </script>
            <script src="../../../modoNoturno.js"></script>
            <script>
                var idPessoa = <?php echo $idPessoa; ?>;

                $(".btnModo").click(function () {
                    var novoTema = $("body").hasClass("noturno") ? "claro" : "noturno";
                    // Salva o novo tema no banco de dados via AJAX
                    $.ajax({
                        url: "../../services/Temas/atualizar_tema.php",
                        method: "POST",
                        data: { tema: novoTema, idPessoa: idPessoa },
                        success: function () {
                            console.log("Tema atualizado com sucesso");
                        },
                        error: function (error) {
                            console.error("Erro ao salvar o tema:", error);
                        }
                    });
                    // Atualiza a classe do body para mudar o tema
                    if (novoTema === "noturno") {
                        $("body").addClass("noturno");
                        Noturno(); // Adicione esta linha para atualizar imediatamente o tema na interface
                    } else {
                        $("body").removeClass("noturno");
                        Claro(); // Adicione esta linha para atualizar imediatamente o tema na interface
                    }

                });
            </script>
        </body>

        </html>
        <?php
    } else {
        // Se não houver questionário com o ID fornecido, exibir uma mensagem de erro
        echo "Questionário não encontrado.";
    }
} else {
    // Se o ID do questionário não foi fornecido na URL, exibir uma mensagem de erro
    header("Location: ../TodosTestes/todosTestes.php");
}
?>