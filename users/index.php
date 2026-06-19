<?php
$path_prefix = "../";
$page_title = "Calendar";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>


<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4">

        <div class="row">
            <div class="col-12">
                <style>
                    .family-members-list::-webkit-scrollbar {
                        display: none;
                    }
                    .family-members-list {
                        -ms-overflow-style: none;  /* IE and Edge */
                        scrollbar-width: none;  /* Firefox */
                    }
                </style>
                <div class="d-flex flex-nowrap align-items-center gap-0 mb-4 family-members-list overflow-x-auto">
                    <!-- Populated by JS -->
                </div>
            </div>
            <!-- Main Calendar Area -->
            <div class="col-lg-9">
                <!-- Family Members Row -->


                <!-- Calendar Controls -->
                <div class="d-lg-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <button id="btn-today"
                            class="btn btn-outline-secondary border px-4 fw-medium text-dark">Today</button>
                        <button id="btn-prev" class="btn btn-link text-dark text-decoration-none p-0"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <button id="btn-next" class="btn btn-link text-dark text-decoration-none p-0"><i
                                class="fa-solid fa-chevron-right"></i></button>
                        <div class="dropdown">
                            <button id="date-picker-btn"
                                class="btn btn-white fw-bold fs-5 border-0 p-0 d-flex align-items-center"
                                type="button">
                                May 12 – May 18, 2024 <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>
                            </button>
                        </div>

                        <!-- Requests Button -->
                        <button id="btn-requests" class="btn btn-outline-primary rounded-pill px-3 fw-medium ms-lg-3" data-bs-toggle="modal" data-bs-target="#requestsModal" onclick="loadMyRequests()">
                            <i class="ri-mail-add-line me-1"></i> Requests <span id="requestsCountBadge" class="badge bg-danger rounded-pill ms-1 d-none">0</span>
                        </button>
                    </div>
                    <div class="btn-group border mt-3 mt-lg-0" role="group">
                        <button type="button" id="btn-view-day"
                            class="btn btn-white text-muted px-4 py-2 border-0 fw-medium toggle-view-btn"
                            data-target="view-day">Day</button>
                        <button type="button" id="btn-view-week"
                            class="btn btn-primary px-4 py-2 border-0 fw-medium toggle-view-btn"
                            data-target="view-week">Week</button>
                        <button type="button" id="btn-view-month"
                            class="btn btn-white text-muted px-4 py-2 border-0 fw-medium toggle-view-btn"
                            data-target="view-month">Month</button>
                    </div>
                </div>

                <!-- Calendar Views Container -->
                <div id="calendar-views-container">

                    <!-- Week View (Default) -->
                    <div id="view-week" class="calendar-grid bg-white border rounded-3 overflow-hidden ">
                        <!-- Wrapper for scrollable grid part -->
                        <div class="calendar-grid-header-meals">
                            <!-- Header Row -->
                            <div class="calendar-row header-row d-flex text-center border-bottom bg-white fw-bold">
                                <div class="time-col border-end p-3 text-muted">Time / Day</div>
                                <div class="day-col flex-fill border-end p-3">Sun<br><span class="fw-normal">May
                                        12</span></div>
                                <div class="day-col flex-fill border-end p-3">Mon<br><span class="fw-normal">May
                                        13</span></div>
                                <div class="day-col flex-fill border-end p-3">Tue<br><span class="fw-normal">May
                                        14</span></div>
                                <div class="day-col flex-fill border-end p-3 text-primary"><span
                                        class="fw-bold">Wed</span><br><span
                                        class="d-inline-block bg-primary text-white rounded-circle mt-1 fw-bold"
                                        style="width: 30px; height: 30px; line-height: 30px;">15</span></div>
                                <div class="day-col flex-fill border-end p-3">Thu<br><span class="fw-normal">May
                                        16</span></div>
                                <div class="day-col flex-fill border-end p-3">Fri<br><span class="fw-normal">May
                                        17</span></div>
                                <div class="day-col flex-fill p-3">Sat<br><span class="fw-normal">May 18</span>
                                </div>
                            </div>

                            <!-- Meals Rows (Breakfast, Lunch, Dinner) -->
                            <div id="meals-container">
                                <!-- Populated by JS -->
                            </div>

                            <!-- All Day Events Row -->
                            <div class="calendar-row all-day-row d-flex border-bottom bg-light">
                                <div
                                    class="time-col border-end p-2 d-flex align-items-center fw-bold text-dark fs-7">
                                    <i class="fa-regular fa-calendar-check text-primary me-2"></i> All Day Events
                                </div>
                                <div class="events-col flex-fill p-2 d-flex gap-2" id="all-day-events-container">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Scheduled Events Row (List View) -->
                        <div class="calendar-row events-list-row bg-white p-3" id="week-events-list-container">
                            <!-- Populated by JS (Vertical List) -->
                        </div>
                    </div>

                    <!-- Day View -->
                    <div id="view-day" class="d-none bg-white border rounded-3 overflow-hidden">
                        <!-- Day View Content will be populated by JS -->
                        <div id="day-view-container" class="day-list-view">
                            <!-- Meals, All Day, and Events Rows go here -->
                        </div>
                    </div>

                    <!-- Month View (FullCalendar) -->
                    <div id="view-month" class="d-none bg-white border rounded-3 p-3">
                        <div id="full-calendar-container"></div>
                    </div>

                </div> <!-- End Calendar Views Container -->
                
                <!-- Mobile Event Details Container -->
                <div id="mobileEventDetailsContainer" class="d-lg-none mt-3 d-none">
                    <div class="card border rounded-4 shadow-sm p-3 position-relative bg-white">
                        <div class="d-flex align-items-center">
                            <!-- Icon -->
                            <div class="me-3 text-primary d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded p-2" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-calendar-days fs-5 text-primary"></i>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow-1 min-w-0" style="overflow: hidden;">
                                <h6 class="fw-bold mb-1 text-truncate text-dark" id="mobileEventTitle" style="font-size: 0.95rem;">Event Title</h6>
                                <div class="text-truncate text-muted" id="mobileEventSubtitle" style="font-size: 0.75rem;">
                                    Jun 2 - Jun 3 &middot; Location
                                </div>
                            </div>
                            
                            <!-- Actions Dropdown -->
                            <div class="dropdown ms-2" id="mobileEventEditContainer" style="display: none !important;">
                                <button class="btn btn-link text-muted p-1 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    <li><a class="dropdown-item d-flex align-items-center text-dark" href="#" id="mobileBtnEditEvent"><i class="fa-solid fa-pen me-2 text-primary"></i> Edit</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center text-danger" href="#" id="mobileBtnDeleteEvent"><i class="fa-solid fa-trash-can me-2"></i> Delete</a></li>
                                </ul>
                            </div>

                            <!-- Close Button (Always visible) -->
                            <button type="button" class="btn-close ms-2 mb-4" aria-label="Close" onclick="document.getElementById('mobileEventDetailsContainer').classList.add('d-none');" style="font-size: 0.6rem; opacity: 0.5;"></button>
                        </div>
                    </div>
                </div>

                <!-- Add Event Button -->
                <div class="mt-4 d-flex justify-content-end">
                    <button type="button"
                        class="btn btn-primary rounded-3 event-btn ps-4 pe-2 py-2 fw-medium d-flex align-items-center"
                        data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="fa-solid fa-plus me-2"></i> Add Event
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                fill="rgba(255,255,255,1)">
                                <path
                                    d="M11.9999 13.1714L16.9497 8.22168L18.3639 9.63589L11.9999 15.9999L5.63599 9.63589L7.0502 8.22168L11.9999 13.1714Z">
                                </path>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- Legend -->
                <div
                    class="mt-4 border rounded-3 p-2 d-inline-flex align-items-center gap-3 bg-white flex-wrap legend-container px-4">
                    <!-- Populated by JS -->
                </div>

            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-3">
                <!-- Weather Widget -->
                <div class="card weather-card rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Weather</h5>
                        <p class="text-muted mb-2">Dallas, TX</p>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h1 class="display-6 mt-3 mb-0">72°F</h1>
                            <figure class="weather-img">
                                <img src="<?php echo $path_prefix; ?>public/img/1163661.png" alt="waether" />
                            </figure>
                        </div>
                        <p class="text-muted mb-4">Partly Cloudy</p>
                        <div class="d-flex gap-4 text-muted">
                            <span>H: 76°</span>
                            <span>L: 58°</span>
                        </div>
                    </div>
                </div>

                <!-- Countdown Events Widget -->
                <div class="card border coundownd-card rounded-3">
                    <div class="card-body p-4">
                        <h6 class="fw-bold ts-titels mb-4">Countdown Events (Top 3)</h6>

                        <div class="countdown-list">
                            <!-- Event 1 -->
                            <div class="d-flex align-items-center mb-4">
                                <div class=" me-2 fs-3">
                                    <img src="<?php echo $path_prefix; ?>public/img/wishlist012.png" alt="wishlist012" />
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
                                <div class=" me-2 fs-3">
                                    <img src="<?php echo $path_prefix; ?>public/img/wishlist02.png" alt="wishlist012" />
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
                                <div class=" me-2 fs-3">
                                    <img src="<?php echo $path_prefix; ?>public/img/gifts.png" alt="wishlist012" />

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
                        <hr />

                        <div class="text-center mt-4">
                            <a href="#" class="text-primary text-decoration-none fw-medium small">View All
                                Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <form action="../helpers/addEvent.php" method="POST" id="addEventForm">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold" id="addEventModalLabel">Add Event</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Type Tabs -->
                    <div class="d-flex border rounded-3 mb-4 overflow-hidden shadow-sm">
                        <div
                            class="flex-fill text-center py-3 border-end bg-primary bg-opacity-10 text-primary fw-bold cursor-pointer border-primary border-bottom border-2">
                            <i class="fa-regular fa-calendar me-2"></i> Event
                        </div>
                        <div
                            class="flex-fill text-center py-3 border-end text-muted fw-medium cursor-pointer hover-bg-light">
                            <i class="fa-solid fa-utensils text-warning me-2"></i> Meal
                        </div>
                        <div
                            class="flex-fill text-center py-3 border-end text-muted fw-medium cursor-pointer hover-bg-light">
                            <i class="fa-regular fa-square-check text-success me-2"></i> Chore
                        </div>
                        <div class="flex-fill text-center py-3 text-muted fw-medium cursor-pointer hover-bg-light"><i
                                class="fa-solid fa-ellipsis me-2" style="color: #ba68c8;"></i> Other</div>
                    </div>

                    <!-- Form Grid -->
                    <input type="hidden" name="member_id" id="selectedMemberId">
                    <div class="row g-4 mb-4">
                        <!-- Title -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark fs-7">Title <span
                                    class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <input type="text" class="form-control border-0 px-3 py-2 text-muted"
                                    id="eventTitle" name="title" placeholder="Enter event title" required>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Date <span
                                    class="text-danger">*</span></label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="eventDate" name="date" required>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6" id="endDateContainer">
                            <label class="form-label fw-semibold text-dark fs-7">End Date</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm" id="endDateInputGroup">
                                <span class="input-group-text bg-transparent border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 bg-transparent text-dark fw-medium px-0"
                                    id="eventEndDate" name="end_date" disabled>
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Start Time <span
                                    class="text-danger">*</span></label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-clock"></i></span>
                                <input type="time" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="eventStartTime" name="start_time" value="10:00">
                            </div>
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">End Time</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white mb-2 shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-clock"></i></span>
                                <input type="time" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="eventEndTime" name="end_time" value="11:00">
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="eventAllDay" name="is_all_day" value="1">
                                <label class="form-check-label text-muted fs-7 fw-medium" for="eventAllDay">
                                    All day event
                                </label>
                            </div>
                        </div>

                        <!-- Add to Calendar (Event Type) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Event Type</label>
                            <select class="form-select border rounded-3 shadow-sm" id="eventType" name="type_id">
                                <!-- Populated by JS -->
                            </select>
                        </div>

                        <!-- Who is this for? -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Who is this for?</label>
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-1" id="modal-members-container">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Location</label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-location-dot"></i></span>
                                <input type="text" class="form-control border-0 px-1 py-2 text-muted"
                                    id="eventLocation" name="location" placeholder="Enter location">
                            </div>
                        </div>

                        <!-- Repeat -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Repeat</label>
                            <select class="form-select border rounded-3 shadow-sm" id="eventRepeat" name="event_repeat">
                                <option value="">Does not repeat</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark fs-7">Notes</label>
                            <div class="position-relative shadow-sm rounded-3">
                                <textarea class="form-control border rounded-3 p-3 text-muted fs-7"
                                    id="eventNotes" name="description" style="background-color: #fafafa;" rows="3"
                                    placeholder="Add notes, agenda or any important details..."></textarea>
                            </div>
                        </div>

                        <!-- Reminder -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Reminder</label>
                            <select class="form-select border rounded-3 shadow-sm mb-3" id="eventReminder" name="remainder">
                                <option value="">None</option>
                                <option value="5">5 minutes before</option>
                                <option value="15" selected>15 minutes before</option>
                                <option value="30">30 minutes before</option>
                                <option value="60">1 hour before</option>
                                <option value="1440">1 day before</option>
                            </select>
                            <div class="form-check mt-3">
                                <input class="form-check-input bg-primary border-primary" type="checkbox" value=""
                                    id="notificationCheck" checked>
                                <label class="form-check-label text-dark fs-7 fw-medium" for="notificationCheck">
                                    Send notification to all selected
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between px-4 pb-4">
                    <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" id="saveEventBtn">Save
                        Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <form action="../helpers/editEvent.php" method="POST" id="editEventForm">
                <input type="hidden" name="event_id" id="editEventId">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold" id="editEventModalLabel">Edit Event</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Type Tabs -->
                    <div class="d-flex border rounded-3 mb-4 overflow-hidden shadow-sm">
                        <div
                            class="flex-fill text-center py-3 border-end bg-primary bg-opacity-10 text-primary fw-bold cursor-pointer border-primary border-bottom border-2">
                            <i class="fa-regular fa-calendar me-2"></i> Event
                        </div>
                    </div>

                    <!-- Form Grid -->
                    <input type="hidden" name="member_id" id="editSelectedMemberId">
                    <div class="row g-4 mb-4">
                        <!-- Title -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark fs-7">Title <span
                                    class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <input type="text" class="form-control border-0 px-3 py-2 text-muted"
                                    id="editEventTitle" name="title" placeholder="Enter event title" required>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Date <span
                                    class="text-danger">*</span></label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="editEventDate" name="date" required>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6" id="editEndDateContainer">
                            <label class="form-label fw-semibold text-dark fs-7">End Date</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm" id="editEndDateInputGroup">
                                <span class="input-group-text bg-transparent border-0 text-muted"><i
                                        class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 bg-transparent text-dark fw-medium px-0"
                                    id="editEventEndDate" name="end_date">
                            </div>
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Start Time <span
                                    class="text-danger">*</span></label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-clock"></i></span>
                                <input type="time" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="editEventStartTime" name="start_time" value="10:00">
                            </div>
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">End Time</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white mb-2 shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-clock"></i></span>
                                <input type="time" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    id="editEventEndTime" name="end_time" value="11:00">
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="editEventAllDay" name="is_all_day" value="1">
                                <label class="form-check-label text-muted fs-7 fw-medium" for="editEventAllDay">
                                    All day event
                                </label>
                            </div>
                        </div>

                        <!-- Add to Calendar (Event Type) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Event Type</label>
                            <select class="form-select border rounded-3 shadow-sm" id="editEventType" name="type_id">
                                <!-- Populated by JS -->
                            </select>
                        </div>

                        <!-- Who is this for? -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Who is this for?</label>
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-1" id="edit-modal-members-container">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Location</label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-location-dot"></i></span>
                                <input type="text" class="form-control border-0 px-1 py-2 text-muted"
                                    id="editEventLocation" name="location" placeholder="Enter location">
                            </div>
                        </div>

                        <!-- Repeat -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Repeat</label>
                            <select class="form-select border rounded-3 shadow-sm" id="editEventRepeat" name="event_repeat">
                                <option value="">Does not repeat</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark fs-7">Notes</label>
                            <div class="position-relative shadow-sm rounded-3">
                                <textarea class="form-control border rounded-3 p-3 text-muted fs-7"
                                    id="editEventNotes" name="description" style="background-color: #fafafa;" rows="3"
                                    placeholder="Add notes, agenda or any important details..."></textarea>
                            </div>
                        </div>

                        <!-- Reminder -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Reminder</label>
                            <select class="form-select border rounded-3 shadow-sm mb-3" id="editEventReminder" name="remainder">
                                <option value="">None</option>
                                <option value="5">5 minutes before</option>
                                <option value="15" selected>15 minutes before</option>
                                <option value="30">30 minutes before</option>
                                <option value="60">1 hour before</option>
                                <option value="1440">1 day before</option>
                            </select>
                            <div class="form-check mt-3">
                                <input class="form-check-input bg-primary border-primary" type="checkbox" value=""
                                    id="editNotificationCheck" checked>
                                <label class="form-check-label text-dark fs-7 fw-medium" for="editNotificationCheck">
                                    Send notification to all selected
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between px-4 pb-4">
                    <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" id="updateEventBtn">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Event Modal -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-labelledby="viewEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="viewEventTitle">Event Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-regular fa-calendar text-muted me-3 fs-5" style="width: 20px;"></i>
                    <span id="viewEventDate" class="fw-medium text-dark"></span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-regular fa-clock text-muted me-3 fs-5" style="width: 20px;"></i>
                    <span id="viewEventTime" class="fw-medium text-dark"></span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fa-solid fa-location-dot text-muted me-3 fs-5" style="width: 20px;"></i>
                    <span id="viewEventLocation" class="fw-medium text-dark"></span>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-solid fa-user text-muted me-3 fs-5" style="width: 20px;"></i>
                    <span id="viewEventMember" class="fw-medium text-dark"></span>
                </div>

                <div class="d-flex justify-content-end gap-2" id="viewEventEditContainer" style="display: none !important;">
                    <a href="#" id="btnEditEvent" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <a href="#" id="btnDeleteEvent" class="btn btn-danger px-3 py-2 rounded-3 shadow-sm" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requests Modal -->
