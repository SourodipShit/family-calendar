<?php
$path_prefix = "../";
$page_title = "Chores";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4" style="max-width: 1400px; margin: 0 auto;">

        <div class="row g-4">
            <div class="col-12">
                <!-- Family Members Row -->
                <div class="d-flex flex-wrap align-items-center gap-0 mb-4 family-members-list">
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
                            class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center">
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
                            <table class="table mb-0 chore-table">
                                <thead>
                                    <tr class="text-center align-middle">
                                        <th class="text-start ps-4" style="width: 250px;">Chore</th>
                                        <th>Mon<br><span class="text-muted fw-normal fs-8">May 12</span></th>
                                        <th>Tue<br><span class="text-muted fw-normal fs-8">May 13</span></th>
                                        <th>Wed<br><span class="text-muted fw-normal fs-8">May 14</span></th>
                                        <th>Thu<br><span class="text-muted fw-normal fs-8">May 15</span></th>
                                        <th>Fri<br><span class="text-muted fw-normal fs-8">May 16</span></th>
                                        <th>Sat<br><span class="text-muted fw-normal fs-8">May 17</span></th>
                                        <th>Sun<br><span class="text-muted fw-normal fs-8">May 18</span></th>
                                        <th style="width: 100px;">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Row 1 -->
                                    <tr class="align-middle text-center" data-member="all">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="chore-icon-wrapper bg-success bg-opacity-10 text-success me-3">
                                                    <i class="fa-solid fa-utensils"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Set the Table</div>
                                                    <div class="text-muted fs-8">Everyone</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">14 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 2 -->
                                    <tr class="align-middle text-center" data-member="mom">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="chore-icon-wrapper bg-primary bg-opacity-10 text-primary me-3">
                                                    <i class="fa-solid fa-shirt"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Fold Laundry</div>
                                                    <div class="text-muted fs-8">Mom</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">9 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 3 -->
                                    <tr class="align-middle text-center" data-member="liam">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="chore-icon-wrapper bg-info bg-opacity-10 text-info me-3"
                                                    style="background-color: rgba(186, 104, 200, 0.1) !important; color: #ba68c8 !important;">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Take Out Trash</div>
                                                    <div class="text-muted fs-8">Liam</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">6 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 4 -->
                                    <tr class="align-middle text-center" data-member="emma">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="chore-icon-wrapper bg-warning bg-opacity-10 text-warning me-3">
                                                    <i class="fa-solid fa-broom"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Vacuum Living Room
                                                    </div>
                                                    <div class="text-muted fs-8">Emma</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">6 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 5 -->
                                    <tr class="align-middle text-center" data-member="ava">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="chore-icon-wrapper bg-success bg-opacity-10 text-success me-3"
                                                    style="background-color: rgba(76, 175, 80, 0.1) !important; color: #4caf50 !important;">
                                                    <i class="fa-solid fa-seedling"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Water Plants</div>
                                                    <div class="text-muted fs-8">Ava</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">9 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 6 -->
                                    <tr class="align-middle text-center" data-member="dad">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="chore-icon-wrapper bg-danger bg-opacity-10 text-danger me-3">
                                                    <i class="fa-solid fa-spray-can-sparkles"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Clean Bathroom</div>
                                                    <div class="text-muted fs-8">Dad</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator mx-auto"></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">6 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
                                    <!-- Row 7 -->
                                    <tr class="align-middle text-center" data-member="all">
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="chore-icon-wrapper bg-info bg-opacity-10 text-info me-3"
                                                    style="background-color: rgba(33, 150, 243, 0.1) !important; color: #2196f3 !important;">
                                                    <i class="fa-solid fa-sink"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark let-text">Wash Dishes</div>
                                                    <div class="text-muted fs-8">Everyone</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="chore-checkbox-indicator checked mx-auto"><i
                                                    class="fa-solid fa-check"></i></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">14 <i
                                                    class="fa-solid fa-star text-warning ms-1"></i></div>
                                        </td>
                                    </tr>
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

<?php include $path_prefix . 'components/footer.php'; ?>
