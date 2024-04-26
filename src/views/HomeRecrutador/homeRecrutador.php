<?php
include "../../services/conexão_com_banco.php";

session_start();


// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    // Se estiver autenticado com e-mail/senha e for do tipo empresa
    $emailUsuario = $_SESSION['email_session'];

} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    // Se estiver autenticado com o Google e for do tipo empresa
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
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

$sql_verificar_empresa = "SELECT * FROM Tb_Anuncios 
JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
WHERE Tb_Pessoas.Email = ?";

$stmt = $_con->prepare($sql_verificar_empresa);
$stmt->bind_param("s", $emailUsuario);
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

// Segunda consulta para obter o status da vaga
$sql2 = "SELECT V.* 
FROM Tb_Vagas V
JOIN Tb_Empresa E ON V.Tb_Empresa_CNPJ = E.CNPJ
JOIN Tb_Pessoas P ON E.Tb_Pessoas_Id = P.Id_Pessoas
WHERE P.Email = ?";

// Prepare a declaração
$stmt = mysqli_prepare($_con, $sql2);

// Verifique se a preparação da declaração foi bem-sucedida
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
    // Se a preparação da declaração falhar, lide com o erro aqui
    echo "Erro na preparação da declaração: " . mysqli_error($_con);
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
        <a href="homeRecrutador.html"><img id="logo"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
            <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagass</a></li>
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li>
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
                <p>Anuncie uma vaga e encontre o candidato ideal para sua empresa!<br>É fácil e conveniente - clique
                    agora
                    mesmo!</p>
                <button onclick="window.location.href='../criarVaga/criarVaga.php'" class="btnInicial">Anunciar</button>
            </div>
        </div>
    </div>
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
    <div class="divCarrossel">
        <div class="divTituloComBtn">
            <h2>Minhas avaliações</h2>
            <button class="btnAdicionar">
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
                        <a class="testeCarrosselLink">
                            <article class="testeCarrossel">
                                <div class="divAcessos">
                                    <img src="../../../imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="../../../imagens/excel.svg"></img>
                                <div class="divDetalhesTeste">
                                    <div>
                                        <p class="nomeTeste">Excel Básico</p>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <a class="testeCarrosselLink">
                            <article class="testeCarrossel">
                                <div class="divAcessos">
                                    <img src="../../../imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="../../../imagens/figma.svg"></img>
                                <div class="divDetalhesTeste">
                                    <div>
                                        <p class="nomeTeste">Figma Intermediário</p>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <a class="testeCarrosselLink">
                            <article class="testeCarrossel">
                                <div class="divAcessos">
                                    <img src="../../../imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="../../../imagens/word.svg"></img>
                                <div class="divDetalhesTeste">
                                    <div>
                                        <p class="nomeTeste">Word Avançado</p>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </article>
                        </a>
                        <a class="testeCarrosselLink">
                            <article class="testeCarrossel">
                                <div class="divAcessos">
                                    <img src="../../../imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="../../../imagens/python.svg"></img>
                                <div class="testeDetalhes">
                                    <div>
                                        <p class="nomeTeste">Python Básico</p>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                    </div>
                                </div>
                            </article>
                        </a>
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
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
                        </a>
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
                        </a>
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
                        </a>
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
                        </a>
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
                        </a>
                        <a class="perfilLink">
                            <article class="perfil">
                                <div class="divImg"></div>
                                <section>
                                    <p class="nomePessoa">Clarice Josefina</p>
                                </section>
                                <section>
                                    <small class="descricaoPessoa">Dev Front-End | Designer Digital | Ciências de dados
                                        | Azure</small>
                                </section>
                            </article>
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
                        <img src="../../assets/images/logos_parceiros/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../assets/images/logos_parceiros/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/css.svg">
                    </div>
                    
        
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../assets/images/logos_parceiros/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../assets/images/logos_parceiros/css.svg">
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


    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="carrosselAnuncios.js"></script>
    <script src="carrosselTestes.js"></script>
    <script src="carrosselPerfis.js"></script>    
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