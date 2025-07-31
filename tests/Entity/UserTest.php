<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire pour l'entité User.
 *  * @covers \App\Entity\User
 */

class UserTest extends TestCase
{
    /**
     * Vérifie le fonctionnement des getters et setters de base.
     */
    public function testUsernameEmailPasswordAccessors(): void
    {
        $user = new User();

        // Username
        $this->assertSame('', $user->getUserIdentifier());
        $this->assertSame('', $user->getUsername());
        $returned = $user->setUsername('testuser');
        $this->assertSame('testuser', $user->getUserIdentifier());
        $this->assertSame('testuser', $user->getUsername());
        $this->assertSame($user, $returned);

        // Email
        $this->assertNull($user->getEmail());
        $returned = $user->setEmail('user@example.com');
        $this->assertSame('user@example.com', $user->getEmail());
        $this->assertSame($user, $returned);

        // Password
        $this->assertSame('', $user->getPassword());
        $returned = $user->setPassword('secret');
        $this->assertSame('secret', $user->getPassword());
        $this->assertSame($user, $returned);
    }

    /**
     * Vérifie que getRoles ajoute toujours ROLE_USER et filtre les doublons.
     */
    public function testRolesDefaultAndMutator(): void
    {
        $user = new User();

        // Par défaut, roles est vide, getRoles doit retourner ['ROLE_USER']
        $roles = $user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertCount(1, $roles);

        // On définit des rôles personnalisés
        $returned = $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertSame($user, $returned);
        $roles = $user->getRoles();
        // ROLE_USER et ROLE_ADMIN, sans doublon
        $this->assertCount(2, $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Vérifie que getSalt renvoie null et eraseCredentials n'affecte rien.
     */
    public function testSaltAndEraseCredentials(): void
    {
        $user = new User();

        $this->assertNull($user->getSalt());

        // Assure que eraseCredentials ne soulève pas d'exception
        $user->eraseCredentials();
        $this->assertTrue(true, 'eraseCredentials() ne doit pas échouer.');
    }

    /**
     * Vérifie que l'identifiant utilisateur est le même que username.
     */
    public function testUserIdentifierConsistency(): void
    {
        $user = new User();
        $user->setUsername('john');
        // getUserIdentifier() doit retourner username
        $this->assertSame('john', $user->getUserIdentifier());
    }
}
