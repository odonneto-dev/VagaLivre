<?php

use PHPUnit\Framework\TestCase;

class CadastroTest extends TestCase {

    // Testa se o email é válido
    public function testValidarEmail() {
        $email = "teste@email.com";

        // usa função nativa do PHP para validar email
        $resultado = filter_var($email, FILTER_VALIDATE_EMAIL);

        $this->assertTrue($resultado !== false);
    }

    // Testa email inválido
    public function testEmailInvalido() {
        $email = "email_invalido";

        $resultado = filter_var($email, FILTER_VALIDATE_EMAIL);

        $this->assertFalse($resultado !== false);
    }

    // Testa se a senha atende o mínimo de 8 caracteres
    public function testValidarSenha() {
        $senha = "12345678";

        $resultado = strlen($senha) >= 8;

        $this->assertTrue($resultado);
    }

    // Testa senha curta
    public function testSenhaCurta() {
        $senha = "123";

        $resultado = strlen($senha) >= 8;

        $this->assertFalse($resultado);
    }

    // Testa se senha e confirmação são iguais
    public function testConfirmarSenha() {
        $senha = "12345678";
        $confirmacao = "12345678";

        $this->assertTrue($senha === $confirmacao);
    }

    // Testa se senha e confirmação são diferentes
    public function testSenhaDiferente() {
        $senha = "12345678";
        $confirmacao = "87654321";

        $this->assertFalse($senha === $confirmacao);
    }

    // Testa telefone válido (somente números e tamanho correto)
    public function testValidarTelefone() {
        $telefone = "83999999999";

        $somenteNumeros = ctype_digit($telefone);
        $tamanhoValido = strlen($telefone) >= 10 && strlen($telefone) <= 11;

        $this->assertTrue($somenteNumeros && $tamanhoValido);
    }

    // Testa telefone inválido (com letras ou tamanho errado)
    public function testTelefoneInvalido() {
        $telefone = "83abc999";

        $somenteNumeros = ctype_digit($telefone);
        $tamanhoValido = strlen($telefone) >= 10 && strlen($telefone) <= 11;

        $this->assertFalse($somenteNumeros && $tamanhoValido);
    }
}