<?php
$xml = "http://localhost/cep/?value=32223130&field=cep&method=xml";
$json = json_encode(((array)simplexml_load_string($xml)),1);
echo $json;