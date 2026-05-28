<?php
include("config.php");
// 1. Define o cabeçalho para o navegador saber que a resposta é um JSON
header('Content-Type: application/json; charset=utf-8');

// 1. Define o fuso horário (ajuste para a sua região se necessário)
date_default_timezone_set('America/Sao_Paulo');


$array=[];

$id_area=1;
$sql="SELECT id_vaga as id, identificador as tipo, status_atual as status, tempoMaximo as tempoVaga FROM vaga WHERE id_area='".$id_area."'";
$i=0;
$campos = $mysqli->query($sql);
while($obj = $campos->fetch_object()){
    $obj->id = (int) $obj->id;
    $obj->status = (int) $obj->status;

    if ($obj->status==1)$obj->status=0;
    else $obj->status=1;

    $obj->tempoVaga = (int) $obj->tempoVaga;
    $obj->tipoVaga = "carro";
    $obj->atualizadoEm = date("H:i:s");

    $array[$i]['id'] = $obj->id;
    $array[$i]['tipo'] = $obj->tipo;
    $array[$i]['status'] = $obj->status;
    $array[$i]['tempoVaga'] = $obj->tempoVaga;
    $array[$i]['tipoVaga'] = $obj->tipoVaga;
    $array[$i]['atualizadoEm'] = $obj->atualizadoEm;
    $i++;
}

echo json_encode($array);

//['id' => 212, 'status' => 0, 'tipo' => 'normal', 'tipoVaga' => 'carro', 'tempoVaga' => 'Sem Limite', 'atualizadoEm' => date("H:i:s")],
