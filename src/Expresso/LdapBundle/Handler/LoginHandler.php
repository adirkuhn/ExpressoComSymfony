<?php 
namespace Expresso\LdapBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $token->setAttribute( 'username' , $request->request->get( '_username' ));
        $token->setAttribute( 'plainPassword' , $request->request->get( '_password' ));
        return new RedirectResponse($request->getBaseUrl());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($request->headers->get('referer'));
    }

    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($request->getBaseUrl());
    }

}
