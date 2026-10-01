const normalize = (text) => text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

const pluralize = (count) => `${count} ${count === 1 ? 'item' : 'items'}`;

const UNDO_TIMEOUT = 5000;

let pendingUndo = null;

function applyFilter() {
    const input = document.querySelector('[data-filter]');
    const query = input ? normalize(input.value.trim()) : '';
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

// Resolves with true on success, reloads the page if the session or CSRF token expired.
function post(url, token) {
    const body = new FormData();
    body.append('_token', token);

    return fetch(url, {
        method: 'POST',
        body,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status === 401 || response.status === 419) {
            window.location.reload();
            return false;
        }
        if (!response.ok) throw new Error(`Request failed with status ${response.status}`);
        return true;
    });
}

function hideToast() {
    clearTimeout(pendingUndo?.timer);
    pendingUndo = null;
    document.querySelector('[data-toast]').hidden = true;
}

function showUndoToast(message, undo) {
    clearTimeout(pendingUndo?.timer);

    const toast = document.querySelector('[data-toast]');
    toast.querySelector('[data-toast-message]').textContent = message;
    toast.hidden = false;

    pendingUndo = { undo, timer: setTimeout(hideToast, UNDO_TIMEOUT) };
}

export function submitAndRemoveRow(form, event) {
    event.preventDefault();
    const row = form.closest('[data-item]');
    const button = form.querySelector('button');
    const token = form.querySelector('input[name="_token"]').value;
    const delta = parseInt(form.dataset.delta || '0', 10);
    button.disabled = true;

    post(form.action, token)
    .then(ok => {
        if (!ok) return;

        updateSelectedCount(delta);
        row.classList.add('is-leaving');

        const parent = row.parentNode;
        const next = row.nextSibling;

        const removeTimer = setTimeout(() => {
            row.remove();
            refresh();
        }, 200);

        const name = row.dataset.name;
        const message = delta > 0 ? `${name} added to your list` : `${name} removed from your list`;

        showUndoToast(message, () => post(row.dataset.undoAction, token).then(ok => {
            if (!ok) return;

            clearTimeout(removeTimer);
            updateSelectedCount(-delta);
            row.classList.remove('is-leaving');
            button.disabled = false;
            parent.insertBefore(row, next && next.parentNode === parent ? next : null);
            refresh();
        }));
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

    document.querySelector('[data-toast-undo]')?.addEventListener('click', () => {
        const undo = pendingUndo?.undo;
        hideToast();
        undo?.().catch(error => console.error('Error:', error));
    });

    document.querySelector('[data-filter]')?.addEventListener('input', applyFilter);
    applyFilter();
});
