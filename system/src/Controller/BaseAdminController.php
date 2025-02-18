<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

namespace Johncms\Controller;

use Johncms\i18n\Translator;
use Johncms\NavChain;
use Johncms\View\AdminRenderEngineFactory;
use Johncms\View\MetaTagManager;
use Johncms\View\Render;
use PDO;

class BaseAdminController extends AbstractController
{
    protected Render $render;
    protected Translator $translator;
    protected NavChain $navChain;
    protected MetaTagManager $metaTagManager;

    /** @var string The module name */
    protected string $moduleName = '';

    public function __construct()
    {
        $this->render = di(AdminRenderEngineFactory::class);
        $this->translator = di(Translator::class);
        $this->navChain = di(NavChain::class);
        $this->metaTagManager = di(MetaTagManager::class);

        $this->translator->addTranslationDomain('admin', MODULES_PATH . 'johncms/admin/locale', false);

        if (! empty($this->moduleName)) {
            // Register Namespace for module templates
            $this->render->addFolder(basename($this->moduleName), MODULES_PATH . $this->moduleName . '/templates/');

            // Register the module languages domain and folder
            $this->translator->addTranslationDomain(basename($this->moduleName), MODULES_PATH . $this->moduleName . '/locale');
        }

        $db = di(PDO::class);
        $this->render->addData(
            [
                'regtotal'   => 0, // $db->query("SELECT COUNT(*) FROM `users` WHERE `preg`='0'")->fetchColumn(),
                'countusers' => 0, // $db->query("SELECT COUNT(*) FROM `users` WHERE `preg`='1'")->fetchColumn(),
                'countadm'   => 0, // $db->query("SELECT COUNT(*) FROM `users` WHERE `rights` >= '1'")->fetchColumn(),
                'bantotal'   => 0, // $db->query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'")->fetchColumn(),
            ],
            [
                'system::app/sidebar-admin-menu',
            ]
        );
        $this->navChain->add(d__('admin', 'Admin Panel'), '/admin/');
    }
}
