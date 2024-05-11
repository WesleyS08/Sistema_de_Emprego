<?php
include "../../services/conexão_com_banco.php";

// Verificar se o ID do questionário foi fornecido na URL
if(isset($_GET['id'])) {
    $id_questionario = $_GET['id'];

    // Contar quantas questões estão associadas a um questionário
    $sql = "SELECT COUNT(*) AS total_questoes FROM Tb_Questoes WHERE Id_Questionario = $id_questionario";    
    $result = $_con->query($sql);

    if ($result->num_rows > 0) {        
        $row = $result->fetch_assoc();        
       
        $totalQuestoes = $row['total_questoes'];        
    } else {
        echo "Nenhuma questão encontrada para este questionário.";
    }

    // Consulta para obter os detalhes do questionário com base no ID
    $sql = "SELECT q.Nome, q.Area, q.DataQuestionario, q.Nivel, q.Descricao, q.Tempo, e.Nome_da_Empresa
            FROM Tb_Questionarios q
            JOIN Tb_Empresa_Questionario eq ON q.Id_Questionario = eq.Id_Questionario
            JOIN Tb_Empresa e ON eq.Id_Empresa = e.CNPJ
            WHERE q.Id_Questionario = $id_questionario";
    $result = $_con->query($sql);
    
    if ($result->num_rows > 0) {
        // Extrair os dados do questionário
        $row = $result->fetch_assoc();
        $nomeQuestionario = $row['Nome'];
        $areaQuestionario = $row['Area'];
        $descricaoQuestionario = $row['Descricao'];
        $dataQuestionario = $row['DataQuestionario'];
        $nivelQuestionario = $row['Nivel'];
        $duracaoTeste = $row['Tempo'];
        $nomeEmpresa = $row['Nome_da_Empresa'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">    
    <link rel="stylesheet" type="text/css" href="preparaTeste.css">
</head>
<body>    
</body>
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
            <li><a href="../PerfilCandidato/perfilCandidato.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="containerTeste">
        <article class="articleTeste">
            <header>                
                <h2 id="nomeTeste"><?php echo $nomeQuestionario; ?></h2>
                <div>                    
                    <p>Competências:&nbsp;</p>
                    <p id="areaTeste"><?php echo $areaQuestionario; ?></p>
                </div>
                <div>                    
                    <p>Data do teste:&nbsp;</p>
                    <p id="dataTeste"><?php echo $dataQuestionario; ?></p>
                </div>
            </header>            
            <section class="sectionPrincipal">
                <div class="divImgteste">
                    <!-- Aqui irá a lógica futura de inserção de imagem no questionário, se houver... -->
                </div>
                <div class="divInformacoes">
                    <div>
                        <div class="divLabels"><label>Por: </label><label id="autorTeste"><?php echo $nomeEmpresa ?></label></div>
                        <div class="divLabels"><label>Nível: </label><label id="nivelTeste"><?php echo $nivelQuestionario ?></label></div>                    
                        <div class="divLabels"><label>Duração: </label><label id="duracaoTeste"><?php echo $duracaoTeste . " minutos"; ?></label></div>
                        <div class="divLabels"><label>Questões: </label><label id="quantidadeQuestoes"><?php echo $totalQuestoes ?></label></div>     
                    </div>
                </div>
            </section>            
            <section class="sectionButton">                
                <a href="../Teste/teste.php?id=<?php echo $id_questionario; ?>">
                    <button>Iniciar</button>
                </a>
            </section>
        </article>
    </div>
            <div class="statusTeste">
                <label>Teste realizado.</label>
                <label>Pontuação obtida:</label>                
                <label id="pontuacao">35/50</label>
            </div>           
        </article>
    </div>    
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p>SIAS 2024</p>
    </footer>    
</html>
<?php
    } else {
        // Se não houver questionário com o ID fornecido, exibir uma mensagem de erro
        echo "Questionário não encontrado.";
    }
} else {
    // Se o ID do questionário não foi fornecido na URL, exibir uma mensagem de erro
    echo "ID do questionário não fornecido.";
}
?>