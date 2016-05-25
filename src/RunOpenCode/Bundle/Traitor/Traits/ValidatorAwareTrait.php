<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorAwareTrait
{
    protected $validator;

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
}
