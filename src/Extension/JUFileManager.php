<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  EditorsXtd.jufilemanager
 */

namespace JU\Plugin\EditorsXtd\JUFileManager\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\Event\Plugin\AjaxEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\String\StringableInterface;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

final class JUFileManager extends CMSPlugin implements SubscriberInterface
{
    private const RF_SHARED_SECRET = '2IT1cU9OyfHVFUmiGD6zOaTv4L7SJMRG';

    private const RF_TOKEN_TTL = 300;

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onEditorButtonsSetup' => 'onEditorButtonsSetup',
            'onAjaxJufilemanager' => 'onAjaxJufilemanager',
        ];
    }

    /**
     * @param EditorButtonsSetupEvent $event
     * @return void
     */
    public function onEditorButtonsSetup(EditorButtonsSetupEvent $event): void
    {
        if (!$this->isAllowed()) {
            return;
        }

        $subject = $event->getButtonsRegistry();
        $disabled = $event->getDisabledButtons();

        $this->loadLanguage();

        $wa = $this->getApplication()->getDocument()->getWebAssetManager();

        $wa->useScript('jquery');
        $wa->useScript('joomla.dialog');
        $wa->registerAndUseScript(
            'editor-button.jufilemanager',
            'plg_editors_xtd_jufilemanager/button.js',
            [],
            ['type' => 'module'],
            ['editors']
        );

        $this->getApplication()->getDocument()->addScriptOptions('plg_editors_xtd_jufilemanager', [
            'ajaxUrl' => 'index.php?option=com_ajax&plugin=jufilemanager&group=editors-xtd&format=json',
            'token' => Session::getFormToken(),
        ]);

        Text::script('PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_IMAGE');
        Text::script('PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_FILE');

        $buttonImage = $this->_name.'-image';

        if (!\in_array($buttonImage, $disabled)) {
            $subject->add(
                new Button(
                    $buttonImage,
                    [
                        'action' => 'jufilemanager-open',
                        'text' => Text::_('PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_IMAGE'),
                        'icon' => 'image',
                        'iconSVG' => '',
                    ],
                    [
                        'kind' => 'image',
                    ]
                )
            );
        }

        $buttonFile = $this->_name.'-file';

        if (!\in_array($buttonFile, $disabled)) {
            $subject->add(
                new Button(
                    $buttonFile,
                    [
                        'action' => 'jufilemanager-open',
                        'text' => Text::_('PLG_EDITORS_XTD_JUFILEMANAGER_BUTTON_FILE'),
                        'icon' => 'file',
                        'iconSVG' => '',
                    ],
                    [
                        'kind' => 'file',
                    ]
                )
            );
        }
    }

    public function onAjaxJUFileManager(AjaxEvent $event): void
    {
        $response = new class () implements StringableInterface {
            public array $data = [];

            public function __toString(): string
            {
                return json_encode($this->data);
            }
        };

        $event->updateEventResult($response);

        if (!Session::checkToken('get')) {
            $response->data = ['error' => 'Invalid token'];

            return;
        }

        if (!$this->isAllowed()) {
            $response->data = ['error' => 'Not authorised'];

            return;
        }

        $kind = $this->getApplication()->getInput()->getCmd('kind', 'image');

        if ($kind === 'file') {
            $folder = trim((string)$this->params->get('files_folder', 'files'), '/');
            $rfType = 2;
        } else {
            $folder = trim((string)$this->params->get('images_folder', 'images'), '/');
            $rfType = 1;
        }

        $akey = $this->generateAkey();

        $link = rtrim(Uri::root(true), '/')
            .'/media/plg_editors_xtd_jufilemanager/filemanager/dialog.php'
            .'?type='.$rfType
            .'&field_id=mfm-target'
            .'&fldr='.urlencode($folder)
            .'&akey='.urlencode($akey);

        $response->data = ['link' => $link];
    }

    /**
     * @return string
     */
    private function generateAkey(): string
    {
        $timeSlot = (int)floor(time() / self::RF_TOKEN_TTL);

        return hash_hmac(
            'sha256',
            'rf-access:'.$timeSlot,
            self::RF_SHARED_SECRET
        );
    }

    /**
     * @return bool
     */
    private function isAllowed(): bool
    {
        $user = $this->getApplication()->getIdentity();

        if ($user->guest || !$user->authorise('core.create', 'com_content')) {
            return false;
        }

        $allowedGroups = array_filter(
            (array)$this->params->get(
                'allowed_groups',
                []
            )
        );

        if (!empty($allowedGroups)) {
            $userGroups = $user->getAuthorisedGroups();

            if (!array_intersect($allowedGroups, $userGroups)) {
                return false;
            }
        }

        return true;
    }
}