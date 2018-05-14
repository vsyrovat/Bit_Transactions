<?php declare(strict_types=1);

namespace App\UI\Controller;

use Framework\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;


class DefaultController
{
    public static function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('Default/index.html.twig');
    }

    public static function loginAction(Request $request, Application $app)
    {
        $alreadyLoggedIn = !empty($app['user']->getUsername());

        if (!$alreadyLoggedIn) {
            $form = $app['form.factory']->createBuilder(FormType\FormType::class)
                ->setAction($app['url_generator']->generate('login'))
                ->setMethod('POST')
                ->add('login', FormType\TextType::class, ['required' => true, 'attr' => ['placeholder' => 'user']])
                ->add('password', FormType\PasswordType::class, ['required' => true, 'attr' => ['placeholder' => '123456']])
                ->getForm();
            /* @var $form \Symfony\Component\Form\Form */

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if ($app['auth']->tryLogin($data['login'], $data['password'])) {
                    $app['session']->getFlashBag()->add('success', 'You logged as '.$data['login']);
                    return new RedirectResponse($app['url_generator']->generate('/'));
                }

                $app['session']->getFlashBag()->add('warning', 'Authorization not completed');
                return new RedirectResponse($app['url_generator']->generate('login'));
            }
        }

        return $app['twig']->render('Default/login.html.twig', [
            'form' => !empty($form) ? $form->createView() : null,
            'alreadyLoggedIn' => $alreadyLoggedIn,
        ]);
    }

    public static function logoutAction(Request $request, Application $app)
    {
        $app['session']->remove('user');
        $app['session']->getFlashBag()->add('success', 'You logged out');
        return new RedirectResponse($app['url_generator']->generate('/'));
    }
}
