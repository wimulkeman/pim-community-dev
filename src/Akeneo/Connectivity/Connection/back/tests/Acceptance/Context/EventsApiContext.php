<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Tests\Acceptance\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * @author Pierre Jolly <pierre.jolly@akeneo.com>
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class EventsApiContext implements Context
{
    public function __construct()
    {
    }

    /**
     * @Then /^an event is raised and added to the Events API message queue$/
     */
    public function anEventIsRaisedAndAddedToTheEventsAPIMessageQueue()
    {
        throw new PendingException();
    }
}
