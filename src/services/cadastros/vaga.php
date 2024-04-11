<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como candidato
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';
$candidatoInscrito = false; 

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado com e-mail/senha e for do tipo candidato
    $emailUsuario = $_SESSION['email_session'];
    $autenticadoComoEmpresa = true;

} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'candidato') {
    // Se estiver autenticado com o Google e for do tipo candidato
    $emailUsuario = $_SESSION['google_session'];
    $autenticadoComoEmpresa = true;
} else {
    $autenticadoComoEmpresa = false;
}

if (isset($_GET['id'])) {
    if ($_con->connect_error) {
        die("Falha na conexão: " . $_con->connect_error);
    }

    // Obter o ID do anúncio da variável GET
    $idAnuncio = $_GET['id'];

    $sql = "SELECT Tb_Anuncios.*, Tb_Empresa.Nome_da_Empresa
    FROM Tb_Anuncios
    INNER JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
    INNER JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
    WHERE Tb_Anuncios.Id_Anuncios = $idAnuncio";

    // Consulta SQL com o ID do anúncio como parâmetro

    $result = mysqli_query($_con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $dadosAnuncio = mysqli_fetch_assoc($result);

        // Agora você pode acessar os detalhes do anúncio e o nome da empresa
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
        $Numero = $dadosAnuncio['Numero'];
        $NomeEmpresa = $dadosAnuncio['Nome_da_Empresa'];

        $enderecoEmpresa = $Rua . ' ' . $Numero . ', ' . $Cidade . ', ' . $Estado . ', ' . $CEP;

        // Verificar se o candidato já se inscreveu para este anúncio
        if (isset($_SESSION['cpf_candidato'])) {
            $cpfCandidato = $_SESSION['cpf_candidato'];
            $sqlVerificaInscricao = "SELECT * FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = $idAnuncio AND Tb_Vagas_Tb_Empresa_CNPJ = '{$dadosAnuncio['Tb_Empresa_CNPJ']}' AND Tb_Candidato_CPF = '$cpfCandidato'";
            $resultVerificaInscricao = mysqli_query($_con, $sqlVerificaInscricao);
            if ($resultVerificaInscricao && mysqli_num_rows($resultVerificaInscricao) > 0) {
                $candidatoInscrito = true;
            }
        }

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

    // Fechar a conexão com o banco de dados
    mysqli_close($_con);
}

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaga</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="vaga.css">
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
    <div class="divCommon">
        <div class="divTitulo" id="divTituloVaga">
            <h2 id="tituloVaga"><?php echo $Titulo; ?></h2>
            <label>Empresa: </label>
            <a id="empresa"><?php echo $NomeEmpresa; ?></a><br>
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
            <label style="color: green;">Aberto</label>
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
                                    <h4>Já Inscrito</h4>
                                    <lord-icon src="https://cdn.lordicon.com/smwmetfi.json" trigger="hover"
                                        colors="primary:#f5f5f5" style="width:80px;height:80px">
                                    </lord-icon>
                                </button>
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
            </div>
            <div id="map"></div>

        </div>


    </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="trocaIcones.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

</body>

</html>