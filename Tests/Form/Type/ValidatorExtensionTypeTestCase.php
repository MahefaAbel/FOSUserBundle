<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mahefa\FOS\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidatorExtensionTypeTestCase
 * FormTypeValidatorExtension added as default. Useful for form types with `constraints` option.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class ValidatorExtensionTypeTestCase extends TypeTestCase
{
    protected function getTypeExtensions(): array
    {
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')->getMock();
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        return [
            new FormTypeValidatorExtension($validator),
        ];
    }
}
