# Web service para consulta e validação de CEPs no Brasil



cep webservice com banco de dado do http://www.republicavirtual.com.br

reinventando a roda, eu sei, foda-se, eu sou doido.


## como usar

A pasta exemplos contém tudo que é necessário para consultar e validar via PHP e JQuery o cep.

### XML


<http://localhost/cep/?value=xxxxxxxx&amp;field=cep&amp;method=xml&amp;debug=1>

* xxxxxxx é o cep a ser consultado

* filed é o campo a ser consulado, apenas cep nessa versão

* debug, opcional, se setado com qualquer valor retorna uma lista contendo os erros

* method, no caso XML, que será o formato do resultado

## JSON


<http://localhost/cep/?value=xxxxxxxx&amp;field=cep&amp;method=json&amp;debug=1>

* xxxxxxx é o cep a ser consultado

* filed é o campo a ser consulado, apenas cep nessa versão

* debug, opcional, se setado com qualquer valor retorna uma lista contendo os erros

* method, no caso JSON, que será o formato do resultado




## Copyright and license

Code released under the MIT License. 