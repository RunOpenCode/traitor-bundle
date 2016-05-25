<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Translation\TranslatorInterface;

trait TranslatorAwareTrait
{
    protected $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
