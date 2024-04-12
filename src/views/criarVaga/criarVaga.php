<?php
session_start();

// Inclua o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";



// Verificar se o usuário está autenticado como empresa
$nomeUsuario = isset ($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$emailUsuario = '';

// Verificar se o usuário está autenticado e definir o e-mail do usuário
if (isset ($_SESSION['email_session']) && isset ($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'empresa') {
    // Se estiver autenticado com e-mail/senha e for do tipo empresa
    $emailUsuario = $_SESSION['email_session'];
    
} elseif (isset ($_SESSION['google_session']) && isset ($_SESSION['google_usuario']) && $_SESSION['google_usuario'] == 'empresa') {
    // Se estiver autenticado com o Google e for do tipo empresa
    $emailUsuario = $_SESSION['google_session'];
} else {
    // Se não estiver autenticado como empresa, redirecione para a página de login
    header("Location: ../Login/login.html");
    exit;
}
$nomeUsuario = isset ($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';

// Atribuindo os valores das variáveis de sessão
$emailSession = isset ($_SESSION['email_session']) ? $_SESSION['email_session'] : '';
$tokenSession = isset ($_SESSION['token_session']) ? $_SESSION['token_session'] : '';

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
        <a href="homeCandidato.html"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="#">Anunciar</a></li>
            <li><a href="#">Minhas vagas</a></li>
            <li><a href="#">Meus testes</a></li>
            <li><a href="#">Perfil</a></li>
        </ul>
    </nav>
    <div class="divCommon">
        <div class="divTituloComBtn" id="divTituloCriacaoVaga">
            <a class="btnVoltar"><img src="../../assets/images/icones_diversos/back.svg"></a>
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
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="area" name="area" type="text" list="areaList" required>
                                    <div class="labelLine">Área</div>
                                    <datalist id="areaList">
                                        <option>Tecnologia da Informação</option>
                                        <option>Medicia</option>
                                        <option>Engenharia</option>
                                        <option>Construção Civil</option>
                                        <option>Educação</option>
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
                                    <input class="inputAnimado" name="endereco" id="endereco" type="text" placeholder="Rua Fulano de Tal, 123" required>
                                    <div class="labelLine">Endereço</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerInput">
                                <div class="contentInput">
                                    <input class="inputAnimado" id="numero" name="numero" type="text" placeholder="Número da empresa" required>
                                    <div class="labelLine">Número</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                        </div>
                        <div class="containerInput">
                            <div class="contentInput">
                                <input class="inputAnimado" name="horario" id="horario" type="text" placeholder="De segunda a sexta, das 9:00 às 16:00" required>
                                <div class="labelLine">Carga horária</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>            
                    <div class="divTextArea">                        
                        <div class="containerTextArea">
                            <div class="contentInputTextArea">
                                <textarea class="textAreaAnimada" name="descricao" id="descricao" type="text" required></textarea>
                                <div class="textArealabelLine">Descrição da Vaga</div>
                            </div>
                            <small name="aviso"></small>
                        </div>                    
                        <div class="divFlex" id="divFlexTextArea">
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="requisitos" id="requisitos" type="text" required></textarea>
                                    <div class="textArealabelLine">Requisitos</div>
                                </div>
                                <small name="aviso"></small>
                            </div>
                            <div class="containerTextArea">
                                <div class="contentInputTextArea">
                                    <textarea class="textAreaAnimada" name="beneficios" id="beneficios" type="text" required></textarea>
                                    <div class="textArealabelLine">Benefícios</div>
                                </div>
                                <small name="aviso"></small>
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
        <p>SIAS 2024</p>
    </footer>
    <script src="modoNoturno.js"></script>
    <script src="radioButtons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#cep').on('change', function() {
            var cep = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

            // Verifica se o CEP tem 8 dígitos
            if (cep.length == 8) {
                $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
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