<div class="modal fade" id="requestsModal" tabindex="-1" aria-labelledby="requestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="requestsModalLabel">Family Invites & Memberships</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="requests-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>

<script>
    // Load my requests
    async function loadMyRequests() {
        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=getMyRequests`);
            const result = await response.json();
            const container = document.getElementById('requests-container');
            const badge = document.getElementById('requestsCountBadge');

            if (result.status === 'success') {
                const visibleRequests = result.data.filter(r => r.status === 'pending' || r.status === 'approved');
                const pendingCount = visibleRequests.filter(r => r.status === 'pending').length;

                if (pendingCount > 0) {
                    badge.textContent = pendingCount;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }

                if (visibleRequests.length > 0) {
                    container.innerHTML = visibleRequests.map(req => {
                        let buttons = '';
                        if (req.status === 'pending') {
                            buttons = `
                            <button class="btn btn-sm btn-success fw-medium px-3" onclick="handleRequest(${req.id}, 'approved')">Accept</button>
                            <button class="btn btn-sm btn-outline-danger fw-medium px-3" onclick="handleRequest(${req.id}, 'rejected')">Reject</button>
                        `;
                        } else if (req.status === 'approved') {
                            buttons = `
                            <button class="btn btn-sm btn-danger fw-medium px-3" onclick="delinkRequest(${req.id})">De-link</button>
                        `;
                        }

                        return `
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded-3 mb-2 bg-white shadow-sm">
                            <div>
                                <h6 class="mb-1 fw-bold">${req.requester_name || 'A user'} wants to join ${req.family_name}</h6>
                                <small class="text-muted">${req.email} &middot; <span class="${req.status === 'pending' ? 'text-warning' : 'text-success'} text-capitalize">${req.status}</span></small>
                            </div>
                            <div class="d-flex gap-2">
                                ${buttons}
                            </div>
                        </div>
                    `;
                    }).join('');
                } else {
                    container.innerHTML = '<p class="text-muted text-center py-4">No incoming requests.</p>';
                }
            }
        } catch (e) {
            console.error(e);
            document.getElementById('requests-container').innerHTML = '<p class="text-danger text-center py-4">Failed to load requests.</p>';
        }
    }

    async function handleRequest(id, status) {
        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=updateStatus`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    status
                })
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert(`Request ${status} successfully.`, 'success');
                loadMyRequests();
                if (status === 'approved') {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                showAlert(result.message, 'error');
            }
        } catch (e) {
            console.error(e);
            showAlert('Network error', 'error');
        }
    }

    async function delinkRequest(id) {
        if (!confirm('Are you sure you want to de-link this user from the family?')) return;
        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=delink`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id
                })
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert(result.message, 'success');
                loadMyRequests();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (e) {
            console.error(e);
            showAlert('Network error', 'error');
        }
    }

    // Initial badge update
    document.addEventListener('DOMContentLoaded', () => {
        loadMyRequests();
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>