<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

trait PropertyAccessorAwareTrait
{
    protected $propertyAccessor;

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }
}
