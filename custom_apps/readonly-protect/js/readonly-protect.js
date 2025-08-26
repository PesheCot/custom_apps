(function() {
    'use strict';
    
    console.log('🔒 Readonly Protect: Initializing for readonly user');
    
    function enableReadonlyProtection() {
        try {
            hideNavigationItems();
            hideActionButtons();
            blockContextMenu();
            blockDragAndDrop();
            blockKeyboardShortcuts();
            protectFileElements();
            hideAdditionalUI();
        } catch (error) {
            console.error('Readonly Protect Error:', error);
        }
    }
    
    function hideNavigationItems() {
        document.querySelectorAll('.app-navigation-entry').forEach(el => {
            const link = el.querySelector('a');
            if (link && !link.href.includes('/apps/files/')) {
                el.style.display = 'none';
            }
        });
        
        const navToggle = document.querySelector('.app-navigation-toggle');
        if (navToggle) navToggle.style.display = 'none';
    }
    
    function hideActionButtons() {
        const selectors = [
            'button[data-action="upload"]',
            'button[data-action="new"]',
            '.button-new',
            '.icon-add',
            '.upload-button',
            '.action-upload'
        ];
        
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                el.style.display = 'none';
                el.disabled = true;
            });
        });
    }
    
    function blockContextMenu() {
        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.file-row, .files-fileList, .filelist-container')) {
                e.preventDefault();
                e.stopPropulation();
                showReadonlyMessage();
                return false;
            }
        }, true);
    }
    
    function blockDragAndDrop() {
        const preventDefault = function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        };
        
        ['dragstart', 'dragover', 'dragenter', 'dragleave', 'drop', 'dragend'].forEach(event => {
            document.addEventListener(event, preventDefault, true);
        });
    }
    
    function blockKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            const forbiddenKeys = ['Delete', 'F2', 'N', 'n', 'O', 'o', 'U', 'u'];
            const key = e.key;
            const ctrl = e.ctrlKey || e.metaKey;
            
            if (forbiddenKeys.includes(key) || (ctrl && forbiddenKeys.includes(key.toUpperCase()))) {
                e.preventDefault();
                e.stopPropagation();
                showReadonlyMessage();
                return false;
            }
        }, true);
    }
    
    function protectFileElements() {
        document.querySelectorAll('.file-row, .files-fileList li, .filelist tr').forEach(el => {
            el.style.pointerEvents = 'none';
            el.style.cursor = 'default';
            el.classList.add('readonly-file');
        });
        
        document.querySelectorAll('.file-row a, .filename a').forEach(link => {
            link.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            };
        });
    }
    
    function hideAdditionalUI() {
        document.querySelectorAll('.action-item, .fileActions, .action-menu').forEach(el => {
            el.style.display = 'none';
        });
        
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.style.display = 'none';
            checkbox.disabled = true;
        });
    }
    
    function showReadonlyMessage() {
        if (OC.Notification) {
            OC.Notification.showTemporary('Readonly mode: File modifications are disabled');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        enableReadonlyProtection();
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    setTimeout(enableReadonlyProtection, 100);
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true
        });
        
        setInterval(enableReadonlyProtection, 1000);
    });
})();
