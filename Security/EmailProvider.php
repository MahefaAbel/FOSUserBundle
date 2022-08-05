<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahefa\FOS\UserBundle\Security;

use Mahefa\FOS\UserBundle\Model\UserInterface;

class EmailProvider extends UserProvider
{
    protected function findUser($username): ?UserInterface
    {
        return $this->userManager->findUserByEmail($username);
    }
}
