<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
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

// Quartar consulta para selecionar o tema que  a pessoa selecionou 
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

$sql = "SELECT e.CNPJ
        FROM Tb_Pessoas p
        INNER JOIN Tb_Empresa e ON p.Id_Pessoas = e.Tb_Pessoas_Id
        WHERE p.Id_Pessoas = '$idPessoa'";

$result = $_con->query($sql);

if ($result->num_rows > 0) {
    // Armazenar o CNPJ da empresa na variável $cnpj_empresa
    $row = $result->fetch_assoc();
    $cnpj_empresa = $row["CNPJ"];
}
// Preparar a consulta para obter o nome da empresa
$sql_nome_empresa = "SELECT Nome_da_Empresa FROM Tb_Empresa WHERE CNPJ = ?";
$stmt = $_con->prepare($sql_nome_empresa);

// Bind the parameter
$stmt->bind_param("s", $cnpj_empresa);

// Execute
$stmt->execute();

// Fetch result
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nome_empresa = $row['Nome_da_Empresa'];
} else {
    // Handle no results case
    $nome_empresa = 'Empresa não encontrada';
}
// Consulta para contar os anúncios por Id_Pessoas
$sql_contar_anuncios = "SELECT COUNT(*) AS total_anuncios FROM Tb_Anuncios 
    JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
    JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
    JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
    WHERE Tb_Pessoas.Id_Pessoas = ?";
$stmt = $_con->prepare($sql_contar_anuncios);

if ($stmt) {
    // Vincule o parâmetro
    $stmt->bind_param("i", $idPessoa);

    // Execute a consulta
    $stmt->execute();

    // Obtenha o resultado
    $result = $stmt->get_result();

    // Verifique se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Obtenha o total de anúncios
        $row = $result->fetch_assoc();
        $total_anuncios = $row['total_anuncios'];

    }
}


// Nova consulta para obter os Anuncios por Id_Pessoas
$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
    JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
    JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
    JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
    WHERE Tb_Pessoas.Id_Pessoas = ?";

$stmt = $_con->prepare($sql_verificar_empresa);
$stmt->bind_param("i", $idPessoa); // 'i' indica que o parâmetro é um número inteiro
$stmt->execute();
$result = $stmt->get_result();

// Verificar se a consulta teve sucesso
if ($result === false) {
    // Tratar o erro, se necessário
    echo "Erro na consulta: " . $_con->error;
    exit;
}


// Atribuindo os valores das variáveis de sessão
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';

// Terceira consulta para obter o status da vaga
$sql2 = "SELECT V.* 
    FROM Tb_Vagas V
    JOIN Tb_Empresa E ON V.Tb_Empresa_CNPJ = E.CNPJ
    JOIN Tb_Pessoas P ON E.Tb_Pessoas_Id = P.Id_Pessoas
    WHERE P.Email = ?";

$stmt = mysqli_prepare($_con, $sql2);
if ($stmt) {
    // Vincule o parâmetro ao placeholder na consulta
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Substitua $email pelo valor real do email
    $email = $emailUsuario;

    // Execute a declaração
    mysqli_stmt_execute($stmt);

    // Obtenha o resultado da consulta
    $result2 = mysqli_stmt_get_result($stmt);

    if ($result2 && mysqli_num_rows($result2) > 0) {
        $row = mysqli_fetch_assoc($result2);
        $Status = $row['Status'];
    } else {
        // Defina um valor padrão para $Status se a consulta não retornar resultados
        $Status = '';
    }
    // Feche a declaração
    mysqli_stmt_close($stmt);
} else {
    echo "Erro na preparação da declaração: " . mysqli_error($_con);
}

$totaldisponivel = 4 - $total_anuncios;

// Recupere o CNPJ da empresa associada ao e-mail do usuário
$sql_empresa = "SELECT e.CNPJ 
                FROM Tb_Empresa e 
                INNER JOIN Tb_Pessoas p ON e.Tb_Pessoas_Id = p.Id_Pessoas 
                WHERE p.Email = ?";
