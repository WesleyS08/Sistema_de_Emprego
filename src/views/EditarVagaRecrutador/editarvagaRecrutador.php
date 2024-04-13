<?php
include "../../services/conexão_com_banco.php";

session_start();

// Verificar se o usuário está autenticado como candidato
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';
$autenticadoComoEmpresa = false; // Corrigido o nome da variável
$candidatoInscrito = false;

// Verificar se o usuário está autenticado como empresa
$autenticadoComoPublicador = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado com e-mail/senha e for do tipo candidato
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session']) && $_SESSION['google_usuario'] == 'candidato') {
    // Se estiver autenticado com o Google e for do tipo candidato
    $emailUsuario = $_SESSION['google_session'];
} else {
    $autenticadoComoEmpresa = false; // Corrigido o nome da variável
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

    } else {
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
            // Exibir um link para a página de edição da vaga
            echo '<a href="../EditarVaga/editarVaga.php?id=' . $idAnuncio . '">Editar Vaga</a><br>';
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
    <title>Anunciar</title>
    <link rel="stylesheet" type="text/css" href="../criarVaga/criaVaga.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="homeCandidato.html"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="#">Anunciar</a></li>
            <li><a href="#">Minhas vagas</a></li>
            <li><a href="#">Meus testes</a></li>
            <li><a href="#">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <a class="btnVoltar"><img src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Editar de Vaga</h2>
        </div>
        <form id="formvaga" method="POST" action="../../../src/services/cadastros/vaga.php" autocomplete="off">
            <div class="containerForm">
                <div class="containerSuperior">
                    <div class="divInputs">
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="titulo" name="titulo" type="text" required
                                        value="<?php echo $dadosAnuncio['Titulo'] ?>">

                                    <div class="labelLine">Titulo</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="area" name="area" type="text" list="areaList"
                                        required value="<?php echo $dadosAnuncio['Area'] ?>">
                                    <div class="labelLine">Área</div>
                                    <datalist id="areaList">
                                        <option>Tecnologia da Informação</option>
                                        <option>Medicia</option>
                                        <option>Engenharia</option>
                                        <option>Construção Civil</option>
                                        <option>Educação</option>
                                    </datalist>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cep" name="cep" type="text" required
                                        value="<?php echo $dadosAnuncio['CEP'] ?>">
                                    <div class="labelLine">CEP</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="bairro" name="bairro" type="text" required
                                        value="<?php echo $dadosAnuncio['Area'] ?>">
                                    <div class="labelLine">Bairro</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="estado" id="estado" type="text" required
                                        value="<?php echo $dadosAnuncio['Estado'] ?>">
                                    <div class="labelLine">Estado</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cidade" name="cidade" type="text" required
                                        value="<?php echo $dadosAnuncio['Cidade'] ?>">
                                    <div class="labelLine">Cidade</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="endereco" id="endereco" type="text"
                                        placeholder="Rua Fulano de Tal, 123" required
                                        value="<?php echo $dadosAnuncio['Rua'] ?>">
                                    <div class="labelLine">Endereço</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="numero" name="numero" type="text"
                                        placeholder="Número da empresa" required
                                        value="<?php echo $dadosAnuncio['Numero'] ?>">
                                    <div class="labelLine">Número</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" name="horario" id="horario" type="text"
                                    placeholder="De segunda a sexta, das 9:00 às 16:00" required
                                    value="<?php echo $dadosAnuncio['Horario'] ?>">
                                <div class="labelLine">Carga horária</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="divTextArea">
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="descricao" id="descricao" type="text" required
                                    value="<?php echo $dadosAnuncio['Descricao'] ?>"></textarea>
                                <div class="textArealabelLine">Descrição da Vaga</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="divFlex" id="divFlexTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="requisitos" id="requisitos" type="text"
                                        required value="<?php echo $dadosAnuncio['Requisitos'] ?>"></textarea>
                                    <div class="textArealabelLine">Requisitos</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="beneficios" id="beneficios" type="text"
                                        required value="<?php echo $dadosAnuncio['Beneficios'] ?>"></textarea>
                                    <div class="textArealabelLine">Benefícios</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="containerInferior">
                    <div class="divFlexRadios">
                        <div>
                            <div class="divRadioContent">
                                <h3>Tipo de profissional:</h3>
                                <input type="radio" name="tipo" id="jovemAprendiz" value="Jovem Aprendiz" <?php echo ($Tipo_Profissional == 'Jovem Aprendiz') ? 'checked' : ''; ?> required>
                                <input type="radio" name="tipo" id="estagio" value="Estágio" <?php echo ($Tipo_Profissional == 'Estágio') ? 'checked' : ''; ?> required>
                                <input type="radio" name="tipo" id="clt" value="CLT" <?php echo ($Tipo_Profissional == 'CLT') ? 'checked' : ''; ?> required>
                                <input type="radio" name="tipo" id="pj" value="PJ" <?php echo ($Tipo_Profissional == 'PJ') ? 'checked' : ''; ?> required>
                                <label for="jovemAprendiz" class="btnRadio" id="btnJovemAprendiz">Jovem Aprendiz</label>
                                <label for="estagio" class="btnRadio" id="btnEstagio">Estágio</label>
                                <label for="clt" class="btnRadio" id="btnClt">CLT</label>
                                <label for="pj" class="btnRadio" id="btnPj">PJ</label>
                            </div>

                            <div class="divRadioContent">
                                <h3>Nível de aprendizado:</h3>
                                <input type="radio" name="nivel" id="medio" value="Ensino Médio" <?php echo ($Nivel_Aprendizado == 'Ensino Médio') ? 'checked' : ''; ?> required>
                                <input type="radio" name="nivel" id="tecnico" value="Técnico" <?php echo ($Nivel_Aprendizado == 'Técnico') ? 'checked' : ''; ?> required>
                                <input type="radio" name="nivel" id="superior" value="Superior" <?php echo ($Nivel_Aprendizado == 'Superior') ? 'checked' : ''; ?> required>
                                <label for="medio" class="btnRadio" id="btnMedio">Ensino Médio</label>
                                <label for="tecnico" class="btnRadio" id="btnTecnico">Ensino Técnico</label>
                                <label for="superior" class="btnRadio" id="btnSuperior">Ensino Superior</label>
                            </div>

                        </div>
                        <div>
                            <div class="divRadioContent">
                                <h3>Modalidade:</h3>
                                <input type="radio" name="modalidade" id="remoto" value="Remoto" <?php echo ($Modalidade == 'Remoto') ? 'checked' : ''; ?> required>
                                <input type="radio" name="modalidade" id="presencial" value="Presencial" <?php echo ($Modalidade == 'Presencial') ? 'checked' : ''; ?> required>
                                <label for="remoto" class="btnRadio" id="btnRemoto">Remoto</label>
                                <label for="presencial" class="btnRadio" id="btnPresencial">Presencial</label>
                            </div>

                            <div class="divRadioContent">
                                <h3>Jornada:</h3>
                                <input type="radio" name="jornada" id="meioPeriodo" value="Meio período" <?php echo ($Jornada == 'Meio período') ? 'checked' : ''; ?> required>
                                <input type="radio" name="jornada" id="integral" value="Tempo integral" <?php echo ($Jornada == 'Tempo integral') ? 'checked' : ''; ?> required>
                                <label for="meioPeriodo" class="btnRadio" id="btnMeioPeriodo">Meio período</label>
                                <label for="integral" class="btnRadio" id="btnIntegral">Tempo integral</label>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="emailSession" value="<?php echo $emailUsuario; ?>">
                    <div class="divSalvar">
                        <input type="submit" value="Salvar" class="btnSalvar">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="modoNoturno.js"></script>
    <script src="radioButtons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#cep').on('change', function () {
                var cep = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

                // Verifica se o CEP tem 8 dígitos
                if (cep.length == 8) {
                    $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function (data) {
                        if (!("erro" in data)) {
                            $('#bairro').val(data.bairro);
                            $('#estado').val(data.uf);
                            $('#cidade').val(data.localidade);
                            $('#endereco').val(data.logradouro);
                        } else {
                            alert('CEP não encontrado.');
                        }
                    });
                } else {
                    alert('CEP inválido. Por favor, insira um CEP válido com 8 dígitos.');
                }
            });
        });
    </script>
</body>

</html>