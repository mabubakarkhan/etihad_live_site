{{-- DataTables for admin listing pages: search, sort, pagination --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<style>
    /* DataTables + dark theme */
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_length,
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_filter,
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_info,
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_paginate {
        color: #64748b;
        padding: 0.5rem 0;
    }
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_length,
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_filter,
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_info,
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_paginate {
        color: #94a3b8;
    }
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_length select,
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
        background: #f8fafc;
        color: #0f172a;
    }
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_length select,
    .dark .admin-datatable-wrapper .dataTables_wrapper .dataTables_filter input {
        border-color: #475569;
        background: #0f172a;
        color: #e2e8f0;
    }
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin: 0 1px;
    }
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #10b981 !important;
        color: #fff !important;
        border-color: #10b981 !important;
    }
    .admin-datatable-wrapper table.dataTable thead th {
        border-bottom: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
    }
    .dark .admin-datatable-wrapper table.dataTable thead th {
        border-bottom-color: #334155;
    }
    .admin-datatable-wrapper table.dataTable tbody td {
        padding: 0.5rem 1rem;
    }
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_length label,
    .admin-datatable-wrapper .dataTables_wrapper .dataTables_filter label {
        font-size: 0.875rem;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tables = document.querySelectorAll('table.admin-datatable');
    tables.forEach(function(table) {
        var emptyRow = table.querySelector('tbody tr[data-empty]');
        if (emptyRow) return;
        if (typeof $ !== 'undefined' && $.fn.DataTable && !$.fn.DataTable.isDataTable(table)) {
            $(table).DataTable({
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                order: [],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'No entries',
                    infoFiltered: '(filtered from _MAX_ total)',
                    paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' },
                    zeroRecords: 'No matching records found'
                },
                columnDefs: [
                    { orderable: false, searchable: false, targets: -1 }
                ],
                drawCallback: function() {
                    var api = this.api();
                    if (api.rows({ page: 'current' }).count() === 0 && api.data().length > 0) {
                        api.page('first').draw(false);
                    }
                }
            });
        }
    });
});
</script>
