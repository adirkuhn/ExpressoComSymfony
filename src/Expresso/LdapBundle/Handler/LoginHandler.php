<?php 
namespace Expresso\LdapBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        //TODO: remover o quanto antes
        //carrega session do expresso antigo
        $_SESSION['_POST']['login'] = $token->getUser()->getAttribute('uid');
        $_SESSION['_POST']['user'] = $token->getUser()->getAttribute('uid');
        $_SESSION['_POST']['passwd'] = 'prognus';
        $_SESSION['_POST']['passwd_type'] = 'text';
        $_SESSION['_POST']['sessionid'] = session_id();

        return new RedirectResponse($request->getBaseUrl());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($request->headers->get('referer'));
    }
}
