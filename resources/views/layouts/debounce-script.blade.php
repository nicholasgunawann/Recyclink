<script>
    (function () {
        window.debounce = window.debounce || function (func, wait = 400) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };

        // Auto-attach debounced auto-submit to search inputs & category/filter selects across all GET forms
        function initDebounceForms() {
            document.querySelectorAll('form').forEach(form => {
                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (method !== 'GET') return;

                const searchInputs = form.querySelectorAll('input[type="search"], input[name="search"], input[name="q"], input.debounce-search');
                searchInputs.forEach(input => {
                    if (input._hasDebounce) return;
                    input._hasDebounce = true;
                    const debouncedSubmit = window.debounce(() => {
                        if (window.Turbo) {
                            const url = new URL(form.action || window.location.href);
                            const formData = new FormData(form);
                            for (let [k, v] of formData.entries()) {
                                if (v) url.searchParams.set(k, v);
                                else url.searchParams.delete(k);
                            }
                            Turbo.visit(url.toString());
                        } else {
                            form.submit();
                        }
                    }, 400);

                    input.addEventListener('input', debouncedSubmit);
                });

                const selects = form.querySelectorAll('select[name="category"], select[name="category_id"], select[name="role"], select[name="status"], select.debounce-filter');
                selects.forEach(select => {
                    if (select._hasDebounce) return;
                    select._hasDebounce = true;
                    const debouncedSubmit = window.debounce(() => {
                        if (window.Turbo) {
                            const url = new URL(form.action || window.location.href);
                            const formData = new FormData(form);
                            for (let [k, v] of formData.entries()) {
                                if (v) url.searchParams.set(k, v);
                                else url.searchParams.delete(k);
                            }
                            Turbo.visit(url.toString());
                        } else {
                            form.submit();
                        }
                    }, 250);

                    select.addEventListener('change', debouncedSubmit);
                });
            });
        }

        document.addEventListener('turbo:load', initDebounceForms);
        if (document.readyState !== 'loading') {
            initDebounceForms();
        } else {
            document.addEventListener('DOMContentLoaded', initDebounceForms);
        }
    })();
</script>