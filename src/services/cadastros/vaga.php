<?php
include "../conexão_com_banco.php"; // Inclua o arquivo de conexão com o banco de dados

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
        $categoriaVaga = $_POST['categoria']; 
        $nivelVaga = $_POST['nivel'];

        session_start();

        // Verificar se a empresa está logada
        if(isset($_SESSION['CNPJ'])) {
            $cnpj_logado = $_SESSION['CNPJ'];

            // Consulta SQL para selecionar o CNPJ da empresa autenticada
            $sql_empresa = "SELECT Tb_Empresa.CNPJ 
                            FROM Tb_Pessoas 
                            JOIN Tb_Empresa ON Tb_Pessoas.Id_Pessoas = Tb_Empresa.Tb_Pessoas_Id 
                            WHERE Tb_Empresa.CNPJ = '$cnpj_logado'";
            $result_empresa = mysqli_query($_con, $sql_empresa);

            // Verificar se a consulta foi bem-sucedida e se o CNPJ foi encontrado
            if ($result_empresa && mysqli_num_rows($result_empresa) > 0) {
                // Inserir os dados da vaga na tabela Tb_anuncios para salvar no banco.
                $sql_inserir_vaga = "INSERT INTO Tb_anuncios (titulo, area, estado, cidade, endereco, horario, descricao, requisitos, beneficios, jornada, modalidade, categoria, nivel) 
                                     VALUES ('$tituloVaga', '$areaVaga', '$estadoVaga', '$cidadeVaga', '$enderecoVaga', '$horarioVaga', '$descricaoVaga', '$requisitosVaga', '$beneficiosVaga', '$jornadaVaga', '$modalidadeVaga', '$categoriaVaga', '$nivelVaga')";

                if (mysqli_query($_con, $sql_inserir_vaga)) {
                    echo "Vaga criada com sucesso!";
                } else {
                    echo "Erro ao criar vaga: " . mysqli_error($_con);
                }
            } else {
                // Se a consulta não retornar resultados, quer dizer que o CNPJ da empresa logada não foi encontrado
                echo "Erro: Empresa não encontrada.";
            }

        } else {
            // Se a empresa não estiver logada, redirecione para a página de login
            header("Location: ../Login/login.html");
            exit;
        }
    } else {
        echo "Erro: Todos os campos do formulário devem ser preenchidos.";
    }

?>
