<?php

namespace App\Form;

use Symfony\Component\Form\Form;

/**
 * Interface to be called for DI for AppFormFactoryInterface related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
interface AppFormFactoryInterface
{
    /**
     * Returns the defined form
     * @return Form
     */
    public function create(string $name, $subject);
}
