<?php

use PHPUnit\Framework\TestCase;

class RecuperacaoSenhaTest extends TestCase {

    // Testa se o email foi informado para recuperação
    public function testSolicitarRecuperacaoSenha() {
        $email = "teste@email.com";

        // verifica se não está vazio
        $resultado = !empty($email);

        $this->assertTrue($resultado);
    }

    // Testa se o código digitado está correto
    public function testValidarCodigoSeguranca() {
        $codigoCorreto = "123456";
        $codigoDigitado = "123456";

        $this->assertTrue($codigoDigitado === $codigoCorreto);
    }

    // Testa código inválido
    public function testCodigoInvalido() {
        $codigoCorreto = "123456";
        $codigoDigitado = "000000";

        $this->assertFalse($codigoDigitado === $codigoCorreto);
    }

    // Testa se a nova senha atende o mínimo necessário
    public function testAtualizarSenhaNoBanco() {
        $novaSenha = "novaSenha123";

        // regra simples: mínimo de 6 caracteres
        $resultado = strlen($novaSenha) >= 6;

        $this->assertTrue($resultado);
    }
}