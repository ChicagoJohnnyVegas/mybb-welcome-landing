(function () {
    'use strict';

    var modalTriggers = {};
    function isWelcomeModal(element) {
        return element && (element.id === 'welcome_login' || element.id === 'welcome_about');
    }

    // Register before DOMContentLoaded: server-side errors can open a modal then,
    // including synchronously when reduced motion disables the transition.
    document.addEventListener('show.bs.modal', function (event) {
        if (!isWelcomeModal(event.target)) return;
        modalTriggers[event.target.id] = event.relatedTarget || document.querySelector(
            event.target.id === 'welcome_login' ? '#btnLogin' : 'a[href="#welcome_about"]');
    });
    document.addEventListener('shown.bs.modal', function (event) {
        var modal = event.target;
        if (!isWelcomeModal(modal)) return;
        var errors = modal.querySelector('.wel-login-error');
        var hasErrors = errors && errors.textContent.trim() && errors.getClientRects().length;
        var target = hasErrors ? errors : modal.querySelector('#txtUserName, #welcome_about_title');
        var form = modal.querySelector('form');
        if (form && hasErrors && errors.id) form.setAttribute('aria-describedby', errors.id);
        else if (form) form.removeAttribute('aria-describedby');
        if (target) target.focus();
    });
    document.addEventListener('hidden.bs.modal', function (event) {
        if (!isWelcomeModal(event.target) || document.querySelector('.modal.show')) return;
        var trigger = modalTriggers[event.target.id];
        if (trigger && trigger.isConnected && trigger.getClientRects().length) trigger.focus();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Tab' || !document.documentElement.classList.contains('wel-modals-ready')) return;
        var modal = event.target.closest('.modal.show');
        if (!isWelcomeModal(modal)) return;
        var controls = Array.from(modal.querySelectorAll('a[href], button, input, select, textarea, [tabindex], [contenteditable="true"]'))
            .filter(function (element) {
                return element.tabIndex >= 0 && !element.matches(':disabled')
                    && element.getClientRects().length && getComputedStyle(element).visibility === 'visible';
            }).sort(function (a, b) { return (a.tabIndex || Infinity) - (b.tabIndex || Infinity); });
        var index = controls.indexOf(document.activeElement);
        // Bootstrap redirects focusin from outside the modal, but browser chrome
        // receives no DOM focusin event. Wrap the two tab boundaries explicitly.
        if (controls.length && ((event.shiftKey && index <= 0) || (!event.shiftKey && index === controls.length - 1))) {
            event.preventDefault();
            controls[event.shiftKey ? controls.length - 1 : 0].focus();
        }
    });

    window.welcomeLandingSetBackground = function (source) {
        var image = new Image();
        image.onload = function () {
            var background = document.createElement('div');
            background.className = 'wel-background';
            background.setAttribute('aria-hidden', 'true');
            background.style.backgroundImage = 'url(' + JSON.stringify(source) + ')';
            document.body.insertBefore(background, document.body.firstChild);
            // Commit the transparent layer before starting its opacity transition.
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    background.classList.add('wel-background-visible');
                });
            });
        };
        image.src = source;
    };

    function enableModals() {
        window.clearTimeout(window.welcomeLandingStartupTimer);
        document.documentElement.classList.remove('wel-modals-pending');
        // Do not replace a fallback already exposed after a slow startup, or
        // interrupt someone who reached the inline form via its anchor.
        if (window.welcomeLandingInline || document.querySelector('#welcome_login:focus-within, #welcome_about:focus-within')) return;
        var jq = window.jQuery;
        if (!jq || !window.bootstrap || typeof window.bootstrap.Modal !== 'function') {
            return;
        }

        // Initialize before hiding the inline fallback. Images are independent.
        document.querySelectorAll('#welcome_login, #welcome_about').forEach(function (element) {
            window.bootstrap.Modal.getOrCreateInstance(element);
        });
        document.documentElement.classList.add('wel-modals-ready');
        if (window.location.hash === '#welcome_login' || window.location.hash === '#welcome_about') {
            var target = document.querySelector(window.location.hash);
            if (target) window.bootstrap.Modal.getOrCreateInstance(target).show();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enableModals);
    } else {
        enableModals();
    }
}());
