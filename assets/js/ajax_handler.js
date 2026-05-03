/**
 * online_exam_system/assets/js/ajax_handler.js
 * Centralized AJAX utility for the Online Exam System
 */

const AjaxHandler = {
    /**
     * Load content from a URL into a target element
     * @param {string} url - The URL to fetch
     * @param {string} targetSelector - CSS selector for the target container
     * @param {boolean} updateHistory - Whether to update the browser history
     */
    loadContent: async function(url, targetSelector, updateHistory = true) {
        const target = document.querySelector(targetSelector);
        if (!target) return Promise.resolve();

        // Add loading state
        target.classList.add('opacity-50', 'pointer-events-none');
        
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            target.innerHTML = html;

            if (updateHistory) {
                window.history.pushState({ url }, '', url);
            }

            // Re-initialize any tab-specific scripts if needed
            this.reinitScripts(target);
            return Promise.resolve();

        } catch (error) {
            console.error('AJAX Error:', error);
            target.innerHTML = `<div class="p-8 text-center text-red-500 font-bold">Failed to load content. Please try again.</div>`;
            return Promise.reject(error);
        } finally {
            target.classList.remove('opacity-50', 'pointer-events-none');
        }
    },

    /**
     * Submit a form via AJAX
     * @param {HTMLFormElement} form - The form element
     * @param {Function} onSuccess - Callback for success
     * @param {Function} onError - Callback for error
     */
    submitForm: async function(form, onSuccess, onError) {
        const formData = new FormData(form);
        const action = form.getAttribute('action');
        const method = form.getAttribute('method') || 'POST';
        try {
            const response = await fetch(action, {
                method: method,
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const result = await response.json();

            if (result.success) {
                if (onSuccess) onSuccess(result);
            } else {
                if (onError) onError(result.error || 'Something went wrong');
            }
        } catch (error) {
            console.error('Form Submission Error:', error);
            if (onError) onError('Network error occurred');
        }
    },

    /**
     * Re-initialize scripts for newly loaded content
     */
    reinitScripts: function(container) {
        // Example: Setup search bars in new content
        const searchInput = container.querySelector('[id$="-search"]');
        const tbody = container.querySelector('tbody');
        if (searchInput && tbody) {
            // Logic to setup search would go here if not already global
        }
        
        // Setup nested forms if any - Removed direct listeners to avoid double submission
        // Global listener in dashboard.php handles form[data-ajax="true"]
    }
};

// Handle browser Back/Forward buttons
window.addEventListener('popstate', (event) => {
    if (event.state && event.state.url) {
        AjaxHandler.loadContent(event.state.url, 'main', false);
    }
});

// Centralized Global Form Listener
document.addEventListener('submit', function(e) {
    const form = e.target.closest('form[data-ajax="true"]');
    if (!form) return;

    e.preventDefault();
    
    // Add loading state to button
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '<span class="flex items-center space-x-2"><span>Processing...</span></span>';

    AjaxHandler.submitForm(form, 
        (res) => {
            if (btn) btn.innerHTML = originalText;
            
            // Close modal if open
            const modal = form.closest('[data-purpose="modal-overlay"]');
            if (modal) modal.classList.add('hidden');

            if (res.redirect) {
                // If it's a relative redirect, try to load via AJAX if in portal
                if (res.redirect.includes('dashboard.php') || res.redirect.includes('detailed_result.php')) {
                    AjaxHandler.loadContent(res.redirect, 'main');
                } else {
                    window.location.href = res.redirect;
                }
            } else if (res.message) {
                if (typeof showNotice === 'function') {
                    showNotice(res.message);
                } else {
                    alert(res.message);
                }
                // Reload current content to show changes
                AjaxHandler.loadContent(window.location.href, 'main', false);
            }
        },
        (err) => {
            if (btn) btn.innerHTML = originalText;
            if (typeof showNotice === 'function') {
                showNotice(err, 'error');
            } else {
                alert('Error: ' + err);
            }
        }
    );
});
