<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

trait TwigAwareTrait
{
    protected $twig;

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
