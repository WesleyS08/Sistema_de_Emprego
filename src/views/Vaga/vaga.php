<?php
include "../../services/conexão_com_banco.php";
session_start();

// Definição de variáveis com valores padrão
$nomeUsuario = $_SESSION['nome_usuario'] ?? 'Anônimo';
$emailUsuario = $_SESSION['email_session'] ?? ($_SESSION['google_session'] ?? '');
$candidatoInscrito = false;
$Status = '';
$verificado = 0;
$total_inscricoes = 0;
$cpf = '';

// Verificar se o usuário está autenticado como candidato
$autenticadoComoCandidato = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato';

// Primeira consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas, Verificado FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $emailUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];
        $verificado = $row['Verificado'] ?? 0;
    } else {
        error_log("Nenhuma linha retornada pela consulta SQL: " . $stmt->error);
    }
    $stmt->close();
} else {
    die("Erro ao preparar a consulta: " . $_con->error);
}

// Consulta para obter o tema
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();
    $result = $stmt->get_result();
    $tema = $result->fetch_assoc()['Tema'] ?? null;
    $stmt->close();
} else {
    die("Erro ao preparar a query: " . $_con->error);
}

// Verifica se o id da vaga foi enviado pela URL
if (isset($_GET['id'])) {
    $idAnuncio = $_GET['id'];

    $sql = "
        SELECT Tb_Anuncios.*, Tb_Empresa.Nome_da_Empresa, Tb_Empresa.Tb_Pessoas_Id AS Id_Pessoa_Empresa, Tb_Vagas.Data_de_Termino
        FROM Tb_Anuncios
        INNER JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        INNER JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        WHERE Tb_Anuncios.Id_Anuncios = ?
    ";
    $stmt = $_con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $idAnuncio);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nomeEmpresa = $row['Nome_da_Empresa'];
            $idPessoaEmpresa = $row['Id_Pessoa_Empresa'];
            $Data_de_Termino = $row['Data_de_Termino'];

            // Atribuição das informações do banco de dados a variáveis
            $Categoria = $row['Categoria'];
            $Titulo = $row['Titulo'];
            $Descricao = $row['Descricao'];
            $Area = $row['Area'];
            $Cidade = $row['Cidade'];
            $Nivel_Operacional = $row['Nivel_Operacional'];
            $Data_de_Criacao = $row['Data_de_Criacao'];
            $Modalidade = $row['Modalidade'];
            $Beneficios = $row['Beneficios'];
            $Requisitos = $row['Requisitos'];
            $Horario = $row['Horario'];
            $Estado = $row['Estado'];
            $Jornada = $row['Jornada'];
            $CEP = $row['CEP'];
            $Rua = $row['Rua'];
            $bairro = $row['Bairro'];
            $Numero = $row['Numero'];
            $NomeEmpresa = $row['Nome_da_Empresa'];
        } else {
            // Campos vazios se nenhum anúncio for encontrado
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
            $Rua = '';
            $bairro = '';
            $Numero = '';
            $NomeEmpresa = '';
            $Data_de_Termino = '';
        }
        $stmt->close();
    } else {
        die("Erro ao preparar a consulta (detalhes do anúncio): " . $_con->error);
    }

    $sql2 = "SELECT Status FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
    $stmtStatus = $_con->prepare($sql2);

    if ($stmtStatus) {
        $stmtStatus->bind_param('i', $idAnuncio);
        $stmtStatus->execute();
        $resultStatus = $stmtStatus->get_result();

        $Status = $resultStatus->fetch_assoc()['Status'] ?? '';
        $stmtStatus->close();
    } else {
        die("Erro ao preparar a consulta (status da vaga): " . $_con->error);
    }

    if ($autenticadoComoCandidato) {
        $sql = "
            SELECT Tb_Candidato.CPF, Tb_Empresa.CNPJ
            FROM Tb_Candidato
            INNER JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
            LEFT JOIN Tb_Empresa ON Tb_Candidato.Tb_Pessoas_Id = Tb_Empresa.Tb_Pessoas_Id
            WHERE Tb_Pessoas.Email = ?
        ";
        $stmt = $_con->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('s', $emailUsuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $cpfCandidato = $row['CPF'];
                $cnpjEmpresa = $row['CNPJ'];

                $sqlVerificaInscricao = "
                    SELECT 1
                    FROM Tb_Inscricoes
                    WHERE Tb_Vagas_Tb_Anuncios_Id = ?
                    AND Tb_Candidato_CPF = ?
                    LIMIT 1
                ";
                $stmtVerifica = $_con->prepare($sqlVerificaInscricao);

                if ($stmtVerifica) {
                    $stmtVerifica->bind_param('is', $idAnuncio, $cpfCandidato);
                    $stmtVerifica->execute();
                    $resultVerifica = $stmtVerifica->get_result();

                    $candidatoInscrito = ($resultVerifica->num_rows > 0);
                    $stmtVerifica->close();
                } else {
                    die("Erro ao preparar a consulta (verificação de inscrição): " . $_con->error);
                }
            } else {
                // Não encontrou CPF/CNPJ, mas não é erro para visitantes
                $cpfCandidato = '';
                $cnpjEmpresa = '';
            }
            $stmt->close();
        } else {
            die("Erro ao preparar a consulta (dados do candidato): " . $_con->error);
        }
    }

    // Consulta para contar o total de inscrições
    if ($autenticadoComoCandidato) {
        $query = "
            SELECT COUNT(*) AS total_inscricoes
            FROM Tb_Inscricoes ins
            JOIN Tb_Vagas va ON ins.Tb_Vagas_Tb_Anuncios_Id = va.Tb_Anuncios_Id
            JOIN Tb_Anuncios an ON va.Tb_Anuncios_Id = an.Id_Anuncios
            JOIN Tb_Empresa em ON va.Tb_Empresa_CNPJ = em.CNPJ
            WHERE ins.Tb_Candidato_CPF = ?
        ";
        $stmt = $_con->prepare($query);

        if ($stmt) {
            $stmt->bind_param('s', $cpfCandidato);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $total_inscricoes = $result->fetch_assoc()['total_inscricoes'];
            }
            $stmt->close();
        } else {
            die("Erro ao preparar a consulta (total de inscrições): " . $_con->error);
        }
    }
}

