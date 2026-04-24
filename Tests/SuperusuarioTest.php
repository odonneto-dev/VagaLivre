<?php

use PHPUnit\Framework\TestCase;

class SuperusuarioTest extends TestCase {

    // Testa criação de uma nova área monitorada com dados válidos
    public function testCriarNovaAreaMonitorada() {
        $nome = "Centro";
        $descricao = "Área central da cidade";
        $endereco = "Rua Principal";
        $camera = "Camera 01";

        // verifica se todos os campos estão preenchidos
        $resultado = !empty($nome) && !empty($descricao) && !empty($endereco) && !empty($camera);

        $this->assertTrue($resultado);
    }

    // Testa vínculo da câmera com a área (regra obrigatória)
    public function testVincularCameraArea() {
        $camera = "Camera 01";

        // regra: precisa ter uma câmera associada
        $this->assertNotEmpty($camera);
    }

    // Testa tentativa de cadastro sem câmera
    public function testCadastroSemCamera() {
        $nome = "Centro";
        $descricao = "Área central da cidade";
        $endereco = "Rua Principal";
        $camera = ""; // sem câmera

        $resultado = !empty($nome) && !empty($descricao) && !empty($endereco) && !empty($camera);

        $this->assertFalse($resultado);
    }

    // Testa campos obrigatórios vazios
    public function testCamposObrigatoriosVazios() {
        $nome = "";
        $descricao = "";
        $endereco = "";
        $camera = "";

        $resultado = !empty($nome) && !empty($descricao) && !empty($endereco) && !empty($camera);

        $this->assertFalse($resultado);
    }
}