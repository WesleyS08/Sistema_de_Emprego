<?php

include "../../services/conexão_com_banco.php";
session_start(); // Inicia a sessão

$emailUsuario = ''; // Supondo que o email do usuário esteja armazenado na sessão
$idPessoa = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session'])) {
    // Se estiver autenticado com e-mail/senha
    $emailUsuario = $_SESSION['email_session'];
} elseif (isset($_SESSION['google_session'])) {
    // Se estiver autenticado com o Google
    $emailUsuario = $_SESSION['google_session'];
}
// Verifique se o email do usuário está definido
if (!empty($emailUsuario)) {
    // Consulta SQL para obter o ID da pessoa pelo email
    $sql_id_pessoa = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
    $stmt_id_pessoa = $_con->prepare($sql_id_pessoa);

    if ($stmt_id_pessoa) {
        $stmt_id_pessoa->bind_param("s", $emailUsuario);
        $stmt_id_pessoa->execute();
        $stmt_id_pessoa->store_result(); // Armazena o resultado para obter o número de linhas
        if ($stmt_id_pessoa->num_rows > 0) {
            $stmt_id_pessoa->bind_result($idPessoa); // Vincula o resultado a uma variável
            $stmt_id_pessoa->fetch(); // Busca o valor do resultado
            // Agora $idPessoa contém o ID da pessoa com o email fornecido
        } else {
            // Se nenhum resultado for encontrado, você pode tratar isso aqui
            echo "Nenhum resultado encontrado para o email fornecido.";
        }
        $stmt_id_pessoa->close(); // Fecha a declaração preparada
    } else {
        // Se houver um erro na preparação da consulta, trate-o aqui
        echo "Erro na preparação da consulta.";
    }
} else {
    // Se o email do usuário não estiver definido, exiba uma mensagem de erro
    echo "Email do usuário não definido.";
}

$sql = "SELECT q.Id_Questionario, q.Nome, q.Area, e.Nome_da_Empresa 
FROM Tb_Questionarios q
INNER JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
INNER JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
INNER JOIN Tb_Pessoas p ON e.Tb_Pessoas_Id = p.Id_Pessoas";
$result = $_con->query($sql);

$_con->close();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testes</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
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
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>

        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">T</h1>
        <i class="pisca"></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" type="text" placeholder="Pesquisar">
                    <button class="searchButton">
                        <lord-icon
                            src="https://cdn.lordicon.com/kkvxgpti.json"
                            trigger="hover"
                            colors="primary:#f5f5f5"
                            style="width:36px;height:36px">
                        </lord-icon>
                    </button>
                </div>
                <div id="mostraFiltros">
                    <h3>Filtros</h3>
                    
                    <img id="iconeFiltro" src="../../assets/images/icones_diversos/showHidden.svg">
                </div>
                <div class="containerFiltros">
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Área:</label>
                        <select class="selectArea">
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
                        </select>
                    </div>
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Criador do teste:</label>
                        <input class="selectArea" type="text" id="criadorFiltro" name="criadorFiltro" placeholder="Cisco, Microsoft, etc">
                    </div>
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Nível:</label>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="basico" value="Básico" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="intermediario" value="Intermediário" required>
                        <input class="checkBoxTipo" type="checkbox" name="nivel" id="experiente" value="Experiente" required>
                        <label for="basico" class="btnCheckBox" id="btnBasico">Básico</label>
                        <label for="intermediario" class="btnCheckBox" id="btnIntermediario">Intermediário</label>
                        <label for="experiente" class="btnCheckBox" id="btnExperiente">Experiente</label>
                    </div>
                    <div class="contentFiltro">
                        <button>Aplicar</button>
                    </div>
                </div>                
            </div>
            <div class="divGridTestes">               
                <?php
                
                if ($result->num_rows > 0) {
                    // Contador para controlar a exibição em grids 3x3
                    $contador = 0;
                
                    // Loop através dos resultados da consulta
                    while ($row = $result->fetch_assoc()) {
                        // Extrai os dados do questionário
                        $idQuestionario = $row['Id_Questionario'];
                        $nome = $row['Nome'];
                        $area = $row['Area'];
                        $nomeEmpresa = $row['Nome_da_Empresa'];
                
                        // Saída HTML para cada questionário
                        echo "<a class='testeCarrosselLink' href='../PreparaTeste/preparaTeste.php?id=$idQuestionario'>";
                        echo '<article class="testeCarrossel">';
                        echo '<div class="divAcessos">';
                        echo '<img src="../../../imagens/people.svg"></img>';
                        echo '<small class="qntdAcessos">800</small>';
                        echo '</div>';
                        echo '<img src="../../../imagens/excel.svg"></img>';
                        echo '<div class="divDetalhesTeste divDetalhesTesteCustom">';
                        echo '<div>';
                        echo '<p class="nomeTeste">' . $nome . '</p>';
                        echo '<small class="autorTeste">' . $nomeEmpresa . '</small><br>';
                        echo '<small class="competenciasTeste">' . $area . '</small>';
                        echo '</div>';
                        echo '</div>';
                        echo '</article>';
                        echo '</a>';
                    }
                } else {
                    echo "<p> Nenhum questionário encontrado.</p>";
                }              
                ?>
            </div>  
        </div>
    </div>
  
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="tituloDigitavel.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>    
    <script src="../../../modoNoturno.js"></script>
</body>
<style>
    /* Adiciona espaçamento entre os questionários */
    .testeCarrosselCustom {
        margin-bottom: 20px;
    }

    /* Define o efeito de hover */
    .testeCarrosselCustom:hover {
        transform: scale(1.05); /* Aumenta em 5% ao passar o mouse */
        transition: transform 0.3s ease; /* Transição suave com duração de 0.3 segundos */
    }
</style>
</html>