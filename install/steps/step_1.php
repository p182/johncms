<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

use Johncms\Http\Request;
use Johncms\Http\Session;
use Johncms\i18n\Languages;
use Johncms\View\Render;

$request = di(Request::class);
$render = di(Render::class);
$session = di(Session::class);

$render->addData(
    [
        'title'      => __('Preparing for installation'),
        'page_title' => 'JohnCMS ' . CMS_VERSION,
    ]
);

$request_locale = $request->getQuery('set_locale', null, FILTER_SANITIZE_STRING);

$lng_list = Languages::getLngList();

// If the user is changing language
if (! empty($request_locale) && array_key_exists($request_locale, $lng_list)) {
    $session->set('lng', $request_locale);
    header('Location: /install/');
    exit;
}

$data = [
    'lng_list' => $lng_list,
];

echo $render->render('install::step_1', ['data' => $data]);
