document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('lots-table');
    const sortButtons = document.querySelectorAll('.sort-button');
    const tbody = table.querySelector('tbody');

    sortButtons.forEach(button => {
        button.addEventListener('click', () => {
            const sortBy = button.dataset.sortBy;
            sortTable(sortBy);
        });
    });

    function sortTable(sortBy) {
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((rowA, rowB) => {
            const cellA = rowA.querySelector('td:nth-child(${getColumnIndex(sortBy)})').textContent.trim();
            const cellB = rowB.querySelector('td:nth-child(${getColumnIndex(sortBy)})').textContent.trim();

            let compareResult;

            const numA = parseFloat(cellA);
            const numB = parseFloat(cellB);

            if (!isNaN(numA) && !isNaN(numB)) {
                compareResult = numA - numB;
            } else {
                compareResult = cellA.localeCompare(cellB);
            }

            return compareResult;
        });

        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        rows.forEach(row => {
            tbody.appendChild(row);
        });

        function getColumnIndex(sortBy) {
            switch (sortBy) {
                case 'name':
                    return 1; // Assuming name is in the first column
                case 'price':
                    return 2; // Assuming price is in the second column
                case 'quantity':
                    return 3; // Assuming quantity is in the third column
                default:
                    return 1; // Default to name if not recognized
            }
        }
    }

    document.getElementById('sort-select').addEventListener('change', function() {
        const sortType = this.value;
        const table = document.getElementById('lots-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('.main-row'));

        function getValue(row) {
            switch (sortType) {
                case 'name':
                    return row.dataset.reference ? row.dataset.reference.toLowerCase() : '';
                case 'quantity':
                    return parseInt(row.dataset.quantite_total, 10) || 0;
                case 'etat':
                    return row.dataset.etat || '';
                case 'fournisseur':
                    return row.dataset.fournisseur_id || '';
                default:
                    return '';
            }
        }

        rows.sort((a, b) => {
            const valA = getValue(a);
            const valB = getValue(b);
            if (typeof valA === 'number' && typeof valB === 'number') {
                return valA - valB;
            }
            return valA.localeCompare(valB, undefined, {numeric: true});
        });

        // Remove all rows (and their details rows)
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }

        // Re-add sorted rows and their details rows
        rows.forEach(row => {
            const detailsRow = row.nextElementSibling && row.nextElementSibling.classList.contains('details-row')
                ? row.nextElementSibling
                : null;
            tbody.appendChild(row);
            if (detailsRow) {
                tbody.appendChild(detailsRow);
            }
        });
    });
});