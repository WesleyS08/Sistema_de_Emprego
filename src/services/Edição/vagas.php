<?php
include "../../services/conexão_com_banco.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se todos os campos do formulário estão definidos
    if (isset($_POST['titulo'], $_POST['area'], $_POST['cep'], $_POST['bairro'], $_POST['estado'], $_POST['cidade'], $_POST['endereco'], $_POST['numero'], $_POST['horario'], $_POST['descricao'], $_POST['requisitos'], $_POST['beneficios'], $_POST['categoria'], $_POST['nivel'], $_POST['modalidade'], $_POST['jornada'], $_POST['status'], $_POST['emailSession'])) {

        // Recuperar os valores dos campos do formulário
        $titulo = $_POST['titulo'];
        $area = $_POST['area'];
        $cep = $_POST['cep'];
        $bairro = $_POST['bairro'];
        $estado = $_POST['estado'];
        $cidade = $_POST['cidade'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $horario = $_POST['horario'];
        $descricao = $_POST['descricao'];
        $requisitos = $_POST['requisitos'];
        $beneficios = $_POST['beneficios'];
        $categoria = $_POST['categoria'];
        $nivel = $_POST['nivel'];
        $modalidade = $_POST['modalidade'];
        $jornada = $_POST['jornada'];
        $status = $_POST['status'];

        $emailUsuario = $_POST['emailSession'];
        $idAnuncio = $_POST['idAnuncio'];
        $dataEncerramento = null;

        // Verificar se a vaga está sendo encerrada
        if ($status === "Encerrado") {
            // Definir a data de encerramento como a data atual
            $dataEncerramento = date("Y-m-d H:i:s");
        }

        // Atualizar os dados no banco de dados
        $sql = "UPDATE Tb_Anuncios 
                SET 
                    Titulo = '$titulo', 
                    Area = '$area', 
                    CEP = '$cep', 
                    Bairro = '$bairro', 
                    Estado = '$estado', 
                    Cidade = '$cidade', 
                    Rua = '$endereco', 
                    Numero = '$numero', 
                    Horario = '$horario', 
                    Descricao = '$descricao', 
                    Requisitos = '$requisitos', 
                    Beneficios = '$beneficios', 
                    Nivel_Operacional = '$nivel', 
                    Categoria = '$categoria', 
                    Modalidade = '$modalidade', 
                    Jornada = '$jornada'
                WHERE Id_Anuncios = $idAnuncio";

        // Executar a consulta SQL de atualização
        if (mysqli_query($_con, $sql)) {
            // Montar a consulta SQL de atualização para Tb_Vagas
            $sqlUpdateVagas = "UPDATE Tb_Vagas 
                               SET Status = '$status', 
                                   Data_de_Termino = NOW() 
                               WHERE Tb_Anuncios_Id = $idAnuncio";

            // Executar a consulta SQL de atualização para Tb_Vagas
            if (mysqli_query($_con, $sqlUpdateVagas)) {
                // Redirecionar o usuário para a página da vaga após o sucesso da atualização
                header("Location: ../../views/Vaga/vaga.php?id=$idAnuncio");
                exit(); // Encerrar o script após o redirecionamento
            } else {
                // Se houver um erro na execução da consulta SQL para Tb_Vagas, exibir uma mensagem de erro
                echo "Erro ao atualizar a vaga: " . mysqli_error($_con);
            }
        } else {
            // Se houver um erro na execução da consulta SQL, exibir uma mensagem de erro
            echo "Erro ao atualizar os dados do anúncio: " . mysqli_error($_con);
        }
    }
}

// Fechar a conexão com o banco de dados
mysqli_close($_con);
?>
