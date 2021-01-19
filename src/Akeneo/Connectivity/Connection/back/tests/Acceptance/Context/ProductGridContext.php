<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Tests\Acceptance\Context;

use Akeneo\Connectivity\Connection\back\tests\Integration\Fixtures\Enrichment\ProductLoader;
use Behat\Behat\Context\Context;

/**
 * @author Pierre Jolly <pierre.jolly@akeneo.com>
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class ProductGridContext implements Context
{
    private ProductLoader $productLoader;

    public function __construct(
        ProductLoader $productLoader
    ) {
        $this->productLoader = $productLoader;
    }

    /**
     * @When /^Julia creates a simple product in the product grid$/
     */
    public function juliaCreatesASimpleProductInTheProductGrid()
    {
        $this->productLoader->create('t-shirt', []);
    }
}