$totaldisponivel = 4 - $total_inscricoes;
?>

<script>
    console.log("Nome do Usuário: <?php echo $nomeUsuario; ?>");
    console.log("Email do Usuário: <?php echo $emailUsuario; ?>");
    console.log("Tema: <?php echo isset($tema) ? $tema : 'Nenhum'; ?>");
    console.log("Total de Inscrições: <?php echo $total_inscricoes; ?>");
    console.log("Inscrições Disponíveis: <?php echo $totaldisponivel; ?>");
    console.log("Candidato Inscrito: <?php echo $candidatoInscrito ? 'Sim' : 'Não'; ?>");
    console.log("Status: <?php echo $Status; ?>");
    console.log("Categoria: <?php echo $Categoria; ?>");
    console.log("Título: <?php echo $Titulo; ?>");
    console.log("Descrição: <?php echo $Descricao; ?>");
    console.log("Área: <?php echo $Area; ?>");
    console.log("Cidade: <?php echo $Cidade; ?>");
    console.log("Nível Operacional: <?php echo $Nivel_Operacional; ?>");
    console.log("Data de Criação: <?php echo $Data_de_Criacao; ?>");
    console.log("Modalidade: <?php echo $Modalidade; ?>");
    console.log("Benefícios: <?php echo $Beneficios; ?>");
    console.log("Requisitos: <?php echo $Requisitos; ?>");
    console.log("Horário: <?php echo $Horario; ?>");
    console.log("Estado: <?php echo $Estado; ?>");
    console.log("Jornada: <?php echo $Jornada; ?>");
    console.log("CEP: <?php echo $CEP; ?>");
    console.log("Rua: <?php echo $Rua; ?>");
    console.log("Bairro: <?php echo $bairro; ?>");
    console.log("Número: <?php echo $Numero; ?>");
    console.log("verificado: <?php echo $verificado; ?>");
    console.log("Nome da Empresa: <?php echo $NomeEmpresa; ?>");
    console.log("Data de Término: <?php echo $Data_de_Termino; ?>");
