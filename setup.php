<?php
$path_prefix = "";
$page_title = "Family Setup - Family Calendar";
include 'components/header.php';
include 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include 'components/navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1400px; margin: 0 auto;">

        <!-- Progress Bar -->
        <div class="d-flex justify-content-center align-items-center mb-5 mt-2 position-relative"
            style="max-width: 800px; margin: 0 auto;">
            <!-- Step 1 -->
            <div class="d-flex align-items-center position-relative z-1 bg-white pe-3 step-indicator"
                id="nav-step-1" data-step="1">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2 step-circle shadow-sm"
                    style="width: 32px; height: 32px; font-size: 0.9rem;">1</div>
                <div class="d-flex flex-column">
                    <span class="fw-bold text-primary fs-7 step-title">Calendar & Location</span>
                    <span class="text-primary fs-8 step-status fw-medium" style="margin-top: -2px;">In
                        Progress</span>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-grow-1 border-top z-0 border-2 border-primary border-opacity-25"
                style="margin: 0 -15px;"></div>

            <!-- Step 2 -->
            <div class="d-flex align-items-center position-relative z-1 bg-white px-3 step-indicator opacity-50"
                id="nav-step-2" data-step="2">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-2 step-circle shadow-sm"
                    style="width: 32px; height: 32px; font-size: 0.9rem;">2</div>
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark fs-7 step-title">Family Members</span>
                    <span class="text-muted fs-8 step-status fw-medium d-none"
                        style="margin-top: -2px;">Pending</span>
                </div>
            </div>

            <!-- Line -->
            <div class="flex-grow-1 border-top z-0 border-2"
                style="margin: 0 -15px; border-color: #e9ecef !important;"></div>

            <!-- Step 3 -->
            <div class="d-flex align-items-center position-relative z-1 bg-white ps-3 step-indicator opacity-50"
                id="nav-step-3" data-step="3">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-2 step-circle shadow-sm"
                    style="width: 32px; height: 32px; font-size: 0.9rem;">3</div>
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark fs-7 step-title">Appointment Types</span>
                    <span class="text-muted fs-8 step-status fw-medium d-none"
                        style="margin-top: -2px;">Pending</span>
                </div>
            </div>
        </div>

        <!-- Wizard Content -->
        <div id="setup-wizard-container">

            <!-- Step 1 Content -->
            <div class="card border border-light-subtle shadow-sm rounded-4 mb-4" id="step1-content">
                <div class="row g-0">
                    <!-- Left form -->
                    <div class="col-lg-7 p-5">
                        <h4 class="fw-bold mb-1">Step 1 of 3: Calendar & Location</h4>
                        <p class="text-muted fs-7 mb-4">Let's set up the basic information for your family
                            calendar.</p>

                        <form>
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark fs-7">Calendar Name <span
                                        class="text-danger">*</span></label>
                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                    <input type="text" class="form-control border-0 px-3 py-2 text-dark"
                                        value="The Johnson Family Calendar">
                                    <span class="input-group-text bg-white border-0 text-muted"><i
                                            class="fa-regular fa-calendar"></i></span>
                                </div>
                                <div class="form-text fs-8 text-muted mt-1">This will be the name of your family
                                    calendar.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark fs-7">Location <span
                                        class="text-danger">*</span></label>
                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                    <input type="text" class="form-control border-0 px-3 py-2 text-dark"
                                        value="Dallas, Texas, USA">
                                    <span class="input-group-text bg-white border-0 text-muted"><i
                                            class="fa-solid fa-location-dot"></i></span>
                                </div>
                                <div class="form-text fs-8 text-muted mt-1">This helps us customize your
                                    calendar experience.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark fs-7">Primary Time Zone</label>
                                <select
                                    class="form-select border rounded-3 py-2 text-dark shadow-sm cursor-pointer fw-medium">
                                    <option selected>(GMT-06:00) Central Time (US & Canada)</option>
                                    <option>(GMT-05:00) Eastern Time (US & Canada)</option>
                                    <option>(GMT-07:00) Mountain Time (US & Canada)</option>
                                    <option>(GMT-08:00) Pacific Time (US & Canada)</option>
                                </select>
                                <div class="form-text fs-8 text-muted mt-1">All dates and times will be shown in
                                    this time zone.</div>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="form-label fw-semibold text-dark fs-7 mb-2 d-flex justify-content-between">
                                    Weather Information
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cursor-pointer bg-primary border-primary"
                                            type="checkbox" role="switch" checked>
                                    </div>
                                </label>
                                <div class="border rounded-3 p-3 d-flex align-items-center bg-white shadow-sm">
                                    <div class="bg-light rounded p-2 me-3 fs-4">
                                        <i class="fa-solid fa-cloud-sun text-warning"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block fs-7 text-dark">Show weather on my
                                            calendar</span>
                                        <span class="text-muted fs-8 d-block">Get daily weather updates for your
                                            location.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark fs-7">Calendar Purpose
                                    (Optional)</label>
                                <textarea class="form-control border rounded-3 p-3 text-dark fs-7 shadow-sm"
                                    rows="3">To organize our family events, meals, activities, and important appointments</textarea>
                                <div class="form-text fs-8 text-muted mt-1">Helps personalize your experience.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-5">
                                <button type="button"
                                    class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm btn-next"
                                    data-next="2">Continue <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Info -->
                    <div class="col-lg-5 bg-light p-5 border-start rounded-end-4 d-flex flex-column">
                        <div class="h-100 d-flex flex-column">
                            <!-- Illustration Placeholder -->
                            <div class="rounded-4 overflow-hidden mb-4 d-flex justify-content-center align-items-center shadow-sm"
                                style="height: 250px; background: url('https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=600&q=80') center/cover; opacity: 0.95;">
                            </div>

                            <h5 class="fw-bold mb-4 text-dark">Why we need this information?</h5>

                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Personalized calendar experience</span>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Local weather updates</span>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Time zone for accurate scheduling</span>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Better event recommendations</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 Content -->
            <div class="card border border-light-subtle shadow-sm rounded-4 mb-4 d-none" id="step2-content">
                <div class="row g-0">
                    <!-- Left form -->
                    <div class="col-lg-7 p-5">
                        <h4 class="fw-bold mb-1">Step 2 of 3: Add Family Members</h4>
                        <p class="text-muted fs-7 mb-4">Add your family members to personalize schedules,
                            chores, meals, and reminders.</p>

                        <h6 class="fw-bold mb-3 fs-7">Family Members</h6>

                        <div class="family-member-list">
                            <!-- Member 1 -->
                            <div
                                class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2 shadow-sm bg-white hover-bg-light cursor-pointer transition-all">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="fa-solid fa-grip-vertical text-muted opacity-50 me-3 cursor-move"></i>
                                    <img src="https://ui-avatars.com/api/?name=Dad&background=random&color=fff"
                                        class="rounded-circle me-3 border border-2 shadow-sm" width="46"
                                        height="46">
                                    <div>
                                        <span class="fw-bold d-block fs-7 text-dark">Dad (You)</span>
                                        <span class="text-muted d-block"
                                            style="font-size: 0.7rem;">Administrator</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-muted fs-7 d-flex align-items-center"><i
                                            class="fa-regular fa-calendar me-2"></i> 35 yrs</div>
                                    <i
                                        class="fa-solid fa-ellipsis-vertical text-muted ms-2 p-2 cursor-pointer hover-text-dark fs-5"></i>
                                </div>
                            </div>
                            <!-- Member 2 -->
                            <div
                                class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2 shadow-sm bg-white hover-bg-light cursor-pointer transition-all">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="fa-solid fa-grip-vertical text-muted opacity-50 me-3 cursor-move"></i>
                                    <img src="https://ui-avatars.com/api/?name=Mom&background=random&color=fff"
                                        class="rounded-circle me-3 border border-2 shadow-sm" width="46"
                                        height="46">
                                    <div>
                                        <span class="fw-bold d-block fs-7 text-dark">Mom</span>
                                        <span class="text-muted d-block"
                                            style="font-size: 0.7rem;">Parent</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-muted fs-7 d-flex align-items-center"><i
                                            class="fa-regular fa-calendar me-2"></i> 34 yrs</div>
                                    <i
                                        class="fa-solid fa-ellipsis-vertical text-muted ms-2 p-2 cursor-pointer hover-text-dark fs-5"></i>
                                </div>
                            </div>
                            <!-- Member 3 -->
                            <div
                                class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2 shadow-sm bg-white hover-bg-light cursor-pointer transition-all">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="fa-solid fa-grip-vertical text-muted opacity-50 me-3 cursor-move"></i>
                                    <img src="https://ui-avatars.com/api/?name=Emma&background=random&color=fff"
                                        class="rounded-circle me-3 border border-2 shadow-sm" width="46"
                                        height="46">
                                    <div>
                                        <span class="fw-bold d-block fs-7 text-dark">Emma</span>
                                        <span class="text-muted d-block" style="font-size: 0.7rem;">Child</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-muted fs-7 d-flex align-items-center"><i
                                            class="fa-regular fa-calendar me-2"></i> 12 yrs</div>
                                    <i
                                        class="fa-solid fa-ellipsis-vertical text-muted ms-2 p-2 cursor-pointer hover-text-dark fs-5"></i>
                                </div>
                            </div>
                        </div>

                        <button
                            class="btn btn-white border border-primary text-primary w-100 rounded-3 py-3 fw-bold mt-3 shadow-sm hover-bg-light d-flex align-items-center justify-content-center fs-7">
                            <i class="fa-solid fa-plus me-2"></i> Add Another Member
                        </button>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button"
                                class="btn btn-white border px-4 py-2 fw-medium rounded-3 shadow-sm text-dark btn-prev hover-bg-light"
                                data-prev="1"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                            <button type="button"
                                class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm btn-next"
                                data-next="3">Continue <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Right Info -->
                    <div class="col-lg-5 bg-light p-5 border-start rounded-end-4 d-flex flex-column">
                        <div class="h-100 d-flex flex-column">
                            <!-- Illustration Placeholder -->
                            <div class="rounded-4 overflow-hidden mb-4 d-flex justify-content-center align-items-center shadow-sm"
                                style="height: 250px; background: url('https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=600&q=80') center/cover; opacity: 0.95;">
                            </div>

                            <h5 class="fw-bold mb-4 text-dark">Why add family members?</h5>

                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Personalized schedules and
                                    reminders</span>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="fa-solid fa-circle-check text-success mt-1 me-3 fs-6"></i>
                                <span class="text-dark fs-7 fw-medium">Assign chores and track progress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 Content -->
            <div class="card border border-light-subtle shadow-sm rounded-4 mb-4 d-none" id="step3-content">
                <div class="row g-0">
                    <!-- Left form -->
                    <div class="col-lg-7 p-5">
                        <h4 class="fw-bold mb-1">Step 3 of 3: Set Appointment Types</h4>
                        <p class="text-muted fs-7 mb-4">Choose the types of appointments you want to schedule
                            and track for your family.</p>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button"
                                class="btn btn-white border px-4 py-2 fw-medium rounded-3 shadow-sm text-dark btn-prev hover-bg-light"
                                data-prev="2"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                            <button type="button"
                                class="btn btn-primary px-5 py-2 fw-medium rounded-3 shadow-sm btn-complete">Complete
                                Setup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End Setup Wizard Container -->

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nextBtns = document.querySelectorAll('.btn-next');
        const prevBtns = document.querySelectorAll('.btn-prev');
        const completeBtn = document.querySelector('.btn-complete');

        nextBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const nextStep = this.getAttribute('data-next');
                document.querySelectorAll('[id^="step"]').forEach(content => content.classList.add('d-none'));
                document.getElementById('step' + nextStep + '-content').classList.remove('d-none');
                
                // Update nav
                document.querySelectorAll('.step-indicator').forEach(indicator => indicator.classList.add('opacity-50'));
                document.getElementById('nav-step-' + nextStep).classList.remove('opacity-50');
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const prevStep = this.getAttribute('data-prev');
                document.querySelectorAll('[id^="step"]').forEach(content => content.classList.add('d-none'));
                document.getElementById('step' + prevStep + '-content').classList.remove('d-none');
                
                // Update nav
                document.querySelectorAll('.step-indicator').forEach(indicator => indicator.classList.add('opacity-50'));
                document.getElementById('nav-step-' + prevStep).classList.remove('opacity-50');
            });
        });

        if (completeBtn) {
            completeBtn.addEventListener('click', function() {
                window.location.href = 'users/index.php';
            });
        }
    });
</script>

<?php include 'components/footer.php'; ?>
