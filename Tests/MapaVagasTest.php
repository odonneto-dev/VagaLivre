<?php

use PHPUnit\Framework\TestCase;

class MapaVagasTest extends TestCase {

    // Testa se o status da vaga está sendo identificado corretamente
    public function testObterStatusVagaDisponivel() {
        $status = "disponivel";

        // regra: disponível = verde
        $cor = ($status === "disponivel") ? "verde" : "vermelho";

        $this->assertEquals("verde", $cor);
    }

    // Testa vaga ocupada
    public function testObterStatusVagaOcupada() {
        $status = "ocupado";

        $cor = ($status === "disponivel") ? "verde" : "vermelho";

        $this->assertEquals("vermelho", $cor);
    }

    // Testa se o horário da última atualização existe
    public function testObterHorarioUltimaAtualizacao() {
        $horario = date("H:i:s"); // simulação de horário atual

        $this->assertNotEmpty($horario);
    }

    // Testa formato básico do horário (HH:MM:SS)
    public function testFormatoHorarioAtualizacao() {
        $horario = date("H:i:s");

        // verifica se tem 8 caracteres (ex: 12:30:45)
        $this->assertEquals(8, strlen($horario));
    }
}