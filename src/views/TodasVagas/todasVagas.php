<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vagas</title>
    <link rel="stylesheet" type="text/css" href="../../assets/styles/homeStyles.css">
    <link rel="stylesheet" type="text/css" href="../../assets/styles/todosStyle.css">
</head>
<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="menuBtn">
            <img src="../../../imagens/menu.svg">
        </label>
        <a href="../homeCandidato/homeCandidato.php"><img id="logo" src="../../assets/images/logos_empresa/logo_sias.png"></a> 
        <button class="btnModo"><img src="../../../imagens/moon.svg"></button> 
        <ul>            
            <li><a href="../TodasVagas/todasVagas.php">Vagas</a></li>
            <li><a href="../TodosTestes/todosTestes.php">Testes</a></li>
            <li><a href="../Cursos/cursos.php">Cursos</a></li>
            <li><a href="../../../index.php">Deslogar</a></li>
            <li><a href="../PerfilCandidato/perfilCandidato.php">Perfil</a></li>
        </ul>
    </nav>
    <div class="divTituloDigitavel" id="divTituloDigitavelTodos">
        <h1 id="tituloAutomatico">V</h1>
        <i></i>
    </div>
    <div class="divCommon">
        <div class="container">
            <div class="divPesquisa">
                <div class="divFlexInput">
                    <input class="inputPesquisa" placeholder="Pesquisar">
                    <button class="searchButton">
                        <lord-icon
                            src="https://cdn.lordicon.com/kkvxgpti.json"
                            trigger="hover"
                            colors="primary:#f5f5f5"
                            style="width:36px;height:36px">
                        </lord-icon>
                    </button>
                </div>
                <div id="mostraFiltros">
                    <h3>Filtros</h3>
                    <img id="iconeFiltro" src="../../assets/images/icones_diversos/showHidden.svg">
                </div>
                <div class="containerFiltros">
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Área:</label>
                        <select class="selectArea">
                            <option>Tecnologia</option>
                            <option>Medicia</option>
                            <option>Engenharia</option>
                            <option>Economia</option>                                        
                            <option>Vendas</option>  
                            <option>Educação</option>                                    
                            <option>Direito</option>                                                                          
                            <option>Administração</option>                                                                                                            
                            <option>Agronegócio</option>                                                                                                                                                  
                            <option>Gastronomia</option>
                        </select>
                    </div>
                    <div class="contentFiltro">
                        <label class="nomeFiltro">Tipo:</label>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="jovemAprendiz" value="Jovem Aprendiz" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="estagio" value="Estágio" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="clt" value="CLT" required>
                        <input class="checkBoxTipo" type="checkbox" name="tipo" id="pj" value="PJ" required>
                        <label for="jovemAprendiz" class="btnCheckBox" id="btnJovemAprendiz">Jovem Aprendiz</label>
                        <label for="estagio" class="btnCheckBox" id="btnEstagio">Estágio</label>
                        <label for="clt" class="btnCheckBox" id="btnClt">CLT</label>
                        <label for="pj" class="btnCheckBox" id="btnPj">PJ</label>
                    </div>
                    <div class="contentFiltro" id="flexContent">
                        <label class="nomeFiltro" for="apenasVagasAbertas">Apenas vagas abertas:</label>
                        <input type="checkbox" id="apenasVagasAbertas">
                    </div>
                    <div class="contentFiltro">
                        <button>Aplicar</button>
                    </div>
                </div>                
            </div>
            <div class="divGridVagas">
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
                <a class="postLink">
                    <article class="post">
                        <header>
                            <img src="../../../imagens/estagio.svg">
                            <label class="tipoVaga">Estágio</label>
                        </header>
                        <section>
                            <h3 class="nomeVaga">Analista de Suporte</h3>
                            <p class="empresaVaga">Microsoft</p>
                        </section>
                        <label class="statusVaga" style="color: green;">Aberta</label>
                        <label class="dataVaga">10/03/2024</label>
                    </article>
                </a>
            </div>
        </div>
    </div>
    <footer>
        <a>Política de Privacidade</a>
        <a>Nosso contato</a>
        <a>Avalie-nos</a>
        <p class="sinopse">SIAS 2024</p>
    </footer>    
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="checkButtons.js"></script>
    <script src="mostrarFiltros.js"></script>    
    <script src="tituloDigitavel.js"></script>
    <script src="../../../modoNoturno.js"></script>
</body>
</html>