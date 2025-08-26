<?php
declare(strict_types=1);

namespace OCA\ReadonlyRedirect\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IURLGenerator;

class Application extends App implements IBootstrap {
    public const APP_ID = 'readonly_redirect';
    private const READONLY_GROUP = 'readonly_users';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {}

    public function boot(IBootContext $context): void {
        $context->injectFn([$this, 'doBoot']);
    }

    public function doBoot(
        IUserSession $userSession,
        IGroupManager $groupManager,
        IRequest $request,
        IURLGenerator $urlGenerator
    ): void {
        $user = $userSession->getUser();
        if ($user === null) return;

        if (!$groupManager->isInGroup($user->getUID(), self::READONLY_GROUP)) {
            return;
        }

        $pathInfo = $request->getPathInfo();

        // Редирект, только если:
        // - GET-запрос
        // - HTML-страница
        // - Не в /apps/files
        // - Не на /login или /logout
        if ($request->getMethod() === 'GET' &&
            strpos($request->getHeader('Accept'), 'text/html') !== false &&
            !preg_match('%^/apps/files%', $pathInfo) &&
            !preg_match('%^/login%', $pathInfo) &&
            !preg_match('%^/logout%', $pathInfo)) {
            header('Location: ' . $urlGenerator->linkToRoute('files.view.index'));
            exit();
        }
    }
}

