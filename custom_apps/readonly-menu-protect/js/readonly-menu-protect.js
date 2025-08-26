(function() {
    'use strict';

    console.log('🔒 Readonly Menu Protect: Initialized');

    const CONFIG = {
        retryInterval: 1000,
        maxRetries: 10,
        allowedPaths: [
            '/apps/files/',
            '/login',
            '/logout',
            '/index.php/login',
            '/index.php/logout',
            '/settings/'
        ]
    };

    let retryCount = 0;

    function isAllowedPath() {
        const path = window.location.pathname + window.location.search;
        return CONFIG.allowedPaths.some(allowed => path.includes(allowed)) ||
               path === '/' || path === '/index.php';
    }

    function redirectToFiles() {
        if (isAllowedPath()) return;

        const filesUrl = OC.generateUrl('/apps/files/');
        console.log('🔒 Redirecting to Files app:', filesUrl);

        if (OC.Notification) {
            OC.Notification.showTemporary(
                t('readonly-menu-protect', 'Access to apps is restricted in readonly mode')
            );
        }

        window.location.href = filesUrl;
    }

    function hideNavigation() {
        const entries = document.querySelectorAll(
            '.app-navigation-entry, [data-app-id], .app-menu-entry, .app-navigation__list li'
        );

        let filesFound = false;

        entries.forEach(entry => {
            const appId = entry.getAttribute('data-app-id') ||
                         entry.getAttribute('data-id') ||
                         '';
            const link = entry.querySelector('a');
            const href = link?.getAttribute('href') || '';
            const text = link?.textContent.trim().toLowerCase() || '';

            const isFiles = appId === 'files' ||
                           text === 'files' ||
                           text === 'файлы' ||
                           href.includes('/apps/files/');

            if (isFiles) {
                filesFound = true;
            } else {
                entry.style.display = 'none';
                entry.remove();
            }
        });

        const toggle = document.querySelector('.app-navigation-toggle');
        if (toggle) toggle.style.display = 'none';

        return filesFound;
    }

    function applyProtection() {
        if (retryCount >= CONFIG.maxRetries) {
            console.log('⏹️ Max retries reached');
            return;
        }

        retryCount++;

        if (!isAllowedPath()) {
            redirectToFiles();
            return;
        }

        const success = hideNavigation();

        if (!success) {
            console.log(`⚠️ Navigation not ready, retry ${retryCount}/${CONFIG.maxRetries}`);
            setTimeout(applyProtection, CONFIG.retryInterval);
        } else {
            console.log('✅ Protection applied');
        }
    }

    function observeDOM() {
        const observer = new MutationObserver(() => {
            console.log('🔁 DOM changed, reapplying protection');
            setTimeout(applyProtection, 500);
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        console.log('🔒 DOM loaded, starting protection...');
        setTimeout(applyProtection, 1000);
        observeDOM();

        setInterval(applyProtection, 5000);
    });
})();
