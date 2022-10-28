<?php

namespace App\Security;

use App\Controller\RequestBase\LoginService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
//use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;


class UserLoginInAuthenticator extends AbstractAuthenticator
{

    function __construct(ManagerRegistry $doctrine)
    {
        $this->ListRoute = ['/api/login'];
        $this->doctrine = $doctrine;
    }
    public function supports(Request $request): ?bool
    {
        if ($request->getMethod() === 'POST' && !empty($request->request->get(LoginService::PASSWORD_TO_SEND)) && !empty($request->request->get(LoginService::PSEUDO_TO_SEND))) {
            for ($i = 0; $i < count($this->ListRoute); $i++) {
                if ($request->getPathInfo() === $this->ListRoute[$i]) {
                    return true;
                }
            }
        }
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $password = $request->request->get(LoginService::PASSWORD_TO_SEND);
        $username = $request->request->get(LoginService::PSEUDO_TO_SEND);
        //$csrfToken = $request->request->get('csrf_token');
        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            []
        );
        // return new SelfValidatingPassport(new UserBadge($request->request->get('pseudo', '')));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => "pseudo ou mot de passe incorect"], 400);
    }
}
