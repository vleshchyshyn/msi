<?php

namespace Magento\ImportExport\Model\Import;

use Magento\ImportExport\Api\SampleFileModuleProviderInterface;

class SampleFileModuleProvider implements SampleFileModuleProviderInterface
{
    /**
     * @var array()
     */
    private $sampleFiles;

    public function __construct(
        $sampleFilesSet = []
    ) {
        $this->sampleFiles = $sampleFilesSet;
    }

    public function getSampleFileModule(string $filename)
    {
        if (isset($this->sampleFiles[$filename])) {
            return $this->sampleFiles[$filename];
        }

        return null;
    }
}
