import os

def listar_estrutura_pasta(caminho):
    estrutura = ""
    for pasta_atual, subpastas, arquivos in os.walk(caminho):
        nivel = pasta_atual.replace(caminho, '').count(os.sep)
        estrutura += "{}{}/\n".format('    ' * (nivel - 1), os.path.basename(pasta_atual))
        for arquivo in arquivos:
            estrutura += "{}    {}\n".format('    ' * nivel, arquivo)
    return estrutura

def main():
    caminho_repositorio = r"C:\Users\Aluno\Downloads\Sistema_de_Emprego-main"
    estrutura = listar_estrutura_pasta(caminho_repositorio)
    with open("estrutura_pastas.txt", "w") as arquivo_saida:
        arquivo_saida.write(estrutura)

if __name__ == "__main__":
    main()
