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

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="homeRecrutador.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a id="logo" href="homeRecrutador.php">SIAS</a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="#">Anunciar</a></li>
            <li><a href="#">Minhas vagas</a></li>
            <li><a href="#">Meus testes</a></li>
            <li><a href="#">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTitle">
        <div class="divTituloDigitavel">
            <h1 id="tituloAutomatico">B</h1>
            <i></i>
        </div>
        <p>Anuncie uma vaga e encontre o candidato ideal para sua empresa!<br>É fácil e conveniente - clique agora
            mesmo!</p>
        <button onclick="window.location.href='../criarVaga/criarVaga.php'">Anunciar</button>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Meus anúncios</h2>
            <button onclick="window.location.href='../criarVaga/criarVaga.php'" class="adicionar">+</button>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftAnuncios">
                <</a>
                    <a class="btnRightSlider" id="rightAnuncios">></a>
                    <div class="carrosselBox" id="carrosselAnuncios">
                        <?php
                        // Verifique se há resultados antes de iniciar o loop
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<a class="postLink">';
                                echo '<article class="post">';
                                echo '<div class="divAcessos">';
                                echo '<img src="../../../imagens/people.svg"></img>';
                                echo '<small class="qntdAcessos">28</small>';
                                echo '</div>';
                                echo '<header>';
                                echo '<img src="../../../imagens/estagio.svg">';

                                if (isset($row["Categoria"])) {
                                    echo '<label class="tipoVaga" style="color:#191970">' . $row["Categoria"] . '</label>';
                                } else {
                                    echo '<label class="tipoVaga" style="color:#191970">Categoria não definida</label>';
                                }

                                echo '</header>';
                                echo '<section>';
                                echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
                                echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida") . '</p>';
                                echo '</section>';
                                echo '<p class="statusVaga" style="color: green;">Aberta</p>';
                                $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
                                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                                echo '</article>';
                                echo '</a>';
                            }
                        } else {
                            // Se não houver resultados, exiba uma mensagem alternativa
                            echo '<p>Nenhuma postagem encontrada</p>';
                        }
                        ?>
                    </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Minhas avaliações</h2>
            <button class="adicionar">+</button>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftTestes">
                <</a>
                    <a class="btnRightSlider" id="rightTestes">></a>
                    <div class="carrosselBox" id="carrosselTestes">
                        <a class="testeLink">
                            <article class="teste">
                                <div class="divAcessos">
                                    <img src=".../../../imagens/people.svg"></img>
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
                        <a class="testeLink">
                            <article class="teste">
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
                        <a class="testeLink">
                            <article class="teste">
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
                        <a class="testeLink">
                            <article class="teste">
                                <div class="divAcessos">
                                    <img src="../../../imagens/people.svg"></img>
                                    <small class="qntdAcessos">800</small>
                                </div>
                                <img src="../../../imagens/python.svg"></img>
                                <div class="divDetalhesTeste">
                                    <div>
                                        <p class="nomeTeste">Python Básico</p>
                                        <small class="competenciasTeste">Estágio, TI, Administração, Negócios</small>
                                        <div>
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
                <</a>
                    <a class="btnRightSlider" id="rightPerfis">></a>
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
                        <img src="../../../imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../../imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/css.svg">
                    </div>

                    <div class="slide">
                        <img src="../../../imagens/mysql.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/php.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/firebase.svg">
                    </div>
                    <div class="slide">
                        <img class="logoFatec" src="../../../imagens/fatec.png">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/javascript.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/html.svg">
                    </div>
                    <div class="slide">
                        <img src="../../../imagens/css.svg">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="modoNoturno.js"></script>
    <script src="carrosselAnuncios.js"></script>
    <script src="carrosselTestes.js"></script>
    <script src="carrosselPerfis.js"></script>
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