$stmt_empresa = $_con->prepare($sql_empresa);
if ($stmt_empresa) {
    // Vincule o parâmetro
    $stmt_empresa->bind_param("s", $emailUsuario);

    // Execute a consulta
    $stmt_empresa->execute();

    // Obtenha o resultado
    $result_empresa = $stmt_empresa->get_result();

    // Verifique se a consulta retornou resultados
    if ($result_empresa->num_rows > 0) {
        // Obtenha o CNPJ da empresa
        $row_empresa = $result_empresa->fetch_assoc();
        $cnpj_empresa = $row_empresa["CNPJ"];

        // Consulta SQL para selecionar as avaliações apenas da empresa específica
        $sql_avaliacoes = "SELECT q.Id_Questionario, q.Nome, q.Area, e.Nome_da_Empresa 
                           FROM Tb_Questionarios q
                           INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
                           INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
                           WHERE e.CNPJ = ?";

        $stmt_avaliacoes = $_con->prepare($sql_avaliacoes);
        if ($stmt_avaliacoes) {
            // Vincule o parâmetro
            $stmt_avaliacoes->bind_param("s", $cnpj_empresa);

            // Execute a consulta
            $stmt_avaliacoes->execute();

            // Obtenha o resultado
            $result_avaliacoes = $stmt_avaliacoes->get_result();
        }
    }
}

// Recupere o CNPJ da empresa associada ao e-mail do usuário
$sql_empresa = "SELECT e.CNPJ 
        FROM Tb_Empresa e 
        INNER JOIN Tb_Pessoas p ON e.Tb_Pessoas_Id = p.Id_Pessoas 
        WHERE p.Email = ?";
$stmt_empresa = $_con->prepare($sql_empresa);
if ($stmt_empresa)
    // Vincule o parâmetro
    $stmt_empresa->bind_param("s", $emailUsuario);

// Execute a consulta
$stmt_empresa->execute();

// Obtenha o resultado
$result_empresa = $stmt_empresa->get_result();

// Verifique se a consulta retornou resultados
if ($result_empresa->num_rows > 0)
    // Obtenha o CNPJ da empresa
    $row_empresa = $result_empresa->fetch_assoc();
$cnpj_empresa = $row_empresa["CNPJ"];

// Consulta SQL para selecionar as avaliações apenas da empresa específica
$sql_avaliacoes = "SELECT q.Id_Questionario, q.Nome, q.Area, q.ImagemQuestionario, e.Nome_da_Empresa
                    FROM Tb_Questionarios q
                    INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
                    INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
                    WHERE e.CNPJ = ?";

$stmt_avaliacoes = $_con->prepare($sql_avaliacoes);
if ($stmt_avaliacoes)
    // Vincule o parâmetro
    $stmt_avaliacoes->bind_param("s", $cnpj_empresa);

// Execute a consulta
$stmt_avaliacoes->execute();

