<?php
include '../../../services/conexão_com_banco.php';
session_start(); // Sempre inicie a sessão no início do arquivo



// Definição de variáveis com valores padrão
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Anônimo';
$emailUsuario = '';
$candidatoInscrito = false;

// Verificar se há um e-mail na sessão
if (isset($_SESSION['email_session'])) {
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    $emailUsuario = $_SESSION['google_session'];
}

// Verificar se o usuário está autenticado como candidato
$autenticadoComoCandidato = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato';

// Definir se a sessão tem um e-mail
$autenticadoComEmail = !empty($emailUsuario);

// Verificar se é uma sessão de candidato com e-mail
$autenticadoCandidatoComEmail = $autenticadoComEmail && $autenticadoComoCandidato;

$sql = "SELECT Tb_Pessoas.Id_Pessoas
FROM Tb_Pessoas
WHERE Tb_Pessoas.Email = '$emailUsuario'";

$result = mysqli_query($_con, $sql); // Executar a consulta

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result); // Obtém o resultado da consulta
    $idPessoa = $row['Id_Pessoas']; // Armazena o ID da pessoa do usuário
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

    }
    $sqlVerificaInscricao = "
    SELECT 1  -- O número '1' é suficiente para verificar a existência
    FROM Tb_Inscricoes 
    WHERE Tb_Vagas_Tb_Anuncios_Id = ?
      AND Tb_Candidato_CPF = ?
    LIMIT 1  -- Limita a uma linha, pois só queremos saber se há inscrição
";

    // Preparar a consulta para prevenir injeção de SQL
    $stmt = $_con->prepare($sqlVerificaInscricao);

    // Vincular parâmetros para segurança
    $stmt->bind_param('is', $idAnuncio, $cpfCandidato);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado
    $result = $stmt->get_result();

    // Verificar se há alguma linha
    $candidatoInscrito = ($result->num_rows > 0);

    // Se o candidato já está inscrito, resultado será true
    if ($candidatoInscrito) {

        $candidatoInscrito = false;
    } else {
        // Se o resultado for falso, significa que o candidato não está inscrito
        $candidatoInscrito = true;
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

}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaga</title>
    <link rel="stylesheet" type="text/css" href="../../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="vaga.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
</head>

<body>
    <?php
    if ($idPessoa) {
        echo '<nav>';
        echo '    <input type="checkbox" id="check"> ';
        echo '    <label for="check" class="menuBtn">';
        echo '        <img src="../../../imagens/menu.svg">';
        echo '    </label> ';
        echo '<a href="../HomeCandidato/homeCandidato.php"><img id="logo" src="../../../assets/images/logos_empresa/logo_sias.png"></a> ';
        echo '<button class="btnModo"><img src="../../../imagens/moon.svg"></button>';
        echo '<ul>';
        echo '   <li><a href="../TodososAnuncios/todasvagas.php">Vagas</a></li>';
        echo '    <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>';
        echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
        echo '  <li><a href="../../../index.php">Deslogar</a></li>';
        echo '    <li><a href="../PerfilCandidato/perfilCandidato.php?id=' . $idPessoa . '">Perfil</a></li>';
        echo '</ul>';
        echo '</nav>';
    } else {
        // Se não for autenticado como candidato, mostrar menu padrão ou genérico
        echo '<nav>';
        echo '    <input type="checkbox" id="check"> ';
        echo '    <label for="check" class="menuBtn">';
        echo '        <img src="../../../imagens/menu.svg">';
        echo '    </label> ';
        echo '<a href="../../../../index.php"><img id="logo" src="../../../assets/images/logos_empresa/logo_sias.png"></a> ';
        echo '<ul>';
        echo '    <li><a href="../TodososAnuncios/todasvagas.php">Vagas</a></li>';
        echo '    <li><a href="../Login/login.html">Testes</a></li>';
        echo '    <li><a href="#">Cursos</a></li>';
        echo '    <li><a href="../Login/login.html">Perfil</a></li>';
        echo '</ul>';
        echo '</nav>';
    }
    ?>
    <div class="divCommon">
        <div class="divTitulo" id="divTituloVaga">
            <h2 id="tituloVaga">
                <?php echo $Titulo; ?>
            </h2>
            <label class="infos">Empresa: </label>
            <?php
            if (!empty($NomeEmpresa)) {
                echo '<a href="../../Empresa/PerfilRecrutador/perfilRecrutador.php?id=' . $idPessoaEmpresa . '">' . $NomeEmpresa . '</a><br>';

            } else {
                echo '<a id="empresa">Confidencial</a><br>';
            }
            ?>
            <label class="infos">Data de anúncio: </label>
            <label class="infos">
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
            <label class="infos">Status:</label>
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
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/pbbsmkso.json" trigger="loop" state="loop-rotate"
                        colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="nomeArea">
                        <?php echo $Area; ?>
                    </label>
                </div>
                <div class="divIconeENome">
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/surcxhka.json" trigger="loop" stroke="bold"
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
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/qvyppzqz.json" trigger="loop" stroke="bold"
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
                    <?php
                    if ($autenticadoComoCandidato == false) {
                        // Se o usuário não for candidato, o bloco está vazio, isso pode ser intencional
                    }
                    if ($Status == 'Aberto' && $candidatoInscrito && $autenticadoComoCandidato) {
                        // Se a vaga estiver aberta e o candidato ainda não estiver inscrito
                        ?>
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
                        <?php
                    } elseif ($candidatoInscrito == false) {
                        // Se o candidato já estiver inscrito na vaga
                        ?>
                        <div class="divSendButton">
                            <button disabled style="cursor: default; background-color: #723911;">
                                <h4>Já inscrito</h4>
                                <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover"
                                    colors="primary:#f5f5f5" style="width:80px;height:80px">
                                </lord-icon>
                            </button>
                        </div>
                        <?php
                    } elseif ($Status != 'Aberto') {
                        // Se a vaga não estiver aberta
                        ?>
                        <p>Status: Encerrado</p>
                        <?php
                    }
                    ?>
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
        <p class="sinopse">SIAS 2024</p>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
    <script src="../../../modoNoturno.js"></script>
    <!-- Eu movi o titulo digitavel pra cá, para pegar o nome do usario que está com seção  -->
    <script>
        <script>
            var idPessoa = <?php echo $idPessoa; ?>;

            $(".btnModo").click(function () {
            var novoTema = $("body").hasClass("noturno") ? "claro" : "noturno";


            // Salva o novo tema no banco de dados via AJAX
            $.ajax({
                url: "../../services/Temas/atualizar_tema.php",
            method: "POST",
            data: {tema: novoTema, idPessoa: idPessoa },
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