# Web service para consulta e validação de CEPs no Brasil

	Esse é um simples script que retorna um json ou xml consulta um banco de dados de cep, o retorna trás os dados de um logradouro a partír da passagem de um cep.

## Banco de Dados

Eu utilizei três arquivos contendo listas de cep para a criação do banco de dados para esse projeto. Utilizei um pouco de python como script para unir esses três arquivos. O resultado final foi uma lista de quase 1 milhão de endereços, não posso garantir que são os dados mais atualizados, mas acredito que tenha ficado bem completa.

Os arquivos que usei nesse projeto foram baixados dos seguintes websites, todos são disponibilizados gratuitamente:

- [República Virtual](http://www.republicavirtual.com.br)
- [CEP Aberto](https://www.cepaberto.com/)

## como usar

A pasta exemplos contém tudo que é necessário para consultar e validar via PHP ou JQuery o cep.

### XML

<http://localhost/cep/?value=passe_o_cep_aqui&amp;method=xml>

* passe_o_cep_aqui no link é o cep a ser consultado, o script formata o cep para que contenha apenas 8 digitos, ou seja, pode-se passar 32.223-100 ou 32223-100 ou 32223100 que funciona do mesmo jeito

* method, no caso XML, que será o formato do resultado

### JSON

<http://localhost/cep/?value=passe_o_cep_aqui&amp;method=json>

* passe_o_cep_aqui no link é o cep a ser consultado, o script formata o cep para que contenha apenas 8 digitos, ou seja, pode-se passar 32.223-100 ou 32223-100 ou 32223100 que funciona do mesmo jeito

* method, no caso JSON, que será o formato do resultado, caso o parametro method não seja explicito o script retorna automaticamente o formato em json

## Instalação

Os arquivos para a instalação estão na pasta banco_de_dados:

* Rode o script cep_criar_bd.sql no seu gereenciador de banco de dados favoritos. Esse script cria um banco de dados chamado cep, com uma tabela cep e seus respectivos campos: cep, uf, cidade, rua, bairro.
* Rode o script cep_dados_bd.sql (está compactado como cep_dados_bd.zip) para inserir os endereços na tabela anterior

* Como extra eu deixei em csv todos endereços em um único arquivo, para outros usos: arquivo banco_de_dados_cep-2020.zip

## Possíveis Problemas e Notas

* Ao usar o comando source pra importar os dados dos scripts pode acontecer do mysql não aceitar a acentuação utf8, pra resolver isso eu utilizo o comando SET NAMES utf8 antes de executar os scripts sql (pode não resolver dependendo da versão do MySQL).

* Se o passo acima não funcionar pode-se usar esse comando: mysql -u [user] -p [database_name] < ..\banco_de_dados\cep_dados_bd.sql como se restaura normalmente

## Contribuições

Caso queira contribuir com melhorias para esse sistema basta enviar um e-mail para erivelton.silva@contagem.mg.gov.br.

## Copyright and license

O script e os banco de dados estão disponíveis como código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).