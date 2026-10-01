const normalize = (text) => text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

const pluralize = (count) => `${count} ${count === 1 ? 'item' : 'items'}`;

function applyFilter() {
    const input = document.querySelector('[data-filter]');
    if (!input) return;

    const query = normalize(input.value.trim());
    let visible = 0;

    document.querySelectorAll('[data-item]').forEach(row => {
        row.hidden = query !== '' && !normalize(row.dataset.name).includes(query);
        if (!row.hidden) visible++;
    });

    document.querySelectorAll('[data-group]').forEach(group => {
        group.hidden = !group.querySelector('[data-item]:not([hidden])');
    });

    document.querySelectorAll('[data-no-match]').forEach(el => {
        el.hidden = query === '' || visible > 0;
        el.querySelector('[data-query]').textContent = input.value.trim();
    });
}

function refresh() {
    const count = document.querySelectorAll('[data-item]').length;

    document.querySelectorAll('[data-group]').forEach(group => {
        if (!group.querySelector('[data-item]')) group.remove();
    });
    document.querySelectorAll('[data-item-count]').forEach(el => el.textContent = pluralize(count));
    document.querySelectorAll('[data-empty]').forEach(el => el.hidden = count > 0);
    document.querySelectorAll('[data-hide-when-empty]').forEach(el => el.hidden = count === 0);

    applyFilter();
}

function updateSelectedCount(delta) {
    document.querySelectorAll('[data-selected-count]').forEach(badge => {
        const count = Math.max(0, parseInt(badge.textContent, 10) + delta);
        badge.textContent = count;
        badge.hidden = count === 0;
    });
}

export function submitAndRemoveRow(form, event) {
    event.preventDefault();
    const row = form.closest('[data-item]');
    const button = form.querySelector('button');
    button.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.status === 401 || response.status === 419) {
            window.location.reload();
            return;
        }
        if (!response.ok) throw new Error(`Request failed with status ${response.status}`);

        updateSelectedCount(parseInt(form.dataset.delta || '0', 10));
        row.classList.add('is-leaving');
        setTimeout(() => {
            row.remove();
            refresh();
        }, 200);
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-remove-row]').forEach(form => {
        form.addEventListener('submit', (event) => submitAndRemoveRow(form, event));
    });

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });

    document.querySelector('[data-filter]')?.addEventListener('input', applyFilter);
    applyFilter();
});
