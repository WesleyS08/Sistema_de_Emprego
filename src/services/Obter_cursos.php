<?php

// Função para extrair informações de cursos das Etecs
function extrairInformacoesCursosEtec($url)
{
    // Obtendo o conteúdo da página
    $html = file_get_contents($url);

    // Criando um novo objeto DOMDocument
    $dom = new DOMDocument();

    // Suprimindo os erros de HTML mal formado
    libxml_use_internal_errors(true);

    // Carregando o HTML na DOMDocument
    $dom->loadHTML($html);

    // Restaurando os erros de HTML mal formado
    libxml_use_internal_errors(false);

    // Obtendo todos os elementos com a classe "listagem-posts-item"
    $cursos = $dom->getElementsByTagName('span');

    // Array para armazenar as informações dos cursos
    $informacoesCursos = [];

    // Iterando sobre os elementos e extraindo informações
    foreach ($cursos as $curso) {
        // Verificando se o elemento tem a classe "listagem-posts-item"
        if ($curso->getAttribute('class') === 'listagem-posts-item') {
            // Obtendo o nome do curso
            $nome = $curso->getElementsByTagName('h3')[0]->nodeValue;

            // Obtendo o link do curso
            $link = $curso->getElementsByTagName('a')[0]->getAttribute('href');

            // Definindo a duração do curso para 3 semestres
            $duracao = "3 semestres";

            // Obtendo o tipo de curso
            $tipoCurso = '';
            foreach ($curso->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->getAttribute('class') === 'lista-tipo lista-tipo-cursos') {
                    $tipoCurso = $child->getElementsByTagName('span')[1]->nodeValue;
                    break;
                }
            }

            // Obtendo o nível do curso
            $nivel = '';
            foreach ($curso->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->getAttribute('class') === 'term-lista-tipo') {
                    $nivel = $child->nodeValue;
                    break;
                }
            }

            // Definindo o preço como "Gratuito"
            $preco = "Gratuito";

            // Obtendo a URL da imagem
            $imgUrl = $curso->getElementsByTagName('img')[0]->getAttribute('data-lazy-src');

            // Armazenando as informações do curso em um array
            $informacoesCursos[] = [
                'Nome' => $nome,
                'Link' => $link,
                'Duração' => $duracao,
                'Nível' => $nivel,
                'Tipo de Curso' => $tipoCurso,
                'Preço' => $preco,
                'URL da Imagem' => $imgUrl
            ];
        }
    }

    // Retornando as informações dos cursos
    return $informacoesCursos;
}

// Função para extrair informações de cursos das Fatecs
function extrairInformacoesCursosFatec($url)
{
    // Obtendo o conteúdo da página
    $html = file_get_contents($url);

    // Criando um novo objeto DOMDocument
    $dom = new DOMDocument();

    // Suprimindo os erros de HTML mal formado
    libxml_use_internal_errors(true);

    // Carregando o HTML na DOMDocument
    $dom->loadHTML($html);

    // Restaurando os erros de HTML mal formado
    libxml_use_internal_errors(false);

    // Obtendo todos os elementos com a classe "listagem-posts-item"
    $cursos = $dom->getElementsByTagName('span');

    // Array para armazenar as informações dos cursos
    $informacoesCursos = [];

    // Iterando sobre os elementos e extraindo informações
    foreach ($cursos as $curso) {
        // Verificando se o elemento tem a classe "listagem-posts-item"
        if ($curso->getAttribute('class') === 'listagem-posts-item') {
            // Obtendo o nome do curso
            $nome = $curso->getElementsByTagName('h3')[0]->nodeValue;

            // Obtendo o link do curso
            $link = $curso->getElementsByTagName('a')[0]->getAttribute('href');

            // Definindo a duração do curso para 6 semestres
            $duracao = "6 semestres";

            // Obtendo o tipo de curso
            $tipoCurso = '';
            foreach ($curso->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->getAttribute('class') === 'lista-tipo lista-tipo-cursos') {
                    $tipoCurso = $child->getElementsByTagName('span')[1]->nodeValue;
                }
            }

            // Obtendo o nível do curso
            $nivel = 'Superior';

            // Definindo o preço como "Gratuito"
            $preco = "Gratuito";

            // Obtendo a URL da imagem
            $imgUrl = $curso->getElementsByTagName('img')[0]->getAttribute('data-lazy-src');

            // Armazenando as informações do curso em um array
            $informacoesCursos[] = [
                'Nome' => $nome,
                'Link' => $link,
                'Duração' => $duracao,
                'Nível' => $nivel,
                'Tipo de Curso' => $tipoCurso,
                'Preço' => $preco,
                'URL da Imagem' => $imgUrl
            ];
        }
    }

    // Retornando as informações dos cursos
    return $informacoesCursos;
}


