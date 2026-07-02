<?php
$path_prefix = "../";
$page_title = "Event Details";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4">

        <div class="row">
            <div class="col-12">
                <!-- Family Members Row -->
                <div class="d-flex flex-wrap align-items-center gap-0 mb-4 family-members-list">
                    <!-- Populated by JS -->
                </div>
            </div>
            <!-- Main Calendar Area -->
            <div class="col-lg-9">


                <!-- Back to Calendar Button -->
                <div class="d-flex justify-content-end mb-4">
                    <a href="index.php"
                        class="btn btn-white text-primary border px-4 rounded-2 fw-medium  hover-bg-light text-decoration-none">
                        <i class="fa-solid fa-chevron-left me-2"></i> Back to Calendar
                    </a>
                </div>

                <!-- Event Details Card -->
                <div class="card border rounded-4 mb-4">
                    <!-- Header Area -->
                    <div
                        class="card-body p-4 border-bottom d-flex align-items-start gap-4 flex-column flex-md-row">
                        <!-- Purple Icon -->
                        <div class="rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 120px; height: 120px; background-color: #d1c4e9; color: #fff;">
                            <!-- Using a custom SVG or Font Awesome for a tooth -->
                            <i class="fa-solid fa-tooth fa-4x text-white drop-shadow"></i>
                        </div>

                        <!-- Title and Info -->
                        <div class="flex-grow-1 w-100">
                            <div
                                class="d-flex justify-content-between align-items-start mb-3 flex-column flex-md-row gap-3">
                                <h3 class="fw-bold mb-0 d-flex align-items-center">
                                    Dentist Appointment (Ava)
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary ms-3 fs-8 rounded-pill fw-medium px-3 py-2">Event</span>
                                </h3>
                                <div class="d-flex gap-2">
                                    <button
                                        class="btn btn-white border rounded-3 fw-medium text-dark shadow-sm px-3 hover-bg-light">
                                        <i class="fa-solid fa-pen text-muted me-2"></i> Edit
                                    </button>
                                    <button
                                        class="btn btn-white border border-danger-subtle text-danger rounded-3 fw-medium shadow-sm px-3 hover-bg-light">
                                        <i class="fa-regular fa-trash-can me-2"></i> Delete
                                    </button>
                                </div>
                            </div>

                            <div class="row g-4 mt-1">
                                <!-- Left Details -->
                                <div class="col-md-7">
                                    <div class="mb-3 d-flex align-items-center text-dark">
                                        <i class="fa-regular fa-calendar text-muted me-3 fs-5"
                                            style="width: 20px;"></i>
                                        <span class="fw-medium">Wednesday, May 15, 2024</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-4 mb-3">
                                        <div class="d-flex align-items-center text-dark">
                                            <i class="fa-regular fa-clock text-muted me-3 fs-5"
                                                style="width: 20px;"></i>
                                            <span>12:30 PM – 1:30 PM</span>
                                        </div>
                                        <div class="d-flex align-items-center text-dark">
                                            <i class="fa-regular fa-bell text-muted me-2 fs-5"
                                                style="width: 20px;"></i>
                                            <span>Reminder: 15 minutes before</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start text-dark">
                                        <i class="fa-solid fa-location-dot text-muted me-3 mt-1 fs-5"
                                            style="width: 20px;"></i>
                                        <div>
                                            <span class="fw-medium d-block">Bright Smiles Clinic</span>
                                            <span class="text-muted d-block">123 Smile Way, Dallas, TX
                                                75201</span>
                                            <a href="#"
                                                class="text-primary text-decoration-none mt-1 d-inline-block fw-medium">View
                                                on map</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right Details -->
                                <div class="col-md-5 border-start-md ps-md-4">
                                    <div class="mb-3 d-flex align-items-center">
                                        <div class="legend-dot rounded-circle me-3"
                                            style="width: 14px; height: 14px; background-color: #ba68c8;"></div>
                                        <div>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.7rem;">Calendar</small>
                                            <span class="fw-medium fs-7">Family Calendar</span>
                                        </div>
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Ava&background=random&color=fff"
                                            class="rounded-circle me-3 border shadow-sm" width="28" height="28">
                                        <div>
                                            <small class="text-muted d-block"
                                                style="font-size: 0.7rem;">Who</small>
                                            <span class="fw-medium fs-7">Ava</span>
                                        </div>
                                    </div>
                                    <div class="mb-3 d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Mom&background=random&color=fff"
                                            class="rounded-circle me-3 border shadow-sm" width="28" height="28">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Added
                                                by</small>
                                            <span class="fw-medium fs-7">Mom</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fa-regular fa-clock text-muted me-3 ms-1"
                                            style="font-size: 1.1rem;"></i>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">Created
                                                on</small>
                                            <span class="text-muted fs-8">May 10, 2024 at 8:45 AM</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body Area -->
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- About & Notes -->
                            <div class="col-md-4 pe-md-4">
                                <h6 class="fw-bold mb-3">About this event</h6>
                                <p class="text-dark mb-4 fs-7" style="line-height: 1.6;">Ava has a regular
                                    dental check-up.<br>Please arrive 10 minutes early.</p>

                                <h6 class="fw-bold mb-3">Notes</h6>
                                <p class="text-dark fs-7" style="line-height: 1.6;">Bring insurance card and any
                                    previous dental records.</p>
                            </div>

                            <!-- Attendees -->
                            <div class="col-md-4 border-start-md px-md-4 border-end-md">
                                <h6 class="fw-bold mb-4">Attendees</h6>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Ava&background=random&color=fff"
                                            class="rounded-circle me-3 border border-2 border-primary shadow-sm"
                                            width="40" height="40">
                                        <div>
                                            <span class="fw-bold d-block fs-7">Ava</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Primary</small>
                                        </div>
                                    </div>
                                    <span class="text-success fs-7 fw-bold"><i
                                            class="fa-solid fa-circle-check me-1"></i> Going</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Mom&background=random&color=fff"
                                            class="rounded-circle me-3 border shadow-sm" width="40" height="40">
                                        <div>
                                            <span class="fw-bold d-block fs-7">Mom</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Secondary
                                                Guardian</small>
                                        </div>
                                    </div>
                                    <span class="text-success fs-7 fw-bold"><i
                                            class="fa-solid fa-circle-check me-1"></i> Going</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Dad&background=random&color=fff"
                                            class="rounded-circle me-3 border shadow-sm" width="40" height="40">
                                        <div>
                                            <span class="fw-bold d-block fs-7">Dad</span>
                                            <small class="text-muted"
                                                style="font-size: 0.7rem;">Guardian</small>
                                        </div>
                                    </div>
                                    <span class="text-muted fs-7 fw-bold"><i
                                            class="fa-regular fa-circle-question me-1"></i> Maybe</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Grandma&background=random&color=fff"
                                            class="rounded-circle me-3 border shadow-sm" width="40" height="40">
                                        <div>
                                            <span class="fw-bold d-block fs-7">Grandma</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Family</small>
                                        </div>
                                    </div>
                                    <span class="text-success fs-7 fw-bold"><i
                                            class="fa-solid fa-circle-check me-1"></i> Going</span>
                                </div>

                                <button
                                    class="btn btn-white border w-100 rounded-3 text-primary fw-medium hover-bg-light mt-2 shadow-sm">
                                    <i class="fa-solid fa-plus me-2"></i> Add Attendee
                                </button>
                            </div>

                            <!-- Location Map -->
                            <div class="col-md-4 ps-md-4">
                                <h6 class="fw-bold mb-3">Location</h6>
                                <div class="rounded-3 overflow-hidden border mb-3 position-relative shadow-sm"
                                    style="height: 180px; background: url('https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&q=80&w=600&h=300') center/cover;">
                                    <!-- Fake map pin -->
                                    <div class="position-absolute top-50 start-50 translate-middle text-primary"
                                        style="font-size: 2rem;">
                                        <i class="fa-solid fa-location-dot drop-shadow"></i>
                                        <!-- Simple pin shadow -->
                                        <div class="bg-dark rounded-circle opacity-25 mx-auto"
                                            style="width: 14px; height: 6px; margin-top: -5px;"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="fw-bold d-block fs-7">Bright Smiles Clinic</span>
                                    <span class="text-muted fs-7">123 Smile Way, Dallas, TX 75201</span>
                                </div>
                                <button
                                    class="btn btn-white border rounded-3 text-primary fw-medium px-4 shadow-sm hover-bg-light fs-7">
                                    Get Directions <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-3">
                <!-- Weather Widget -->
                <div class="card border  rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Weather</h5>
                        <p class="text-muted mb-2">Dallas, TX</p>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h1 class="display-4 fw-bold mb-0">72°F</h1>
                            <i class="fa-solid fa-cloud-sun fa-3x text-warning"></i>
                        </div>
                        <p class="text-muted mb-4">Partly Cloudy</p>
                        <div class="d-flex gap-4 text-muted">
                            <span>H: 76°</span>
                            <span>L: 58°</span>
                        </div>
                    </div>
                </div>

                <!-- Countdown Events Widget -->
                <div class="card border rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Countdown Events (Top 3)</h5>

                        <div class="countdown-list">
                            <!-- Event 1 -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded p-2 me-3 fs-3">
                                    🚗
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Memorial Day Trip</h6>
                                    <small class="text-muted">May 27, 2024</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-0 fw-bold">12</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Days</small>
                                </div>
                            </div>

                            <!-- Event 2 -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded p-2 me-3 fs-3">
                                    🎉
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Emma's Birthday</h6>
                                    <small class="text-muted">Jun 3, 2024</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-0 fw-bold">19</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Days</small>
                                </div>
                            </div>

                            <!-- Event 3 -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded p-2 me-3 fs-3">
                                    🧳
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Summer Vacation</h6>
                                    <small class="text-muted">Jun 15, 2024</small>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-0 fw-bold">31</h4>
                                    <small class="text-muted" style="font-size: 0.7rem;">Days</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="#" class="text-primary text-decoration-none fw-medium small">View All
                                Events</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Widget -->
                <div class="mt-4">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <button
                                class="btn btn-white border rounded-3 w-100 text-start py-2 px-2 fw-medium text-dark fs-7 shadow-sm hover-bg-light d-flex align-items-center bg-white"
                                data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="fa-regular fa-calendar-plus text-primary me-2 fs-6"></i> Add Event
                            </button>
                        </div>
                        <div class="col-6">
                            <button
                                class="btn btn-white border rounded-3 w-100 text-start py-2 px-2 fw-medium text-dark fs-7 shadow-sm hover-bg-light d-flex align-items-center bg-white">
                                <i class="fa-solid fa-utensils text-warning me-2 fs-6"></i> Add Meal
                            </button>
                        </div>
                        <div class="col-6">
                            <button
                                class="btn btn-white border rounded-3 w-100 text-start py-2 px-2 fw-medium text-dark fs-7 shadow-sm hover-bg-light d-flex align-items-center bg-white">
                                <i class="fa-regular fa-square-check text-success me-2 fs-6"></i> Add Chore
                            </button>
                        </div>
                        <div class="col-6">
                            <button
                                class="btn btn-white border rounded-3 w-100 text-start py-2 px-2 fw-medium text-dark fs-7 shadow-sm hover-bg-light d-flex align-items-center bg-white">
                                <i class="fa-regular fa-note-sticky text-warning me-2 fs-6"></i> Add Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>