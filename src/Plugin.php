<?php

namespace Detain\MyAdminNovnc;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminNovnc
 */
class Plugin
{
    public static $name = 'Novnc Plugin';
    public static $description = 'Allows handling of NoVNC HTML5 VNC Connections';
    public static $help = '';
    public static $type = 'plugin';

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function getHooks()
    {
        return [
            //'system.settings' => [__CLASS__, 'getSettings'],
            //'ui.menu' => [__CLASS__, 'getMenu'],
        ];
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getMenu(GenericEvent $event)
    {
        $menu = $event->getSubject();
        if ($GLOBALS['tf']->ima == 'admin') {
            function_requirements('has_acl');
            if (has_acl('client_billing')) {
            }
        }
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getRequirements(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Plugins\Loader $this->loader
         */
        $loader = $event->getSubject();
        $loader->add_requirement('class.Novnc', '/../vendor/detain/myadmin-novnc-plugin/src/Novnc.php');
        $loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php');
        $loader->add_requirement('deactivate_abuse', '/../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php');
        $loader->add_requirement('get_abuse_licenses', '/../vendor/detain/myadmin-novnc-plugin/src/abuse.inc.php');
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getSettings(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Settings $settings
         **/
        $settings = $event->getSubject();
    }
}
