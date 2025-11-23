document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById('filter-search');
    const roleSelect = document.getElementById('filter-role');
    const statusSelect = document.getElementById('filter-status');
    const tableBody = document.getElementById('user-table-body');
    const userRows = tableBody ? tableBody.querySelectorAll('tr') : [];
    const noResultsRow = tableBody ? tableBody.querySelector('tr td[colspan="9"]') : null;

    if (searchInput) searchInput.value = '';
    if (roleSelect) roleSelect.value = '';
    if (statusSelect) statusSelect.value = '';

    function filterUsers() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedRole = roleSelect.value;
        const selectedStatus = statusSelect.value;
        let visibleCount = 0;

        userRows.forEach(row => {
            if (row === noResultsRow?.parentNode) return;

            const id = row.dataset.id;
            const user = row.dataset.user.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const role = row.dataset.role;
            const status = row.dataset.status;

            const matchesSearch = searchText === '' ||
                id.includes(searchText) ||
                user.includes(searchText) ||
                email.includes(searchText);

            const matchesRole = selectedRole === '' || role === selectedRole;

            const matchesStatus = selectedStatus === '' || status === selectedStatus;

            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultsRow) {
            noResultsRow.parentNode.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterUsers);
    if (roleSelect) roleSelect.addEventListener('change', filterUsers);
    if (statusSelect) statusSelect.addEventListener('change', filterUsers);

    filterUsers();
});