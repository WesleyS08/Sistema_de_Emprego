<?php
session_start();

// Inclua o arquivo de conexão com o banco de dados
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Atributos para cada campo
    $tituloVaga = mysqli_real_escape_string($_con, $_POST['titulo']);
    $areaVaga = mysqli_real_escape_string($_con, $_POST['area']);
    $estadoVaga = mysqli_real_escape_string($_con, $_POST['estado']);
    $cidadeVaga = mysqli_real_escape_string($_con, $_POST['cidade']);
    $enderecoVaga = mysqli_real_escape_string($_con, $_POST['endereco']);
    $horarioVaga = mysqli_real_escape_string($_con, $_POST['horario']);
    $descricaoVaga = mysqli_real_escape_string($_con, $_POST['descricao']);
    $requisitosVaga = mysqli_real_escape_string($_con, $_POST['requisitos']);
    $beneficiosVaga = mysqli_real_escape_string($_con, $_POST['beneficios']);
    $jornadaVaga = mysqli_real_escape_string($_con, $_POST['jornada']);
    $modalidadeVaga = mysqli_real_escape_string($_con, $_POST['modalidade']);
    $categoriaVaga = mysqli_real_escape_string($_con, $_POST['tipo']);
    $nivelVaga = mysqli_real_escape_string($_con, $_POST['nivel']);
    $emailSession = mysqli_real_escape_string($_con, $_POST['email_session']);
    $tokenSession = mysqli_real_escape_string($_con, $_POST['token_session']);

    date_default_timezone_set('America/Sao_Paulo'); // Define o fuso horário para São Paulo
    $dataEhoraDeHoje = date("Y-m-d H:i:s"); // Obtém a data e hora atual

    $estado = "aberto";

    // Inserir os dados da vaga na tabela Tb_anuncios para salvar no banco.
    $sql_inserir_vaga = "INSERT INTO Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficicios, Requisitos, Horario, Estado, Jornada) 
                      VALUES ('$categoriaVaga', '$tituloVaga', '$descricaoVaga', '$areaVaga', '$cidadeVaga', '$nivelVaga', NOW(), '$modalidadeVaga', '$beneficiosVaga', '$requisitosVaga', '$horarioVaga', '$estadoVaga', '$jornadaVaga')";


    // Executar a consulta para inserir a vaga
    if (mysqli_query($_con, $sql_inserir_vaga)) {
        echo "Vaga criada com sucesso!";
        
        // Obtendo o ID da vaga recém-inserida
        $idVagaInserida = mysqli_insert_id($_con);
        
        // Consultar o CNPJ da empresa associada ao e-mail da sessão
        $sql_consulta_cnpj = "SELECT CNPJ FROM Tb_Empresa 
                                JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas 
                                WHERE Tb_Pessoas.Email = '$emailSession'";
        
        $result_consulta_cnpj = mysqli_query($_con, $sql_consulta_cnpj);

        // Verificar se a consulta foi bem-sucedida e se o CNPJ foi encontrado
        if ($result_consulta_cnpj && mysqli_num_rows($result_consulta_cnpj) > 0) {
            // Extrair o CNPJ da consulta
            $row = mysqli_fetch_assoc($result_consulta_cnpj);
            $cnpjEmpresa = $row['CNPJ']; // CNPJ da empresa logada
            
            // Preencher a tabela Tb_Vagas
            $sql_preencher_tabela_vagas = "INSERT INTO Tb_Vagas (Tb_Anuncios_Id, Tb_Empresa_CNPJ, Estados, Data_de_Termino) 
                                            VALUES ('$idVagaInserida', '$cnpjEmpresa', '$estado', NULL)";

            if (mysqli_query($_con, $sql_preencher_tabela_vagas)) {
                echo "Tabela Tb_Vagas preenchida com sucesso!";
            } else {
                echo "Erro ao preencher a tabela Tb_Vagas: " . mysqli_error($_con);
            }
        } else {
            echo "Erro ao criar vaga: " . mysqli_error($_con);
        }
    } else {
        echo "Erro ao criar vaga: " . mysqli_error($_con);
    }
} else {
    echo "Erro ao criar vaga: " . mysqli_error($_con);
}
?>
