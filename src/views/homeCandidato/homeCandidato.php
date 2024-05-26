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

// Primeira consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas, Verificado FROM Tb_Pessoas WHERE Email = ?";
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
        $verificado = $row['Verificado'];
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
$sql_puxarQuestionarios = "SELECT DISTINCT q.Id_Questionario, q.Nome, q.Area, e.Nome_da_Empresa, q.ImagemQuestionario
FROM Tb_Questionarios q
INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
INNER JOIN Tb_Pessoas p ON e.Tb_Pessoas_Id = p.Id_Pessoas ORDER BY RAND() LIMIT 5";
$stmt_questionarios = $_con->prepare($sql_puxarQuestionarios);
$stmt_questionarios->execute();
$result_questionarios = $stmt_questionarios->get_result();

// Verificar se a consulta obteve sucesso
if ($result_questionarios === false) {
    echo "Erro na consulta" . $_con->error;
    exit;
}
$query = "SELECT CPF FROM Tb_Candidato WHERE Tb_Pessoas_Id = ?";

$stmt = $_con->prepare($query);
$stmt->bind_param('i', $idPessoa);
$stmt->execute();

$result = $stmt->get_result();

$cpf = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cpf = $row['CPF'];
} else {
    echo 'CPF não encontrado para o ID especificado.';
}

$query = "
    SELECT
        COUNT(*) AS total_inscricoes
    FROM
        Tb_Inscricoes ins
    JOIN
        Tb_Vagas va ON ins.Tb_Vagas_Tb_Anuncios_Id = va.Tb_Anuncios_Id
    JOIN
        Tb_Anuncios an ON va.Tb_Anuncios_Id = an.Id_Anuncios
    JOIN
        Tb_Empresa em ON va.Tb_Empresa_CNPJ = em.CNPJ
    WHERE
        ins.Tb_Candidato_CPF = ?
    ORDER BY
        (va.Status = 'Encerrado'),
        va.Data_de_Termino ASC
";

$stmt = $_con->prepare($query);
$stmt->bind_param('s', $cpf);
$stmt->execute();
$result = $stmt->get_result();

