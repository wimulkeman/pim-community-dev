<?php

declare(strict_types=1);

namespace Akeneo\Apps\Domain\Model;

/**
 * @author Romain Monceau <romain@akeneo.com>
 * @copyright {2019} Akeneo SAS (http://www.akeneo.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class App
{

    private $code;

    private $label;

    private $flowType;

    private function __construct(AppCode $code, string $label, FlowType $flowType)
    {
        $this->code = $code;
        $this->label = $label;
        $this->flowType = $flowType;
    }

    public function create(AppCode $appCode, string $label, FlowType $flowType): self
    {
        return new self(
            $appCode,
            $label,
            $flowType
        );
    }
}
