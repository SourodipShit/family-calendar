<?php
$path_prefix = "../";
$page_title = "Login Logs";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-3 p-md-4" style="max-width: 1200px;">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-0">Login Logs</h4>
                <p class="text-muted small mb-0">Monitor user login activity and devices.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
            <div class="table-responsive">
                <table id="loginLogsTable" class="table table-hover align-middle border-0 mb-0 w-100">
                    <thead class="bg-light border-0">
                        <tr>
                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Time</th>
                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">User</th>
                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Family</th>
                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Device/Browser</th>
                            <th class="border-0 rounded-end py-2 text-uppercase extra-small ls-1 fw-bold text-muted">IP & Location</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        initLoginLogsTable();
    });

    function initLoginLogsTable() {
        if ($.fn.DataTable.isDataTable('#loginLogsTable')) {
            $('#loginLogsTable').DataTable().destroy();
        }

        $('#loginLogsTable').DataTable({
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo $path_prefix; ?>api/loginlogs.php",
                "type": "GET"
            },
            "columns": [{
                    "data": "login_time_formatted",
                    "render": function(data, type, row) {
                        return `<span class="small fw-semibold text-dark">${data}</span>`;
                    }
                },
                {
                    "data": "user_name",
                    "render": function(data, type, row) {
                        return `<span class="small fw-bold text-primary">${data || 'Unknown'}</span>`;
                    }
                },
                {
                    "data": "family_name",
                    "render": function(data, type, row) {
                        return data ? `<span class="small fw-semibold text-dark">${data}</span>` : `<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">N/A</span>`;
                    }
                },
                {
                    "data": "device",
                    "render": function(data, type, row) {
                        return `<span class="small text-muted d-block">${row.device} / ${row.os}</span><span class="extra-small text-muted">${row.browser}</span>`;
                    }
                },
                {
                    "data": "ip_address",
                    "render": function(data, type, row) {
                        return `<span class="small text-muted d-block"><i class="ri-map-pin-line me-1"></i>${row.location || 'Unknown'}</span><span class="extra-small text-muted">${row.ip_address}</span>`;
                    }
                }
            ],
            "order": [
                [0, "desc"]
            ],
            "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            "pageLength": 10,
            "language": {
                "search": "",
                "searchPlaceholder": "Search logs...",
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            }
        });
    }
</script>

<style>
    .ls-1 {
        letter-spacing: 0.05rem;
    }

    .extra-small {
        font-size: 0.65rem;
    }
</style>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>