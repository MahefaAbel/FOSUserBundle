<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahefa\FOS\UserBundle\Tests\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Mahefa\FOS\UserBundle\Doctrine\UserManager;
use Mahefa\FOS\UserBundle\Model\User;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    public const USER_CLASS = 'Mahefa\FOS\UserBundle\Tests\Doctrine\DummyUser';

    /** @var UserManager */
    protected $userManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    protected function setUp(): void
    {
        if (!interface_exists(ObjectManager::class)) {
            $this->markTestSkipped('"doctrine/persistence" ^1.3 has to be installed for this test to run.');
        }

        $passwordUpdater = $this->getMockBuilder('Mahefa\FOS\UserBundle\Util\PasswordUpdaterInterface')->getMock();
        $fieldsUpdater = $this->getMockBuilder('Mahefa\FOS\UserBundle\Util\CanonicalFieldsUpdater')
            ->disableOriginalConstructor()
            ->getMock();
        $class = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $this->om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->repository = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::USER_CLASS));

        $this->userManager = new UserManager($passwordUpdater, $fieldsUpdater, $this->om, static::USER_CLASS);
    }

    public function testDeleteUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('remove')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->deleteUser($user);
    }

    public function testGetClass()
    {
        $this->assertSame(static::USER_CLASS, $this->userManager->getClass());
    }

    public function testFindUserBy()
    {
        $crit = ['foo' => 'bar'];
        $this->repository->expects($this->once())->method('findOneBy')->with($this->equalTo($crit))->will($this->returnValue([]));

        $this->userManager->findUserBy($crit);
    }

    public function testFindUsers()
    {
        $this->repository->expects($this->once())->method('findAll')->will($this->returnValue([]));

        $this->userManager->findUsers();
    }

    public function testUpdateUser()
    {
        $user = $this->getUser();
        $this->om->expects($this->once())->method('persist')->with($this->equalTo($user));
        $this->om->expects($this->once())->method('flush');

        $this->userManager->updateUser($user);
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        $userClass = static::USER_CLASS;

        return new $userClass();
    }
}

class DummyUser extends User
{
}
