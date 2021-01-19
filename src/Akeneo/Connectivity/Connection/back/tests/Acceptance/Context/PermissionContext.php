<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Tests\Acceptance\Context;

use Akeneo\UserManagement\Component\Factory\UserFactory;
use Akeneo\UserManagement\Component\Model\UserInterface;
use Behat\Behat\Context\Context;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Pierre Jolly <pierre.jolly@akeneo.com>
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class PermissionContext implements Context
{
    private UserFactory $userFactory;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        UserFactory $userFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->userFactory = $userFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Given /^Julia has all the required permissions to create a product$/
     */
    public function juliaHasAllTheRequiredPermissionsToCreateAProduct()
    {
        /** @var UserInterface $user */
        $user = $this->userFactory->create();
        $user->setUsername('julia');

        $token = new UsernamePasswordToken($user, null, 'main', ['ROLE_ADMINISTRATOR']);
        $this->tokenStorage->setToken($token);
    }
}
