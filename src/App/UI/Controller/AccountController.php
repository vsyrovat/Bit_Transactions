<?php declare(strict_types=1);

namespace App\UI\Controller;

use App\Domain\Entity\Money;
use App\Domain\Exception\UnableCreateWithdrawalException;
use Framework\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type as FormType;

class AccountController
{
    public static function indexAction(Request $request, Application $app)
    {
        if (!$app['user']->hasRole('ROLE_USER')) {
            return new RedirectResponse($app['url_generator']->generate('login'));
        }

        $account = $app['app.uc.findAccountByUsername']->run($app['user']->getUsername());

        return $app['twig']->render('Account/index.html.twig', [
            'account' => $account,
        ]);
    }

    public static function newWithdrawalAction(Request $request, Application $app)
    {
        if (!$app['user']->hasRole('ROLE_USER')) {
            return new RedirectResponse($app['url_generator']->generate('login'));
        }

        $account = $app['app.uc.findAccountByUsername']->run($app['user']->getUsername());

        $form = $app['form.factory']->createBuilder(FormType\FormType::class)
            ->setMethod('POST')
            ->add('amount', FormType\MoneyType::class, [
                'required' => true,
                'currency' => $account->getBalance()->getCurrency()
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $app['app.uc.createWithdrawal']->run($app['user'], $account, new Money($data['amount'], 'USD'));
                $app['session']->getFlashBag()->set('success', 'Withdrawal successfully created');
            } catch (UnableCreateWithdrawalException $e) {
                $app['session']->getFlashBag()->set('danger', 'Error creating withdrawal: '.$e->getMessage());
            }

            return new RedirectResponse($app['url_generator']->generate('account'));
        }

        return $app['twig']->render('Account/new_withdrawal.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