// Verifique se a consulta retornou resultados
if ($result->num_rows > 0) {
    // Obtenha o total de inscrições
    $row = $result->fetch_assoc();
    $total_inscricoes = $row['total_inscricoes'];

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
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
ORDER BY Tb_Anuncios.Data_de_Criacao DESC";


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
    <script>
        function limparLocalStorageComExcecao() {
            const chavesParaLimpar = ['tipos', 'area', 'apenasVagasAbertas', 'termoPesquisa'];

            chavesParaLimpar.forEach(function (chave) {
                localStorage.removeItem(chave);
            });
            const areaPadrao = 'Todas'; // Valor padrão para "area"
            if (!localStorage.getItem('area')) {
                localStorage.setItem('area', areaPadrao);
            }
            console.log("LocalStorage após limpeza:", localStorage);
        }
        window.addEventListener('load', limparLocalStorageComExcecao);
    </script>
    <style>
        .aviso-verificado {
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .aviso-nao-verificado {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
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
                <button onclick="location.href='../TodasVagas/todasVagas.php'" class="btnInicial">Encontre sua
                    vaga</button>
            </div>
        </div>
    </div>
    <?php
    if ($verificado == 1) {
    } else {
        echo "<div class='aviso-nao-verificado'>Sua conta ainda não foi verificada. Por favor, verifique sua conta para desfrutar do máximo possível.</div>";
        $totaldisponivel = 4 - $total_inscricoes;
        echo "<div class='aviso-nao-verificado'>Você tem  " . $totaldisponivel . " buscas disponíveis.</div>";
    }
    ?>
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
                    echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? (strlen($row["Titulo"]) > 14 ? substr($row["Titulo"], 0, 20) . '...' : $row["Titulo"]) : "Título não definido") . '</h3>';

                    // Se não houver empresa, definir um valor padrão
                    if (empty($nome_empresa)) {
                        $nome_empresa = 'Confidencial';
                    }
                    // Agora pode imprimir
                    echo '<p class="empresaVaga">' . $nome_empresa . '</p>';

                    // Exibir o status da vaga e a data de criação
                    $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                    $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
                    if ($row['Status'] == 'Aberto') {
                        echo '<h4 class="statusVaga" style="color:green">Aberto</h4>';
                        echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                    } else {
                        echo '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
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
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Últimas Inscrições</h2>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftUltimosAnuncios">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightUltimosAnuncios">
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
            <div class="carrosselBox" id="ultimosAnuncios">
                <?php
                if ($cpf) {
                    $horaAtual = date('d/m/Y H:i');
                    $query = "
                        SELECT
                            va.Tb_Anuncios_Id,
                            an.Id_Anuncios,
                            va.Tb_Empresa_CNPJ,
                            an.Titulo,
                            an.Categoria,
                            em.Nome_da_Empresa,
                            va.Status,
                            DATE_FORMAT(va.Data_de_Termino, '%d/%m/%Y') AS Data_Termino,
                            DATE_FORMAT(ins.Data_de_Inscricao, '%d/%m/%Y %H:%i') AS Data_Inscricao,
                            NOW() AS Hora_Atual
                        FROM
                            Tb_Inscricoes ins
                        JOIN
                            Tb_Vagas va ON ins.Tb_Vagas_Tb_Anuncios_Id = va.Tb_Anuncios_Id
                        JOIN
                            Tb_Anuncios an ON va.Tb_Anuncios_Id = an.Id_Anuncios
                        JOIN
                            Tb_Empresa em ON va.Tb_Empresa_CNPJ = em.CNPJ
                        WHERE
                            ins.Tb_Candidato_CPF = ?
                        ORDER BY
                            ins.Data_de_Inscricao DESC";

                    $stmt = $_con->prepare($query);
                    $stmt->bind_param('s', $cpf);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<a class="postLink" href="../Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
                            echo ' <article class="post">';
                            echo '<header>';
                            switch ($row["Categoria"]) {
                                case "CLT":
                                    echo '<img src="../../../imagens/clt.svg">';
                                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                                    break;
                                case "Estágio":
                                case "Jovem Aprendiz":
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
                            echo ' <section>';
                            echo ' <h3 class="nomeVaga">' . $row['Titulo'] . '</h3>';
                            $nomeEmpresa = isset($row['Nome_da_Empresa']) && $row['Nome_da_Empresa'] !== null
                                ? $row['Nome_da_Empresa']
                                : 'Confidencial';

                            // Exibir o nome da empresa ou "Confidencial"
                            echo '<p class="empresaVaga">' . $nomeEmpresa . '</p>';
                            echo ' </section>';
                            echo '<label class="statusVaga" style="color: ' . ($row['Status'] == 'Aberto' ? 'green' : 'red') . ';">' . $row['Status'] . '</label>';
                            echo ' <label class="dataVaga">' . $row['Data_Inscricao'] . '</label>';
                            echo ' </article>';
                            echo '</a>';
                        }
                    } else {
                        echo "<p style='text-align:center; margin:0 auto;'>Não há inscrições realizadas.</p>";
                    }
                }
                ?>
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
                            $nomeEmpresa = $row['Nome_da_Empresa'];
                            $imagem_questionario = $row['ImagemQuestionario'];
                            $sql_contagem_respostas = "SELECT COUNT(*) AS total_respostas FROM Tb_Resultados WHERE Tb_Questionarios_ID = ?";
                            $stmt_contagem_respostas = $_con->prepare($sql_contagem_respostas);

                            if ($stmt_contagem_respostas) {
                                $stmt_contagem_respostas->bind_param("i", $idQuestionario);
                                $stmt_contagem_respostas->execute();
                                $result_contagem_respostas = $stmt_contagem_respostas->get_result();

                                // Verifica se a consulta retornou algum resultado
                                if ($result_contagem_respostas->num_rows > 0) {
                                    $row_contagem_respostas = $result_contagem_respostas->fetch_assoc();
                                    $total_respostas = $row_contagem_respostas['total_respostas'];
                                } else {
                                    $total_respostas = 0;
                                }

                                // Saída HTML para cada questionário no carrossel
                                echo '<a class="testeCarrosselLink" href="../PreparaTeste/preparaTeste.php?id=' . $idQuestionario . '">';
                                echo '<article class="testeCarrossel">';
                                echo '<div class="divAcessos">';
                                echo '<img src="../../../imagens/people.svg"></img>';
                                echo '<small class="qntdAcessos">' . $total_respostas . '</small>';
                                echo '</div>';
                                echo '<img class="imgTeste"  src="' . $imagem_questionario . '"></img>';
                                echo '<div class="divDetalhesTeste">';
                                echo '<div>';
                                $limite = 21;

                                // Obtenha o nome e limite-o se necessário
                                if (strlen($nome) > $limite) {
                                    $nome_limitado = mb_substr($nome, 0, $limite) . '...'; // Cortar o texto e adicionar reticências
                                } else {
                                    $nome_limitado = $nome; // Se não ultrapassar o limite, use o nome inteiro
                                }

                                // Exibir o nome limitado
                                echo '<p class="nomeTeste">' . $nome_limitado . '</p>';
                                echo '<small class="autorTeste">' . $nomeEmpresa . '</small><br>';
                                echo '<small class="competenciasTeste">' . $area . '</small>';
                                echo '</div>';
                                echo '</div>';
                                echo '</article>';
                                echo '</a>';
                            }
                        }
                        echo '<div class="divBtnVerMais">';
                        echo '<a href="../todosTestes/todosTestes.php" class="btnVerMais">';
                        echo '<button class="verMais">Ver mais</button>';
                        echo '</a>';
                        echo '</div>';

                    } else {
                        echo "<p>Nenhum questionário encontrado.</p>";
                    }
                    ?>
                </a>
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
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.php">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="carrosselUltimosAnuncios.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="carrosselMinhasVagas.js"></script>
    <script src="carrosselTestes.js"></script>
    <script>
        // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
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