</script>

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
    <style>
        .mensagem {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-top: 10px;
            height: 60px;
        }
    </style>
</head>

<body>
    <?php
    if ($idPessoa) {
        echo '<nav>';
        echo '    <input type="checkbox" id="check"> ';
        echo '    <label for="check" class="menuBtn">';
        echo '        <img src="../../../imagens/menu.svg">';
        echo '    </label> ';
        echo '<a href="../HomeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> ';
        echo '<button class="btnModo"><img src="../../../imagens/moon.svg"></button>';
        echo '<ul>';
        echo '    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>';
        echo '    <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>';
        echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
        echo '    <li><a href="../PerfilCandidato/perfilCandidato.php?id=' . $idPessoa . '">Perfil</a></li>';
        echo '</ul>';
        echo '</nav>';
    } else {
        // Se não for autenticado como candidato, mostrar menu padrão 
        echo '<nav>';
        echo '    <input type="checkbox" id="check"> ';
        echo '    <label for="check" class="menuBtn">';
        echo '        <img src="../../../imagens/menu.svg">';
        echo '    </label> ';
        echo '<a href="../../../index.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> ';
        echo '<ul>';
        echo '    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>';
        echo '    <li><a href="../Login/login.html">Testes</a></li>';
        echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
        echo '    <li><a href="../Login/login.html">Login</a></li>'; // Se não autenticado, redireciona para login
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
                echo '<a href="../PerfilRecrutador/perfilRecrutador.php?id=' . $idPessoaEmpresa . '">' . $NomeEmpresa . '</a><br>';

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
                    <?php echo 'Encerrado'; ?>
                </label>
            <?php } ?>
        </div>
        <div class="container">
            <div class="divInformacoesIniciais">
                <div class="divIconeENome">
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/pbbsmkso.json" trigger="loop"
                        state="loop-rotate" colors="primary:#242424,secondary:#c74b16" style="width:34px;height:34px">
                    </lord-icon>
                    <label id="nomeArea">
                        <?php echo $Area; ?>
                    </label>
                </div>
                <div class="divIconeENome">
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/surcxhka.json" trigger="loop"
                        stroke="bold" state="loop-roll" colors="primary:#242424,secondary:#c74b16"
                        style="width:34px;height:34px">
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
                    <lord-icon class="iconeVaga" src="https://cdn.lordicon.com/qvyppzqz.json" trigger="loop"
                        stroke="bold" state="loop-oscillate" colors="primary:#242424,secondary:#c74b16"
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
                    $caseType = '';

                    // Verifica se idPessoa está definido
                    if (isset($idPessoa) && $idPessoa != '') {
                        // Se a vaga não estiver aberta, é considerada encerrada
                        if ($Status != 'Aberto') {
                            $caseType = 'encerrado';
                        } elseif ($verificado == 1) {
                            // Se o candidato foi verificado
                            if ($candidatoInscrito) {
                                $caseType = 'verificadoInscrito';
                            } else {
                                $caseType = 'verificadoNaoInscrito';
                            }
                        } elseif ($verificado == 0) {
                            // Se o candidato não foi verificado
                            if ($total_inscricoes >= 4) {
                                $caseType = 'naoVerificadoLimiteExcedido';
                            } elseif ($total_inscricoes >= 0) {
                                if ($candidatoInscrito) {
                                    $caseType = 'naoVerificadoInscritoComInscricoes';
                                } else {
                                    $caseType = 'naoVerificadoNaoInscritoComInscricoes';
                                }
                            } else {
                                if (!$candidatoInscrito) {
                                    $caseType = 'naoVerificadoNaoInscritoSemInscricoes';
                                } else {
                                    $caseType = 'naoVerificadoInscritoSemInscricoes';
                                }
                            }
                        }
                    } else {
                        // Caso não tenha o idPessoa
                        $caseType = 'semIdPessoa';
                    }

                    switch ($caseType) {
                        case 'encerrado':
                            // Indique que a vaga está encerrada
                            echo '<p>Status: Encerrado</p>';
                            break;

                        case 'verificadoInscrito':
                            // Indicar que o candidato já está inscrito e permitir retirar candidatura
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '     <div class="divSendButton">';
                            echo '        <button style="background-color: #a04809; border-color:#a04809 ">';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '     </div>';
                            echo '    </form>';
                            break;

                        case 'verificadoNaoInscrito':
                            // Carregue o formulário de candidatura para verificado e não inscrito
                            echo '<form method="POST" action="../../services/cadastros/processar_candidatura.php?id_anuncio=' . $idAnuncio . '"> ';
                            echo ' <div class="divSendButton">';
                            echo '     <button>';
                            echo '         <h4>Candidatar-se</h4>';
                            echo '         <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '     </button>';
                            echo ' </div>';
                            break;

                        case 'naoVerificadoInscritoComInscricoes':
                            // Indicar que o candidato já está inscrito
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '     <div class="divSendButton">';
                            echo '        <button style="background-color: #a04809; border-color:#a04809 ">';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '     </div>';
                            echo '    </form>';
                            break;

                        case 'naoVerificadoNaoInscritoComInscricoes':
                            // Carregue o formulário de candidatura para não verificado e não inscrito
                            echo '<form method="POST" action="../../services/cadastros/processar_candidatura.php?id_anuncio=' . $idAnuncio . '"> ';
                            echo ' <div class="divSendButton">';
                            echo '     <button>';
                            echo '         <h4>Candidatar-se</h4>';
                            echo '         <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '     </button>';
                            echo ' </div>';
                            break;

                        case 'naoVerificadoInscritoSemInscricoes':
                            // Indicar que o candidato já está inscrito e não há inscrições disponíveis
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '     <div class="divSendButton">';
                            echo '        <button style="background-color: #a04809; border-color:#a04809 ">';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '     </div>';
                            echo '    </form>';
                            break;
                        case 'semIdPessoa':
                            break;
                        case 'naoVerificadoNaoInscritoSemInscricoes':
                            // Indicar que não há inscrições disponíveis
                            echo '<div class="mensagem">Você não tem anúncios disponíveis para se candidatar.</div>';
                            break;
                        default:
                            echo '<div class="mensagem">Situação não reconhecida.</div>';
                            break;
                    }
                    ?>
                </div>
                <?php
                // Divida os requisitos e benefícios por vírgula e remova espaços em branco desnecessários
                $arrayRequisitos = array_filter(array_map('trim', explode(',', $Requisitos)));
                $arrayBeneficios = array_filter(array_map('trim', explode(',', $Beneficios)));
                ?>
                <div class="divFlex" id="divBoxes">
                    <div class="divBox">
                        <h3>Requisitos</h3>
                        <ul>
                            <?php foreach ($arrayRequisitos as $requisito) { ?>
                                <li class="infos">
                                    <?php echo $requisito; ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="divBox">
                        <h3>Benefícios</h3>
                        <ul>
                            <?php foreach ($arrayBeneficios as $beneficio) { ?>
                                <li class="infos">
                                    <?php echo $beneficio; ?>
                                </li>
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
        <a href="../NossoContato/nossoContato.html">Nosso contato</a>
        <a href="../AvalieNos/avalieNos.html">Avalie-nos</a>
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