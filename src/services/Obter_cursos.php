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

// Função para extrair informações de cursos do Sebrae
// Função para extrair informações de cursos do Sebrae
function extrairInformacoesCursosSebrae($url)
{
    // URL base
    $urlBase = "https://sebrae.com.br";

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

    // Obtendo todos os elementos com a classe "sb-components__card"
    $cursos = $dom->getElementsByTagName('div');

    // Array para armazenar as informações dos cursos
    $informacoesCursos = [];

    // Iterando sobre os elementos e extraindo informações
    foreach ($cursos as $curso) {
        // Verificando se o elemento tem a classe "sb-components__card"
        if ($curso->getAttribute('class') === 'sb-components__card') {
            // Obtendo o nome do curso
            $nome = $curso->getElementsByTagName('h2')[0]->nodeValue;

            // Obtendo a duração do curso
            $duracao = '';
            foreach ($curso->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->getAttribute('class') === 'sb-components__card__info__details__icon ic1') {
                    $duracao = $child->nodeValue;
                    break;
                }
            }

            // Obtendo a categoria do curso
            $categoria = '';
            foreach ($curso->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->getAttribute('class') === 'sb-components__card__info__tags__theme familia-default') {
                    $categoria = $child->nodeValue;
                    break;
                }
            }

            $nivel = "Técnico"; // Definindo o nível como "Técnico" (baseado no seu comentário)

            // Obtendo o link do curso
            $link = $urlBase . $curso->getElementsByTagName('a')[0]->getAttribute('href');

            // Mostrar os valores obtidos para verificar se estão corretos
            var_dump($nome, $duracao, $categoria, $nivel, $link);

            // Obtendo a URL da imagem
            $imgUrl = ''; // Deixando vazio, pois não foi encontrado na página fornecida

            // Armazenando as informações do curso em um array
            $informacoesCursos[] = [
                'Nome' => $nome,
                'Duração' => $duracao,
                'Categoria' => $categoria,
                'Nível' => $nivel,
                'Link' => $link,
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
        $stmt->bind_param("sssssss", $curso['Nome'], $curso['Duração'], $curso['Nivel'], $curso['Link'], $curso['URL da Imagem'], $curso['Tipo'], $categoria);

        // Executando a declaração
        $resultado = $stmt->execute();

        if ($resultado === false) {
            die("Erro na execução da declaração: " . $stmt->error);
        }
    }

    // Fechando a declaração
    $stmt->close();
}

// URL da página das Fatecs
$urlFatec = "https://www.cps.sp.gov.br/fatec/cursos-oferecidos-pelas-fatecs/";

// URL da página das Etecs
$urlEtec = "https://www.cps.sp.gov.br/etec/cursos-oferecidos-pelas-etecs/";

// URL da página do Sebrae
$urlSebrae = "https://sebrae.com.br/sites/PortalSebrae/cursosonline";

// Obtendo informações dos cursos das Fatecs
$informacoesFatec = extrairInformacoesCursosFatec($urlFatec);

// Obtendo informações dos cursos das Etecs
$informacoesEtec = extrairInformacoesCursosEtec($urlEtec);

// Obtendo informações dos cursos do Sebrae
$informacoesSebrae = extrairInformacoesCursosSebrae($urlSebrae);

// Conectando ao banco de dados
$conexao = conectarBancoDados();

// Inserindo informações dos cursos no banco de dados
inserirInformacoesCursos($informacoesFatec, $conexao, "Fatecs");
inserirInformacoesCursos($informacoesEtec, $conexao, "Etecs");
inserirInformacoesCursos($informacoesSebrae, $conexao, "Sebrae");

// Fechando a conexão com o banco de dados
$conexao->close();
?>