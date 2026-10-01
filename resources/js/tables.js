export function selectAndHide(form, event) {
    event.preventDefault();
    const row = form.closest('tr');

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            row.style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[select-and-hide]').forEach(form => {
        form.addEventListener('submit', (event) => selectAndHide(form, event));
    });
});
