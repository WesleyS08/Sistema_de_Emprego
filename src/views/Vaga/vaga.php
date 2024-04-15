<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar o nome do usario

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';


// Verificar se o usuário está autenticado como empresa
$autenticadoComoPublicador = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa';

// definição de variaveis 
$emailUsuario = '';
$autenticadoComoEmpresa = false;
$candidatoInscrito = false;



// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado com e-mail/senha e for do tipo candidato
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && $_SESSION['google_usuario'] == 'candidato') {
    // Se estiver autenticado com o Google e for do tipo candidato
    $emailUsuario = $_SESSION['google_session'];
} else {
    // verificação para a possivel edição 
    $autenticadoComoEmpresa = false;
}

// verifica se o id da vafa doi mandando pela url 
if (isset($_GET['id'])) {
    if ($_con->connect_error) {
        die("Falha na conexão: " . $_con->connect_error);
    }

    // Obter o ID do anúncio da variável GET
    $idAnuncio = $_GET['id'];


    // Primeira consulta no banco de dados para informações do anúncio
    $sql = "SELECT Tb_Anuncios.*, Tb_Empresa.Nome_da_Empresa
    FROM Tb_Anuncios
    INNER JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
    INNER JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
    WHERE Tb_Anuncios.Id_Anuncios = $idAnuncio";


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
    }
    // Segunda consulta no banco de dados para pegar o CPF do candidato
    $sql = "SELECT Tb_Candidato.CPF
       FROM Tb_Candidato
       INNER JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
       WHERE Tb_Pessoas.Email = '$emailUsuario'";

    $result = mysqli_query($_con, $sql); // Executar a consulta
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result); // Obtém o resultado da consulta
        $cpfCandidato = $row['CPF']; // Armazena o CPF do candidato
    }

    // Verifica se o usuário já se inscreveu para a vaga
    if (isset($cpfCandidato)) {
        $sqlVerificaInscricao = "SELECT * FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = $idAnuncio AND Tb_Vagas_Tb_Empresa_CNPJ = '{$dadosAnuncio['Tb_Empresa_CNPJ']}' AND Tb_Candidato_CPF = '$cpfCandidato'";
        $resultVerificaInscricao = mysqli_query($_con, $sqlVerificaInscricao);
        if ($resultVerificaInscricao && mysqli_num_rows($resultVerificaInscricao) > 0) {
            $candidatoInscrito = true;
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

// Fechar a conexão com o banco de dados
mysqli_close($_con);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaga</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="vaga.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../../.."><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="#">Vagas</a></li>
            <li><a href="#">Pesquisar</a></li>
            <li><a href="#">Cursos</a></li>
            <li><a href="#">Perfil</a></li>
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


    <div class="divCommon">
        <div class="divTitulo" id="divTituloVaga">
            <h2 id="tituloVaga"><?php echo $Titulo; ?></h2>
            <label>Empresa: </label>
            <?php

            if (!empty($NomeEmpresa)) {
                echo '<a href="../PerfilRecrutador/perfilRecrutador.php?empresa_nome=' . urlencode($NomeEmpresa) . '">' . $NomeEmpresa . '</a><br>';
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
                ?>
            </label><br>
            <label>Status:</label>
            <?php if ($Status == 'Aberto') { ?>
                <label style="color: green;"><?php echo $Status; ?></label>
            <?php } else { ?>
                <label style="color: red;"><?php echo $Status; ?></label>
            <?php } ?>


        </div>
        <div class="container">
            <div class="divInformacoesIniciais">
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/pbbsmkso.json" trigger="loop" state="loop-rotate"
                        colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="nomeArea"><?php echo $Area; ?></label>
                </div>
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/surcxhka.json" trigger="loop" stroke="bold"
                        state="loop-roll" colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="cidade"><?php echo $Cidade; ?></label>
                    <label>,&nbsp;</label>
                    <label id="estado"><?php echo $Estado; ?></label>
                </div>
                <div class="divIconeENome">
                    <lord-icon src="https://cdn.lordicon.com/qvyppzqz.json" trigger="loop" stroke="bold"
                        state="loop-oscillate" colors="primary:#242424,secondary:#c74b16"
                        style="width:34px;height:34px">
                    </lord-icon>
                    <label id="cargaHoraria"><?php echo $Horario; ?></label>
                </div>
            </div>
            <div class="divInformacoesPrincipais">
                <div class="divFlexWithButton">
                    <div class="divFlex">
                        <div class="divDetalhesCurtos">
                            <div class="divIconeENome">
                                <img id="imgTipoProfissional" src="" class="icone">
                                <label id="tipoProfissional"><?php echo $Categoria; ?></label>
                            </div>
                            <div class="divIconeENome">
                                <img id="imgModalidade" src="" class="icone">
                                <label id="modalidade"><?php echo $Modalidade; ?></label>
                            </div>
                            <div class="divIconeENome">
                                <img id="imgJornada" src="" class="icone">
                                <label id="jornada"><?php echo $Jornada; ?></label>
                            </div>
                            <div class="divIconeENome">
                                <img id="imgNivel" src="" class="icone">
                                <label id="nivel"><?php echo $Nivel_Operacional; ?></label>
                            </div>
                        </div>
                        <div class="divDescricao">
                            <h3>Descrição da vaga</h3>
                            <p><?php echo $Descricao; ?></p>

                        </div>
                    </div>
                    <?php if ($autenticadoComoEmpresa) { ?>
                        <div class="divSendButton">
                            <?php if ($Status == 'Aberto') { ?>
                                <?php if (!$candidatoInscrito) { ?>
                                    <form method="POST" action="../../services/cadastros/processar_candidatura.php">
                                        <input type="hidden" name="id_anuncio" value="<?php echo $idAnuncio; ?>">
                                        <button type="submit">
                                            <h4>Candidatar-se</h4>
                                            <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover"
                                                colors="primary:#f5f5f5" style="width:80px;height:80px">
                                            </lord-icon>
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <button disabled>
                                        <h4>Já amInscrito</h4>
                                        <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover"
                                            colors="primary:#f5f5f5" style="width:80px;height:80px">
                                        </lord-icon>
                                    </button>
                                <?php } ?>
                            <?php } else { ?>
                                <p>Status: Encerrado</p>
                            <?php } ?>
                        </div>
                    <?php } ?>


                </div>
                <div class="divFlex" id="divBoxes">
                    <div class="divBox">
                        <h3>Requisitos</h3>
                        <p><?php echo $Requisitos; ?></p>
                    </div>
                    <div class="divBox">
                        <h3>Benefícios</h3>
                        <p><?php echo $Beneficios; ?></p>
                    </div>

                </div>
                <div id="map" style="height: 400px; margin-top:5%"></div>
            </div>
        </div>
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
</body>

</html>