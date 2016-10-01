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

    /**
     * @BeforeSuite
     */
    public static function beforeSuite()
    {
        copy('app/behat.db.cache', 'app/behat.db.cache.bak');
    }

    /**
     * @BeforeScenario
     */
    public static function beforeScenario()
    {
        copy('app/behat.db.cache.bak', 'app/behat.db.cache');
    }

    /**
     * @AfterScenario
     */
    public static function afterScenario()
    {
        unlink('app/behat.db.cache');
    }

    /**
     * @AfterSuite
     */
    public static function afterSuite()
    {
        unlink('app/behat.db.cache.bak');
    }
}
