<?php
session_start();
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
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <label id="logo">SIAS</label>
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button>
        <ul>
            <li><a href="#">Anunciar</a></li>
            <li><a href="#">Minhas vagas</a></li>
            <li><a href="#">Meus testes</a></li>
            <li><a href="#">Perfil</a></li>
        </ul>
    </nav>
    <article>
        <h2>Criação de Vaga</h2>
        <form id="formvaga" method="POST" action="../../../src/services/cadastros/vaga.php">
            <div class="divFlexBox">
                <div class="divEsquerda">
                    <div class="inputsLadoALado">
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
                                    <option>TI</option>
                                </datalist>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="inputsLadoALado">
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
                    <div class="inputsLadoALado">
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
                                <input class="inputAnimado" name="horario" id="horario" type="text"
                                    placeholder="De segunda a sexta, das 9:00 às 16:00" required>
                                <div class="labelLine">Carga horária</div>
                            </div>
                            <small name="aviso"></small>
                        </div>
                    </div>
                    <div class="containerInput">
                        <div class="contentInputTextArea">
                            <textarea class="textAreaAnimada" name="descricao" id="descricao" type="text"
                                required></textarea>
                            <div class="textArealabelLine">Descrição da Vaga</div>
                        </div>
                        <small name="aviso"></small>
                    </div>
                </div>
                <div class="divDireita">
                    <div class="containerInput">
                        <div class="contentInputTextArea">
                            <textarea class="textAreaAnimada" name="requisitos" id="requisitos" type="text"
                                required></textarea>
                            <div class="textArealabelLine">Requisitos</div>
                        </div>
                        <small name="aviso"></small>
                    </div>
                    <div class="containerInput">
                        <div class="contentInputTextArea">
                            <textarea class="textAreaAnimada" name="beneficios" id="beneficios" type="text"
                                required></textarea>
                            <div class="textArealabelLine">Benefícios</div>
                        </div>
                        <small name="aviso"></small>
                    </div>
                </div>
            </div>
            <div class="divRadios">
                <div class="divRadiosContent">
                    <h3>Jornada:</h3>
                    <input type="radio" name="jornada" id="meioPeriodo" value="Meio período" required>
                    <input type="radio" name="jornada" id="integral" value="Integral" required>
                    <label for="meioPeriodo" class="btnRadio" id="btnMeioPeriodo">Meio período</label>
                    <label for="integral" class="btnRadio" id="btnIntegral">Integral</label>
                </div>
                <div class="divRadiosContent">
                    <h3>Modalidade:</h3>
                    <input type="radio" name="modalidade" id="remoto" value="Remoto" required>
                    <input type="radio" name="modalidade" id="presencial" value="Presencial" required>
                    <label for="remoto" class="btnRadio" id="btnRemoto">Remoto</label>
                    <label for="presencial" class="btnRadio" id="btnPresencial">Presencial</label>
                </div>
                <div class="divRadiosContent">
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
                <div class="divRadiosContent">
                    <h3>Nível:</h3>
                    <input type="radio" name="nivel" id="medio" value="Ensino Médio" required>
                    <input type="radio" name="nivel" id="tecnico" value="Técnico" required>
                    <input type="radio" name="nivel" id="superior" value="Superior" required>
                    <label for="medio" class="btnRadio" id="btnMedio">Ensino Médio</label>
                    <label for="tecnico" class="btnRadio" id="btnTecnico">Técnico</label>
                    <label for="superior" class="btnRadio" id="btnSuperior">Superior</label>
                </div>
            </div>
            <input type="hidden" name="emailSession" value="<?php echo $emailUsuario; ?>">
            
            <div class="divSalvar">
            <input type="submit" value="Salvar" class="btnSalvar">
            </div>
        </form>
    </article>
    <script src="modoNoturno.js"></script>
    <script src="radioButtons.js"></script>
</body>

</html>