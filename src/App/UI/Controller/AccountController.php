<?php declare(strict_types=1);

namespace App\UI\Controller;

use Framework\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AccountController
{
    public static function indexAction(Request $request, Application $app)
    {
        if (!$app['user']->isGrant('ROLE_USER')) {
            return new RedirectResponse($app['url_generator']->generate('login'));
        }

        $account = $app['app.uc.findAccountByUsername']->run($app['user']->getUsername());

        return $app['twig']->render('Account/index.html.twig', [
            'account' => $account,
        ]);
    }
}