// Obtenha o resultado
$result_avaliacoes = $stmt_avaliacoes->get_result();

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
            const chavesParaLimpar = ['tipos', 'area', 'apenasVagasAbertas', 'termoPesquisa', 'btnJovemAprendizState', 'btnEstagioState', 'btnCltState', 'btnPjState'];

            chavesParaLimpar.forEach(function (chave) {
                localStorage.removeItem(chave);
            });

            const areaPadrao = 'Todas'; // Valor padrão para "area"
            if (!localStorage.getItem('area')) {
                localStorage.setItem('area', areaPadrao);
            }
        }

        window.addEventListener('load', function () {
            limparLocalStorageComExcecao();
            console.table(localStorage);
        });
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
        <img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
            <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagas</a></li>
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li><!--Arrumar esse link  -->
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divCentraliza">
            <div>
                <div class="divTituloDigitavel">
                    <h1 id="tituloAutomatico">B</h1>
                    <i></i>
                </div>
                <p class="sinopse">Anuncie uma vaga e encontre o candidato ideal para sua empresa!<br>É fácil e
                    conveniente - clique
                    agora
                    mesmo!</p>
                <button onclick="window.location.href='../criarVaga/criarVaga.php'" class="btnInicial">Anunciar</button>
            </div>
        </div>
    </div>
    <?php
    if ($verificado == 1) {
    } else {
        echo "<div class='aviso-nao-verificado'>Sua conta ainda não foi verificada. Por favor, verifique sua conta para desfrutar do máximo possível.</div>";
        $totaldisponivel = 4 - $total_anuncios;
        echo "<div class='aviso-nao-verificado'>Você tem mais " . $totaldisponivel . " anúncios disponíveis.</div>";
    }
    ?>
    <div id="infoTema"></div>
    <div class="divCarrossel">
        <div class="divTituloComBtn">
            <h2>Meus anúncios</h2>
            <button class="btnAdicionar" onclick="window.location.href='../criarVaga/criarVaga.php'">
                <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                    state="hover-rotation" colors="primary:#000000,secondary:#ffffff" style="width:40px;height:40px">
                </lord-icon>
            </button>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftAnuncios">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightAnuncios">
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
            <div class="carrosselBox" id="carrosselAnuncios">
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
                    echo '<a class="postLink" href="../MinhaVaga/Minhavaga.php?id=' . $row["Id_Anuncios"] . '">';
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
        <div class="divTituloComBtn">
            <h2>Minhas avaliações</h2>
            <button class="btnAdicionar" onclick="window.location.href='../criarTeste/criarTeste.php'">
                <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                    state="hover-rotation" colors="primary:#000000,secondary:#ffffff" style="width:40px;height:40px">
                </lord-icon>
            </button>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftTestes">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightTestes">
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
            <div class="carrosselBox" id="carrosselTestes">
                <?php
                if ($result_avaliacoes->num_rows > 0) {
                    // Loop através dos resultados da consulta
                    while ($row_avaliacao = $result_avaliacoes->fetch_assoc()) {
                        // Extrai os dados do teste
                        $idQuestionario = $row_avaliacao['Id_Questionario'];
                        $nome = $row_avaliacao['Nome'];
                        $area = $row_avaliacao['Area'];
                        $img = $row_avaliacao['ImagemQuestionario'];
                        $nomeEmpresa = $row_avaliacao['Nome_da_Empresa'];
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

                            // Saída HTML para cada teste no carrossel
                            echo '<a class="testeCarrosselLink" href="../PreparaTeste/preparaTeste.php?id=' . $idQuestionario . '">';
                            echo '<article class="testeCarrossel">';
                            echo '<div class="divAcessos">';
                            echo '<img src="../../../imagens/people.svg"></img>';
                            echo '<small class="qntdAcessos">' . $total_respostas . '</small>';
                            echo '</div>';
                            echo '<img class="imgTeste" src="' . $row_avaliacao['ImagemQuestionario'] . '"></img>'; // Exibindo a imagem do Excel do banco de dados
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
                            echo '<small class="competenciasTeste">' . $area . '</small>';
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
                    echo "<p style='text-align:center; margin:0 auto;'>Nenhum teste encontrado.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Perfis de usuários</h2>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftPerfis">
                <img src="../../assets/images/icones_diversos/leftSlider.svg">
            </a>
            <a class="btnRightSlider" id="rightPerfis">
                <img src="../../assets/images/icones_diversos/rightSlider.svg">
            </a>
            <div class="carrosselBox" id="carrosselPerfis">
                <?php
                $sqlCandidatos = "SELECT p.Id_Pessoas AS Pessoa_Id, p.Nome AS Nome, c.Autodefinicao AS Autodefinicao, c.Img_Perfil AS Img_Perfil
                FROM Tb_Inscricoes i
                INNER JOIN Tb_Candidato c ON i.Tb_Candidato_CPF = c.CPF
                INNER JOIN Tb_Pessoas p ON c.Tb_Pessoas_Id = p.Id_Pessoas
                WHERE i.Tb_Vagas_Tb_Empresa_CNPJ = '$cnpj_empresa'";

                $resultCandidatos = mysqli_query($_con, $sqlCandidatos);

                // Verificar se a consulta retornou resultados
                if ($resultCandidatos && mysqli_num_rows($resultCandidatos) > 0) {
                    // Loop sobre as informações das candidaturas
                    while ($candidatura = mysqli_fetch_assoc($resultCandidatos)) {
                        ?>
                        <a class="perfilLink"
                            href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $candidatura['Pessoa_Id']; ?>">
                            <article class="perfil">
                                <div class="divImg">
                                    <!-- Mostrar a imagem de perfil -->
                                    <img src="<?php echo $candidatura['Img_Perfil']; ?>" alt=""
                                        style="width: 100%;height: 99%;display: block;border-radius: 50%;object-fit: cover;">
                                </div>

                                <section>
                                    <p class="nomePessoa">
                                        <?php echo $candidatura['Nome']; ?>
                                    </p>
                                </section>
                                <section>
                                    <?php
                                    $limite_caracteres = 55;
                                    $autodefinicao = $candidatura['Autodefinicao']; // Atribua a string à uma variável para facilitar o acesso
                            
                                    if (strlen($autodefinicao) > $limite_caracteres) {
                                        $autodefinicao = substr($autodefinicao, 0, $limite_caracteres) . '...'; // Adiciona os pontos suspensivos
                                    }
                                    ?>
                                    <small class="descricaoPessoa">
                                        <?php echo $autodefinicao; ?>
                                    </small>
                                </section>
                            </article>
                        </a>
                        <?php
                    }

                } else {
                    // Caso não haja candidatos inscritos
                    echo "<p style='text-align:center; margin:0 auto;'>Não há candidatos inscritos para suas vagas.</p>";

                }
                ?>

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
        <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>


    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="carrosselAnuncios.js"></script>
    <script src="carrosselTestes.js"></script>
    <script src="carrosselPerfis.js"></script>
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