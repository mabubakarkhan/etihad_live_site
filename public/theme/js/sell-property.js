(function () {
    var form = document.getElementById('sell-property-form');
    if (!form) return;

    var msg = document.getElementById('sell-property-form-msg');
    var submitBtn = document.getElementById('sell-property-form-submit');
    var submitText = submitBtn ? submitBtn.querySelector('.sell-property-form__submit-text') : null;
    var submitLoading = submitBtn ? submitBtn.querySelector('.sell-property-form__submit-loading') : null;
    var rentOnly = form.querySelector('.sell-property-form__rent-only');
    var bedsGroup = form.querySelector('.sell-property-form__beds-group');
    var typeChips = form.querySelector('[data-sell-chips="property_type"]');

    function setPillActive(group) {
        if (!group) return;
        group.querySelectorAll('.sell-property-form__pill').forEach(function (pill) {
            var input = pill.querySelector('input');
            pill.classList.toggle('is-active', input && input.checked);
        });
    }

    function setChipActive(container) {
        if (!container) return;
        container.querySelectorAll('.sell-property-form__chip').forEach(function (chip) {
            var input = chip.querySelector('input');
            chip.classList.toggle('is-active', input && input.checked);
        });
    }

    form.querySelectorAll('[data-sell-toggle]').forEach(function (group) {
        group.addEventListener('change', function () {
            setPillActive(group);
            syncIntentUi();
        });
        setPillActive(group);
    });

    form.querySelectorAll('[data-sell-chips]').forEach(function (group) {
        group.addEventListener('change', function () {
            setChipActive(group);
        });
        setChipActive(group);
    });

    function syncIntentUi() {
        var intent = (form.querySelector('input[name="intent"]:checked') || {}).value || 'sell';
        if (rentOnly) {
            rentOnly.hidden = intent !== 'rent';
        }
        if (bedsGroup) {
            bedsGroup.hidden = (form.querySelector('input[name="category"]:checked') || {}).value === 'commercial';
        }
    }

    function rebuildPropertyTypes() {
        if (!typeChips) return;
        var category = (form.querySelector('input[name="category"]:checked') || {}).value || 'residential';
        var types = (typeChips.getAttribute(category === 'commercial' ? 'data-commercial' : 'data-residential') || '').split(',');
        typeChips.innerHTML = '';
        types.forEach(function (type, idx) {
            type = type.trim();
            if (!type) return;
            var label = document.createElement('label');
            label.className = 'sell-property-form__chip';
            label.innerHTML = '<input type="radio" name="property_type" value="' + type + '"' + (idx === 0 ? ' checked' : '') + '><span>' + type + '</span>';
            typeChips.appendChild(label);
        });
        setChipActive(typeChips);
    }

    form.querySelectorAll('input[name="category"]').forEach(function (input) {
        input.addEventListener('change', function () {
            rebuildPropertyTypes();
            syncIntentUi();
        });
    });

    function setLoading(isLoading) {
        if (!submitBtn) return;
        submitBtn.disabled = isLoading;
        if (submitText) submitText.hidden = isLoading;
        if (submitLoading) submitLoading.hidden = !isLoading;
    }

    function showMsg(text, type) {
        if (!msg) return;
        msg.textContent = text;
        msg.className = 'sell-property-form__msg' + (type ? ' is-' + type : '');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.reportValidity()) return;

        showMsg('', '');
        setLoading(true);

        var fd = new FormData(form);
        if ((fd.get('intent') || '') !== 'rent') {
            fd.delete('rent_frequency');
        }

        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then(function (res) {
                return res.json().then(function (json) {
                    return { ok: res.ok, json: json };
                }).catch(function () {
                    return { ok: false, json: { success: false, message: 'Invalid response.' } };
                });
            })
            .then(function (result) {
                if (result.json && result.json.success) {
                    showMsg(result.json.message || 'Submitted successfully.', 'success');
                    form.reset();
                    form.querySelectorAll('[data-sell-toggle]').forEach(setPillActive);
                    rebuildPropertyTypes();
                    syncIntentUi();
                } else {
                    showMsg((result.json && result.json.message) || 'Something went wrong. Please try again.', 'error');
                }
            })
            .catch(function () {
                showMsg('Something went wrong. Please try again.', 'error');
            })
            .finally(function () {
                setLoading(false);
            });
    });

    rebuildPropertyTypes();
    syncIntentUi();
})();
