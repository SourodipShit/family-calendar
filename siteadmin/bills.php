<?php
$path_prefix = "../";
$page_title = "Manage Bills & Invoices";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Payment.php';

$accountFilter = $_GET['account'] ?? null;
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                Bills & Invoices
                <?php if ($accountFilter): ?>
                    <small class="text-muted fs-6">for Account: <?php echo htmlspecialchars($accountFilter); ?></small>
                <?php endif; ?>
            </h4>
            <?php if ($accountFilter): ?>
                <a href="bills.php" class="btn btn-outline-secondary"><i class="ri-close-line me-1"></i> Clear Filter</a>
            <?php endif; ?>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="billSearch" class="form-control bg-light border-0" placeholder="Search bills...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="billsTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Invoice Date</th>
                                <th class="border-0">Account Number</th>
                                <th class="border-0">Family Name</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $paymentsRes = Payment::getAllPayments($accountFilter);
                            $payments = $paymentsRes['status'] === 'success' ? $paymentsRes['data'] : [];

                            foreach ($payments as $payment):
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small fw-medium"><?php echo htmlspecialchars($payment['invoice_date']); ?></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($payment['account_number']); ?></td>
                                <td><?php echo htmlspecialchars($payment['family_name']); ?></td>
                                <td class="fw-medium">$<?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if ($payment['status'] === 'paid') $statusClass = 'success';
                                    if ($payment['status'] === 'unpaid') $statusClass = 'warning';
                                    if ($payment['status'] === 'failed') $statusClass = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>-subtle text-<?php echo $statusClass; ?> px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                                        <?php echo htmlspecialchars($payment['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (!empty($payment['pdf_path'])): ?>
                                        <?php 
                                            // Ensure we output a valid web path
                                            $fileName = basename($payment['pdf_path']);
                                            $publicUrl = "../public/uploads/pdfs/" . $fileName;
                                        ?>
                                        <a href="<?php echo $publicUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3">
                                            <i class="ri-file-pdf-line me-1"></i> View PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No PDF</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    const table = $('#billsTable').DataTable({
        "dom": '<"d-none"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
        "pageLength": 15,
        "order": [[ 0, "desc" ]],
        "language": {
            "search": "",
            "paginate": {
                "next": '<i class="ri-arrow-right-s-line"></i>',
                "previous": '<i class="ri-arrow-left-s-line"></i>'
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": 5 } // Disable ordering on Actions column
        ]
    });

    // Custom search logic
    $('#billSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>
