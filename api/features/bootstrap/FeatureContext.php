<?php

use Behat\WebApiExtension\Context\WebApiContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends WebApiContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }
}
