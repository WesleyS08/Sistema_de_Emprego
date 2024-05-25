<?php
include "../../services/conexão_com_banco.php";

// Verifique se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se um arquivo foi enviado
    if (isset($_FILES['inputImagem'])) {
        $file = $_FILES['inputImagem'];

        // Diretório base onde as imagens serão armazenadas
        $diretorioBase = '../../assets/ImagesTestes/';


        // Verifica se o diretório base existe; se não, cria-o
        if (!file_exists($diretorioBase)) {
            if (!mkdir($diretorioBase, 0755, true)) {
                die("Erro ao criar o diretório base.");
            }
        }

        // Verifique se a pasta do usuário (com base no ID da pessoa) existe, se não, crie-a
        $idPessoa = $_POST['idPessoa']; // Obtém o ID da pessoa do formulário
        $diretorioUsuario = $diretorioBase . $idPessoa . '/';
        if (!file_exists($diretorioUsuario)) {
            mkdir($diretorioUsuario, 0777, true); // Cria a pasta com permissões de leitura, escrita e execução para todos
        }

        // Gera um nome único para a imagem
        $nome_arquivo = uniqid() . '_' . $file['name'];

        // Caminho completo da imagem
        $caminho = $diretorioUsuario . $nome_arquivo;

        // Move o arquivo para o diretório desejado
        if (move_uploaded_file($file['tmp_name'], $caminho)) {
            // Consulta SQL para obter o CNPJ da empresa com base no ID da pessoa
            $sql = "SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
            $stmt = $_con->prepare($sql);
            $stmt->bind_param("i", $idPessoa); // "i" indica que $idPessoa é um integer
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se há resultados
            if ($result->num_rows > 0) {
                // Obtém o CNPJ da empresa
                $row = $result->fetch_assoc();
                $cnpj = $row["CNPJ"];

                // Consulta SQL para obter o último registro da Tb_Empresa_Questionario para o CNPJ especificado
                $sql = "SELECT * FROM Tb_Empresa_Questionario WHERE Id_Empresa = '$cnpj' ORDER BY Id_Questionario DESC LIMIT 1";

                // Executa a segunda consulta
                $result = $_con->query($sql);

                // Verifica se há resultados na segunda consulta
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $idQuestionario = $row['Id_Questionario'];

                    // Atualiza a coluna ImagemQuestionario na tabela Tb_Questionarios com base no último registro da Tb_Empresa_Questionario
                    $sql_update = "UPDATE Tb_Questionarios SET ImagemQuestionario = '$caminho' WHERE Id_Questionario = $idQuestionario";

                    // Executa a atualização
                    $_con->query($sql_update);

                    // Retorna o caminho da imagem e a mensagem de sucesso para o JavaScript
                    echo json_encode(
                        array(
                            "success" => "Questionário criado com sucesso",
                            "redirect" => "../AvisoQuestionarioCriado/avisoQuestionarioCriado.html",
                            "caminho_imagem" => $caminho
                        )
                    );
                } else {
                    // Se não houver resultados na segunda consulta, retorna uma mensagem de erro
                    echo json_encode(array('error' => 'Nenhum registro encontrado na Tb_Empresa_Questionario para o CNPJ especificado.'));
                }
            } else {
                // Se não houver resultados na primeira consulta, retorna uma mensagem de erro
                echo json_encode(array('error' => 'Nenhum CNPJ encontrado para o ID de pessoa especificado.'));
            }
        } else {
            // Se houver um erro no upload, retorna uma mensagem de erro
            echo json_encode(array('error' => 'Erro ao fazer upload da imagem.'));
        }
    } else {
        // Se não foi enviado um arquivo, retorna uma mensagem de erro
        echo json_encode(array('error' => 'Nenhuma imagem foi enviada.'));
    }
} else {
    // Se o método de requisição não for POST, retorna uma mensagem de erro
    echo json_encode(array('error' => 'Método de requisição inválido.'));
}
?>