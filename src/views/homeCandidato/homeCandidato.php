<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado como candidato
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado como candidato
    $autenticadoComoCandidato = true;
    $emailUsuario = '';

    // Definir o e-mail do usuário com base no tipo de sessão
    if (isset($_SESSION['email_session'])) {
        $emailUsuario = $_SESSION['email_session'];
    } elseif (isset($_SESSION['google_session'])) {
        $emailUsuario = $_SESSION['google_session'];
    }
} else {
    // Se não estiver autenticado como candidato, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
// Consulta para obter o ID da pessoa logada
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
        // Obtenha o ID da pessoa
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];

        // Use o ID da pessoa como necessário no restante do seu código
    } else {
        // Se não houver resultados, lide com isso de acordo com sua lógica de aplicativo
    }

    // Feche a declaração
    $stmt->close();
}

// Consulta SQL para verificar dados do candidato
$sql_verificar_candidato = "SELECT * FROM Tb_Candidato
    JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
    WHERE Tb_Pessoas.Email = ?";

$stmt_verificar_candidato = $_con->prepare($sql_verificar_candidato);
$stmt_verificar_candidato->bind_param("s", $emailUsuario);
$stmt_verificar_candidato->execute();
$result_verificar_candidato = $stmt_verificar_candidato->get_result();

// Verificar se a consulta teve sucesso
if ($result_verificar_candidato === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}

// Consulta para puxar os questionários
$sql_puxarQuestionarios = "SELECT Id_Questionario, Nome, Area FROM Tb_Questionarios ORDER BY RAND() LIMIT 7";
$stmt_questionarios = $_con->prepare($sql_puxarQuestionarios);
$stmt_questionarios->execute();
$result_questionarios = $stmt_questionarios->get_result();

// Verificar se a consulta obteve sucesso
if ($result_questionarios === false) {
    echo "Erro na consulta" . $_con->error;
    exit;
}


// Obter dados do candidato
$candidato = $result_verificar_candidato->fetch_assoc();

// Verificar se o candidato foi encontrado
if (!$candidato) {
    echo "Candidato não encontrado";
    exit;
}

// Dados do candidato
$nomeUsuario = $candidato['Nome'];
$emailUsuario = $candidato['Email'];

// Limpar variáveis de sessão não utilizadas
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';

// Consulta SQL para verificar empresas e puxar as vagas
$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ";

$stmt = $_con->prepare($sql_verificar_empresa);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se a consulta teve sucesso
if ($result === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}

