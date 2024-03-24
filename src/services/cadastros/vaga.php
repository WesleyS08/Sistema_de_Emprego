<?php
session_start();

// Inclua o arquivo de conexão com o banco de dados
include "../conexão_com_banco.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Atributos para cada campo
    $tituloVaga = $_POST['titulo'];
    $areaVaga = $_POST['area'];
    $estadoVaga = $_POST['estado'];
    $cidadeVaga = $_POST['cidade'];
    $enderecoVaga = $_POST['endereco'];
    $horarioVaga = $_POST['horario'];
    $descricaoVaga = $_POST['descricao'];
    $requisitosVaga = $_POST['requisitos'];
    $beneficiosVaga = $_POST['beneficios'];
    $jornadaVaga = $_POST['jornada'];
    $modalidadeVaga = $_POST['modalidade'];
    $categoriaVaga = $_POST['tipo'];
    $nivelVaga = $_POST['nivel'];
    $emailSession = $_POST['email_session'];
    $tokenSession = $_POST['token_session'];
    $dataEhoraDeHoje = date("Y-m-d H:i:s");
    $estado = "aberto";

    $emailSession = mysqli_real_escape_string($_con, $_POST['email_session']);

    $sql_verificar_empresa = "SELECT * FROM Tb_Empresa 
    JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas 
    WHERE Tb_Pessoas.Email = '$emailSession'";

    $result_verificar_empresa = mysqli_query($_con, $sql_verificar_empresa);

    // Verificar se a consulta foi bem-sucedida e se o email foi encontrado
    if ($result_verificar_empresa && mysqli_num_rows($result_verificar_empresa) > 0) {
        // Inserir os dados da vaga na tabela Tb_anuncios para salvar no banco.
        $sql_inserir_vaga = "INSERT INTO Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficicios, Requisitos, Horario, Estado, Jornada) 
                            VALUES ('$categoriaVaga', '$tituloVaga', '$descricaoVaga', '$areaVaga', '$cidadeVaga', '$nivelVaga', '$dataEhoraDeHoje', '$modalidadeVaga', '$beneficiosVaga', '$requisitosVaga', '$horarioVaga', '$estadoVaga', '$jornadaVaga')";

        if (mysqli_query($_con, $sql_inserir_vaga)) {
            echo "Vaga criada com sucesso!";
            
            // Obtendo o ID da vaga recém-inserida
            $idVagaInserida = mysqli_insert_id($_con);
            
            $sql_consulta_cnpj = "SELECT CNPJ FROM Tb_Empresa JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas WHERE Tb_Pessoas.Email = '$emailSession'";

            // Executar a consulta
            $result_consulta_cnpj = mysqli_query($_con, $sql_consulta_cnpj);

            // Verificar se a consulta foi bem-sucedida e se o CNPJ foi encontrado
            if ($result_consulta_cnpj && mysqli_num_rows($result_consulta_cnpj) > 0) {
                // Extrair o CNPJ da consulta
                $row = mysqli_fetch_assoc($result_consulta_cnpj);
                $cnpjEmpresa = $row['CNPJ']; // CNPJ da empresa logada
                
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
        // Se a consulta não retornar resultados, quer dizer que a empresa não está logada
        echo "Erro: Empresa não encontrada.";
        echo "<br>";
        echo "Email: $emailSession";
        echo "<br>";
        echo "Token: $tokenSession";
    }
}
?>
