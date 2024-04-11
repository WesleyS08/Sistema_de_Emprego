<?php
session_start();

if (!isset($_SESSION['nome_usuario']) || $_SESSION['tipo_usuario'] !== 'candidato') {
    // Redirecionar o usuário se não estiver autenticado como candidato
    header("Location: /caminho/para/a/pagina/de/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar se o ID do anúncio está presente e é um número inteiro válido
    $idAnuncio = isset($_POST['id_anuncio']) ? intval($_POST['id_anuncio']) : null;

    if ($idAnuncio) {
        // Obter o CPF do candidato
        $cpfCandidato = $_SESSION['cpf_candidato']; // ou qualquer campo que identifique unicamente o candidato

        // Inserir a candidatura no banco de dados
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "INSERT INTO Tb_Inscricoes (Tb_Anuncios_Id, Tb_Empresa_CNPJ, Tb_Candidato_CPF, Data_de_Inscricao) 
                VALUES ($idAnuncio, '$cnpjEmpresa', '$cpfCandidato', '$dataAtual')";



        // Redirecionar o usuário de volta para a página do anúncio
        header("Location: ../../src/views/Vaga/vaga.php?id=$idAnuncio");
        exit;
    } else {
        // ID do anúncio inválido
        // Redirecionar o usuário de volta para a página inicial ou exibir uma mensagem de erro
    }
} else {
    // Método de solicitação inválido
    // Redirecionar o usuário de volta para a página inicial ou exibir uma mensagem de erro
}
?>
