/**
 * Premium Custom Alert System
 * Handles dynamic alert modals with different types and glassmorphism styling.
 */

const AlertSystem = (() => {
    // Inject styles into the head
    const injectStyles = () => {
        if (document.getElementById('custom-alert-styles')) return;

        const style = document.createElement('style');
        style.id = 'custom-alert-styles';
        style.innerHTML = `
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

            :root {
                --alert-success-bg: rgba(16, 185, 129, 0.1);
                --alert-success-border: rgba(16, 185, 129, 0.2);
                --alert-success-accent: #10b981;
                --alert-success-glow: rgba(16, 185, 129, 0.15);

                --alert-error-bg: rgba(239, 68, 68, 0.1);
                --alert-error-border: rgba(239, 68, 68, 0.2);
                --alert-error-accent: #ef4444;
                --alert-error-glow: rgba(239, 68, 68, 0.15);

                --alert-warning-bg: rgba(245, 158, 11, 0.1);
                --alert-warning-border: rgba(245, 158, 11, 0.2);
                --alert-warning-accent: #f59e0b;
                --alert-warning-glow: rgba(245, 158, 11, 0.15);

                --alert-info-bg: rgba(14, 165, 233, 0.1);
                --alert-info-border: rgba(14, 165, 233, 0.2);
                --alert-info-accent: #0ea5e9;
                --alert-info-glow: rgba(14, 165, 233, 0.15);

                --alert-shadow-premium: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02), 0 0 0 1px rgba(0, 0, 0, 0.05);
            }

            .alert-container {
                position: fixed;
                top: 24px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 10000;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
                pointer-events: none;
                font-family: 'Outfit', sans-serif;
            }

            .alert-modal {
                min-width: 360px;
                max-width: 480px;
                padding: 14px 18px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px) saturate(180%);
                -webkit-backdrop-filter: blur(20px) saturate(180%);
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow: var(--alert-shadow-premium);
                display: flex;
                align-items: center;
                gap: 16px;
                transform: translateY(-150%) scale(0.9);
                opacity: 0;
                transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
                pointer-events: auto;
                cursor: pointer;
                position: relative;
                overflow: hidden;
            }

            .alert-modal:hover {
                transform: translateY(2px) scale(1.01);
                background: rgba(255, 255, 255, 0.95);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
            }

            .alert-modal.show {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            /* Type Specific Glow & Accents */
            .alert-success { box-shadow: var(--alert-shadow-premium), 0 0 20px var(--alert-success-glow); border-color: var(--alert-success-border); }
            .alert-error { box-shadow: var(--alert-shadow-premium), 0 0 20px var(--alert-error-glow); border-color: var(--alert-error-border); }
            .alert-warning { box-shadow: var(--alert-shadow-premium), 0 0 20px var(--alert-warning-glow); border-color: var(--alert-warning-border); }
            .alert-info { box-shadow: var(--alert-shadow-premium), 0 0 20px var(--alert-info-glow); border-color: var(--alert-info-border); }

            .alert-icon-wrapper {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                position: relative;
                overflow: hidden;
            }

            .alert-success .alert-icon-wrapper { background: var(--alert-success-bg); color: var(--alert-success-accent); }
            .alert-error .alert-icon-wrapper { background: var(--alert-error-bg); color: var(--alert-error-accent); }
            .alert-warning .alert-icon-wrapper { background: var(--alert-warning-bg); color: var(--alert-warning-accent); }
            .alert-info .alert-icon-wrapper { background: var(--alert-info-bg); color: var(--alert-info-accent); }

            .alert-content {
                flex-grow: 1;
                padding-right: 8px;
            }

            .alert-title {
                font-weight: 700;
                font-size: 14px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-bottom: 3px;
            }

            .alert-success .alert-title { color: var(--alert-success-accent); }
            .alert-error .alert-title { color: var(--alert-error-accent); }
            .alert-warning .alert-title { color: var(--alert-warning-accent); }
            .alert-info .alert-title { color: var(--alert-info-accent); }

            .alert-message {
                font-size: 15px;
                font-weight: 400;
                color: #374151;
                line-height: 1.5;
            }

            .alert-close {
                color: #9ca3af;
                transition: all 0.2s;
                background: rgba(0, 0, 0, 0.03);
                border: none;
                width: 28px;
                height: 28px;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0.6;
            }

            .alert-close:hover {
                opacity: 1;
                background: rgba(0, 0, 0, 0.08);
                color: #1f2937;
                transform: rotate(90deg);
            }

            /* Sleek Progress Bar */
            .alert-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 4px;
                background: rgba(0, 0, 0, 0.03);
                width: 100%;
            }

            .alert-progress-fill {
                height: 100%;
                width: 100%;
                transform-origin: left;
                filter: brightness(1.1);
            }

            .alert-success .alert-progress-fill { background: var(--alert-success-accent); }
            .alert-error .alert-progress-fill { background: var(--alert-error-accent); }
            .alert-warning .alert-progress-fill { background: var(--alert-warning-accent); }
            .alert-info .alert-progress-fill { background: var(--alert-info-accent); }

            @keyframes progress {
                from { transform: scaleX(1); }
                to { transform: scaleX(0); }
            }

            @keyframes icon-pop {
                0% { transform: scale(0.5); opacity: 0; }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); opacity: 1; }
            }

            .alert-modal.show .alert-icon-wrapper svg {
                animation: icon-pop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }
        `;
        document.head.appendChild(style);
    };

    const getIcon = (type) => {
        const icons = {
            success: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
            error: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`,
            warning: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
            info: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
        };
        return icons[type] || icons.info;
    };

    let container = null;

    const init = () => {
        injectStyles();
        if (!container) {
            container = document.createElement('div');
            container.className = 'alert-container';
            document.body.appendChild(container);
        }
    };

    const show = (message, type = 'info', duration = 4000) => {
        if (!container) init();

        const alert = document.createElement('div');
        alert.className = `alert-modal alert-${type}`;
        
        alert.innerHTML = `
            <div class="alert-icon-wrapper">${getIcon(type)}</div>
            <div class="alert-content">
                <div class="alert-title">${type}</div>
                <div class="alert-message">${message}</div>
            </div>
            <button class="alert-close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="alert-progress">
                <div class="alert-progress-fill" style="animation: progress ${duration}ms linear forwards"></div>
            </div>
        `;

        container.appendChild(alert);

        // Trigger entrance animation
        setTimeout(() => alert.classList.add('show'), 10);

        const closeAlert = () => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 500);
        };

        alert.querySelector('.alert-close').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAlert();
        });

        alert.addEventListener('click', closeAlert);

        if (duration > 0) {
            setTimeout(closeAlert, duration);
        }
    };

    return { init, show };
})();

/**
 * Initializes the alert system. 
 * Can be called manually, but will also self-initialize on first use.
 */
window.initializeAlert = AlertSystem.init;

/**
 * Global function to show alerts
 * @param {string} message - The message to display
 * @param {string} type - success, error, warning, info
 * @param {number} duration - Time in ms before auto-close
 */
window.showAlert = AlertSystem.show;
