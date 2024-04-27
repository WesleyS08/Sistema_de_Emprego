<?php
include "../../services/conexão_com_banco.php";

session_start();

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$autenticadoComoEmpresa = false; // Corrigido o nome da variável
$candidatoInscrito = false;

if (empty($emailUsuario)) {
    die("Erro: O e-mail do usuário não está definido. Verifique a sessão.");
}

if ($_con->connect_error) {
    die("Erro: Falha na conexão com o banco de dados: " . $_con->connect_error);
}

// Verificar se há um ID de anúncio nos parâmetros GET
if (isset($_GET['id'])) {
    $idAnuncio = intval($_GET['id']); // Converte para um número inteiro por segurança

    // Consulta para obter dados do anúncio
    $sql = "SELECT Tb_Anuncios.*, Tb_Empresa.Nome_da_Empresa
            FROM Tb_Anuncios
            INNER JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
            INNER JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
            WHERE Tb_Anuncios.Id_Anuncios = ?";

    $stmt = $_con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $dadosAnuncio = mysqli_fetch_assoc($result);
            // Atribuição dos valores dos campos
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
            $Bairro = $dadosAnuncio['Bairro'];
            $NomeEmpresa = $dadosAnuncio['Nome_da_Empresa'];
        } else {
            die("Erro: Anúncio não encontrado.");
        }

        $stmt->close();
    } else {
        die("Erro ao preparar a consulta para obter detalhes do anúncio.");
    }

    // Consulta para obter o status da vaga
    $sql2 = "SELECT Status FROM Tb_Vagas WHERE Tb_Anuncios_Id = ?";
    $stmt2 = $_con->prepare($sql2);

    if ($stmt2) {
        $stmt2->bind_param("i", $idAnuncio);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2 && $result2->num_rows > 0) {
            $row = mysqli_fetch_assoc($result2);
            $Status = $row['Status'];
        } else {
            $Status = ''; // Valor padrão se não houver resultados
        }

        $stmt2->close();
    } else {
        die("Erro ao preparar a consulta para obter o status da vaga.");
    }

    // Consulta para verificar se o usuário é o publicador da vaga
    $sql3 = "SELECT Tb_Pessoas.Email 
            FROM Tb_Pessoas 
            JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id 
            JOIN Tb_Vagas ON Tb_Empresa.CNPJ = Tb_Vagas.Tb_Empresa_CNPJ 
            WHERE Tb_Vagas.Tb_Anuncios_Id = ?";

    $stmt3 = $_con->prepare($sql3);

    if ($stmt3) {
        $stmt3->bind_param("i", $idAnuncio);
        $stmt3->execute();
        $result3 = $stmt3->get_result();

        if ($result3 && $result3->num_rows > 0) {
            $row = mysqli_fetch_assoc($result3);
            $emailCriadorVaga = $row['Email'];

            if ($emailCriadorVaga == $emailUsuario) {
                $autenticadoComoPublicador = true; // O usuário é o publicador da vaga
            }
        }

        $stmt3->close();
    } else {
        die("Erro ao preparar a consulta para verificar o criador da vaga.");
    }

    // Consulta para obter as áreas únicas
    $sql_areas = "SELECT DISTINCT Area FROM Tb_Anuncios ORDER BY Area ASC";
    $stmt_areas = $_con->prepare($sql_areas);

    if ($stmt_areas) {
        $stmt_areas->execute();
        $result_areas = $stmt_areas->get_result();

        if ($result_areas && $result_areas->num_rows > 0) {
            $areas = [];
            while ($row = $result_areas->fetch_assoc()) {
                $areas[] = $row['Area'];
            }
        } else {
            $areas = []; // No caso de não haver resultados
        }

        $stmt_areas->close();
    } else {
        die("Erro ao preparar a consulta para obter áreas.");
    }

    // Consulta para obter o ID da pessoa com base no e-mail
    $sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
    $stmt = $_con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $emailUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $idPessoa = $row['Id_Pessoas'];
        } else {
            die("Erro: Usuário não encontrado.");
        }

        $stmt->close();
    } else {
        die("Erro ao preparar a consulta para obter o ID da pessoa.");
    }

    // Consulta para obter o tema selecionado pelo usuário
    $query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt_tema = $_con->prepare($query);

    if ($stmt_tema) {
        $stmt_tema->bind_param("i", $idPessoa);
        $stmt_tema->execute();
        $result_tema = $stmt_tema->get_result();

        if ($result_tema && $result_tema->num_rows > 0) {
            $row = $result_tema->fetch_assoc();
            $tema = $row['Tema'];
        } else {
            $tema = null; // Valor padrão se não houver resultados
        }

        $stmt_tema->close();
    } else {
        die("Erro ao preparar a consulta para obter o tema do usuário.");
    }
}
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
        <a href="../HomeRecrutador/homeRecrutador.php"><img id="logo"
                src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
        <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
            <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagas</a></li>
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li><!--Arrumar esse link  -->
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <?php
            // Verificar se o parâmetro 'id' está definido na URL
            if (isset($_GET['id'])) {
                $idAnuncio = $_GET['id'];
                echo '<a class="btnVoltar" href="../MinhaVaga/Minhavaga.php?id=' . $idAnuncio . '">';
            } else {
                // Se 'id' não estiver definido na URL, exibir uma mensagem de erro
                echo '<p class="error">ID do anúncio não especificado na URL</p>';
            }
            ?>
            <img src="../../assets/images/icones_diversos/back.svg">
            </a>
            <h2>Editar de Vaga</h2>
        </div>


        <form id="formvaga" method="POST" action="../../services/Edição/vagas.php" autocomplete="off">
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
                                        <?php
                                        foreach ($areas as $area) {
                                            echo "<option value='$area'>$area</option>";
                                        }
                                        ?>
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
                                        value="<?php echo $dadosAnuncio['Bairro'] ?>">
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
                                <textarea class="textAreaAnimada" name="descricao" id="descricao"
                                    required><?php echo $dadosAnuncio['Descricao']; ?></textarea>
                                <div class="textArealabelLine">Descrição da Vaga</div>
                            </div>
                            <small name="aviso"></small>
                        </div>

                        <div class="divFlex" id="divFlexTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="requisitos" id="requisitos"
                                        required><?php echo $dadosAnuncio['Requisitos']; ?></textarea>
                                    <div class="textArealabelLine">Requisitos</div>
                                </div>
                                <small name="aviso">Separe os elementos por vírgula</small>

                            </div>

                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="beneficios" id="beneficios"
                                        required><?php echo $dadosAnuncio['Beneficios']; ?></textarea>
                                    <div class="textArealabelLine">Benefícios</div>
                                </div>
                                <small name="aviso">Separe os elementos por vírgula</small>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="containerInferior">
                    <div class="divFlexRadios">
                        <div>
                            <div class="divRadioContent">
                                <h3>Tipo de profissional:</h3>
                                <input type="radio" name="categoria" id="jovemAprendiz" value="Jovem Aprendiz" required
                                    <?php echo ($dadosAnuncio['Categoria'] == 'Jovem Aprendiz') ? 'checked' : ''; ?>>
                                <input type="radio" name="categoria" id="estagio" value="Estágio" required <?php echo ($dadosAnuncio['Categoria'] == 'Estágio') ? 'checked' : ''; ?>>
                                <input type="radio" name="categoria" id="clt" value="CLT" required <?php echo ($dadosAnuncio['Categoria'] == 'CLT') ? 'checked' : ''; ?>>
                                <input type="radio" name="categoria" id="pj" value="PJ" required <?php echo ($dadosAnuncio['Categoria'] == 'PJ') ? 'checked' : ''; ?>>

                                <label for="jovemAprendiz" class="btnRadio" id="btnJovemAprendiz">Jovem Aprendiz</label>
                                <label for="estagio" class="btnRadio" id="btnEstagio">Estágio</label>
                                <label for="clt" class="btnRadio" id="btnClt">CLT</label>
                                <label for="pj" class="btnRadio" id="btnPj">PJ</label>
                                <small name="aviso"></small>
                            </div>
                            <div class="divRadioContent">
                                <h3>Nível de aprendizado:</h3>
                                <input type="radio" name="nivel" id="medio" value="Ensino Médio" required <?php echo ($dadosAnuncio['Nivel_Operacional'] == 'Ensino Médio') ? 'checked' : ''; ?>>
                                <input type="radio" name="nivel" id="tecnico" value="Ensino Técnico" required <?php echo ($dadosAnuncio['Nivel_Operacional'] == 'Ensino Técnico') ? 'checked' : ''; ?>>
                                <input type="radio" name="nivel" id="superior" value="Ensino Superior" required <?php echo ($dadosAnuncio['Nivel_Operacional'] == 'Ensino Superior') ? 'checked' : ''; ?>>

                                <label for="medio" class="btnRadio" id="btnMedio">Ensino Médio</label>
                                <label for="tecnico" class="btnRadio" id="btnTecnico">Ensino Técnico</label>
                                <label for="superior" class="btnRadio" id="btnSuperior">Ensino Superior</label>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div>
                            <div class="divRadioContent">
                                <h3>Modalidade:</h3>
                                <input type="radio" name="modalidade" id="remoto" value="Remoto" required <?php echo ($dadosAnuncio['Modalidade'] == 'Remoto') ? 'checked' : ''; ?>>
                                <input type="radio" name="modalidade" id="presencial" value="Presencial" required <?php echo ($dadosAnuncio['Modalidade'] == 'Presencial') ? 'checked' : ''; ?>>
                                <label for="remoto" class="btnRadio" id="btnRemoto">Remoto</label>
                                <label for="presencial" class="btnRadio" id="btnPresencial">Presencial</label>
                                <small name="aviso"></small>
                            </div>
                            <div class="divRadioContent">
                                <h3>Jornada:</h3>
                                <!-- Botão de rádio para meio período -->
                                <input type="radio" name="jornada" id="meio_periodo" value="Meio período" required <?php echo ($dadosAnuncio['Jornada'] === 'Meio período') ? 'checked' : ''; ?>>
                                <label for="meio_periodo" class="btnRadio" id="btnMeioPeriodo">Meio período</label>

                                <!-- Botão de rádio para tempo integral -->
                                <input type="radio" name="jornada" id="tempo_integral" value="Tempo integral" required
                                    <?php echo ($dadosAnuncio['Jornada'] === 'Tempo integral') ? 'checked' : ''; ?>>
                                <label for="tempo_integral" class="btnRadio" id="btnIntegral">Tempo integral</label>

                                <small name="aviso"></small> <!-- Mensagens de validação -->
                            </div>


                        </div>
                        <div>
                            <div class="divRadioContent">
                                <h3>Status:</h3>
                                <div class="contentInput">
                                    <!-- Verifique se $Status tem o valor esperado -->
                                    <input type="radio" name="status" id="aberto" value="Aberto" required <?php echo ($Status === 'Aberto') ? 'checked' : ''; ?>>
                                    <input type="radio" name="status" id="encerrado" value="Encerrado" required <?php echo ($Status === 'Encerrado') ? 'checked' : ''; ?>>

                                    <label for="aberto" class="btnRadio" id="btnAberto">Aberto</label>
                                    <label for="encerrado" class="btnRadio" id="btnEncerrado">Encerrado</label>

                                    <small name="aviso"></small> <!-- Mensagem de aviso ou validação -->
                                </div>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="emailSession" value="<?php echo $emailUsuario; ?>">
                    <input type="hidden" name="idAnuncio" value="<?php echo $idAnuncio; ?>">


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
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="radioButtons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
    <script src="../../../modoNoturno.js"></script>
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