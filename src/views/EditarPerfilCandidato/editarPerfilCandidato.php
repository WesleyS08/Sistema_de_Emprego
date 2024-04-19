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

// Recuperar informações do candidato do banco de dados com base no e-mail do usuário
$query = "SELECT p.Nome, p.Sobrenome, p.Email, c.Descricao, c.Area_de_Interesse, c.Motivacoes, c.Experiencia, c.Cursos, c.Escolaridade, c.Cidade, c.Telefone, c.PCD, c.Idade, c.Data_Nascimento, c.Genero, c.Estado_Civil, c.Autodefinicao, c.Img_Perfil, c.Banner
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
        <a href="../HomeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button> 
        <ul>            
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTeste/todosTeste.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
        <button class="btnVoltar" onclick="window.location.href='../perfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>'"><</button>
        <h2>Editar Perfil</h2>
        </div>
        <div class="divEdicaoPerfil">
            <form method="post" action="../../../src/services/Perfil/PerfilCandidato.php?id=<?php echo $idPessoa; ?>" autocomplete="off" enctype="multipart/form-data">
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
                                <input class="inputAnimado" id="nome" name="nome" type="text" required value= "<?php echo $nomeUsuario?>">
                                <div class="labelLine">Nome</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="area" name="area" type="text" list="areaList" required value="<?php echo $areaUsuario?>">
                                <div class="labelLine">Área de Atuação</div>
                                <datalist id="areaList">
                                    <option>Tecnologia</option>
                                    <option>Medicia</option>
                                    <option>Engenharia</option>
                                    <option>Economia</option>                                        
                                    <option>Vendas</option>  
                                    <option>Educação</option>                                    
                                    <option>Direito</option>                                                                          
                                    <option>Administração</option>                                                                                                            
                                    <option>Agronegócio</option>                                                                                                                                                  
                                    <option>Gastronomia</option>
                                </datalist>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>     
                    <div class="inputUnico">                                       
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="breveDescricao" name="breveDescricao" type="text" placeholder="Ex: Desenvolvedor Fullstack | Certificação AWS | Administrador de Rede |" 
                                value="<?php echo $autoDefinicaoUsuario ?>" required>
                                <div class="labelLine">Breve descrição</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="email" name="email" type="text" value="<?php echo $emailUsuario ?>" required>
                                <div class="labelLine">Email</div>
                            </div>
                            <small id="aviso" name="aviso" style="display: none;">Caso altere o email é necessário
                                    realizar login novamente</small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" maxlength="11" id="telefone" name="telefone" type="text" value="<?php echo $telefoneUsuario ?>"required>
                                <div class="labelLine">Telefone</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
                        <div class="containerInput">
                        <div class="contentInput">
                            <!-- Preenche o campo de data com a data de nascimento do usuário -->
                            <input class="inputAnimado" maxlength="10" id="data" name="data" type="text" value="<?php echo htmlspecialchars($dataNascimentoUsuario); ?>" required>
                            <div class="labelLine">Data de Nascimento</div>
                        </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="genero" name="genero" type="text" list="generoList" value="<?php echo $generoUsuario?>" required>
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
                                <input class="inputAnimado" id="estado" name="estado" type="text" value="<?php echo $estadoUsuario?>"required>
                                <div class="labelLine">Estado Civil</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" id="cidade" name="cidade" type="text" value="<?php echo $cidadeUsuario ?>" required>
                                <div class="labelLine">Cidade</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="divCheckBox">
                        <input type="checkbox" id="pcd" name="pcd" <?php echo $pcdUsuario == 1 ? 'checked' : ''; ?>>
                        <label for="pcd">Pessoa com Deficiência</label>
                    </div>
                    <div class="divTextArea" id="divTextAreaCandidato">                        
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="sobre" id="sobre" type="text" required><?php echo $sobreUsuario?></textarea>
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
                                        <textarea class="textAreaAnimada" name="habilidades" id="habilidades" type="text" required><?php echo $cursoUsuario ?></textarea>
                                        <div class="textArealabelLine">Adicione cursos e tecnologias que você domina</div>
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
                                        <textarea class="textAreaAnimada" name="cursos" id="cursos" type="text" required><?php echo $escolaridadeUsuario ?></textarea>
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
                                        <textarea class="textAreaAnimada" name="experiencias" id="experiencias" type="text" required><?php echo $experienciaUsuario ?></textarea>
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

</body>
</html>