<?php
include "../../services/conexão_com_banco.php"; // Verifique se o caminho está correto
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
        $verificado = $row['Verificado'] ?? 0;
    } else {
        // Registre um erro no arquivo de log do servidor
        error_log("Nenhuma linha retornada pela consulta SQL: " . $stmt->error);
        // Ou você pode imprimir uma mensagem de erro na tela para fins de depuração
        echo "Nenhuma linha retornada pela consulta SQL: " . $stmt->error;
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

    // Consulta para pegar o CPF do candidato e o CNPJ da empresa
    $sql = "SELECT Tb_Candidato.CPF, Tb_Empresa.CNPJ
FROM Tb_Candidato
INNER JOIN Tb_Pessoas ON Tb_Candidato.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
LEFT JOIN Tb_Empresa ON Tb_Candidato.Tb_Pessoas_Id = Tb_Empresa.Tb_Pessoas_Id
WHERE Tb_Pessoas.Email = ?";

    $stmt = $_con->prepare($sql);
    $stmt->bind_param('s', $emailUsuario); // Bind do parâmetro para evitar injeção de SQL
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Obtém o resultado da consulta
        $cpfCandidato = $row['CPF']; // Armazena o CPF do candidato
        $cnpjEmpresa = $row['CNPJ']; // Armazena o CNPJ da empresa, se houver

        // Verifica se o usuário já se inscreveu para a vaga (apenas se for um candidato)
        $sqlVerificaInscricao = "SELECT 1
FROM Tb_Inscricoes 
WHERE Tb_Vagas_Tb_Anuncios_Id = ?
AND Tb_Candidato_CPF = ?
LIMIT 1";

        // Preparar a consulta para prevenir injeção de SQL
        $stmtVerifica = $_con->prepare($sqlVerificaInscricao);
        $stmtVerifica->bind_param('is', $idAnuncio, $cpfCandidato); // Vincular parâmetros para segurança
        $stmtVerifica->execute();
        $resultVerifica = $stmtVerifica->get_result();

        // Definir a variável com base no resultado da consulta
        $candidatoInscrito = ($resultVerifica->num_rows > 0) ? true : false;

        // Terceira consulta para obter o status da vaga
        $sql2 = "SELECT Status FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
        $stmtStatus = $_con->prepare($sql2);
        $stmtStatus->bind_param('i', $idAnuncio); // Bind do parâmetro para evitar injeção de SQL
        $stmtStatus->execute();
        $resultStatus = $stmtStatus->get_result();

        if ($resultStatus && $resultStatus->num_rows > 0) {
            $rowStatus = $resultStatus->fetch_assoc();
            $Status = $rowStatus['Status'];
        } else {
            // Defina um valor padrão para $Status se a consulta não retornar resultados
            $Status = '';
        }
    } else {

    }
}
$totaldisponivel = 4 - $total_inscricoes;
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
        echo '<a href="../../../index.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> ';
        echo '<ul>';
        echo '    <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>';
        echo '    <li><a href="../Login/login.html">Testes</a></li>';
        echo '    <li><a href="../Cursos/cursos.php">Cursos</a></li>';
        echo '    <li><a href="../Login/login.html">Perfil</a></li>'; // Se não autenticado, redireciona para login
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
                    <?php echo $Status; ?>
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

                    if ($Status != 'Aberto') {
                        // Se a vaga não estiver aberta, é considerada encerrada
                        $caseType = 'encerrado';

                    } elseif ($verificado == 1) {
                        // Se o candidato foi verificado
                    
                        if ($candidatoInscrito == true) {
                            // Se o candidato está inscrito, ele é considerado "verificado e inscrito"
                            $caseType = 'verificadoInscrito';
                        } else {
                            // Se o candidato não está inscrito, ele é considerado "verificado e não inscrito"
                            $caseType = 'verificadoNaoInscrito';
                        }
                    } elseif ($verificado == 0) {
                        // Se o candidato não foi verificado
                    
                        if ($total_inscricoes >= 4) {
                            // Se o total de inscrições do candidato for maior ou igual a 4, ele atingiu o limite de inscrições
                            $caseType = 'naoVerificadoLimiteExcedido';

                        } elseif ($total_inscricoes >= 0) {
                            // Se o candidato não atingiu o limite de inscrições e já possui inscrições
                    
                            if ($candidatoInscrito == true) {
                                // Se o candidato está inscrito, ele é considerado "não verificado e inscrito com inscrições"
                                $caseType = 'naoVerificadoInscritoComInscricoes';
                            } else {
                                // Se o candidato não está inscrito, ele é considerado "não verificado e não inscrito com inscrições"
                                $caseType = 'naoVerificadoNaoInscritoComInscricoes';
                            }
                        } else {
                            // Se o candidato não atingiu o limite de inscrições e não possui inscrições anteriores
                            // Permitir que candidatos não verificados se inscrevam mesmo sem inscrições anteriores
                            if (!$candidatoInscrito) {
                                // Se o candidato não está inscrito, ele é considerado "não verificado e não inscrito sem inscrições"
                                $caseType = 'naoVerificadoNaoInscritoSemInscricoes';
                            } else {
                                // Se o candidato está inscrito, ele é considerado "não verificado e inscrito sem inscrições"
                                $caseType = 'naoVerificadoInscritoSemInscricoes';
                            }
                        }
                    }

                    switch ($caseType) {
                        case 'encerrado':
                            // Indique que a vaga está encerrada
                            echo '<p>Status: Encerrado</p>';
                            break;

                        case 'verificadoInscrito':
                            // Indicar que o candidato já está inscrito e permitir retirar candidatura
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '        <button>';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '    </form>';
                            echo '    </div>';
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
                            echo '    <div class="divSendButton">';
                            echo '    <button disabled style="cursor: default; background-color: #723911;">';
                            echo '        <h4>Já inscrito</h4>';
                            echo '        <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '    </button>';
                            echo '</div>';
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '        <button>';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '    </form>';
                            echo '    </div>';
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
                            echo '    <div class="divSendButton">';
                            echo '    <button disabled style="cursor: default; background-color: #723911;">';
                            echo '        <h4>Já inscrito</h4>';
                            echo '        <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '    </button>';
                            echo '</div>';
                            echo '    <form method="POST" action="../../services/cadastros/processar_retirada_candidatura.php?id_anuncio=' . $idAnuncio . '">';
                            echo '        <button>';
                            echo '            <h4>Retirar Candidatura</h4>';
                            echo '            <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#f5f5f5" style="width:80px;height:80px"></lord-icon>';
                            echo '        </button>';
                            echo '    </form>';
                            echo '    </div>';
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
                $arrayRequisitos = array_filter(array_map('trim', explode(',', $dadosAnuncio['Requisitos'])));
                $arrayBeneficios = array_filter(array_map('trim', explode(',', $dadosAnuncio['Beneficios'])));
                ?>
                <div class="divFlex" id="divBoxes">
                    <div class="divBox">
                        <h3>Requisitos</h3>
                        <ul>
                            <?php foreach ($arrayRequisitos as $requisito) { ?>
                                <li>
                                    <p><?php echo $requisito; ?></p>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="divBox">
                        <h3>Benefícios</h3>
                        <ul>
                            <?php foreach ($arrayBeneficios as $beneficio) { ?>
                                <li>
                                    <p><?php echo $beneficio; ?></p>
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