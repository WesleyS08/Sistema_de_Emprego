<?php
session_start();

// Inclua o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";



// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset($_SESSION['email_session']) && isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    // Se estiver autenticado com e-mail/senha e for do tipo empresa
    $emailUsuario = $_SESSION['email_session'];

} elseif (isset($_SESSION['google_session']) && isset($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    // Se estiver autenticado com o Google e for do tipo empresa
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}
$nomeUsuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Atribuindo os valores das variáveis de sessão
$emailSession = isset($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset($_SESSION['token_session']) ? $_SESSION['token_session'] : '';


// Primeira consulta para obter o ID da pessoa logada
$sql = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $_con->prepare($sql);

// Verifique se a preparação da declaração foi bem-sucedida
if ($stmt) {
    // Vincule o parâmetro ao placeholder na consulta
    $stmt->bind_param("s", $emailUsuario);
    // Execute a declaração
    $stmt->execute();
    // Obtenha o resultado da consulta
    $result = $stmt->get_result();
    // Verifique se a consulta retornou resultados
    if ($result->num_rows > 0) {
        // Obtenha o ID da pessoa
        $row = $result->fetch_assoc();
        $idPessoa = $row['Id_Pessoas'];
    } else {
    }
    $stmt->close();
}
// Quartar consulta para selecionar o tema que  a pessoa selecionou 
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

// Verifique se a preparação foi bem-sucedida
if ($stmt) {
    // Execute a query com o parâmetro
    $stmt->bind_param('i', $idPessoa); // Vincula o parâmetro
    $stmt->execute();

    // Obter resultado usando o método correto
    $result = $stmt->get_result(); // Obtenha o resultado como mysqli_result
    if ($result) {
        $row = $result->fetch_assoc(); // Obter a linha como array associativo
        if ($row && isset($row['Tema'])) {
            $tema = $row['Tema'];
        } else {
            $tema = null; // No caso de não haver resultado
        }
    } else {
        $tema = null; // Se o resultado for nulo
    }
} else {
    die("Erro ao preparar a query.");
}

$sql_areas = "
    SELECT DISTINCT Area 
    FROM Tb_Anuncios 
    ORDER BY Area ASC
";

// Preparar e executar a consulta para obter as áreas únicas
$stmt_areas = $_con->prepare($sql_areas);
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();



if ($result_areas && $result_areas->num_rows > 0) {
    while ($row = $result_areas->fetch_assoc()) {
        $areas[] = $row['Area']; // Adicionar áreas ao array
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anunciar</title>
    <link rel="stylesheet" type="text/css" href="criaVaga.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../HomeRecrutador/homeRecrutador.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="../CriarVaga/criarVaga.php">Anunciar</a></li>
            <li><a href="../MinhasVagas/minhasVagas.php">Minhas vagas</a></li>
            <li><a href="../MeusTestes/meusTestes.php">Meus testes</a></li><!--Arrumar esse link  -->
            <li><a href="../PerfilRecrutador/perfilRecrutador.php?id=<?php echo $idPessoa; ?>">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <a class="btnVoltar"  href="../HomeRecrutador/homeRecrutador.php"><img src="../../assets/images/icones_diversos/back.svg"></a>
            <h2>Criação de Vaga</h2>
        </div>
        <form id="formvaga" method="POST" action="../../../src/services/cadastros/vaga.php" autocomplete="off">
            <div class="containerForm">
                <div class="containerSuperior">
                    <div class="divInputs">
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="titulo" name="titulo" type="text" required>
                                    <div class="labelLine">Título</div>
                                </div>
                                <small name="aviso" id="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="area" name="area" type="text" list="areaList"
                                        required>
                                    <div class="labelLine">Área</div>
                                    <datalist id="areaList">
                                        <?php
                                        foreach ($areas as $area) {
                                            echo "<option value='$area'>$area</option>";
                                        }
                                        ?>
                                    </datalist>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cep" name="cep" type="text" required>
                                    <div class="labelLine">CEP</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="bairro" name="bairro" type="text" required>
                                    <div class="labelLine">Bairro</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="estado" id="estado" type="text" required>
                                    <div class="labelLine">Estado</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="cidade" name="cidade" type="text" required>
                                    <div class="labelLine">Cidade</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="divFlex">
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" name="endereco" id="endereco" type="text"
                                        placeholder="Rua Fulano de Tal, 123" required>
                                    <div class="labelLine">Endereço</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="numero" name="numero" type="text"
                                        placeholder="Número da empresa" required>
                                    <div class="labelLine">Número</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" name="horario" id="horario" type="text"
                                    placeholder="De segunda a sexta, das 9:00 às 16:00" required>
                                <div class="labelLine">Carga horária</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="divTextArea">
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="descricao" id="descricao" type="text"
                                    required></textarea>
                                <div class="textArealabelLine">Descrição da Vaga</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                        <div class="divFlex" id="divFlexTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="requisitos" id="requisitos" type="text"
                                        required></textarea>
                                    <div class="textArealabelLine">Requisitos</div>
                                </div>
                                <small name="aviso">Separe os elementos por vírgula</small>
                            </div>
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="beneficios" id="beneficios" type="text"
                                        required></textarea>
                                    <div class="textArealabelLine">Benefícios</div>
                                </div>
                                <small name="aviso">Separe os elementos por vírgula</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="containerInferior">
                    <div class="divFlexRadios">
                        <div>
                            <div class="divRadioContent">
                                <h3>Tipo de profissional:</h3>
                                <input type="radio" name="tipo" id="jovemAprendiz" value="Jovem Aprendiz" required>
                                <input type="radio" name="tipo" id="estagio" value="Estágio" required>
                                <input type="radio" name="tipo" id="clt" value="CLT" required>
                                <input type="radio" name="tipo" id="pj" value="PJ" required>
                                <label for="jovemAprendiz" class="btnRadio" id="btnJovemAprendiz">Jovem Aprendiz</label>
                                <label for="estagio" class="btnRadio" id="btnEstagio">Estágio</label>
                                <label for="clt" class="btnRadio" id="btnClt">CLT</label>
                                <label for="pj" class="btnRadio" id="btnPj">PJ</label>
                            </div>
                            <div class="divRadioContent">
                                <h3>Nível de aprendizado:</h3>
                                <input type="radio" name="nivel" id="medio" value="Ensino Médio" required>
                                <input type="radio" name="nivel" id="tecnico" value="Técnico" required>
                                <input type="radio" name="nivel" id="superior" value="Superior" required>
                                <label for="medio" class="btnRadio" id="btnMedio">Ensino Médio</label>
                                <label for="tecnico" class="btnRadio" id="btnTecnico">Ensino Técnico</label>
                                <label for="superior" class="btnRadio" id="btnSuperior">Ensino Superior</label>
                            </div>
                        </div>
                        <div>
                            <div class="divRadioContent">
                                <h3>Modalidade:</h3>
                                <input type="radio" name="modalidade" id="remoto" value="Remoto" required>
                                <input type="radio" name="modalidade" id="presencial" value="Presencial" required>
                                <label for="remoto" class="btnRadio" id="btnRemoto">Remoto</label>
                                <label for="presencial" class="btnRadio" id="btnPresencial">Presencial</label>
                            </div>
                            <div class="divRadioContent">
                                <h3>Jornada:</h3>
                                <input type="radio" name="jornada" id="meioPeriodo" value="Meio período" required>
                                <input type="radio" name="jornada" id="integral" value="Tempo integral" required>
                                <label for="meioPeriodo" class="btnRadio" id="btnMeioPeriodo">Meio período</label>
                                <label for="integral" class="btnRadio" id="btnIntegral">Tempo integral</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="emailSession" value="<?php echo $emailUsuario; ?>">
                    <div class="divSalvar">
                        <input type="submit" value="Salvar" class="btnSalvar">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Defina uma variável JavaScript para armazenar o tema obtido do banco de dados
        var temaDoBancoDeDados = "<?php echo $tema; ?>";
    </script>
    <script src="../../../modoNoturno.js"></script>
    <!-- Eu movi o titulo digitavel pra cá, para pegar o nome do usario que está com seção  -->
    <script>
        var nomeUsuario = "<?php echo $nomeUsuario; ?>";

        if (nomeUsuario.trim() !== '') {
            // O nome do usuário não está vazio, execute o código de animação
            setTimeout(() => {
                const titulo = document.querySelector("#tituloAutomatico");
                const interval = 150; // variável do tempo de digitação

                function DeterminaHorario() {
                    let hora = new Date().getHours().toString().padStart(2, '0');
                    if (hora < 12) {
                        return "om dia";
                    } else if (hora < 18) {
                        return "oa tarde";
                    } else {
                        return "oa noite";
                    }
                }

                let text1 = `${DeterminaHorario()}, ${nomeUsuario}!`;

                function showText(titulo, text1, interval) {
                    let char = text1.split("").reverse();

                    let typer = setInterval(() => {
                        if (!char.length) {
                            return clearInterval(typer);
                        }

                        let next = char.pop();
                        titulo.innerHTML += next;

                    }, interval);
                }

                showText(titulo, text1, interval);
            },);
        } else {
            console.log("Nome do usuário está vazio!");
        }
    </script>

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
    <script src="radioButtons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let tituloValido = false; // Variável para rastrear se o título é válido

            function verificarTitulo(palavra, callback) {
                if (palavra) {
                    $.ajax({
                        url: "verificar-palavra.php", // Endpoint para verificar a palavra
                        type: "POST",
                        data: { palavra: palavra },
                        success: function (response) {
                            console.log("Resposta recebida:", response); // Log para depuração
                            try {
                                const resultado = JSON.parse(response); // Converte para objeto
                                if (resultado.proibido) { // Se contiver palavra proibida
                                    $("#aviso").text("O título contém palavras proibidas. Por favor, escolha outro título.");
                                    tituloValido = false;
                                } else if (!resultado.existe) { // Se a palavra não existe
                                    $("#aviso").text("Palavra não existe, por favor digite outra palavra.");
                                    tituloValido = false;
                                } else {
                                    $("#aviso").text(""); // Limpa a mensagem se a palavra existir
                                    tituloValido = true;
                                }
                            } catch (e) {
                                console.error("Erro ao processar resposta do servidor:", e);
                                $("#aviso").text("Erro ao processar resposta do servidor.");
                                tituloValido = false;
                            }
                            callback(); // Notifica que a verificação terminou
                        },
                        error: function (xhr, status, error) {
                            console.error("Erro na requisição AJAX:", error);
                            $("#aviso").text("Erro ao verificar palavra. Tente novamente.");
                            tituloValido = false;
                            callback(); // Notifica que houve erro
                        },
                        timeout: 10000 // Aumenta o tempo limite para 10 segundos
                    });
                } else {
                    $("#aviso").text("Por favor, insira uma palavra antes de sair."); // Se estiver vazio
                    tituloValido = false;
                    callback(); // Notifica que a verificação terminou
                }
            }

            // Verificar o título ao sair do campo
            $("#titulo").on("blur", function () {
                const palavra = $(this).val();
                verificarTitulo(palavra, function () {
                    console.log("Verificação do título concluída."); // Log para depuração
                });
            });

            // Impede o envio do formulário se o título não for válido
            $("#formvaga").on("submit", function (e) {
                const palavra = $("#titulo").val();

                // Use uma função de retorno de chamada para aguardar a resposta do AJAX
                verificarTitulo(palavra, function () {
                    if (!tituloValido) { // Se título é inválido
                        e.preventDefault(); // Impede o envio
                        alert("O formulário não pode ser enviado, o título é inválido."); // Mostra mensagem
                    }
                });

                // Bloqueia o envio do formulário para aguardar a resposta do AJAX
                if (!tituloValido) {
                    e.preventDefault(); // Impede o envio até que o AJAX confirme que o título é válido
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#cep').on('change', function () {
                var cep = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

                // Verifica se o CEP tem 8 dígitos
                if (cep.length == 8) {
                    $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function (data) {
                        if (!("erro" in data)) {
                            $('#bairro').val(data.bairro);
                            $('#estado').val(data.uf);
                            $('#cidade').val(data.localidade);
                            $('#endereco').val(data.logradouro);
                        } else {
                            alert('CEP não encontrado.');
                        }
                    });
                } else {
                    alert('CEP inválido. Por favor, insira um CEP válido com 8 dígitos.');
                }
            });
        });
    </script>
</body>

</html>