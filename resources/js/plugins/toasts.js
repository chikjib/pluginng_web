function messageText(message) {
    if (Array.isArray(message)) return message.map(messageText).join(' ');
    if (message && typeof message === 'object') return Object.values(message).map(messageText).join(' ');
    return String(message ?? '');
}

export function createToasts({ duration = 1000 } = {}) {
    return {
        show(message, options = {}) {
            let container = document.querySelector('[data-pluginng-toasts]');
            if (!container) {
                container = document.createElement('div');
                container.dataset.pluginngToasts = '';
                container.className = 'pluginng-toasts';
                container.setAttribute('aria-live', 'polite');
                document.body.append(container);
            }
            const toast = document.createElement('div');
            toast.className = 'pluginng-toast';
            toast.setAttribute('role', 'status');
            toast.textContent = messageText(message);
            container.append(toast);
            const remove = () => {
                toast.remove();
                if (!container.childElementCount) container.remove();
            };
            const timer = setTimeout(remove, options.duration ?? duration);
            return { goAway: () => { clearTimeout(timer); remove(); } };
        },
    };
}