// Função para extrair informações de cursos do novo site
// Função para extrair informações de cursos do site EV
function extrairInformacoesCursosEV($url)
{
    // Obtendo o conteúdo da página
    $html = file_get_contents($url);

    // Criando um novo objeto DOMDocument
    $dom = new DOMDocument();

    // Suprimindo os erros de HTML mal formado
    libxml_use_internal_errors(true);

    // Carregando o HTML na DOMDocument
    $dom->loadHTML($html);

    // Restaurando os erros de HTML mal formado
    libxml_use_internal_errors(false);

    // Obtendo todos os elementos com a classe "m-card"
    $cursos = $dom->getElementsByTagName('article');

    // Array para armazenar as informações dos cursos
    $informacoesCursos = [];

    // Iterando sobre os elementos e extraindo informações
    foreach ($cursos as $curso) {
        // Verificando se o elemento tem a classe "m-card"
        if (strpos($curso->getAttribute('class'), 'm-card') !== false) {
            // Obtendo o nome do curso
            $nome = $curso->getElementsByTagName('h3')[0]->nodeValue;

            // Obtendo a descrição do curso
            $descricao = $curso->getElementsByTagName('p')[0]->nodeValue;

            // Obtendo a duração do curso
            $duracao = '';
            foreach ($curso->getElementsByTagName('p') as $p) {
                if (strpos($p->nodeValue, 'Duração') !== false) {
                    $duracao = $p->getElementsByTagName('strong')[0]->nodeValue;
                    break;
                }
            }

            // Obtendo o nível do curso
            $nivel = '';
            foreach ($curso->getElementsByTagName('p') as $p) {
                if (strpos($p->nodeValue, 'Nível') !== false) {
                    $nivel = $p->getElementsByTagName('strong')[0]->nodeValue;
                    break;
                }
            }

            // Obtendo o link do curso
            $link = "https://www.ev.org.br" . $curso->getElementsByTagName('a')[1]->getAttribute('href');

            // Definindo o preço como "Gratuito"
            $preco = "Gratuito";

            // Obtendo a URL da imagem (se disponível)
            $imgUrl = 'https://d1yjjnpx0p53s8.cloudfront.net/styles/logo-thumbnail/s3/032013/bradesco_v_rgb.png?itok=58ZX99XK';
        

            // Armazenando as informações do curso em um array
            $informacoesCursos[] = [
                'Nome' => $nome,
                'Link' => $link,
                'Duração' => $duracao,
                'Nível' => $nivel,
                'Tipo de Curso' => '',
                'Preço' => $preco,
                'URL da Imagem' => $imgUrl
            ];
        }
    }

    // Retornando as informações dos cursos
    return $informacoesCursos;
}

// Função para conectar ao banco de dados
function conectarBancoDados()
{
    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "SIAS";

    // Criando conexão
    $conexao = new mysqli($host, $usuario, $senha, $banco);

    // Verificando a conexão
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    return $conexao;
}

// Função para inserir informações dos cursos no banco de dados
function inserirInformacoesCursos($informacoesCursos, $conexao, $categoria)
{
    // Preparando a query SQL
    $sql = "INSERT INTO Tb_Cursos (Nome_do_Curso, Duração, Nivel, Link, URL_da_Imagem, Tipo, Categoria, Ultima_Atualizacao) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    // Preparando a declaração
    $stmt = $conexao->prepare($sql);

    if ($stmt === false) {
        die("Erro na preparação da declaração: " . $conexao->error);
    }

    // Iterando sobre as informações dos cursos
    foreach ($informacoesCursos as $curso) {
        // Ligando parâmetros
        $stmt->bind_param("sssssss", $curso['Nome'], $curso['Duração'], $curso['Nível'], $curso['Link'], $curso['URL da Imagem'], $curso['Tipo de Curso'], $categoria);

        // Executando a declaração
        $resultado = $stmt->execute();

        if ($resultado === false) {
            die("Erro na execução da declaração: " . $stmt->error);
        }
    }

    // Fechando a declaração
    $stmt->close();
}

// URLs das páginas
$urlFatec = "https://www.cps.sp.gov.br/fatec/cursos-oferecidos-pelas-fatecs/";
$urlEtec = "https://www.cps.sp.gov.br/etec/cursos-oferecidos-pelas-etecs/";
$urlEV = "https://www.ev.org.br/cursos";

// Obtendo informações dos cursos
$informacoesFatec = extrairInformacoesCursosFatec($urlFatec);
$informacoesEtec = extrairInformacoesCursosEtec($urlEtec);
$informacoesEV = extrairInformacoesCursosEV($urlEV);

// Conectando ao banco de dados
$conexao = conectarBancoDados();

// Inserindo informações dos cursos no banco de dados
inserirInformacoesCursos($informacoesFatec, $conexao, "Fatecs");
inserirInformacoesCursos($informacoesEtec, $conexao, "Etecs");
inserirInformacoesCursos($informacoesEV, $conexao, "Bradesco");

// Fechando a conexão com o banco de dados
$conexao->close();
?>