<?php

namespace Magento\ImportExport\Api;

interface SampleFileModuleProviderInterface
{
    public function getSampleFileModule(string $filename);
}