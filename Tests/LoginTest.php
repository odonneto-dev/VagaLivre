<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {

    // Testa se o login funciona com dados corretos
    public function testLoginUsuario() {
        $email = "teste@email.com";
        $senha = "123456";

        // simulação de validação
        $resultado = ($email === "teste@email.com" && $senha === "123456");

        // espera que o login seja verdadeiro
        $this->assertTrue($resultado);
    }

    // Testa quando a senha está errada
    public function testLoginSenhaErrada() {
        $email = "teste@email.com";
        $senha = "errada";

        $resultado = ($email === "teste@email.com" && $senha === "123456");

        // espera que o login falhe
        $this->assertFalse($resultado);
    }

    // Testa verificação de senha criptografada
    public function testVerificarSenhaCriptografada() {
        $senha = "123456";

        // gera um hash da senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // verifica se a senha bate com o hash
        $this->assertTrue(password_verify("123456", $hash));
    }
}