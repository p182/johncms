<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Johncms\Checker\DBChecker;
use Johncms\Config;
use Johncms\Http\Request;
use Johncms\Install\Database;
use Johncms\Modules\ModuleInstaller;
use Johncms\Modules\Modules;
use Johncms\View\Render;

$request = di(Request::class);
$render = di(Render::class);

$render->addData(
    [
        'title'      => __('Database'),
        'page_title' => __('Database'),
    ]
);

$fields = [
    'db_host'     => $request->getPost('db_host', 'localhost', FILTER_SANITIZE_STRING),
    'db_port'     => $request->getPost('db_port', 3306, FILTER_VALIDATE_INT),
    'db_name'     => $request->getPost('db_name', 'johncms', FILTER_SANITIZE_STRING),
    'db_user'     => $request->getPost('db_user', '', FILTER_SANITIZE_STRING),
    'db_password' => $request->getPost('db_password', '', FILTER_SANITIZE_SPECIAL_CHARS),
];

$errors = [];

if ($request->getMethod() === 'POST') {
    $capsule = new Capsule();
    $capsule->addConnection(
        [
            'driver'    => 'mysql',
            'host'      => $fields['db_host'],
            'port'      => $fields['db_port'],
            'database'  => $fields['db_name'],
            'username'  => $fields['db_user'],
            'password'  => $fields['db_password'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'timezone'  => '+00:00',
        ]
    );
    $capsule->setAsGlobal();
    $connection = $capsule->getConnection();

    try {
        $connection->getPdo();
        $db_checker = new DBChecker();
        $version_info = $db_checker->versionInfo();
        $check_mysqlnd = $db_checker->checkMysqlnd();

        // Создаем системный файл database.local.php
        $db_settings = [
            'pdo' => [
                'db_host' => $fields['db_host'],
                'db_port' => $fields['db_port'],
                'db_name' => $fields['db_name'],
                'db_user' => $fields['db_user'],
                'db_pass' => $fields['db_password'],
            ],
        ];
        $db_file = "<?php\n\n" . 'return ' . var_export($db_settings, true) . ";\n";

        if (
            $check_mysqlnd &&
            (! $version_info['error'] || $request->getPost('anyway_continue') === 'yes') &&
            file_put_contents(CONFIG_PATH . 'autoload/database.local.php', $db_file)
        ) {
            Database::createTables($version_info['error']);
            // reread configs
            $container = \Johncms\Container\ContainerFactory::getContainer();
            $container->instance('config', (new Config())());

            di(PDO::class);
            // Installing modules
            $modules = new Modules();
            $installed_modules = $modules->getInstalled();
            foreach ($installed_modules as $module) {
                (new ModuleInstaller($module))->getInstaller()->install();
            }

            // Run the after install scripts
            foreach ($installed_modules as $module) {
                (new ModuleInstaller($module))->getInstaller()->afterInstall();
            }

            header('Location: /install/?step=4');
            exit;
        }

        if ($version_info['error']) {
            $db_version_error = true;
            $errors['unknown'][] = __("Correct work of JohnCMS is not guaranteed on your version of mysql server.<br>You can ignore this warning at your own risk.");
            $errors['unknown'][] = __("Server name: <b>%s</b>. <br>Your mysql server version is <b>%s</b>. But <b>%s</b> is required.", $version_info['server_name'], $version_info['version_clean'], $version_info['required_version']);
        }

        if (! $check_mysqlnd) {
            $errors['unknown'][] = __("The system requires a properly configured <a href='https://www.php.net/manual/en/intro.mysqlnd.php' target='_blank'>MySQL Native Driver (mysqlnd)</a>.");
            $errors['unknown'][] = __("Please contact your hosting provider's technical support to resolve this issue.");
        }

        if (empty($errors['unknown'])) {
            $errors['unknown'][] = __("ERROR: Can't write database.local.php");
        }
    } catch (Exception $exception) {
        $db_error = $exception->getMessage();
        $error_code = $exception->getCode();
        if ($error_code === 2002) {
            $errors['db_host'][] = __('Invalid database host name');
        } elseif ($error_code === 1045) {
            $errors['db_user'][] = __('Invalid database user or password');
        } elseif ($error_code === 1049) {
            $errors['db_name'][] = __('Database does not exist');
        } else {
            $errors['unknown'][] = $db_error;
            if (file_exists(CONFIG_PATH . 'autoload/database.local.php')) {
                unlink(CONFIG_PATH . 'autoload/database.local.php');
            }
        }
    }
}

$data = [
    'errors'             => $errors,
    'fields'             => $fields,
    'next_step_disabled' => false,
    'db_version_error'   => $db_version_error ?? false,
];

echo $render->render('install::step_3', ['data' => $data]);
