<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../classes/Family.php';
// require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/ThemeReward.php';

$path_prefix = "../";
$page_title = "Chores";

$familyId = $_SESSION['user']['active_family_id'] ?? null;
$familyMembers = $familyId ? Family::getMembersByFamilyId($familyId) : [];

$rewardsGrouped = [];
if ($familyId) {
    $res = json_decode(ThemeReward::getByFamily($familyId), true);
    if ($res && $res['status'] === 'success') {
        $rewardsGrouped = $res['data'];
    }
}

include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4" style="max-width: 1400px; margin: 0 auto;">

        <div class="row g-4">
            <div class="col-12">
                <style>
                    .family-members-list::-webkit-scrollbar {
                        display: none;
                    }

                    .family-members-list {
                        -ms-overflow-style: none;
                        /* IE and Edge */
                        scrollbar-width: none;
                        /* Firefox */
                    }
                </style>
                <!-- Family Members Row -->
                <div class="d-flex flex-nowrap align-items-center gap-0 mb-4 family-members-list overflow-x-auto">
                    <!-- Populated by JS -->
                </div>
            </div>
            <!-- Main Chores Area -->
            <div class="col-lg-9">


                <!-- Tabs and Filters -->
                <div class="d-lg-flex justify-content-between align-items-end mb-4 border-bottom">
                    <ul class="nav nav-tabs border-0" id="choreTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link active border-0 border-bottom border-primary border-3 bg-transparent fw-bold text-primary px-4 pb-3"
                                id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart" type="button"
                                role="tab">Chore Chart</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link border-0 bg-transparent fw-medium text-muted px-4 pb-3"
                                id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button"
                                role="tab">Chore List</button>
                        </li>
                    </ul>
                </div>

                <!-- Chore Controls -->
                <div class="d-lg-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="btn-group border rounded-3 overflow-hidden shadow-sm">
                            <button class="btn btn-white border-0 py-2 px-3" id="btn-prev"><i
                                    class="fa-solid fa-chevron-left text-muted"></i></button>
                            <button class="btn btn-white border-0 py-2 px-3 fw-bold border-start border-end"
                                id="date-picker-btn"><i class="fa-regular fa-calendar me-2"></i> May 12 – May
                                18, 2024</button>
                            <button class="btn btn-white border-0 py-2 px-3" id="btn-next"><i
                                    class="fa-solid fa-chevron-right text-muted"></i></button>
                        </div>
                        <button class="btn btn-white border rounded-3 py-2 px-4 fw-medium shadow-sm"
                            id="btn-today">Today</button>
                    </div>
                    <div class="d-flex gap-3 mt-3 mt-lg-0">
                        <button
                            class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addChoreModal">
                            <i class="fa-solid fa-plus me-2"></i> Add Chore
                        </button>
                        <button
                            class="btn btn-white border rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center">
                            <i class="fa-solid fa-gear me-2"></i> Chore Settings
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="choreTabsContent">
                    <!-- Chore Chart Tab -->
                    <div class="tab-pane fade show active" id="chart" role="tabpanel">
                        <div class="bg-white border rounded-3 overflow-hidden chores-tablesd">
                            <table class="table mb-0 chore-table" id="dynamicChoreTable">
                                <thead>
                                    <tr class="text-center align-middle" id="choreTableHeader">
                                        <th class="text-start ps-4" style="width: 250px;">Chore</th>
                                        <!-- Generated by JS -->
                                        <th style="width: 100px;">Points</th>
                                    </tr>
                                </thead>
                                <tbody id="choreTableBody">
                                    <!-- Generated by JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Motivational Banner -->
                        <div class="mt-4 p-4 rounded-4 d-flex align-items-center shadow-sm"
                            style="background-color: #f0f7ff;">
                            <div class="bg-white rounded p-2 me-3 fs-5 shadow-sm">🏆</div>
                            <div class="flex-grow-1">
                                <span class="fw-bold">Great job! Keep it up!</span> You're earning points for
                                your family.
                            </div>
                            <div>
                                <a href="#" class="text-primary text-decoration-none fw-medium small">Learn more
                                    about rewards <i class="fa-solid fa-circle-info ms-1 text-muted"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Chore List Tab (Placeholder) -->
                    <div class="tab-pane fade" id="list" role="tabpanel">
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm">
                            <i class="fa-solid fa-list-check fa-4x mb-4 opacity-25"></i>
                            <h3>Chore List View</h3>
                            <p>This view will show chores as a simple list with due dates and priorities.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-3">
                <!-- Weekly Progress Widget -->
                <div class="card border rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Weekly Progress</h5>

                        <div class="d-flex align-items-center gap-4 mb-2">
                            <div class="progress-ring-container">
                                <svg class="progress-ring" width="100" height="100">
                                    <circle class="progress-ring__circle" stroke="#f0f0f0" stroke-width="8"
                                        fill="transparent" r="42" cx="50" cy="50" />
                                    <circle class="progress-ring__circle" stroke="#43a047" stroke-width="8"
                                        stroke-dasharray="263.89" stroke-dashoffset="58.06" fill="transparent"
                                        r="42" cx="50" cy="50" />
                                </svg>
                                <div class="progress-text">78%</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Great job, family!</div>
                                <div class="text-muted small">You're almost there.</div>
                                <div class="mt-1 fw-bold text-success">125 <span class="text-muted fw-normal">/
                                        160 points</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Points Leaderboard Widget -->
                <div class="card border rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Points Leaderboard</h5>

                        <div class="leaderboard-list">
                            <div class="leaderboard-item">
                                <span class="rank-number">1</span>
                                <img src="https://ui-avatars.com/api/?name=Emma&background=random&color=fff"
                                    alt="Emma">
                                <span class="name">Emma</span>
                                <span class="points text-dark">38</span>
                                <i class="fa-solid fa-star star-icon"></i>
                            </div>
                            <div class="leaderboard-item">
                                <span class="rank-number">2</span>
                                <img src="https://ui-avatars.com/api/?name=Liam&background=random&color=fff"
                                    alt="Liam">
                                <span class="name">Liam</span>
                                <span class="points text-dark">32</span>
                                <i class="fa-solid fa-star star-icon"></i>
                            </div>
                            <div class="leaderboard-item">
                                <span class="rank-number">3</span>
                                <img src="https://ui-avatars.com/api/?name=Dad&background=random&color=fff"
                                    alt="Dad">
                                <span class="name">Dad</span>
                                <span class="points text-dark">28</span>
                                <i class="fa-solid fa-star star-icon"></i>
                            </div>
                            <div class="leaderboard-item">
                                <span class="rank-number">4</span>
                                <img src="https://ui-avatars.com/api/?name=Ava&background=random&color=fff"
                                    alt="Ava">
                                <span class="name">Ava</span>
                                <span class="points text-dark">17</span>
                                <i class="fa-solid fa-star star-icon"></i>
                            </div>
                            <div class="leaderboard-item border-0">
                                <span class="rank-number">5</span>
                                <img src="https://ui-avatars.com/api/?name=Mom&background=random&color=fff"
                                    alt="Mom">
                                <span class="name">Mom</span>
                                <span class="points text-dark">10</span>
                                <i class="fa-solid fa-star star-icon"></i>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="#" class="text-primary text-decoration-none fw-medium small">View All
                                Rewards</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Widget -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Quick Actions</h5>

                        <a href="#" class="quick-action-item">
                            <i class="fa-solid fa-user-plus bg-primary bg-opacity-10 text-primary"></i>
                            <span class="action-name">Assign Chores</span>
                            <i class="fa-solid fa-chevron-right chevron"></i>
                        </a>

                        <a href="#" class="quick-action-item">
                            <i class="fa-solid fa-plus bg-info bg-opacity-10 text-info"
                                style="background-color: rgba(3, 169, 244, 0.1) !important; color: #03a9f4 !important;"></i>
                            <span class="action-name">Add Chore</span>
                            <i class="fa-solid fa-chevron-right chevron"></i>
                        </a>

                        <a href="#" class="quick-action-item border-0 mb-0">
                            <i class="fa-solid fa-gear bg-secondary bg-opacity-10 text-secondary"></i>
                            <span class="action-name">Chore Settings</span>
                            <i class="fa-solid fa-chevron-right chevron"></i>
                        </a>
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
                <form>
                    <div class="row g-4 mb-4">
                        <!-- Title -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Title <span
                                    class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <input type="text" class="form-control border-0 px-3 py-2 text-muted"
                                    placeholder="Enter event title">
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
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="May 15, 2024" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
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
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="10:00 AM" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
                            </div>
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">End Time</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white mb-2 shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-clock"></i></span>
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="11:00 AM" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="" id="allDayCheck">
                                <label class="form-check-label text-muted fs-7 fw-medium" for="allDayCheck">
                                    All day event
                                </label>
                            </div>
                        </div>

                        <!-- Add to Calendar -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Add to Calendar</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <div class="legend-dot rounded-circle"
                                        style="width: 12px; height: 12px; background-color: #ba68c8;"></div>
                                </span>
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="Family Calendar" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
                            </div>
                        </div>

                        <!-- Who is this for? -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Who is this for?</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <!-- Dad -->
                                <div class="text-center position-relative cursor-pointer avatar-selector selected opacity-100"
                                    data-member="dad">
                                    <img src="https://ui-avatars.com/api/?name=Dad&background=random&color=fff"
                                        class="rounded-circle border border-primary border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark fw-medium avatar-name"
                                        style="font-size: 0.7rem;">Dad</div>
                                </div>
                                <!-- Mom -->
                                <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                                    data-member="mom">
                                    <img src="https://ui-avatars.com/api/?name=Mom&background=random&color=fff"
                                        class="rounded-circle border border-transparent border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon d-none"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.7rem;">Mom
                                    </div>
                                </div>
                                <!-- Emma -->
                                <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                                    data-member="emma">
                                    <img src="https://ui-avatars.com/api/?name=Emma&background=random&color=fff"
                                        class="rounded-circle border border-transparent border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon d-none"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.7rem;">Emma
                                    </div>
                                </div>
                                <!-- Liam -->
                                <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                                    data-member="liam">
                                    <img src="https://ui-avatars.com/api/?name=Liam&background=random&color=fff"
                                        class="rounded-circle border border-transparent border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon d-none"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.7rem;">Liam
                                    </div>
                                </div>
                                <!-- Ava -->
                                <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                                    data-member="ava">
                                    <img src="https://ui-avatars.com/api/?name=Ava&background=random&color=fff"
                                        class="rounded-circle border border-transparent border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon d-none"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.7rem;">Ava
                                    </div>
                                </div>
                                <!-- Grandma -->
                                <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                                    data-member="grandma">
                                    <img src="https://ui-avatars.com/api/?name=Grandma&background=random&color=fff"
                                        class="rounded-circle border border-transparent border-2 p-1 avatar-img"
                                        width="44" height="44">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon d-none"
                                        style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.7rem;">Grandma
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Location</label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-location-dot"></i></span>
                                <input type="text" class="form-control border-0 px-1 py-2 text-muted"
                                    placeholder="Enter location">
                            </div>
                            <div class="mt-2">
                                <a href="#" class="text-primary text-decoration-none fw-medium"
                                    style="font-size: 0.8rem;"><i class="fa-solid fa-video me-1"></i> Add video call
                                    link</a>
                            </div>
                        </div>

                        <!-- Repeat -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Repeat</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-rotate-right"></i></span>
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="Does not repeat" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Notes</label>
                            <div class="position-relative shadow-sm rounded-3">
                                <textarea class="form-control border rounded-3 p-3 text-muted fs-7"
                                    style="background-color: #fafafa;" rows="3"
                                    placeholder="Add notes, agenda or any important details..."></textarea>
                                <span class="position-absolute bottom-0 end-0 p-2 text-muted fw-medium"
                                    style="font-size: 0.7rem;">0 / 500</span>
                            </div>
                        </div>

                        <!-- Reminder -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark fs-7">Reminder</label>
                            <div
                                class="input-group border rounded-3 overflow-hidden cursor-pointer bg-white mb-3 shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-regular fa-bell"></i></span>
                                <input type="text" class="form-control border-0 bg-white text-dark fw-medium px-0"
                                    value="15 minutes before" readonly>
                                <span class="input-group-text bg-white border-0 text-muted"><i
                                        class="fa-solid fa-chevron-down fs-7"></i></span>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input bg-primary border-primary" type="checkbox" value=""
                                    id="notificationCheck" checked>
                                <label class="form-check-label text-dark fs-7 fw-medium" for="notificationCheck">
                                    Send notification to all selected
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-between px-4 pb-4">
                <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm">Save
                    Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Chore Modal -->
