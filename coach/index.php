<?php
$path_prefix = "../";
$page_title = "Coach Dashboard";
require_once $path_prefix . 'components/coach-header.php';
require_once $path_prefix . 'components/coach-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/coach-navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4">
        <div class="row">
            <div class="col-12">
                <style>
                    .coach-families-list::-webkit-scrollbar {
                        display: none;
                    }
                    .coach-families-list {
                        -ms-overflow-style: none;  /* IE and Edge */
                        scrollbar-width: none;  /* Firefox */
                    }
                    @media (max-width: 991px) {
                        .fc-dayGridMonth-view .fc-event {
                            pointer-events: none !important;
                        }
                    }
                </style>
                <div class="d-flex flex-nowrap align-items-center gap-0 mb-4 coach-families-list overflow-x-auto" id="coach-families-container">
                    <!-- Populated by JS -->
                </div>
            </div>
            
            <!-- Main Calendar Area -->
            <div class="col-lg-9">
                <!-- Calendar Controls -->
                <div class="d-lg-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <button id="btn-today" class="btn btn-outline-secondary border px-4 fw-medium text-dark">Today</button>
                        <button id="btn-prev" class="btn btn-link text-dark text-decoration-none p-0"><i class="fa-solid fa-chevron-left"></i></button>
                        <button id="btn-next" class="btn btn-link text-dark text-decoration-none p-0"><i class="fa-solid fa-chevron-right"></i></button>
                        <div class="dropdown">
                            <button id="date-picker-btn" class="btn btn-white fw-bold fs-5 border-0 p-0 d-flex align-items-center" type="button">
                                Loading... <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        <button type="button" class="btn btn-success fw-medium" data-bs-toggle="modal" data-bs-target="#uploadPlanModal">
                            <i class="fa-solid fa-upload me-1"></i> Upload Plan (CSV)
                        </button>
                        <div class="btn-group border" role="group">
                            <button type="button" id="btn-view-day" class="btn btn-white text-muted px-4 py-2 border-0 fw-medium toggle-view-btn" data-target="view-day">Day</button>
                            <button type="button" id="btn-view-week" class="btn btn-primary px-4 py-2 border-0 fw-medium toggle-view-btn" data-target="view-week">Week</button>
                            <button type="button" id="btn-view-month" class="btn btn-white text-muted px-4 py-2 border-0 fw-medium toggle-view-btn" data-target="view-month">Month</button>
                        </div>
                    </div>
                </div>

                <!-- Calendar Views Container -->
                <div id="calendar-views-container">
                    <!-- Week View (Default) -->
                    <div id="view-week" class="calendar-grid bg-white border rounded-3 overflow-hidden ">
                        <div class="calendar-grid-header">
                            <!-- Header Row -->
                            <div class="calendar-row header-row d-flex text-center border-bottom bg-white fw-bold">
                                <div class="time-col border-end p-3 text-muted">Time / Day</div>
                                <div class="day-col flex-fill border-end p-3">Sun<br><span class="fw-normal">--</span></div>
                                <div class="day-col flex-fill border-end p-3">Mon<br><span class="fw-normal">--</span></div>
                                <div class="day-col flex-fill border-end p-3">Tue<br><span class="fw-normal">--</span></div>
                                <div class="day-col flex-fill border-end p-3 text-primary"><span class="fw-bold">Wed</span><br><span class="d-inline-block bg-primary text-white rounded-circle mt-1 fw-bold" style="width: 30px; height: 30px; line-height: 30px;">--</span></div>
                                <div class="day-col flex-fill border-end p-3">Thu<br><span class="fw-normal">--</span></div>
                                <div class="day-col flex-fill border-end p-3">Fri<br><span class="fw-normal">--</span></div>
                                <div class="day-col flex-fill p-3">Sat<br><span class="fw-normal">--</span></div>
                            </div>

                            <!-- All Day Events Row -->
                            <div class="calendar-row all-day-row d-flex border-bottom bg-light">
                                <div class="time-col border-end p-2 d-flex align-items-center fw-bold text-dark fs-7">
                                    <i class="fa-regular fa-calendar-check text-primary me-2"></i> All Day Events
                                </div>
                                <div class="events-col flex-fill p-2 d-flex gap-2" id="all-day-events-container">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Scheduled Events Row (List View) -->
                        <div class="calendar-row events-list-row bg-white p-3" id="week-events-list-container">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Day View -->
                    <div id="view-day" class="d-none bg-white border rounded-3 overflow-hidden">
                        <div id="day-view-container" class="day-list-view">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Month View (FullCalendar) -->
                    <div id="view-month" class="d-none bg-white border rounded-3 p-3 position-relative">
                        <div id="full-calendar-container"></div>
                    </div>
                </div> 
            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-3">
                <!-- Upcoming Sessions Widget (Placeholder for Coach) -->
                <div class="card border rounded-3">
                    <div class="card-body p-4">
                        <h6 class="fw-bold ts-titels mb-4">Upcoming Sessions</h6>
                        <div class="text-muted text-center py-4">
                            <i class="fa-regular fa-calendar-xmark fs-2 mb-2 text-light"></i>
                            <p class="mb-0 fs-7">No upcoming sessions today.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Plan Modal -->
<div class="modal fade" id="uploadPlanModal" tabindex="-1" aria-labelledby="uploadPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadPlanForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="uploadPlanModalLabel">Upload CSV Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <input type="hidden" name="action" value="upload_csv">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Family</label>
                        <select class="form-select" name="family_coach_id" id="uploadFamilySelect" required>
                            <option value="">Choose a family...</option>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">CSV File</label>
                        <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                        <small class="text-muted">Format: Date, Title, Description, StartTime, EndTime</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-medium">Upload Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Coach Review Modal -->
<div class="modal fade" id="coachReviewModal" tabindex="-1" aria-labelledby="coachReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="coachReviewForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="coachReviewModalLabel">Review Family Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <input type="hidden" name="action" value="coach_review">
                    <input type="hidden" name="event_id" id="reviewEventId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Feedback / Notes</label>
                        <textarea class="form-control" name="feedback" id="reviewFeedback" rows="3" placeholder="Great job! Keep it up..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Action</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusApprove" value="coach_approved" checked>
                                <label class="form-check-label text-success fw-bold" for="statusApprove">Approve (Mark Complete)</label>
                            </div>
                            <div class="form-check ms-3">
                                <input class="form-check-input" type="radio" name="status" id="statusReopen" value="reopened">
                                <label class="form-check-label text-danger fw-bold" for="statusReopen">Reopen (Needs Work)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-medium">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!-- Premium Alert System -->
<script src="../public/js/alert.js"></script>
<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<!-- Coach Calendar JS -->
<script>
    const API_PATH = '<?php echo $path_prefix; ?>api/';
</script>
<script src="<?php echo $path_prefix; ?>public/js/coach-calendar.js?v=<?php echo time(); ?>"></script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
