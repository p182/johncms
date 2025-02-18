<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

namespace Johncms;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Johncms\Console\Commands\ClearCacheCommand;
use Johncms\Console\Commands\MakeMigrationCommand;
use Johncms\Console\Commands\MigrateCommand;
use Johncms\Database\PdoFactory;
use Johncms\Debug\DebugBar;
use Johncms\Files\Filesystem;
use Johncms\Http\Environment;
use Johncms\Http\Request;
use Johncms\Http\RequestFactory;
use Johncms\Http\ResponseFactory;
use Johncms\Http\Session;
use Johncms\i18n\Translator;
use Johncms\i18n\TranslatorServiceFactory;
use Johncms\Media\MediaEmbed;
use Johncms\Middlewares\CsrfMiddleware;
use Johncms\Middlewares\SessionMiddleware;
use Johncms\Settings\SiteSettings;
use Johncms\Users\AuthProviders\CookiesAuthProvider;
use Johncms\Users\AuthProviders\SessionAuthProvider;
use Johncms\Users\Ban\SystemBanTypes;
use Johncms\View\AdminRenderEngineFactory;
use Johncms\View\Extension\{AdminAssets, Avatar};
use Johncms\View\Extension\Assets;
use Johncms\View\MetaTagManager;
use Johncms\View\Render;
use Johncms\View\RenderEngineFactory;
use PDO;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

class ConfigProvider
{
    #[Pure]
    public function __invoke(): array
    {
        return [
            'dependencies'   => $this->getDependencies(),
            'middleware'     => $this->getMiddlewares(),
            'providers'      => [],
            'commands'       => $this->getCommands(),
            'auth_providers' => $this->getAuthProviders(),
            'bans'           => [
                'ban_types' => [
                    SystemBanTypes::class,
                ],
            ],
        ];
    }

    #[ArrayShape(['aliases' => "string[]", 'factories' => "string[]"])]
    private function getDependencies(): array
    {
        return [
            'aliases' => [
                Request::class        => ServerRequestInterface::class,
                RequestFactory::class => ServerRequestInterface::class,
            ],

            'factories' => [
                AdminAssets::class              => AdminAssets::class,
                Assets::class                   => Assets::class,
                Avatar::class                   => Avatar::class,
                Environment::class              => Environment::class,
                PDO::class                      => PdoFactory::class,
                Render::class                   => RenderEngineFactory::class,
                AdminRenderEngineFactory::class => AdminRenderEngineFactory::class,
                ServerRequestInterface::class   => RequestFactory::class,
                Translator::class               => TranslatorServiceFactory::class,
                Users\User::class               => Users\UserFactory::class,
                Filesystem::class               => Filesystem::class,
                ResponseFactoryInterface::class => ResponseFactory::class,
                CacheInterface::class           => Cache::class,
                MediaEmbed::class               => MediaEmbed::class,
                Session::class                  => Session::class,
                MetaTagManager::class           => MetaTagManager::class,
                SiteSettings::class             => SiteSettings::class,
                DebugBar::class                 => DebugBar::class,
            ],
        ];
    }

    private function getMiddlewares(): array
    {
        return [
            SessionMiddleware::class,
            CsrfMiddleware::class,
        ];
    }

    private function getCommands(): array
    {
        return [
            MakeMigrationCommand::class,
            MigrateCommand::class,
            ClearCacheCommand::class,
        ];
    }

    private function getAuthProviders(): array
    {
        return [
            SessionAuthProvider::class,
            CookiesAuthProvider::class,
        ];
    }
}
