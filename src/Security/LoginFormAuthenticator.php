<?php
// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->request->get('_username', '');
        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, mixed $token, string $firewallName): ?Response
    {
        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login');
    }

    // Attention ici : on enlève le "?" pour retourner un Response non-nullable
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // Stocke l'erreur en session pour l'afficher ensuite
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->getLoginUrl($request));
    }
}
