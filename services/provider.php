<?php
/**
 * JUFileManager EditorsXtd plugin
 *
 * @version       1.x
 * @package       JUPWA
 * @author        Denys D. Nosov (denys@joomla-ua.org)
 * @copyright (C) 2026 by Denys D. Nosov (https://joomla-ua.org)
 * @license       GNU General Public License version 2 or later; see LICENSE.md
 *
 **/

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use JU\Plugin\EditorsXtd\JUFileManager\Extension\JUFileManager;

return new class () implements ServiceProviderInterface {
    /**
     * @param Container $container
     * @return void
     *
     * @since 1.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new JUFileManager(
                    $container->get(DispatcherInterface::class),
                    (array)PluginHelper::getPlugin('editors-xtd', 'jufilemanager')
                );

                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};