<div class="modal fade" id="addChoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold">Add Chore</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addChoreForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-7">Chore Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Set the Table" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark fs-7">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark fs-7">Recurrence</label>
                            <select name="recurrence" class="form-select rounded-3">
                                <option value="once">Once</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark fs-7">Repeat Until</label>
                            <input type="date" name="repeat_until" class="form-control rounded-3" value="">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-7">Assigned To</label>
                        <input type="hidden" name="assigned_to" id="choreAssignedTo" value="<?= !empty($familyMembers) ? $familyMembers[0]['id'] : '' ?>" required>
                        <div class="d-flex flex-wrap align-items-center gap-3 mt-1" id="add-chore-members-container">
                            <?php foreach ($familyMembers as $index => $member):
                                $avatarUrl = $member['image'] ? $member['image'] : 'https://ui-avatars.com/api/?name=' . urlencode($member['nickname'] ?? $member['name']) . '&background=random&color=fff';
                                $isSelected = ($index === 0);
                                $borderClass = $isSelected ? 'border-primary' : 'border-transparent';
                                $checkIconClass = $isSelected ? '' : 'd-none';
                                $nameWeight = $isSelected ? 'fw-medium' : '';
                            ?>
                                <div class="text-center position-relative cursor-pointer avatar-selector <?= $isSelected ? 'selected opacity-100' : 'opacity-75 hover-opacity-100' ?>"
                                    data-member-id="<?= $member['id'] ?>"
                                    onclick="selectChoreMember(this, <?= $member['id'] ?>)">

                                    <img src="<?= $avatarUrl ?>" class="rounded-circle border <?= $borderClass ?> border-2 p-1 avatar-img" width="44" height="44" alt="<?= htmlspecialchars($member['nickname'] ?? $member['name']) ?>">
                                    <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon <?= $checkIconClass ?>" style="top: -2px; right: -2px; font-size: 14px;"></i>
                                    <div class="fs-8 mt-1 text-dark avatar-name <?= $nameWeight ?>" style="font-size: 0.7rem;"><?= htmlspecialchars(empty($member['nickname']) || is_null($member['nickname']) || trim($member['nickname']) == '' ? $member['name'] : $member['nickname']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-7">Reward Points</label>
                        <select name="reward_id" class="form-select rounded-3" required>
                            <option value="">Select Reward Level</option>
                            <?php foreach ($rewardsGrouped as $group): ?>
                                <optgroup label="<?= htmlspecialchars($group['name']) ?>">
                                    <?php foreach ($group['levels'] as $level): ?>
                                        <option value="<?= $level['id'] ?>"><?= htmlspecialchars($level['level']) ?> (<?= $level['points'] ?> pts)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-between px-4 pb-4">
                <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" onclick="submitChore()">Save Chore</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Chore Action Modal -->
<div class="modal fade" id="choreActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="choreActionTitle">Chore Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="choreActionButtons">
                <!-- Action buttons will be injected here via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    const CURRENT_USER_ID = <?= json_encode($_SESSION['user']['id']) ?>;
    const CURRENT_USER_ROLE = <?= json_encode($_SESSION['user']['role'] ?? 'member') ?>;
    const TODAY_STR = '<?= date('Y-m-d') ?>';

    function submitChore() {
        let form = document.getElementById('addChoreForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        let formData = new FormData(form);
        formData.append('action', 'addChore');

        fetch('../api/chore.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    var modalEl = document.getElementById('addChoreModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    form.reset();
                    if (typeof window.renderTable === 'function') {
                        window.renderTable();
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.error || data.msg || 'Error adding chore');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred');
            });
    }

    function selectChoreMember(element, memberId) {
        document.querySelectorAll('#add-chore-members-container .avatar-selector').forEach(a => {
            a.classList.remove('selected', 'opacity-100');
            a.classList.add('opacity-75');
            a.querySelector('.avatar-img').classList.remove('border-primary');
            a.querySelector('.avatar-img').classList.add('border-transparent');
            a.querySelector('.check-icon').classList.add('d-none');
            a.querySelector('.avatar-name').classList.remove('fw-medium');
        });

        element.classList.remove('opacity-75');
        element.classList.add('selected', 'opacity-100');
        element.querySelector('.avatar-img').classList.remove('border-transparent');
        element.querySelector('.avatar-img').classList.add('border-primary');
        element.querySelector('.check-icon').classList.remove('d-none');
        element.querySelector('.avatar-name').classList.add('fw-medium');

        document.getElementById('choreAssignedTo').value = memberId;
    }

    function openChoreActionModal(instanceId, status, assignedToId, choreTitle, dateStr) {
        // Format date string nicely (e.g. YYYY-MM-DD -> Month DD, YYYY)
        let d = new Date(dateStr + 'T00:00:00');
        let options = {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        };
        let formattedDate = d.toLocaleDateString('en-US', options);

        document.getElementById('choreActionTitle').innerHTML = choreTitle;

        let buttonsHtml = `<div class="mb-3 text-muted small"><i class="fa-regular fa-calendar me-1"></i> ${formattedDate}</div>`;
        buttonsHtml += `<div class="d-flex gap-2 justify-content-center">`;

        if (CURRENT_USER_ROLE === 'family-head') {
            if (status === 'pending' || status === 'requested') {
                buttonsHtml += `<button class="btn btn-success flex-fill" onclick="handleChoreAction('approveChore', ${instanceId})">Mark Complete</button>`;
            }
            if (status !== 'skipped' && status !== 'complete') {
                buttonsHtml += `<button class="btn btn-outline-secondary flex-fill" onclick="handleChoreAction('skipChore', ${instanceId})">Skip</button>`;
            }
        } else {
            // Normal member
            if (status === 'pending') {
                buttonsHtml += `<button class="btn btn-primary flex-fill" onclick="handleChoreAction('requestComplete', ${instanceId})">Request Complete</button>`;
                buttonsHtml += `<button class="btn btn-outline-secondary flex-fill" onclick="handleChoreAction('skipChore', ${instanceId})">Skip</button>`;
            } else if (status === 'requested') {
                buttonsHtml += `<div class="alert alert-warning mb-0 fs-7 w-100">Awaiting approval from Family Head</div>`;
            }
        }

        buttonsHtml += `</div>`;

        if (buttonsHtml.trim() === '') return;

        document.getElementById('choreActionButtons').innerHTML = buttonsHtml;
        var myModal = new bootstrap.Modal(document.getElementById('choreActionModal'));
        myModal.show();
    }

    function handleChoreAction(action, instanceId) {
        let formData = new FormData();
        formData.append('action', action);
        formData.append('instance_id', instanceId);

        fetch('../api/chore.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    var modalEl = document.getElementById('choreActionModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    if (typeof window.renderTable === 'function') {
                        window.renderTable();
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.error || data.msg || 'Error processing chore action');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred');
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        let currentStartDate = new Date();
        // adjust to Monday
        let day = currentStartDate.getDay();
        let diff = currentStartDate.getDate() - day + (day == 0 ? -6 : 1);
        currentStartDate.setDate(diff);

        function formatDateYMD(date) {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        function renderTable() {
            let endDate = new Date(currentStartDate);
            endDate.setDate(endDate.getDate() + 6);

            let startStr = formatDateYMD(currentStartDate);
            let endStr = formatDateYMD(endDate);

            // Update header dates
            let headerHtml = '<th class="text-start ps-4" style="width: 250px;">Chore</th>';
            let tempDate = new Date(currentStartDate);
            let daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            let datesMap = [];
            for (let i = 0; i < 7; i++) {
                headerHtml += `<th>${daysOfWeek[tempDate.getDay()]}<br><span class="text-muted fw-normal fs-8">${shortMonths[tempDate.getMonth()]} ${tempDate.getDate()}</span></th>`;
                datesMap.push(formatDateYMD(tempDate));
                tempDate.setDate(tempDate.getDate() + 1);
            }
            headerHtml += '<th style="width: 100px;">Points</th>';
            document.getElementById('choreTableHeader').innerHTML = headerHtml;

            document.getElementById('date-picker-btn').innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${shortMonths[currentStartDate.getMonth()]} ${currentStartDate.getDate()} – ${shortMonths[endDate.getMonth()]} ${endDate.getDate()}, ${endDate.getFullYear()}`;

            fetch(`../api/chore.php?action=getChores&start=${startStr}&end=${endStr}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    // Group by chore
                    let choresMap = {};
                    data.forEach(instance => {
                        if (!choresMap[instance.chore_id]) {
                            choresMap[instance.chore_id] = {
                                title: instance.title,
                                assigned_to_id: instance.assigned_to_id,
                                assigned: instance.assigned_nickname || instance.assigned_member,
                                avatar: instance.assigned_image ? instance.assigned_image : `https://ui-avatars.com/api/?name=${instance.assigned_nickname || instance.assigned_member}&background=random&color=fff`,
                                points: instance.reward_points || 0,
                                instances: {}
                            };
                        }
                        choresMap[instance.chore_id].instances[instance.due_date] = instance;
                    });

                    let tbodyHtml = '';
                    for (let choreId in choresMap) {
                        let chore = choresMap[choreId];
                        tbodyHtml += `<tr class="align-middle text-center">
                        <td class="text-start ps-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="${chore.avatar}" class="rounded-circle border" width="40" height="40" alt="${chore.assigned}">
                                </div>
                                <div>
                                    <div class="fw-bold text-dark let-text">${chore.title}</div>
                                    <div class="text-muted fs-8">${chore.assigned}</div>
                                </div>
                            </div>
                        </td>`;

                        datesMap.forEach(dateStr => {
                            let instance = chore.instances[dateStr];
                            if (instance) {
                                let statusIcon = '';
                                if (instance.instance_status === 'complete') {
                                    statusIcon = '<div class="chore-checkbox-indicator checked mx-auto"><i class="fa-solid fa-check"></i></div>';
                                } else if (instance.instance_status === 'requested') {
                                    statusIcon = '<div class="chore-checkbox-indicator mx-auto bg-warning border-warning text-white"><i class="fa-solid fa-clock" style="display: block;"></i></div>';
                                } else if (instance.instance_status === 'skipped') {
                                    statusIcon = '<div class="chore-checkbox-indicator mx-auto bg-danger border-danger text-white"><i class="fa-solid fa-xmark" style="display: block; font-size: 1rem;"></i></div>';
                                } else {
                                    statusIcon = '<div class="chore-checkbox-indicator mx-auto bg-secondary border-secondary text-white"><i class="fa-solid fa-minus" style="display: block; font-size: 1rem;"></i></div>';
                                }

                                // let isToday = (dateStr === TODAY_STR);
                                let canInteract = false;

                                if (CURRENT_USER_ROLE === 'family-head') {
                                    canInteract = true;
                                } else if (chore.assigned_to_id == CURRENT_USER_ID) {
                                    canInteract = true;
                                }


                                if (canInteract && (instance.instance_status === 'pending' || instance.instance_status === 'requested')) {
                                    let safeTitle = chore.title.replace(/'/g, "\\'");
                                    tbodyHtml += `<td style="cursor:pointer;" onclick="openChoreActionModal(${instance.instance_id}, '${instance.instance_status}', ${chore.assigned_to_id}, '${safeTitle}', '${dateStr}')" title="Click for actions">${statusIcon}</td>`;
                                } else {
                                    tbodyHtml += `<td>${statusIcon}</td>`;
                                }
                            } else {
                                tbodyHtml += `<td><div class="chore-checkbox-indicator mx-auto" style="border-color: transparent; background: transparent;"></div></td>`;
                            }
                        });

                        tbodyHtml += `<td><div class="fw-bold">${chore.points} <i class="fa-solid fa-star text-warning ms-1"></i></div></td></tr>`;
                    }

                    if (Object.keys(choresMap).length === 0) {
                        tbodyHtml = '<tr><td colspan="9" class="text-center py-4 text-muted">No chores scheduled for this week.</td></tr>';
                    }

                    document.getElementById('choreTableBody').innerHTML = tbodyHtml;
                });
        }

        window.renderTable = renderTable;

        renderTable();

        document.getElementById('btn-prev').addEventListener('click', function() {
            currentStartDate.setDate(currentStartDate.getDate() - 7);
            renderTable();
        });
        document.getElementById('btn-next').addEventListener('click', function() {
            currentStartDate.setDate(currentStartDate.getDate() + 7);
            renderTable();
        });
        document.getElementById('btn-today').addEventListener('click', function() {
            currentStartDate = new Date();
            let day = currentStartDate.getDay();
            let diff = currentStartDate.getDate() - day + (day == 0 ? -6 : 1);
            currentStartDate.setDate(diff);
            renderTable();
        });
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>