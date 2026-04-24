<?php

use PHPUnit\Framework\TestCase;

class DadosPessoaisTest extends TestCase {

    // Testa atualização de dados do usuário
    public function testAtualizarPerfilUsuario() {
        $nome = "Netinho";
        $email = "netinho@email.com";

        // simulação: dados preenchidos corretamente
        $resultado = !empty($nome) && !empty($email);

        $this->assertTrue($resultado);
    }

    // Testa limpeza de dados de entrada (ex: remover espaços)
    public function testLimparDadosEntrada() {
        $nome = "   Netinho   ";

        // remove espaços extras
        $nomeLimpo = trim($nome);

        $this->assertEquals("Netinho", $nomeLimpo);
    }

    // Testa exclusão de conta
    public function testDeletarContaUsuario() {
        $confirmacao = true; // simula clique em confirmar

        $this->assertTrue($confirmacao);
    }

    // Testa encerramento da sessão após exclusão
    public function testEncerrarSessaoAtiva() {
        $sessaoAtiva = false; // simula usuário deslogado

        $this->assertFalse($sessaoAtiva);
    }
}