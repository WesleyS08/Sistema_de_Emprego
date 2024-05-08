<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

$idPessoa = isset($_GET['id']) ? $_GET['id'] : '';

// Verificar se o usuário está autenticado como candidato
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'candidato') {
    // Se estiver autenticado como candidato
    $autenticadoComoCandidato = true;
    $emailUsuario = '';

    // Definir o e-mail do usuário com base no tipo de sessão
    if (isset($_SESSION['email_session'])) {
        $emailUsuario = $_SESSION['email_session'];
    } elseif (isset($_SESSION['google_session'])) {
        $emailUsuario = $_SESSION['google_session'];
    }
} else {
    // Se não estiver autenticado como candidato, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}


// Quartar consulta para selecionar o tema que  a pessoa selecionou 
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

// Recuperar informações do candidato do banco de dados com base no e-mail do usuário
$query = "SELECT p.Nome, p.Email, c.Descricao, c.Area_de_Interesse, c.Motivacoes, c.Experiencia, c.Cursos, c.Escolaridade, c.Cidade, c.Telefone, c.PCD, c.Idade, c.Data_Nascimento, c.Genero, c.Estado_Civil, c.Autodefinicao, c.Img_Perfil, c.Banner
          FROM Tb_Pessoas AS p 
          INNER JOIN Tb_Candidato AS c ON p.Id_Pessoas = c.Tb_Pessoas_Id 
          WHERE p.Email = '$emailUsuario'";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosCandidato = mysqli_fetch_assoc($result);
    // Preencher os campos HTML com as informações recuperadas do banco de dados
    $nomeUsuario = $dadosCandidato['Nome'];
    $areaUsuario = $dadosCandidato['Area_de_Interesse'];
    $autoDefinicaoUsuario = $dadosCandidato['Autodefinicao'];
    $telefoneUsuario = $dadosCandidato['Telefone'];
    $dataNascimentoUsuario = $dadosCandidato['Data_Nascimento'];
    $generoUsuario = $dadosCandidato['Genero'];
    $estadoUsuario = $dadosCandidato['Estado_Civil'];
    $cidadeUsuario = $dadosCandidato['Cidade'];
    $sobreUsuario = $dadosCandidato['Descricao'];
    $experienciaUsuario = $dadosCandidato['Experiencia'];
    $cursoUsuario = $dadosCandidato['Cursos'];
    $escolaridadeUsuario = $dadosCandidato['Escolaridade'];
    $pcdUsuario = $dadosCandidato['PCD'];
} else {
    $nomeUsuario = 'Não informado';
    $areaUsuario = 'Não informado';
    $autoDefinicaoUsuario = 'Não Informado';
    $telefoneUsuario = 'Não informado';
    $dataNascimentoUsuario = 'Não informado';
    $generoUsuario = 'Não informado';
    $estadoUsuario = 'Não informado';
    $cidadeUsuario = 'Não Informado';
    $sobreUsuario = 'Não Informado';
}

$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_Anuncios 
    ORDER BY Area ASC
";

// Preparar e executar a consulta para obter as áreas únicas
$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();

