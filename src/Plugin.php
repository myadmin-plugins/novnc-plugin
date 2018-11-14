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
				$menu->add_link('admin', 'choice=none.abuse_admin', '/lib/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', __('Novnc'));
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
		$settings->add_text_setting(__('General'), __('Novnc'), 'abuse_imap_user', __('Novnc IMAP User'), __('Novnc IMAP Username'), ABUSE_IMAP_USER);
		$settings->add_text_setting(__('General'), __('Novnc'), 'abuse_imap_pass', __('Novnc IMAP Pass'), __('Novnc IMAP Password'), ABUSE_IMAP_PASS);
	}
}
