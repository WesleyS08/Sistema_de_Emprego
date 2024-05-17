<?php
// Receber a lista de palavras do cliente
$palavras = isset($_POST['palavras']) ? $_POST['palavras'] : [];
$palavras = array_map('strtolower', array_map('trim', $palavras));

// Lista de palavras proibidas
$palavrasProibidas = [
    // Discriminação por gênero
    "homem", "mulher", "menina", "menino", "masculino", "feminino",

    // Lista de palavrões
    "animaldeteta", "arrombado", "arrombado_con", "asshole", "baba-ovo", 
    "babaca", "babaovo", "bacura_con", "bagos", "baitola", "bebum", 
    "benfiquista", "biba", "bicha", "biroska", "bisca", "bitch", 
    "bixa", "boazuda", "bocal", "boceta", "boioca", "boiola", "bolagato", 
    "bolcat","boquete", "boqueteiro", "bostana", "brioco", "bronha", "buca", 
    "buceta", "bunda", "bundao", "bunduda", "burra dms", "cabaco", 
    "cacetadas", "cacete", "cadelona", "cafona", "cala a boca", 
    "cala boca", "cambista", "canalha", "capiroto", "caralho", 
    "casseta", "cassete", "checheca", "chereca", "chibumba", 
    "chibumbo", "chifruda", "chifrudo", "ching chong", "chink", 
    "chochota", "chong", "chota", "chupada", "chupado", "clb", 
    "clbc", "clit+ris", "clitoris", "coc+_con", "coca-na", "cocaina", 
    "cocodrilo", "cocozento", "coon", "corna", "corno", "cornuda", "cornudo", "corrupta", "corrupto", 
    "cretina", "cretino", "crlh", "cruz-credo", "cu", "cuck", 
    "culh+es", "culh+o", "culhao", "cunt", "curalho", "cuz+o", 
    "cuzão", "cuzona", "débil", "débil mental", "débiloide", 
    "defunto", "demônio", "demente", "desciclope", "desgraçado", 
    "difunto", "drogado", "eguenorante", "é burro", "endemoniado", 
    "energúmeno", "enfianocu", "engole rola", "escrota", "escroto", 
    "esdrúxulo", "esp+rro", "esporrada", "esporrado", "esporro", 
    "estúpida", "estúpido", "estigalhado", "estrume", "estrunxado", "estúpida", "estupidez", "estúpido", "fag", 
    "faggot", "fdp", "fds", "fds pra sua opinião", "fedida", "fedido", 
    "fedoreta", "feiosa", "feioso", "felação", "ferre-se", "ffs", "fgot", 
    "filho de uma puta", "filhodaPuta", "fiofo", "foda", "foda-se", "fodão", 
    "fuck", "fuder", "fudido", "fulera", "fúrio", "furnica", "furnicar", 
    "furona", "gaiata", "gaiato", "GeisyArruda", "gnu", "gonorreia", 
    "gordo escroto", "gozado", "grelinho", "grelão", "grupo merda", "herege", "Hitler", "horrenda", "idiota", 
    "idiotice", "imbecil", "inascível", "invertebrado", "iscrota", "iscroto", 
    "jacu", "jap", "kct", "kike", "Komodo", "ku", "kys", "lésbica", "ladrão", 
    "ladroeira", "ladrona", "lalau", "lazarento", "Lázaro", "leprosa", "leproso", 
    "lezado", "lico", "limpeza anal", "machona", "machorra", "manguaca", 
    "MariMoon", "masturba", "meretriz", "miolo de cu", "mocorongo", "mocreia", 
    "moleca", "mondronga", "mondrongo", "mongoloide", "MontedeMerda", "Morfetico", "motherfucker", "Mulambo", "n1bb", "n1gg", "NABA", 
    "NADEGA", "Nazi", "Nazista", "negro", "n_cong", "Nhaca", "nibb", "nigg", "𝖓𝖎𝖌𝖌", 
    "ninguém te quer", "ninguém te quer nesse servidor", "no teu ouvido", "Nonsense", 
    "ɴɪɢɢ", "Olhodocu", "OlhoGordo", "OLHOTA", "OT-RIA", "OT-RIO", "otalio", 
    "OTARIA", "Otario", "OTARIO", "P+STULA", "Panaca", "PASPALHA", "PASPALHÃO", 
    "PASPALHO", "Passaralho", "PauNoCu", "pedo", "PEIA", "PEMBA", "penis", 
    "PENTELHA", "PENTELHO", "PER_con", "Periquita", "PIC+O", "PICAO", "Pimenteira", 
    "PIRANHA", "PIROCA", "PIROCO", "PIRU", "porra", "Porra", "pqp", "PROSTIBULO", 
    "prostituta", "PROSTITUTA", "PROSTITUTO", "Punheta", "PUS", "PUSTULA", "Puta","PutaQuePariu", "PUTO", "Quasimodo", "Quenga", "Quirguistao", "r3t4r", "r3tar", "RAB+O", 
    "RABAO", "RABUDA", "RABUDAO", "RABUDO", "RABUDONA", "RACHAD+O", "RACHADAO", "Rampero", 
    "rapariga", "Rapariga", "ret4r", "retard", "RETARDADA", "Retardado", "RETARDADO", 
    "RID-CULA", "RIDICULA", "Rusguento", "S-FILIS", "SAFADO", "Sanguesuga", "SAPAT+O", 
    "seu burro", "seu piranha", "seu puta", "seu random", "shemale", "SIFILIS", "SIRIRICA", 
    "slut", "spic", "stfu", "sua piranha", "sua puta", "sua random", "Tapado", "Taporra", 
    "TARADA", "Tarado", "TARADO", "TESTUDA", "Tetuda", "Tetudo", "TEZ+O", "TEZAO", "TEZUDA", 
    "TEZUDO", "this server is sucks", "Tmnc", "tnc", "Tosco", "Tragado", "Trepadeira", 
    "TROCHA", "Troglodita", "TROLHA", "Troucha", "TROUCHA", "TROUXA", "Troxa", "TROXA", 
    "twat", "vadia", "Vadia", "VAGABUNDA", "Vagabundo", "VAGABUNDO", "Vagaranha", 
    "VAGINA", "vai tomar no cu", "Vaiamerda", "vaisefuder", "Vaitomarnocu", "vc e chato", 
    "vc é chato", "voce e chato", "voce é chato", "você e chato", "você é chato", 
    "Vsfd", "Vtmnc", "Vtnc", "whore", "XANA", "XANINHA", "Xavasca", "XAVASCA", "XIBIU", 
    "XIBUMBA", "Xixizento", "XOCHOTA", "XOTA", "XOXOTA", "Xupetinha", "Xupisco", 
    "Xurupita", "Xuxexo", "xvideo", "ZeBuceta", "Ziguizira", "Zina", "Zoiudo", 
    "Zoneira", "Zulu", "Zureta",


    // conteúdo sexual:
    "NUS", "anal", "anus", "arse", "asshat", "asshole", "b0", "b1tch", "ballsac", "ballsack",
    "bct", "bcta", "bdsm", "beastiality", "beefcurtains", "biatch", "bitch", "blowjob", 
    "blowjobs", "bo0b", "bollock", "bollok", "boner", "boob", "boobs", "booty", "Boquete",
    "BOSSETA", "Brasino", "buceta", "buceta_con", "Bucetão", "bucetinha", "Bucetuda", "Bucetudinha", 
    "bucta", "Busseta", "Busseta_con", "Buttock", "Buttock_con", "buttplug", "buttplug_con", "buzeta", 
    "buzeta_con", "ceu pau", "chupo paus", "clitoris", "clitoris_con", "cock", "comendo a tua", 
    "comendo o teu", "comendo teu", "comendo tua", "comerei a sua", "comerei o seu", "comerei sua", 
    "comi a sua", "comi o seu", "comi sua", "Culhao", "Culhao_con",
];


// Converte todas as palavras proibidas para minúsculas para uma comparação consistente
$palavrasProibidasMin = array_map('strtolower', $palavrasProibidas);

$palavrasInvalidas = [];
$palavrasNaoExistem = [];

// Construa uma string com todas as palavras separadas por "+"
$palavrasString = implode("+", $palavras);

// Endpoint do Dicionário Priberam para as palavras pesquisadas
$endpoint = "https://dicionario.priberam.org/" . urlencode($palavrasString);
// Configuração do contexto HTTP para capturar erros sem falhar
$options = [
    'http' => [
        'method' => 'GET',
        'ignore_errors' => true,
    ],
];
$context = stream_context_create($options);

// Obtenha o conteúdo da página do Priberam
$response = file_get_contents($endpoint, false, $context);

// Verifique se a frase "Palavra não encontrada." está presente na resposta
$nao_existe = strpos($response, 'Palavra não encontrada.') !== false;

if ($nao_existe) {
    $palavrasNaoExistem = $palavras;
}

// Retorne o resultado como JSON
echo json_encode([
    'invalidas' => $palavrasInvalidas,
    'nao_existem' => $palavrasNaoExistem,
]);
