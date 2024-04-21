<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar o nome do usario

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';


// Verificar se o usuário está autenticado como empresa
$autenticadoComoEmpresa = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa';

// Verificar se o usuário está autenticado como candidato
$autenticadoComoCandidato = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato';

// Definição de variáveis
$emailUsuario = '';
$candidatoInscrito = false;
$autenticadoComoPublicador = false;

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google
    $emailUsuario = $_SESSION['google_session'];
}

$sql = "SELECT Tb_Pessoas.Id_Pessoas
FROM Tb_Pessoas
WHERE Tb_Pessoas.Email = '$emailUsuario'";

$result = mysqli_query($_con, $sql); // Executar a consulta

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result); // Obtém o resultado da consulta
    $idPessoaUsuario = $row['Id_Pessoas']; // Armazena o ID da pessoa do usuário
}


// verifica se o id da vaga doi mandando pela url 
if (isset($_GET['id'])) {
    if ($_con->connect_error) {
        die("Falha na conexão: " . $_con->connect_error);
    }

    // Obter o ID do anúncio da variável GET
    $idAnuncio = $_GET['id'];

    // Primeira consulta no banco de dados para informações do anúncio
    $sql = "SELECT Tb_Anuncios.*, Tb_Empresa.Nome_da_Empresa, Tb_Empresa.Tb_Pessoas_Id AS Id_Pessoa_Empresa,
Tb_Vagas.Data_de_Termino
FROM Tb_Anuncios
INNER JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
INNER JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
WHERE Tb_Anuncios.Id_Anuncios = $idAnuncio";

    $result = mysqli_query($_con, $sql); // Executar a consulta SQL

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nomeEmpresa = $row['Nome_da_Empresa'];
        $idPessoaEmpresa = $row['Id_Pessoa_Empresa']; // Aqui está o ID da pessoa representando a empresa
        $Data_de_Termino = $row['Data_de_Termino']; // Aqui está a data de término do anúncio
    }

    // Verifica se retornou alguma coisa da pesquisa
    $result = mysqli_query($_con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $dadosAnuncio = mysqli_fetch_assoc($result);

        // Atribuição das informações do banco de dados a variáveis
        $Categoria = $dadosAnuncio['Categoria'];
        $Titulo = $dadosAnuncio['Titulo'];
        $Descricao = $dadosAnuncio['Descricao'];
        $Area = $dadosAnuncio['Area'];
        $Cidade = $dadosAnuncio['Cidade'];
        $Nivel_Operacional = $dadosAnuncio['Nivel_Operacional'];
        $Data_de_Criacao = $dadosAnuncio['Data_de_Criacao'];
        $Modalidade = $dadosAnuncio['Modalidade'];
        $Beneficios = $dadosAnuncio['Beneficios'];
        $Requisitos = $dadosAnuncio['Requisitos'];
        $Horario = $dadosAnuncio['Horario'];
        $Estado = $dadosAnuncio['Estado'];
        $Jornada = $dadosAnuncio['Jornada'];
        $CEP = $dadosAnuncio['CEP'];
        $Rua = $dadosAnuncio['Rua'];
        $bairro = $dadosAnuncio['Bairro'];
        $Numero = $dadosAnuncio['Numero'];
        $NomeEmpresa = $dadosAnuncio['Nome_da_Empresa'];
    } else {
        // Caso não seja encontrado nenhum anúncio com o ID fornecido
// Definir os campos como vazios
        $Categoria = '';
        $Titulo = '';
        $Descricao = '';
        $Area = '';
        $Cidade = '';
        $Nivel_Operacional = '';
        $Data_de_Criacao = '';
        $Modalidade = '';
        $Beneficios = '';
        $Requisitos = '';
        $Horario = '';
        $Estado = '';
        $Jornada = '';
        $CEP = '';
        $Numero = '';
        $NomeEmpresa = '';
        $Data_de_Termino = ''; // Definir também a data de término como vazia
    }

    // Segunda consulta no banco de dados para pegar o CPF do candidato e o CNPJ da empresa
    $sql = "SELECT Tb_Candidato.CPF, Tb_Empresa.CNPJ
   FROM Tb_Candidato
   INNER JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
   LEFT JOIN Tb_Empresa ON Tb_Candidato.Tb_Pessoas_Id = Tb_Empresa.Tb_Pessoas_Id
   WHERE Tb_Pessoas.Email = '$emailUsuario'";

    $result = mysqli_query($_con, $sql); // Executar a consulta
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // Obtém o resultado da consulta
        $cpfCandidato = $row['CPF']; // Armazena o CPF do candidato
        $cnpjEmpresa = $row['CNPJ']; // Armazena o CNPJ da empresa, se houver

        // Verifica se o usuário já se inscreveu para a vaga (apenas se for um candidato)

        if (isset($cpfCandidato) && !$autenticadoComoPublicador) {
            $sqlVerificaInscricao = "SELECT * FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = $idAnuncio AND Tb_Candidato_CPF = '$cpfCandidato'";
            $resultVerificaInscricao = mysqli_query($_con, $sqlVerificaInscricao);
            if ($resultVerificaInscricao && mysqli_num_rows($resultVerificaInscricao) > 0) {
                $candidatoInscrito = true;
            }
        }

    }


    // Terceira consulta para obter o status da vaga
    $sql2 = "SELECT Status FROM Tb_Vagas WHERE Tb_Anuncios_Id = $idAnuncio";
    $result2 = mysqli_query($_con, $sql2);

    if ($result2 && mysqli_num_rows($result2) > 0) {
        $row = mysqli_fetch_assoc($result2);
        $Status = $row['Status'];
    } else {
        // Defina um valor padrão para $Status se a consulta não retornar resultados
        $Status = '';
    }


    // Quarta consulta para verificar se a sessão é a mesma que postou a vaga
    $sql = "SELECT Tb_Pessoas.Email
        FROM Tb_Pessoas
        JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id
        JOIN Tb_Vagas ON Tb_Empresa.CNPJ = Tb_Vagas.Tb_Empresa_CNPJ
        WHERE Tb_Vagas.Tb_Anuncios_Id = $idAnuncio";

    $result = mysqli_query($_con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // A consulta foi bem-sucedida e retornou pelo menos uma linha
        $row = mysqli_fetch_assoc($result);
        $emailCriadorVaga = $row['Email'];

        if ($emailCriadorVaga == $emailUsuario) {
            // Se o usuário atual é o mesmo que criou a vaga, definir $autenticadoComopublicador como true
            $autenticadoComoPublicador = true; // Corrigido o nome da variável
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaga</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="minhaVaga.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
    <style>
        .modal {
            display: none;
            /* Esconde o modal por padrão */
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            /* Permite rolar se necessário */
            background-color: rgba(0, 0, 0, 0.5);
            /* Fundo translúcido para o overlay */
        }

        .modal-content {

            background-color: white;
            margin: 15% auto;
            /* Centraliza o modal na tela */
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            /* Tamanho do modal */
            text-align: center;
            /* Centraliza o texto */
            border-radius: 10px;
        }

        .modal-content p {
            text-align: center;
            padding: 10px 20px;

        }

        .modal-content .let {
            text-align: center;
            padding: 10px 20px;

        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .delete-button {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-button {
            background-color: gray;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover,
        .cancel-button:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <?php
        if ($autenticadoComoCandidato) {
            echo '<a href="../HomeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>';
            echo '<button class="btnModo"><img src="../../../imagens/moon.svg"></button>';
        } else {
            echo '<a href="../index.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>';
            echo '<button class="btnModo"><img src="../../../imagens/moon.svg"></button>';
        }
        ?>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <?php
            if ($autenticadoComoCandidato) {
                echo '<li><a href="../PerfilCandidato/perfilCandidato.php?id=' . $idPessoaUsuario . '">Perfil</a></li>';
            } else {
                echo '<li><a href="../Login/login.html? ">Perfil</a></li>';
            }
            ?>

        </ul>
    </nav>
    <?php if ($autenticadoComoPublicador == true) { ?>
        <?php
        echo '<a class="acessarEditarPerfil" href="../EditarVagaRecrutador/editarvagaRecrutador.php?id=' . $idAnuncio . '">';
        ?>
        <div style="padding: 6px 12px;
                box-shadow: 0px 0px 6px silver;
                display: flex;
                align-items: center;
                background-color: #000;
                color: whitesmoke;
                border-radius: 10px;
                width: 7%;
                margin-top: 1%;
                margin-left: 2%;">
            <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold" state="hover-line"
                colors="primary:#ffffff,secondary:#ffffff" style="width:30px;height:30px">
            </lord-icon>
            <label>Editar</label>
        </div>
        </a>
    <?php } ?>
    <?php if ($autenticadoComoPublicador == true): ?>
        <a class="acessarEditarPerfil" href="#"
            onclick="openModal('LInK do Arquivo para delete .php?id=<?php echo $idAnuncio; ?>&action=delete')">
            <div style="padding: 6px 12px;
                    box-shadow: 0px 0px 6px silver;
                    display: flex;
                    align-items: center;
                    background-color: #830404; /* Corrigido o duplo # */
                    color: whitesmoke;
                    border-radius: 10px;
                    width: 9%;
                    margin-top: -3%;
                    margin-left: 89%;">
                <lord-icon src="https://cdn.lordicon.com/wpyrrmcq.json" trigger="hover" colors="primary:#ffffff"
                    style="width:30px;height:30px">
                </lord-icon>
                <label>Deletar</label>
            </div>
        </a>
    <?php endif; ?>
    <!-- Modal de Confirmação -->
    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirmação de Deleção</h2>
            <p>Você tem certeza de que deseja deletar esta vaga?</p>
            <button class="cancel-button" onclick="closeModal()">Cancelar</button>
            <a id="confirmDeleteButton" href="#" class="delete-button">Deletar</a>
        </div>
    </div>


    <div class="divCommon">
        <div class="divTitulo" id="divTituloVaga">
            <h2 id="tituloVaga">
                <?php echo $Titulo; ?>
            </h2>
            <label>Empresa: </label>
            <?php
            if (!empty($NomeEmpresa)) {
                echo '<a href="../PerfilRecrutador/perfilRecrutador.php?id=' . $idPessoaEmpresa . '">' . $NomeEmpresa . '</a><br>';

            } else {
                echo '<a id="empresa">Confidencial</a><br>';
            }
            ?>
            <label>Data de anúncio: </label>
            <label>
                <?php
                // Extrai o dia, mês e ano da variável $Data_de_Criacao
                $dia = date('d', strtotime($Data_de_Criacao));
                $mes = date('m', strtotime($Data_de_Criacao));
                $ano = date('Y', strtotime($Data_de_Criacao));

                // Exibe a data no formato desejado (dia/mês/ano)
                echo "$dia/$mes/$ano";

                // Se o status for 'Encerrado', exibe a data de encerramento das inscrições
                if ($Status == 'Encerrado') {
                    // Verifica se a data de término está definida
                    if (!empty($Data_de_Termino)) {
                        // Formata a data de término para o formato desejado (dia/mês/ano)
                        $dataFormatada = date('d/m/Y \a\s H:i', strtotime($Data_de_Termino));
                        // Exibe a mensagem com a data formatada
                        echo " (Inscrições encerradas em $dataFormatada) ";
                    }
                }
                ?>
            </label><br>
            <label>Status:</label>
            <?php if ($Status == 'Aberto') { ?>
                <label style="color: green;">
                    <?php echo $Status; ?>
                </label>
            <?php } else { ?>
                <label style="color: red;">
                    <?php echo $Status; ?>
                </label>
            <?php } ?>


        </div>
        <div class="container">
            <div class="divInformacoesIniciais">
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/pbbsmkso.json" trigger="loop" state="loop-rotate"
                        colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="nomeArea">
                        <?php echo $Area; ?>
                    </label>
                </div>
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/surcxhka.json" trigger="loop" stroke="bold"
                        state="loop-roll" colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="cidade">
                        <?php echo $Cidade; ?>
                    </label>
                    <label>,&nbsp;</label>
                    <label id="estado">
                        <?php echo $Estado; ?>
                    </label>
                </div>
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/qvyppzqz.json" trigger="loop" stroke="bold"
                        state="loop-oscillate" colors="primary:#242424,secondary:#c74b16"
                        style="width:34px;height:34px">
                    </lord-icon>
                    <label id="cargaHoraria">
                        <?php echo $Horario; ?>
                    </label>
                </div>
            </div>
            <div class="divInformacoesPrincipais">
                <div class="divFlexWithButton">
                    <div class="divFlex">
                        <div class="divDetalhesCurtos">
                            <div class="divIconeENome">
                                <img id="imgTipoProfissional" src="" class="icone">
                                <label id="tipoProfissional">
                                    <?php echo $Categoria; ?>
                                </label>
                            </div>

                            <div class="divIconeENome">
                                <img id="imgModalidade" src="" class="icone">
                                <label id="modalidade">
                                    <?php echo $Modalidade; ?>
                                </label>
                            </div>
                            <div class="divIconeENome">
                                <img id="imgJornada" src="" class="icone">
                                <label id="jornada">
                                    <?php echo $Jornada; ?>
                                </label>
                            </div>
                            <div class="divIconeENome">
                                <img id="imgNivel" src="" class="icone">
                                <label id="nivel">
                                    <?php echo $Nivel_Operacional; ?>
                                </label>
                            </div>
                        </div>
                        <div class="divDescricao">
                            <h3>Descrição da vaga</h3>
                            <p>
                                <?php echo $Descricao; ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($autenticadoComoPublicador == false) { ?>
                    <?php }
                    if ($autenticadoComoCandidato == false) { ?>
                    <?php } elseif ($Status == 'Aberto' && !$candidatoInscrito) { ?>
                        <form method="POST"
                            action="../../services/cadastros/processar_candidatura.php?id_anuncio=<?php echo $idAnuncio; ?>">
                            <div class="divSendButton">
                                <button>
                                    <h4>Candidatar-se</h4>
                                    <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover"
                                        colors="primary:#f5f5f5" style="width:80px;height:80px">
                                    </lord-icon>
                                </button>
                            </div>
                        </form>
                    <?php } elseif ($candidatoInscrito) { ?>
                        <div class="divSendButton">
                            <button disabled style="cursor: default; background-color:  #723911">
                                <h4>Já inscrito</h4>
                                <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover"
                                    colors="primary:#f5f5f5" style="width:80px;height:80px">
                                </lord-icon>
                            </button>
                        </div>
                    <?php } elseif ($Status != 'Aberto') { ?>
                        <p>Status: Encerrado</p>
                    <?php } ?>



                </div>
                <?php
                // Divida os requisitos e benefícios por vírgula e remova espaços em branco desnecessários
                $arrayRequisitos = array_filter(array_map('trim', explode(',', $dadosAnuncio['Requisitos'])));
                $arrayBeneficios = array_filter(array_map('trim', explode(',', $dadosAnuncio['Beneficios'])));
                ?>

                <div class="divFlex" id="divBoxes">
                    <div class="divBox">
                        <h3>Requisitos</h3>
                        <ul>
                            <?php foreach ($arrayRequisitos as $requisito) { ?>
                                <li><?php echo $requisito; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="divBox">
                        <h3>Benefícios</h3>
                        <ul>
                            <?php foreach ($arrayBeneficios as $beneficio) { ?>
                                <li><?php echo $beneficio; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="divCarrossel">
        <div class="divTitulo">
            <h2>Candidaturas</h2>
        </div>
        <div class="container">
            <a class="btnLeftSlider" id="leftPerfis">
                <</a>
                    <a class="btnRightSlider" id="rightPerfis">></a>
                    <div class="carrosselBox" id="carrosselPerfis">
                        <?php
                        $sqlCandidatos = "SELECT c.*, c.Img_Perfil AS Foto_Perfil, p.Nome
                                FROM Tb_Candidato c
                                JOIN Tb_Pessoas p ON c.Tb_Pessoas_Id = p.Id_Pessoas
                                JOIN Tb_Inscricoes i ON c.CPF = i.Tb_Candidato_CPF
                                WHERE i.Tb_Vagas_Tb_Anuncios_Id = $idAnuncio";

                        $resultCandidatos = mysqli_query($_con, $sqlCandidatos);

                        // Verificar se a consulta retornou resultados
                        if ($resultCandidatos && mysqli_num_rows($resultCandidatos) > 0) {
                            // Loop sobre as informações das candidaturas
                            while ($candidatura = mysqli_fetch_assoc($resultCandidatos)) {
                                ?>
                                <a class="perfilLink"
                                    href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $candidatura['Tb_Pessoas_Id']; ?>">
                                    <article class="perfil">
                                        <div class="divImg">
                                            <!-- Mostrar a imagem de perfil -->
                                            <img src="<?php echo $candidatura['Img_Perfil']; ?>" alt="Foto de Perfil"
                                                style="width: 100%;height: 99%;display: block;border-radius: 50%;object-fit: cover;">
                                        </div>

                                        <section>
                                            <p class="nomePessoa"><?php echo $candidatura['Nome']; ?></p>
                                        </section>
                                        <section>
                                            <?php
                                            $limite_caracteres = 55; // Defina o limite de caracteres desejado
                                            $autodefinicao = $candidatura['Autodefinicao']; // Atribua a string à uma variável para facilitar o acesso
                                    
                                            if (strlen($autodefinicao) > $limite_caracteres) {
                                                $autodefinicao = substr($autodefinicao, 0, $limite_caracteres) . '...'; // Adiciona os pontos suspensivos
                                            }
                                            ?>
                                            <small class="descricaoPessoa"><?php echo $autodefinicao; ?></small>
                                        </section>
                                    </article>
                                </a>
                                <?php
                            }

                        } else {
                            // Caso não haja candidatos inscritos
                            echo "<p style='margin-left: 36%;'>Não há candidatos inscritos para esta vaga.</p>";

                        }
                        ?>
                    </div>
        </div>
    </div>


    <div id="map" style="height: 400px;margin-top: 1%;position: relative;outline: none;width: 95%;margin-left: 3%;">
    </div>

    <div id="endereco" style="display: none;" data-rua="<?php echo $Rua; ?>" data-numero="<?php echo $Numero; ?>"
        data-bairro="<?php echo $bairro; ?>" data-cidade="<?php echo $Cidade; ?>" data-estado="<?php echo $Estado; ?>"
        data-cep="<?php echo $CEP; ?>">
    </div>





    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="trocaIcones.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="carrosselPerfis.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
    <script>
        // Função para inicializar o mapa com base no endereço fornecido
        function initMap(latitude, longitude) {
            var map = L.map('map').setView([latitude, longitude], 30);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([latitude, longitude]).addTo(map)
                .bindPopup('<?php echo !empty($NomeEmpresa) ? $NomeEmpresa : "Confidencial"; ?>')
                .openPopup();


        }


        // Obtenha as informações de endereço do HTML
        var enderecoDiv = document.getElementById('endereco');
        var rua = enderecoDiv.getAttribute('data-rua');
        var numero = enderecoDiv.getAttribute('data-numero');
        var bairro = enderecoDiv.getAttribute('data-bairro');
        var cidade = enderecoDiv.getAttribute('data-cidade');
        var estado = enderecoDiv.getAttribute('data-estado');
        var cep = enderecoDiv.getAttribute('data-cep');

        // Verifique se todas as variáveis de endereço estão definidas e têm valores
        if (rua && numero && bairro && cidade && estado && cep) {
            var enderecoCompleto = rua + ', ' + numero + ', ' + bairro + ', ' + cidade + ', ' + estado + ', ' + cep;

            // Faça a solicitação de geocodificação
            var url = "https://api.tomtom.com/search/2/geocode/" + encodeURIComponent(enderecoCompleto) + ".json?key=RXAoSJz7XgFFtKpBXExayEkmyWdYw7xt";
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        var latitude = data.results[0].position.lat;
                        var longitude = data.results[0].position.lon;
                        initMap(latitude, longitude);
                    } else {
                        console.error("Nenhum resultado encontrado para o endereço fornecido.");
                    }
                })
                .catch(error => {
                    console.error("Erro ao fazer a solicitação:", error);
                });
        } else {
            console.error("Alguma das variáveis de endereço não está definida ou está vazia.");
        }
    </script>
    <script>
        function openModal(deleteUrl) {
            // Define o URL de deleção
            document.getElementById("confirmDeleteButton").setAttribute("href", deleteUrl);
            // Exibe o modal
            document.getElementById("confirmDeleteModal").style.display = "block";
        }

        function closeModal() {
            // Fecha o modal
            document.getElementById("confirmDeleteModal").style.display = "none";
        }

        document.querySelector(".close-button").addEventListener("click", closeModal);

        // Fecha o modal ao clicar fora do modal
        window.onclick = function (event) {
            if (event.target == document.getElementById("confirmDeleteModal")) {
                closeModal();
            }
        };

    </script>
</body>

</html>