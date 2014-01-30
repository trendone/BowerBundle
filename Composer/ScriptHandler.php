<?php

namespace Toa\Bundle\BowerBundle\Composer;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

/**
 * ScriptHandler
 *
 * @author Enrico Thies <enrico.thies@gmail.com>
 */
class ScriptHandler extends BaseScriptHandler
{
    /**
     * @param CommandEvent $event CommandEvent A instance
     */
    public static function installBowerComponents(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        static::executeCommand($event, $appDir, 'toa:bower:components:install');
    }
}
