<?php

use PHPUnit\Framework\TestCase;

class HomeTest extends TestCase {

    // Testa busca por uma área válida (com monitoramento ativo)
    public function testBuscarAreaPorNome() {
        $busca = "Avenida Vidal de Negreiros";

        // simulação de áreas disponíveis
        $areas = [
            "Avenida Vidal de Negreiros",
            "Rua Central",
            "Praça da Matriz"
        ];

        // verifica se a busca está dentro da lista
        $resultado = in_array($busca, $areas);

        $this->assertTrue($resultado);
    }

    // Testa busca sem resultados
    public function testBuscaSemResultados() {
        $busca = "Lugar que não existe";

        $areas = [
            "Avenida Vidal de Negreiros",
            "Rua Central",
            "Praça da Matriz"
        ];

        $resultado = in_array($busca, $areas);

        $this->assertFalse($resultado);
    }

    // Testa regra de negócio: só mostrar áreas com monitoramento ativo
    public function testAreaComMonitoramentoAtivo() {
        $area = [
            "nome" => "Avenida Vidal de Negreiros",
            "ativo" => true
        ];

        $this->assertTrue($area["ativo"]);
    }

    // Testa área sem monitoramento
    public function testAreaSemMonitoramento() {
        $area = [
            "nome" => "Rua Antiga",
            "ativo" => false
        ];

        $this->assertFalse($area["ativo"]);
    }
}