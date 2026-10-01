export function selectAndHide(element, event) {
    event.preventDefault();
    const row = element.closest('tr');
    const url = element.href;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
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
    document.querySelectorAll('[select-and-hide]').forEach(element => {
        element.addEventListener('click', (event) => selectAndHide(element, event));
    });
});