
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
	<meta name="author" content="Eri Silva - www.erisilva.net">
    <meta name="description" content="webservice cep, consulta cep, xml exemplo">
    <meta name="keywords" content="">
    <meta name="robots" content="noindex, nofollow">
	<link rel="icon" href="../img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Starter Template for Bootstrap</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
  </head>

  <body>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Consulta XML</div>

                        <br>

                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $cep = (isset($_POST['cep']) ? strip_tags(trim($_POST['cep'])) : '');

                            //para não listar o erro use: $link = "http://localhost/cep/?value=" . $cep . "&field=cep&method=xml";

                            // listando os erros
                            $link = "http://localhost/cep/?value=" . $cep . "&field=cep&method=xml&debug=1";

                            $xml = simplexml_load_file($link);

                            echo "<pre>\n";
                            print_r($xml);
                            echo "</pre>\n";

                            echo "<pre>\n";

                            if (!$xml->erro->cep) {
                                echo "Cidade: " . $xml->endereco->cidade . "<br>";
                                echo "Estado: " . $xml->endereco->uf . "<br>";
                            } else {
                                echo "Erro encontrado: " . $xml->erro->cep . "<br>";
                            }

                            echo "</pre>\n";

                        }


                        ?>

                        <form class="form-horizontal"  method="post"
                              action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="cep">CEP:</label>
                                <div class="col-md-2">
                                  <input type="text" class="form-control" id="cep" name="cep" maxlength="10">
                                </div>
                                <div class="col-md-7">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="logradouro">Logradouro:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="logradouro" name="logradouro">
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="bairro">Bairro:</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="bairro" name="bairro">
                                </div>
                                <div class="col-md-7">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="cidade">Cidade:</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="cidade" name="cidade">
                                </div>
                                <div class="col-md-5">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label" for="uf">UF:</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" id="uf" name="uf">
                                </div>
                                <div class="col-md-7">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Clique para enviar o formulário</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="saida"></section>
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){

            function limpa_formulario_cep() {
                // Limpa valores do formulário de cep.
                $("#logradouro").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
            }

            $("#cep").blur(function () {
                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');


                if (cep != "") {

                    var validacep = /^[0-9]{8}$/;

                    if(validacep.test(cep)) {

                        $("#logradouro").val("...");
                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");

                        $.ajax({
                            type: "GET",
                            url: "http://localhost/cep/?value=" + cep + "&field=cep&method=xml",
                            dataType: "xml",
                            success: function(xml) {

                                var naoencontrou = $(xml).find('erro').text();

                                if (!(naoencontrou == "erroerro")) {

                                    var bairro = $(xml).find('bairro').text();
                                    $("#bairro").val(bairro);
                                    var cidade = $(xml).find('cidade').text();
                                    $("#cidade").val(cidade);
                                    var uf = $(xml).find('uf').text();
                                    $("#uf").val(uf.toUpperCase());
                                    var tipo = $(xml).find('tipo').text();
                                    var logradouro = $(xml).find('logradouro').text();
                                    $("#logradouro").val(tipo + ' ' + logradouro);

                                } else {
                                    //alert('Cep nãoi encontrado!');
                                    limpa_formulario_cep();
                                }

                            }

                        });

                    } else {
                        limpa_formulario_cep();
                    }
                } else {
                    limpa_formulario_cep();
                }
            });


            



        });
    </script>
  </body>
</html>