// Função para determinar a imagem com base na categoria do trabalho
function determinarImagemCategoria($categoria)
{
    switch ($categoria) {
        case 'Estágio':
            return 'estagio';
        case 'CLT':
            return 'clt';
        case 'PJ':
            return 'pj';
        case 'Jovem Aprendiz':
            return 'estagio';
        // Adicione mais casos conforme necessário
        default:
            return 'default'; // Retorna uma imagem padrão caso não haja correspondência
    }
}

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divCentraliza">
            <div>
                <div class="divTituloDigitavel">
                    <h1 id="tituloAutomatico">B</h1>
                    <i></i>
                </div>
                <p class="sinopse">Encontre vagas que combinam com você!<br>Clique e explore as oportunidades.</p>
                <button onclick="location.href='../TodasVagas/todasVagas.php'" class="btnInicial">Ver todas as vagas</button>
            </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <?php $emailUsuario ?>
            <h2>Últimas vagas</h2>
        </div>
        <div class="container">
        <a class="btnLeftSlider" id="leftMinhasVagas">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightMinhasVagas">
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
                    <div class="carrosselBox" id="minhasVagas">
                        <?php
                        // Loop para exibir as vagas restantes no carrossel
                        while ($row = $result->fetch_assoc()) {
                            // Consulta para contar o número de inscritos para esta vaga
                            $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
                            $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
                            $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" indica que o parâmetro é um inteiro
                            $stmt_inscricoes->execute();
                            $result_inscricoes = $stmt_inscricoes->get_result();

                            // Verificar se a consulta teve sucesso
                            if ($result_inscricoes === false) {
                                // Tratar o erro, se necessário
                                echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                                exit;
                            }

                            // Obter o resultado da contagem de inscrições
                            $row_inscricoes = $result_inscricoes->fetch_assoc();
                            $total_inscricoes = $row_inscricoes['total_inscricoes'];

                            // Exibir a vaga e o número de inscritos
                            echo '<a class="postLink" href="../Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                            echo '<article class="post">';
                            echo '<div class="divAcessos">';
                            echo '<img src="../../../imagens/people.svg"></img>';
                            echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
                            echo '</div>';

                            echo '<header>';
                            switch ($row["Categoria"]) {
                                case "CLT":
                                    echo '<img src="../../../imagens/clt.svg">';
                                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                                    break;
                                case "Estágio":
                                case "Jovem Aprendiz": // Caso tenham a mesma aparência visual
                                    echo '<img src="../../../imagens/estagio.svg">';
                                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                                    break;
                                case "PJ":
                                    echo '<img src="../../../imagens/pj.svg">';
                                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                                    break;
                                default:
                                    echo '<label class="tipoVaga">Categoria não definida</label>';
                                    break;
                            }
                            echo '</header>';
                            echo '<section>';
                            echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
                            echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? (strlen($row["Descricao"]) > 55 ? substr($row["Descricao"], 0, 55) . '...' : $row["Descricao"]) : "Descrição não definida") . '</p>';
                            // Exibir o status da vaga e a data de criação
                            $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                            $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
                            if ($row['Status'] == 'Aberto') {
                                echo '<p style="color: green;">' . $row['Status'] . '</p>';
                                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                            } else {
                                echo '<p style="color: red;">' . $row['Status'] . '</p>';
                                echo '<p class="dataVaga">' . $datadeTermino . '</p>';
                            }

                            echo '</section>';
                            echo '</article>';
                            echo '</a>';
                        }
                        ?>
                    </div>
        </div>
    </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Testes de Habilidades</h2>
            <p>Realize testes para ganhar pontos e se destacar em processos seletivos!</p>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftTestes">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightTestes">                
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
                <div class="carrosselBox" id="carrosselTestes">                       
                    <a class="testeCarrosselLink">
                    <?php
                        if ($result_questionarios->num_rows > 0) {
                            // Loop através dos resultados da consulta
                            while ($row = $result_questionarios->fetch_assoc()) {
                                // Extrai os dados do questionário
                                $idQuestionario = $row['Id_Questionario'];
                                $nome = $row['Nome'];
                                $area = $row['Area'];
                                // Saída HTML para cada questionário no carrossel
                                echo '<a class="testeCarrosselLink" href="../PreparaTeste/preparaTeste.php?id=' . $idQuestionario . '">';
                                echo '<article class="testeCarrossel">';
                                echo '<div class="divAcessos">';
                                echo '<img src="../../../imagens/people.svg"></img>';
                                echo '<small class="qntdAcessos">800</small>';
                                echo '</div>';
                                echo '<img src="../../../imagens/excel.svg"></img>';
                                echo '<div class="divDetalhesTeste">';
                                echo '<div>';
                                echo '<p class="nomeTeste">' . $nome . '</p>';
                                echo '<small class="autorTeste">Por Jefferson Evangelista</small><br>';
                                echo '<small class="competenciasTeste">' . $area . '</small>';
                                echo '</div>';
                                echo '</div>';
                                echo '</article>';
                                echo '</a>';
                            }
                        } else {
                            echo "Nenhum questionário encontrado.";
                        }
                    ?>
                    </a>
                </div>
                <div class="divBtnVerMais">
                    <a href="../todosTestes/todosTestes.php" class="btnVerMais"><button class="verMais">Ver mais</button></a>
                </div>
        </div>
    </div>
    <div class="divCommon">
        <div class="divTitulo">
            <h2>Nossos Parceiros</h2>
        </div>
        <div class="container">
            <div class="carrosselInfinito">
                <div class="trilhaCarrossel">
                <div class="slide">
                        <img class="slideMysql" src="../../assets/images/logos_parceiros/mysql.svg">
                    </div>
                    <div class="slide">
                        <img class="slidePhp" src="../../assets/images/logos_parceiros/php.svg">
                    </div>
                    <div class="slide">
                        <img class="slideFirebase" src="../../assets/images/logos_parceiros/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../assets/images/logos_parceiros/fatec.png">
                    </div>
                    <div class="slide">
                        <img class="slideJs" src="../../assets/images/logos_parceiros/javascript.svg">
                    </div>
                    <div class="slide">
                        <img class="slideHtml" src="../../assets/images/logos_parceiros/html.svg">
                    </div>
                    <div class="slide">
                        <img class="slideCss" src="../../assets/images/logos_parceiros/css.svg">
                    </div>
                    <div class="slide">
                        <img class="slideMysql" src="../../assets/images/logos_parceiros/mysql.svg">
                    </div>
                    <div class="slide">
                        <img class="slidePhp" src="../../assets/images/logos_parceiros/php.svg">
                    </div>
                    <div class="slide">
                        <img class="slideFirebase" src="../../assets/images/logos_parceiros/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../assets/images/logos_parceiros/fatec.png">
                    </div>
                    <div class="slide">
                        <img class="slideJs" src="../../assets/images/logos_parceiros/javascript.svg">
                    </div>
                    <div class="slide">
                        <img class="slideHtml" src="../../assets/images/logos_parceiros/html.svg">
                    </div>
                    <div class="slide">
                        <img class="slideCss" src="../../assets/images/logos_parceiros/css.svg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="carrosselUltimosAnuncios.js"></script>
    <script src="carrosselMinhasVagas.js"></script>
    <script src="carrosselTestes.js"></script>    
    <script src="../../../modoNoturno.js"></script>
    <!-- Eu movi o titulo digitavel pra cá, para pegar o nome do usario que está com seção  -->
    <script>
        var nomeUsuario = "<?php echo $nomeUsuario; ?>";

        if (nomeUsuario.trim() !== '') {
            // O nome do usuário não está vazio, execute o código de animação
            setTimeout(() => {
                const titulo = document.querySelector("#tituloAutomatico");
                const interval = 150; // variável do tempo de digitação

                function DeterminaHorario() {
                    let hora = new Date().getHours().toString().padStart(2, '0');
                    if (hora < 12) {
                        return "om dia";
                    } else if (hora < 18) {
                        return "oa tarde";
                    } else {
                        return "oa noite";
                    }
                }

                let text1 = `${DeterminaHorario()}, ${nomeUsuario}!`;

                function showText(titulo, text1, interval) {
                    let char = text1.split("").reverse();

                    let typer = setInterval(() => {
                        if (!char.length) {
                            return clearInterval(typer);
                        }

                        let next = char.pop();
                        titulo.innerHTML += next;

                    }, interval);
                }

                showText(titulo, text1, interval);
            },);
        } else {
            console.log("Nome do usuário está vazio!");
        }
    </script>
</body>

</html>