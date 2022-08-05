<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahefa\FOS\UserBundle\Tests\Util;

use Mahefa\FOS\UserBundle\Tests\TestUser;
use Mahefa\FOS\UserBundle\Util\HashingPasswordUpdater;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class HashingPasswordUpdaterTest extends TestCase
{
    /**
     * @var HashingPasswordUpdater
     */
    private $updater;
    private $passwordHasherFactory;

    protected function setUp(): void
    {
        if (!interface_exists(PasswordHasherFactoryInterface::class)) {
            self::markTestSkipped('This test requires having the password-hasher component.');
        }

        $this->passwordHasherFactory = $this->getMockBuilder(PasswordHasherFactoryInterface::class)->getMock();

        $this->updater = new HashingPasswordUpdater($this->passwordHasherFactory);
    }

    public function testUpdatePassword()
    {
        $hasher = $this->getMockBuilder(PasswordHasherInterface::class)->getMock();
        $user = new TestUser();
        $user->setPlainPassword('password');

        $this->passwordHasherFactory->expects($this->once())
            ->method('getPasswordHasher')
            ->with($user)
            ->will($this->returnValue($hasher));

        $hasher->expects($this->once())
            ->method('hash')
            ->with('password', $this->identicalTo(null))
            ->will($this->returnValue('hashedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('hashedPassword', $user->getPassword(), '->updatePassword() sets hashed password');
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testUpdatePasswordWithLegacyHasher()
    {
        $hasher = $this->getMockBuilder(LegacyPasswordHasherInterface::class)->getMock();
        $user = new TestUser();
        $user->setPlainPassword('password');
        $user->setSalt('old_salt');

        $this->passwordHasherFactory->expects($this->once())
            ->method('getPasswordHasher')
            ->with($user)
            ->will($this->returnValue($hasher));

        $hasher->expects($this->once())
            ->method('hash')
            ->with('password', $this->isType('string'))
            ->will($this->returnValue('hashedPassword'));

        $this->updater->hashPassword($user);
        $this->assertSame('hashedPassword', $user->getPassword(), '->updatePassword() sets hashed password');
        $this->assertNotNull($user->getSalt());
        $this->assertNull($user->getPlainPassword(), '->updatePassword() erases credentials');
    }

    public function testDoesNotUpdateWithEmptyPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword('');

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }

    public function testDoesNotUpdateWithoutPlainPassword()
    {
        $user = new TestUser();
        $user->setPassword('hash');

        $user->setPlainPassword(null);

        $this->updater->hashPassword($user);
        $this->assertSame('hash', $user->getPassword());
    }
}