if ($result_areas && $result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row['Area']; // Adicionar áreas ao array
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/editarStyles.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../HomeCandidato/homeCandidato.php"><img id="logo"
                src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTeste/todosTeste.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn">
            <a class="btnVoltar" href='../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>'> <img
                    src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Editar Perfil</h2>
        </div>
        <div class="divEdicaoPerfil">
            <form method="post" action="../../../src/services/Perfil/PerfilCandidato.php?id=<?php echo $idPessoa; ?>"
                autocomplete="off" enctype="multipart/form-data">
                <div class="divBackgroundImg">
                    <div class="btnEditarFundo">
                        <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold"
                            colors="primary:#f5f5f5,secondary:#f5f5f5" style="width:34px;height:34px">
                        </lord-icon>
                    </div>
                    <input type="file" id="fundo_upload" name="fundo_upload" style="display: none;"
                        accept="image/jpeg, image/png">

                    <!-- Adicione um campo de entrada de arquivo -->
                    <input type="file" id="foto_perfil_upload" name="foto_perfil_upload" style="display: none;"
                        accept="image/jpeg, image/png">

                    <!-- Div de Foto de Perfil -->
                    <div class="divFotoDePerfil" id="divFotoDePerfil" style="position: relative;">
                        <!-- Div para exibir a imagem de perfil -->
                        <div id="preview_container"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center;border-radius: 50%">
                        </div>
                        <!-- Div para o ícone de edição -->
                        <div class="divIconeEditar">
                            <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold"
                                colors="primary:#f5f5f5,secondary:#f5f5f5" style="width:110px;height:110px">
                            </lord-icon>
                        </div>
                    </div>

                </div>
                <div class="divContentPerfil">
                    <div class="divAvisoInicial">
                        <small>Há informações que podem estar faltando!</small>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="nome" name="nome" type="text" required
                                    value="<?php echo $nomeUsuario ?>">
                                <div class="labelLine">Nome</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="area" name="area" type="text" list="areaList" required
                                    value="<?php echo $areaUsuario ?>">
                                <div class="labelLine">Área de Atuação</div>
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
                    <div class="inputUnico">
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="breveDescricao" name="breveDescricao" type="text"
                                    placeholder="Ex: Desenvolvedor Fullstack | Certificação AWS | Administrador de Rede |"
                                    value="<?php echo $autoDefinicaoUsuario ?>" required>
                                <div class="labelLine">Breve descrição</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="email" name="email" type="text"
                                    value="<?php echo $emailUsuario ?>" required>
                                <div class="labelLine">Email</div>
                            </div>
                            <small id="aviso" name="aviso" style="display: none;">Caso altere o email é necessário
                                realizar login novamente</small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" maxlength="15" id="telefone" name="telefone" type="text"
                                    value="<?php echo $telefoneUsuario ?>" required>
                                <div class="labelLine">Telefone</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                            <?php
                            $dataNascimentoUsuario_formatada = date('d/m/Y', strtotime($dataNascimentoUsuario));
                            ?>

                            <div class="contentInput">
                                <!-- Preenche o campo de data com a data de nascimento do usuário -->
                                <input class="inputAnimado" maxlength="10" id="data" name="data" type="text"
                                    value="<?php echo htmlspecialchars($dataNascimentoUsuario_formatada); ?>" required>
                                <div class="labelLine">Data de Nascimento</div>
                            </div>

                            <small name="aviso"></small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="genero" name="genero" type="text" list="generoList"
                                    value="<?php echo $generoUsuario ?>" required>
                                <div class="labelLine">Gênero</div>
                                <datalist id="generoList">
                                    <option>Homem Cisgênero</option>
                                    <option>Mulher Cisgênero</option>
                                    <option>Homem Transgênero</option>
                                    <option>Mulher Transgênero</option>
                                    <option>Não Binário</option>
                                    <option>Agênero</option>
                                </datalist>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                            <div class="contentInput">
                                <select class="inputAnimado" id="estado" name="estado" required>
                                    <option value="Solteiro(a)" <?php echo ($estadoUsuario == 'Solteiro(a)') ? 'selected' : ''; ?>>Solteiro(a)</option>
                                    <option value="Casado(a)" <?php echo ($estadoUsuario == 'Casado(a)') ? 'selected' : ''; ?>>Casado(a)</option>
                                    <option value="Divorciado(a)" <?php echo ($estadoUsuario == 'Divorciado(a)') ? 'selected' : ''; ?>>Divorciado(a)</option>
                                    <option value="Viúvo(a)" <?php echo ($estadoUsuario == 'Viúvo(a)') ? 'selected' : ''; ?>>Viúvo(a)</option>
                                    <option value="União Estável" <?php echo ($estadoUsuario == 'União Estável') ? 'selected' : ''; ?>>União Estável</option>
                                    <!-- Adicione mais opções conforme necessário -->
                                </select>
                                <div class="labelLine">Estado Civil</div>
                            </div>
                            <small name="aviso"></small>
                        </div>


                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="cidade" name="cidade" type="text"
                                    value="<?php echo $cidadeUsuario ?>" required>
                                <div class="labelLine">Cidade</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="divCheckBox">
                        <input type="checkbox" id="pcd" name="pcd" <?php echo $pcdUsuario == 1 ? 'checked' : ''; ?>>
                        <label for="pcd">PCD</label>
                    </div>
                    <div class="divTextArea" id="divTextAreaCandidato">
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="sobre" id="sobre" type="text"
                                    required><?php echo $sobreUsuario ?></textarea>
                                <div class="textArealabelLine">Sobre Mim</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="divElementos">

                        <div class="divAdiconaElementos">
                            <h2>Habilidades e Tecnologias</h2>
                            <div class="divTextArea">
                                <div class="containerTextArea">
                                    <div class="contentInputTextArea">
                                        <textarea class="textAreaAnimada" name="habilidades" id="habilidades"
                                            type="text" required><?php echo $cursoUsuario ?></textarea>
                                        <div class="textArealabelLine">Adicione cursos e tecnologias que você domina
                                        </div>
                                    </div>
                                    <small name="aviso"></small>
                                </div>
                            </div>
                        </div>
                        <div class="divAdiconaElementos">
                            <h2>Cursos e Formações</h2>
                            <div class="divTextArea">
                                <div class="containerTextArea">
                                    <div class="contentInputTextArea">
                                        <textarea class="textAreaAnimada" name="cursos" id="cursos" type="text"
                                            required><?php echo $escolaridadeUsuario ?></textarea>
                                        <div class="textArealabelLine">Adicione suas formações</div>
                                    </div>
                                    <small name="aviso"></small>
                                </div>
                            </div>
                        </div>
                        <div class="divAdiconaElementos">
                            <h2>Experiências de Trabalho</h2>
                            <div class="divTextArea">
                                <div class="containerTextArea">
                                    <div class="contentInputTextArea">
                                        <textarea class="textAreaAnimada" name="experiencias" id="experiencias"
                                            type="text" required><?php echo $experienciaUsuario ?></textarea>
                                        <div class="textArealabelLine">Adicione suas experiências profissionais</div>
                                    </div>
                                    <small name="aviso"></small>
                                </div>
                            </div>
                        </div>

                        <!--
                        <div class="divAdiconaElementos">                            
                            <h2>Habilidades e Tecnologias</h2>     
                            <div class="divTituloAddElementos">                   
                                <input class="inputSimples" id="habilidade" name="habilidade" type="text" placeholder="Ex: Manutenção de Computadores" required>
                                <div class="btnAdicionarElemento" id="adicionaHabilidade">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/zrkkrrpl.json"
                                        trigger="hover"
                                        stroke="bold"
                                        state="hover-rotation"
                                        colors="primary:#000000,secondary:#000000"
                                        style="width:36px;height:36px">
                                    </lord-icon>
                                </div>
                            </div>                            
                            <small name="aviso" id="avisoHabilidades"></small>
                            <ul class="elementosAdicionados" id="habilidadesAdicionadas"> 
                            </ul>
                        </div>
                        -->

                        <!--<div class="divAdiconaElementos">
                            <h2>Cursos e Formações</h2> 
                            <div class="divTituloAddElementos">                       
                                <input class="inputSimples" id="curso" name="curso" type="text" placeholder="Ex: Design de Interiores" required>
                                <div class="btnAdicionarElemento" id="adicionaCurso">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/zrkkrrpl.json"
                                        trigger="hover"
                                        stroke="bold"
                                        state="hover-rotation"
                                        colors="primary:#000000,secondary:#000000"
                                        style="width:36px;height:36px">
                                    </lord-icon>
                                </div>
                            </div>     
                            <small name="aviso" id="avisoCursos"></small>
                            <ul class="elementosAdicionados" id="cursosAdicionados">
                            </ul>
                        </div-->

                        <!--<div class="divAdiconaElementos">
                            <h2>Experiências de Trabalho</h2> 
                            <div class="divTituloAddElementos">                       
                                <input class="inputSimples" id="experiencia" name="experiencia" type="text" placeholder="Ex: Atendente de Telemarketing" required>
                                <div class="btnAdicionarElemento" id="adicionaExperiencia">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/zrkkrrpl.json"
                                        trigger="hover"
                                        stroke="bold"
                                        state="hover-rotation"
                                        colors="primary:#000000,secondary:#000000"
                                        style="width:36px;height:36px">
                                    </lord-icon>
                                </div>
                            </div>     
                            <small name="aviso" id="avisoExperiencias"></small>
                            <ul class="elementosAdicionados" id="experienciasAdicionadas">
                            </ul>
                        </div>-->

                    </div>
                    <div class="divBtnAtualizar">
                        <input type="submit" value="Atualizar">
                    </div>


                    <form method="post" action="../../../src/services/ExcluirConta/excluirContaCandidato.php">
                        <div style="    text-align: center;
    margin-top: 20px;
    cursor: pointer;
    border: 1px solid #c90000;
    font-size: 16pt;
    width: 200px;
    height: 50px;
    background-color: #ef0505;
    color: whitesmoke;
    border-radius: 14px;
    transition: 0.2s ease;
    box-shadow: 0px 0px 8px silver;
    align-content: center;
    margin-left: 35%;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                            <a
                                href="../../../src/services/ExcluirConta/excluirContaCandidato.php?id=<?php echo $idPessoa; ?>">Excluir
                                Conta</a>
                        </div>
                    </form>









                    <input type="hidden" name="email_usuario" value="<?php echo $emailUsuario; ?>">
                </div>
            </form>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="mostraIcone.js"></script>
    <script src="avisoInicial.js"></script>
    <script src="adicionaElementos.js"></script>
    <script src="mascaras.js"></script>
    <script>
        // Função para exibir o aviso quando o usuário alterar o campo de email
        function exibirAviso() {
            document.getElementById('aviso').style.display = 'block';
        }

        // Adicionar um evento de clique ao campo de email para chamar a função exibirAviso()
        document.getElementById('email').addEventListener('change', exibirAviso);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php
    // Obtém a URL da imagem de perfil do banco de dados
    $urlImagemPerfil = $dadosCandidato['Img_Perfil'];
    ?>

    <script>
        $(document).ready(function () {
            // Adiciona um evento de clique ao botão de edição de foto de perfil
            $('#divFotoDePerfil').click(function () {
                $('#foto_perfil_upload').click(); // Simula o clique no campo de upload de foto de perfil
            });

            // Evento disparado quando um arquivo é selecionado para a foto de perfil
            $('#foto_perfil_upload').change(function () {
                var file = this.files[0];
                var reader = new FileReader();

                // Define o que fazer quando o arquivo é lido
                reader.onload = function (e) {
                    $('#preview_container').css('background-image', 'url(' + e.target.result + ')'); // Define a imagem de fundo com a imagem carregada
                    $('#preview_container').show(); // Exibe o contêiner de pré-visualização
                };

                // Lê o arquivo como uma URL de dados
                reader.readAsDataURL(file);
            });

            // Carrega a imagem de perfil ao carregar a página
            $('#preview_container').css('background-image', 'url(<?php echo $urlImagemPerfil; ?>)');
        });
    </script>

    <?php
    $urlImagemFundo = $dadosCandidato['Banner'];
    ?>
    <script>
        $(document).ready(function () {
            // Função para carregar a imagem de fundo com base na URL fornecida
            function carregarImagemDeFundo(url) {
                $('.divBackgroundImg').css('background-image', 'url(' + url + ')');
            }

            // Carrega a imagem de fundo ao carregar a página
            carregarImagemDeFundo('<?php echo $urlImagemFundo; ?>');

            // Adiciona um evento de clique ao botão de edição do fundo
            $('.btnEditarFundo').click(function () {
                // Simula o clique no campo de upload de fundo
                $('#fundo_upload').click();
            });

            // Evento disparado quando um arquivo é selecionado
            $('#fundo_upload').change(function () {
                // Lê o arquivo selecionado
                var file = this.files[0];
                var reader = new FileReader();

                // Define o que fazer quando o arquivo é lido
                reader.onload = function (e) {
                    // Aplica a imagem de fundo à div
                    carregarImagemDeFundo(e.target.result);
                };

                // Lê o arquivo como uma URL de dados
                reader.readAsDataURL(file);
            });
        });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
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

</body>

</html>