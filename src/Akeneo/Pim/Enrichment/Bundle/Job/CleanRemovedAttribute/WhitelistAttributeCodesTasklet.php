<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Bundle\Job\CleanRemovedAttribute;

use Akeneo\Pim\Structure\Bundle\Manager\AttributeCodeBlacklister;
use Akeneo\Tool\Component\Batch\Item\TrackableTaskletInterface;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Akeneo\Tool\Component\Connector\Step\TaskletInterface;

class WhitelistAttributeCodesTasklet implements TaskletInterface, TrackableTaskletInterface
{
    private StepExecution $stepExecution;
    private AttributeCodeBlacklister $attributeCodeBlacklister;

    public function __construct(
        AttributeCodeBlacklister $attributeCodeBlacklister
    ) {
        $this->attributeCodeBlacklister = $attributeCodeBlacklister;
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    public function isTrackable(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $attributeCodes = $this->stepExecution
            ->getJobExecution()
            ->getJobParameters()
            ->get('attribute_codes');

        foreach ($attributeCodes as $attributeCode) {
            $this->attributeCodeBlacklister->whitelist($attributeCode);
        }
    }
}