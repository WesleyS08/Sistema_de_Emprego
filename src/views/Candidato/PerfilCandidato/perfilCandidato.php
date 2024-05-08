<?php
include "../../services/conexão_com_banco.php";

// Iniciar a sessão
session_start();

$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';
$candidatoInscrito = false;
$autenticadoComoPublicador = false;

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google
    $emailUsuario = $_SESSION['google_session'];
}

$idPessoa = isset($_GET['id']) ? $_GET['id'] : '';

$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();

    // Verificar resultado
    $result = $stmt->get_result();
    $tema = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['Tema'] : null;

    $stmt->close();
} else {
    die("Erro ao preparar a consulta para obter o tema.");
}

// Recuperar informações do candidato do banco de dados com base no ID fornecido na URL
$query = "SELECT p.Nome, p.Email, c.Area_de_Interesse, c.CPF, c.Descricao, c.Experiencia, c.Cursos, c.Experiencia, c.Escolaridade, c.Idade, c.Cidade, c.Telefone, c.PCD, c.Genero, c.Estado_Civil, c.Autodefinicao, c.Img_Perfil, c.Banner
          FROM Tb_Pessoas AS p 
          INNER JOIN Tb_Candidato AS c ON p.Id_Pessoas = c.Tb_Pessoas_Id 
          WHERE p.Id_Pessoas = '$idPessoa'";
$result = mysqli_query($_con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $dadosCandidato = mysqli_fetch_assoc($result);
    // Preencher os campos HTML com as informações recuperadas do banco de dados
    $nomeUsuario = $dadosCandidato['Nome'];
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
    $cpfCandidato = $dadosCandidato['CPF'];

    // Verificar se o perfil tem o mesmo email da sessão que está ativada
    if ($emailUsuario == $dadosCandidato['Email']) {
        // O perfil tem o mesmo email da sessão
        $podeEditar = true;
    } else {
        // O perfil não tem o mesmo email da sessão
        $podeEditar = false;
    }

    // Lógica da pontuação dos questionários

    // Recuperar o CPF do candidato da consulta resultante
    if ($result && mysqli_num_rows($result) > 0) {
        $dadosCandidato = mysqli_fetch_assoc($result);
        //$cpfCandidato = $dadosCandidato['CPF'];

        // Consultar todas as áreas únicas dos questionários respondidos pelo candidato
        $queryAreas = "SELECT DISTINCT q.Area 
                    FROM Tb_Resultados r
                    INNER JOIN Tb_Questionarios q ON r.Tb_Questionarios_ID = q.Id_Questionario
                    WHERE r.Tb_Candidato_CPF = '$cpfCandidato'";

        // Array para armazenar a pontuação total de cada área
        $pontuacaoPorArea = array();

        // Executar a consulta para obter todas as áreas únicas dos questionários respondidos pelo candidato
        $resultAreas = mysqli_query($_con, $queryAreas);

        // Verificar se a consulta retornou resultados
        if ($resultAreas && mysqli_num_rows($resultAreas) > 0) {
            // Loop através de todas as áreas recuperadas
            while ($row = mysqli_fetch_assoc($resultAreas)) {
                $area = $row['Area'];

                // Consultar a pontuação total do candidato na área atual
                $queryPontuacaoArea = "SELECT SUM(Nota) AS PontuacaoTotal FROM Tb_Resultados WHERE Tb_Candidato_CPF = '$cpfCandidato' AND Tb_Questionarios_ID IN (SELECT Id_Questionario FROM Tb_Questionarios WHERE Area = '$area')";
                $resultPontuacaoArea = mysqli_query($_con, $queryPontuacaoArea);

                // Verificar se a consulta retornou resultados
                if ($resultPontuacaoArea && mysqli_num_rows($resultPontuacaoArea) > 0) {
                    $rowPontuacao = mysqli_fetch_assoc($resultPontuacaoArea);
                    $pontuacaoTotal = $rowPontuacao['PontuacaoTotal'];
                    // Armazenar a pontuação total da área atual no array
                    $pontuacaoPorArea[$area] = $pontuacaoTotal;
                } else {
                    // Se não houver pontuação registrada para a área atual, definir a pontuação como 0
                    $pontuacaoPorArea[$area] = 0;
                }
            }
        }
    }

} else {

    echo "Candidato não encontrado.";
    
    exit(); // Termina o script após exibir a mensagem
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
        <a href="../homeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button> 
        <ul>            
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divBackgroundImg" id="divBackgroundImgDefinida">
    <img src="<?php echo $caminhoImagemBanner; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
    <div class="divFotoDePerfil" id="divFotoDePerfilDefinida">
            <img src="<?php echo $caminhoImagemPerfil; ?>" alt=""
                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        </div>
        <?php if ($podeEditar) { ?>
            <a class="acessarEditarPerfil" href="../EditarPerfilCandidato/editarPerfilCandidato.php?id=<?php echo $idPessoa; ?>">
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
                    <?php

                    arsort($pontuacaoPorArea);
                    
                    foreach ($pontuacaoPorArea as $area => $pontuacao) {
                        echo "<div class='divPontuacaoArea'>";
                        echo "<label class='nomeArea'>$area:</label>";
                        echo "<div class='divProgresso'>";
                        echo "<div class='progresso'></div>"; // Adicione a classe 'progresso' aqui
                        echo "<label class='numPontos'>$pontuacao</label>"; // Adicione a classe 'numPontos' aqui
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
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
        <p class="sinopse">SIAS 2024</p>
    </footer>    
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="acumuloDePontos.js"></script>
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