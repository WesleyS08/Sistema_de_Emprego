<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

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
$query = "SELECT p.Nome, p.Sobrenome, p.Email, c.Area_de_Interesse, c.Descricao, c.Experiencia, c.Cursos, c.Experiencia, c.Escolaridade, c.Idade, c.Cidade, c.Telefone, c.PCD, c.Genero, c.Estado_Civil, c.Autodefinicao, c.Img_Perfil, c.Banner
          FROM Tb_Pessoas AS p 
          INNER JOIN Tb_Candidato AS c ON p.Id_Pessoas = c.Tb_Pessoas_Id 
          WHERE p.Email = '$emailUsuario'";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosCandidato = mysqli_fetch_assoc($result);
    // Preencher os campos HTML com as informações recuperadas do banco de dados
    $nomeUsuario = $dadosCandidato['Nome'] . ' ' . $dadosCandidato['Sobrenome'];
    $areaUsuario = $dadosCandidato['Area_de_Interesse'];
    $autoDefinicaoUsuario = $dadosCandidato['Autodefinicao'];
    $telefoneUsuario = $dadosCandidato['Telefone'];
    $dataNascimentoUsuario = $dadosCandidato['Idade'];
    $generoUsuario = $dadosCandidato['Genero'];
    $estadoUsuario = $dadosCandidato['Estado_Civil'];
    $cidadeUsuario = $dadosCandidato['Cidade'];
    $sobreUsuario = $dadosCandidato['Descricao'];
    $caminhoImagemPerfil = $dadosCandidato['Img_Perfil'];
    $caminhoImagemBanner = $dadosCandidato['Banner'];
    $experienciaUsuario = $dadosCandidato['Experiencia'];
    $cursoUsuario = $dadosCandidato['Cursos'];
    $escolaridadeUsuario = $dadosCandidato['Escolaridade'];
    $pcdUsuario = $dadosCandidato['PCD'];
} else {
    $nomeUsuario = 'Não informado';
    $areaUsuario = 'Não informado';
    $autoDefinicaoUsuario = 'Não informado';
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
    <title>Perfil</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/editarStyles.css">    
    <link rel="stylesheet" type="text/css" href="../../assets/styles/perfilStyle.css">
</head>
<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../HomeCandidato/homeCandidato.html"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button> 
        <ul>            
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTeste/todosTeste.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="divBackgroundImg" id="divBackgroundImgDefinida">
    <img src="<?php echo $caminhoImagemBanner; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
    <div class="divFotoDePerfil" id="divFotoDePerfilDefinida">
            <img src="<?php echo $caminhoImagemPerfil; ?>" alt=""
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        </div>
        <?php if ($autenticadoComoCandidato) { ?>
            <a class="acessarEditarPerfil" href="../EditarPerfilCandidato/editarPerfilCandidato.php">
                <div>
                    <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" trigger="hover" stroke="bold" state="hover-line"
                        colors="primary:#ffffff,secondary:#ffffff" style="width:30px;height:30px">
                    </lord-icon>
                    <label>Editar</label>
                </div>
            </a>
        <?php } ?>
    </div>
    <div class="divCommon">        
        <div class="containerPerfil">
            <div class="divTitulo">
                <h2 id="nomeUsuario"><?php echo $nomeUsuario ?></h2>
                <p id="breveDescricao"><?php echo $autoDefinicaoUsuario ?></p>
            </div>
            <div class="contentPerfil" id="informacoesIniciais">
                <div class="divFlex">
                    <lord-icon
                        src="https://cdn.lordicon.com/lenjvibx.json"
                        trigger="loop"
                        stroke="bold"
                        state="loop-flutter"
                        colors="primary:#000000,secondary:#e88c30"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <label id="idadeUsuario"><?php echo $dataNascimentoUsuario, " Anos"?></label>
                </div>
                <div class="divFlex">
                    <lord-icon
                        src="https://cdn.lordicon.com/bgebyztw.json"
                        trigger="morph"
                        stroke="bold"
                        state="hover-jumping"
                        colors="primary:#000000,secondary:#e88c30"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <label id="generoUsuario"><?php echo $generoUsuario?></label>
                </div>
                <div class="divFlex">
                    <lord-icon
                        src="https://cdn.lordicon.com/surcxhka.json"
                        trigger="loop"
                        stroke="bold"
                        state="loop-roll"
                        colors="primary:#000000,secondary:#e88c30"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <label id="localUsuario"><?php echo $cidadeUsuario?></label>
                </div>
                <div class="divFlex">
                    <lord-icon
                        src="https://cdn.lordicon.com/lqcyrjta.json"
                        trigger="hover"
                        stroke="bold"
                        colors="primary:#000000,secondary:#e88c30"
                        style="width:30px;height:30px">
                    </lord-icon>                    
                    <label id="pcdUsuario">
                        <?php echo $pcdUsuario == 1 ? 'PCD: Sim' : 'PCD: Não'; ?>
                    </label>
                </div>
            </div>
            <div class="contentPerfil">                
                <h3>Área</h3>
                <p id="areaUsuario"><?php echo $areaUsuario?></p>
            </div>
            <div class="contentPerfil" id="contentSobre">
                <fieldset>
                    <legend><h3>Sobre Mim</h3></legend>
                    <p id="sobre"><?php echo $sobreUsuario?></p>
                </fieldset>
            </div>
            <div class="divGridElementos">
                <div class="contentPerfil">                
                <h3>Habilidades e Tecnologias</h3>
                    <p id="habilidades">
                        <?php
                            // Divida os dados em um array usando traços, vírgulas, dois pontos, etc.
                            $padroes_de_separacao = array("-", ",", ":");

                            // Substitua todos os padrões de separação por uma marca única (§ neste exemplo)
                            $cursoUsuario = str_replace($padroes_de_separacao, "§", $cursoUsuario);

                            // Divida os dados em um array usando a marca única como separador
                            $dados_array = explode("§", $cursoUsuario);

                            // Imprima cada item do array em uma linha separada
                            foreach ($dados_array as $dado) {
                                // Se o dado não estiver vazio, imprima-o
                                if (!empty(trim($dado))) {
                                    echo "- $dado <br>";
                                }
                            }
                        ?>
                    </p>   

                    <!--
                    <ul class="elementosAdicionados" id="habilidadesAdicionadas">
                        <li>Python</li>
                        <li>Azure</li>
                        <li>Cisco Packet Tracer</li>
                        <li>Formatação de Windows</li>
                    </ul>
                    -->                    
                    
                </div>
                <div class="contentPerfil">                
                <h3>Cursos e Formações</h3>
                    <p id="cursos">
                        <?php
                            // Divida os dados em um array usando traços, vírgulas, dois pontos, etc.
                            $padroes_de_separacao = array("-", ",");

                            // Substitua todos os padrões de separação por uma marca única (§ neste exemplo)
                            $escolaridadeUsuario = str_replace($padroes_de_separacao, "§", $escolaridadeUsuario);

                            // Divida os dados em um array usando a marca única como separador
                            $dados_array = explode("§", $escolaridadeUsuario);

                            // Imprima cada item do array em uma linha separada
                            foreach ($dados_array as $dado) {
                                // Se o dado não estiver vazio, imprima-o
                                if (!empty(trim($dado))) {
                                    echo "- $dado <br>";
                                }
                            }
                        ?>
                    </p>
                    
                    <!--
                    <ul class="elementosAdicionados" id="cursosAdicionados">
                        <li>Desenvolvimento de Software Muliplataforma</li>
                        <li>Redes de Computadores</li>
                    </ul>
                    -->
                    
                </div>
                <div class="contentPerfil">                
                <h3>Experiências de Trabalho</h3>
                    <p id="experiencias">
                        <?php
                            // Divida os dados em um array usando traços, vírgulas, dois pontos, etc.
                            $padroes_de_separacao = array("-", ",", ":");

                            // Substitua todos os padrões de separação por uma marca única (§ neste exemplo)
                            $experienciaUsuario = str_replace($padroes_de_separacao, "§", $experienciaUsuario);

                            // Divida os dados em um array usando a marca única como separador
                            $dados_array = explode("§", $experienciaUsuario);

                            // Imprima cada item do array em uma linha separada
                            foreach ($dados_array as $dado) {
                                // Se o dado não estiver vazio, imprima-o
                                if (!empty(trim($dado))) {
                                    echo "- $dado <br>";
                                }
                            }
                        ?>
                    </p>
                    
                    <!--
                    <ul class="elementosAdicionados" id="experienciasAdicionadas">
                        <li>Atendente de Telemarketing</li>
                        <li>Lixeiro</li>
                        <li>Desenvolvimento de Bomba Atômica</li>
                    </ul>
                    -->                    
                </div>
            </div>  
            <div class="contentPerfil" id="contentPerfilPontuacao">
                <div class="divTituloDigitavel">
                    <lord-icon
                    src="https://cdn.lordicon.com/iawrhwdo.json"
                    trigger="loop"
                    stroke="bold"
                    state="loop-cycle"
                    colors="primary:#000000,secondary:#c76f16"
                    style="width:40px;height:40px">
                </lord-icon>
                    <h3>Pontos acumulados</h3>
                    <lord-icon
                    src="https://cdn.lordicon.com/iawrhwdo.json"
                    trigger="loop"
                    stroke="bold"
                    state="loop-cycle"
                    colors="primary:#000000,secondary:#c76f16"
                    style="width:40px;height:40px">
                </lord-icon>
                </div>
                <div class="divPontuacao">
                    <div class="divPontuacaoArea">                        
                        <label class="nomeArea">Tecnologia:</label>
                        <div class="divProgresso"> 
                            <div class="progresso" name="progressoPontos"></div>
                            <label id="pontosTi" name="numPontos">100</label>
                        </div>
                    </div>
                    <div class="divPontuacaoArea">                        
                        <label class="nomeArea">Direito:&nbsp</label>
                        <div class="divProgresso"> 
                            <div class="progresso" name="progressoPontos"></div>
                            <label id="pontosDireito" name="numPontos">100</label>
                        </div>
                    </div>
                    <div class="divPontuacaoArea">                        
                        <label class="nomeArea">Medicina:&nbsp</label>
                        <div class="divProgresso"> 
                            <div class="progresso" name="progressoPontos"></div>
                            <label id="pontosMedicina" name="numPontos">300</label>
                        </div>
                    </div>
                </div>
            </div>           
            <div class="contentPerfil">
                <h3>Contato</h3>
                <div class="divFlexContato">
                    <lord-icon
                        src="https://cdn.lordicon.com/nzixoeyk.json"
                        trigger="hover"
                        colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="email"><?php echo $emailUsuario?></a>
                </div>           
                <div class="divFlexContato">
                    <lord-icon
                        src="https://cdn.lordicon.com/rsvfayfn.json"
                        trigger="hover"
                        colors="primary:#000000"
                        style="width:30px;height:30px">
                    </lord-icon>
                    <a id="telefone"><?php echo $telefoneUsuario ?></a>
                </div>     
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>    
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="acumuloDePontos.js"></script>
</body>
</html>