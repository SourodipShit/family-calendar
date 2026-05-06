<?php
$path_prefix = "../";
$page_title = "Coaches";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<style>
    .category-pill {
        background-color: white;
        border: 1px solid #e0e0e0;
        color: #555;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .category-pill:hover {
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .coach-card {
        border: 1px solid #eef0f2;
        border-radius: 16px;
        transition: all 0.3s;
        height: 100%;
    }

    .coach-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-color: #dee2e6;
    }

    .status-badge {
        background-color: #e8f5e9;
        color: #4caf50;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .coach-img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 50%;
    }

    .tag-pill {
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 500;
    }

    .info-card {
        border: 1px solid #eef0f2;
        border-radius: 16px;
        background-color: white;
    }

    .tip-card {
        background-color: #f8faff;
        border: 1px solid #eef2ff;
    }

    .step-circle {
        width: 28px;
        height: 28px;
        background-color: #e0e7ff;
        color: #4f46e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .nav-tabs .nav-link {
        color: #64748b;
        font-weight: 500;
        border: none;
        padding: 1rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background: transparent;
    }

    .verification-badge {
        color: #0d6efd;
        font-size: 1rem;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #f8f9fa 0%, #f3f0ff 100%);
    }

    .search-icon {
        color: #94a3b8;
    }
</style>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1400px; margin: 0 auto;">

        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8 col-xl-9">
                <p class="text-muted fs-6 mb-4">Find and manage coaches to support your family's goals.</p>

                <!-- Tabs and Actions -->
                <div class="d-lg-flex justify-content-between align-items-end mb-4 border-bottom">
                    <ul class="nav nav-tabs border-0" id="coachesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="find-tab" data-bs-toggle="tab"
                                data-bs-target="#find" type="button" role="tab">Find a Coach</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="my-coaches-tab" data-bs-toggle="tab"
                                data-bs-target="#my-coaches" type="button" role="tab">My Coaches</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="applications-tab" data-bs-toggle="tab"
                                data-bs-target="#applications" type="button" role="tab">Applications</button>
                        </li>
                    </ul>
                    <button class="btn btn-primary rounded-3 px-4 py-2 mt-2 mt-lg-0 fw-medium mb-2 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Hire a Coach
                    </button>
                </div>

                <div class="tab-content" id="coachesTabsContent">
                    <div class="tab-pane fade show active" id="find" role="tabpanel" aria-labelledby="find-tab">

                        <!-- Search and Filters -->
                        <div class="d-flex flex-column flex-md-row gap-3 mb-4">
                            <div class="position-relative flex-grow-1">
                                <i
                                    class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 search-icon"></i>
                                <input type="text" id="coachSearchInput"
                                    class="form-control border rounded-3 py-2 ps-5"
                                    placeholder="Search coaches by name, specialty, or keyword...">
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn btn-white border rounded-3 py-2 px-3 text-dark d-flex align-items-center justify-content-between bg-white"
                                    style="min-width: 160px;" type="button" data-bs-toggle="dropdown">
                                    <span id="categoryDropdownLabel">All Categories</span>
                                    <i class="fa-solid fa-chevron-down text-muted ms-2 fs-7"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                    <li><a class="dropdown-item active category-filter" href="#"
                                            data-category="all">All Categories</a></li>
                                    <li><a class="dropdown-item category-filter" href="#"
                                            data-category="Academic">Academic</a></li>
                                    <li><a class="dropdown-item category-filter" href="#"
                                            data-category="Sports">Sports & Fitness</a></li>
                                    <li><a class="dropdown-item category-filter" href="#"
                                            data-category="Music">Music & Arts</a></li>
                                    <li><a class="dropdown-item category-filter" href="#"
                                            data-category="Life Skills">Life Skills</a></li>
                                    <li><a class="dropdown-item category-filter" href="#"
                                            data-category="Wellness">Wellness</a></li>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn btn-white border rounded-3 py-2 px-3 text-dark d-flex align-items-center justify-content-between bg-white"
                                    style="min-width: 140px;" type="button" data-bs-toggle="dropdown">
                                    <span>Availability</span>
                                    <i class="fa-solid fa-chevron-down text-muted ms-2 fs-7"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                    <li><a class="dropdown-item active" href="#">Any Availability</a></li>
                                    <li><a class="dropdown-item" href="#">Available Now</a></li>
                                    <li><a class="dropdown-item" href="#">Evenings</a></li>
                                    <li><a class="dropdown-item" href="#">Weekends</a></li>
                                </ul>
                            </div>
                            <button
                                class="btn btn-white border rounded-3 py-2 px-4 text-dark bg-white d-flex align-items-center">
                                <i class="fa-solid fa-filter text-muted me-2"></i> Filters
                            </button>
                        </div>

                        <!-- Category Pills -->
                        <div class="d-flex flex-wrap gap-3 mb-5" id="categoryPills">
                            <a href="#" class="category-pill shadow-sm category-filter"
                                data-category="Academic">
                                <i class="fa-solid fa-book-open text-primary"></i> Academic
                            </a>
                            <a href="#" class="category-pill shadow-sm category-filter" data-category="Sports">
                                <i class="fa-solid fa-futbol text-dark"></i> Sports & Fitness
                            </a>
                            <a href="#" class="category-pill shadow-sm category-filter" data-category="Music">
                                <i class="fa-solid fa-music text-info" style="color: #ba68c8 !important;"></i>
                                Music & Arts
                            </a>
                            <a href="#" class="category-pill shadow-sm category-filter"
                                data-category="Life Skills">
                                <i class="fa-solid fa-leaf text-success"></i> Life Skills
                            </a>
                            <a href="#" class="category-pill shadow-sm category-filter"
                                data-category="Wellness">
                                <i class="fa-regular fa-heart text-danger"></i> Wellness
                            </a>
                            <a href="#" class="category-pill shadow-sm text-primary category-filter"
                                data-category="all" style="border-color: #0d6efd;">
                                <i class="fa-solid fa-border-all"></i> View All
                            </a>
                        </div>

                        <!-- Featured Coaches -->
                        <h5 class="fw-bold mb-4">Featured Coaches</h5>
                        <div class="row g-4 mb-4">
                            <!-- Coach Card 1 -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card coach-card bg-white p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg"
                                            class="coach-img shadow-sm" alt="Sarah Johnson">
                                        <span class="status-badge">Available</span>
                                    </div>
                                    <div class="mb-1 d-flex align-items-center">
                                        <h5 class="fw-bold mb-0 me-2">Sarah Johnson</h5>
                                        <i class="fa-solid fa-circle-check verification-badge"></i>
                                    </div>
                                    <div class="text-muted fs-7 mb-2">Academic Coach</div>
                                    <div class="d-flex align-items-center mb-3 fs-7">
                                        <i class="fa-solid fa-star text-warning me-1"></i>
                                        <span class="fw-bold me-1">4.9</span>
                                        <span class="text-muted">(128 reviews)</span>
                                    </div>
                                    <p class="text-muted fs-7 mb-4 flex-grow-1">Specializes in Math, Science,
                                        and Study Skills for middle and high school students.</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        <span class="tag-pill">Math</span>
                                        <span class="tag-pill">Science</span>
                                        <span class="tag-pill">Study Skills</span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                        <div>
                                            <span class="fw-bold fs-5">$45</span>
                                            <span class="text-muted fs-7"> / 60 min</span>
                                        </div>
                                        <button
                                            class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                            Profile</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Coach Card 2 -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card coach-card bg-white p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg"
                                            class="coach-img shadow-sm" alt="Mike Thompson">
                                        <span class="status-badge">Available</span>
                                    </div>
                                    <div class="mb-1 d-flex align-items-center">
                                        <h5 class="fw-bold mb-0 me-2">Mike Thompson</h5>
                                        <i class="fa-solid fa-circle-check verification-badge"></i>
                                    </div>
                                    <div class="text-muted fs-7 mb-2">Sports Coach</div>
                                    <div class="d-flex align-items-center mb-3 fs-7">
                                        <i class="fa-solid fa-star text-warning me-1"></i>
                                        <span class="fw-bold me-1">4.8</span>
                                        <span class="text-muted">(96 reviews)</span>
                                    </div>
                                    <p class="text-muted fs-7 mb-4 flex-grow-1">Youth sports coach specializing
                                        in basketball skills, teamwork, and condition training.</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        <span class="tag-pill">Basketball</span>
                                        <span class="tag-pill">Fitness</span>
                                        <span class="tag-pill">Teamwork</span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                        <div>
                                            <span class="fw-bold fs-5">$40</span>
                                            <span class="text-muted fs-7"> / 60 min</span>
                                        </div>
                                        <button
                                            class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                            Profile</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Coach Card 3 -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card coach-card bg-white p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <img src="https://randomuser.me/api/portraits/women/65.jpg"
                                            class="coach-img shadow-sm" alt="Lisa Chen">
                                        <span class="status-badge">Available</span>
                                    </div>
                                    <div class="mb-1 d-flex align-items-center">
                                        <h5 class="fw-bold mb-0 me-2">Lisa Chen</h5>
                                        <i class="fa-solid fa-circle-check verification-badge"></i>
                                    </div>
                                    <div class="text-muted fs-7 mb-2">Music Instructor</div>
                                    <div class="d-flex align-items-center mb-3 fs-7">
                                        <i class="fa-solid fa-star text-warning me-1"></i>
                                        <span class="fw-bold me-1">4.9</span>
                                        <span class="text-muted">(156 reviews)</span>
                                    </div>
                                    <p class="text-muted fs-7 mb-4 flex-grow-1">Guitar and piano lessons for
                                        beginners to advanced students. All ages welcome.</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        <span class="tag-pill">Guitar</span>
                                        <span class="tag-pill">Piano</span>
                                        <span class="tag-pill">Music Theory</span>
                                    </div>
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                        <div>
                                            <span class="fw-bold fs-5">$50</span>
                                            <span class="text-muted fs-7"> / 60 min</span>
                                        </div>
                                        <button
                                            class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                            Profile</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- More Coaches (Hidden by default) -->
                        <div class="collapse" id="moreCoaches">
                            <div class="row g-4 mb-4">
                                <!-- Coach Card 4 -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card coach-card bg-white p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <img src="https://randomuser.me/api/portraits/men/45.jpg"
                                                class="coach-img shadow-sm" alt="David Lee">
                                            <span class="status-badge"
                                                style="background-color: #f8f9fa; color: #6c757d;">Busy</span>
                                        </div>
                                        <div class="mb-1 d-flex align-items-center">
                                            <h5 class="fw-bold mb-0 me-2">David Lee</h5>
                                        </div>
                                        <div class="text-muted fs-7 mb-2">Life Skills Coach</div>
                                        <div class="d-flex align-items-center mb-3 fs-7">
                                            <i class="fa-solid fa-star text-warning me-1"></i>
                                            <span class="fw-bold me-1">4.7</span>
                                            <span class="text-muted">(82 reviews)</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-4 flex-grow-1">Empowering kids to build
                                            independence through practical life skills and time management.</p>
                                        <div class="d-flex flex-wrap gap-2 mb-4">
                                            <span class="tag-pill">Organization</span>
                                            <span class="tag-pill">Time Mgmt</span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                            <div>
                                                <span class="fw-bold fs-5">$35</span>
                                                <span class="text-muted fs-7"> / 60 min</span>
                                            </div>
                                            <button
                                                class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                                Profile</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coach Card 5 -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card coach-card bg-white p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <img src="https://randomuser.me/api/portraits/women/23.jpg"
                                                class="coach-img shadow-sm" alt="Emily Davis">
                                            <span class="status-badge">Available</span>
                                        </div>
                                        <div class="mb-1 d-flex align-items-center">
                                            <h5 class="fw-bold mb-0 me-2">Emily Davis</h5>
                                            <i class="fa-solid fa-circle-check verification-badge"></i>
                                        </div>
                                        <div class="text-muted fs-7 mb-2">Wellness Coach</div>
                                        <div class="d-flex align-items-center mb-3 fs-7">
                                            <i class="fa-solid fa-star text-warning me-1"></i>
                                            <span class="fw-bold me-1">5.0</span>
                                            <span class="text-muted">(210 reviews)</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-4 flex-grow-1">Focuses on mental
                                            well-being, mindfulness, and healthy habits for children and teens.
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 mb-4">
                                            <span class="tag-pill">Mindfulness</span>
                                            <span class="tag-pill">Nutrition</span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                            <div>
                                                <span class="fw-bold fs-5">$60</span>
                                                <span class="text-muted fs-7"> / 60 min</span>
                                            </div>
                                            <button
                                                class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                                Profile</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coach Card 6 -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card coach-card bg-white p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <img src="https://randomuser.me/api/portraits/men/78.jpg"
                                                class="coach-img shadow-sm" alt="James Wilson">
                                            <span class="status-badge">Available</span>
                                        </div>
                                        <div class="mb-1 d-flex align-items-center">
                                            <h5 class="fw-bold mb-0 me-2">James Wilson</h5>
                                        </div>
                                        <div class="text-muted fs-7 mb-2">Art Instructor</div>
                                        <div class="d-flex align-items-center mb-3 fs-7">
                                            <i class="fa-solid fa-star text-warning me-1"></i>
                                            <span class="fw-bold me-1">4.6</span>
                                            <span class="text-muted">(45 reviews)</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-4 flex-grow-1">Creative arts and crafts
                                            lessons focusing on painting, drawing, and sculpting.</p>
                                        <div class="d-flex flex-wrap gap-2 mb-4">
                                            <span class="tag-pill">Painting</span>
                                            <span class="tag-pill">Drawing</span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                            <div>
                                                <span class="fw-bold fs-5">$40</span>
                                                <span class="text-muted fs-7"> / 60 min</span>
                                            </div>
                                            <button
                                                class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium">View
                                                Profile</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-2 mb-5">
                            <button
                                class="btn btn-white border text-primary fw-medium rounded-3 px-4 py-2 bg-white shadow-sm"
                                type="button" data-bs-toggle="collapse" data-bs-target="#moreCoaches"
                                aria-expanded="false" aria-controls="moreCoaches" id="viewAllBtn">
                                View All Coaches
                            </button>
                        </div>

                    </div> <!-- End #find tab-pane -->

                    <!-- My Coaches Tab Pane -->
                    <div class="tab-pane fade" id="my-coaches" role="tabpanel" aria-labelledby="my-coaches-tab">
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm mb-4 mt-2">
                            <i class="fa-solid fa-users fa-3x mb-3 text-primary opacity-50"></i>
                            <h4>Your Coaches</h4>
                            <p>You haven't hired any coaches yet. Browse coaches to find the right fit for your
                                family.</p>
                            <button class="btn btn-outline-primary mt-2"
                                onclick="document.getElementById('find-tab').click()">Browse Coaches</button>
                        </div>
                    </div>

                    <!-- Applications Tab Pane -->
                    <div class="tab-pane fade" id="applications" role="tabpanel"
                        aria-labelledby="applications-tab">
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm mb-4 mt-2">
                            <i class="fa-regular fa-file-lines fa-3x mb-3 text-info opacity-50"></i>
                            <h4>Applications</h4>
                            <p>You have no pending coach applications at this time.</p>
                        </div>
                    </div>
                </div> <!-- End #coachesTabsContent -->

            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-4 col-xl-3">

                <!-- Why hire a coach -->
                <div class="info-card p-4 mb-4 bg-gradient-purple text-center">
                    <div class="d-flex justify-content-center mb-3 position-relative">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px; border: 4px solid #f3f0ff;">
                            <i class="fa-solid fa-user text-primary"
                                style="font-size: 2.5rem; color: #c4b5fd !important;"></i>
                        </div>
                        <div class="position-absolute bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-2"
                            style="width: 28px; height: 28px; bottom: 0; right: 50%; transform: translate(35px, 5px);">
                            <i class="fa-solid fa-star fs-8"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-4 text-start">Why hire a coach?</h5>
                    <ul class="list-unstyled text-start fs-7 text-dark mb-0 d-flex flex-column gap-3">
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Personalized guidance for your family's goals
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Build skills and confidence
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Flexible scheduling to fit your routine
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Track progress and achievements
                        </li>
                    </ul>
                </div>

                <!-- Tip Card -->
                <div class="info-card tip-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-regular fa-lightbulb text-primary me-2 fs-5"></i>
                        <h6 class="fw-bold mb-0">Tip</h6>
                    </div>
                    <p class="text-muted fs-7 mb-0">Once hired, coaches can be added to your calendar and
                        assigned to family members.</p>
                </div>

                <!-- How it works -->
                <div class="info-card p-4">
                    <h5 class="fw-bold mb-4">How it works</h5>

                    <div class="d-flex mb-4">
                        <div class="step-circle me-3">1</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Find a coach that fits your needs</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="step-circle me-3 bg-primary text-white">2</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Review profile and send request</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="step-circle me-3">3</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Coach accepts and you can start scheduling</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Expand/Collapse functionality ---
        const moreCoaches = document.getElementById('moreCoaches');
        const viewAllBtn = document.getElementById('viewAllBtn');

        if (moreCoaches && viewAllBtn) {
            moreCoaches.addEventListener('show.bs.collapse', event => {
                viewAllBtn.innerHTML = 'Show Less Coaches';
            });
            moreCoaches.addEventListener('hide.bs.collapse', event => {
                viewAllBtn.innerHTML = 'View All Coaches';
            });
        }

        // --- Search and Category Filtering ---
        const searchInput = document.getElementById('coachSearchInput');
        const categoryFilters = document.querySelectorAll('.category-filter');
        const allCoachCards = document.querySelectorAll('.coach-card');

        let currentCategory = 'all';
        let searchQuery = '';

        function filterCoaches() {
            let activeCount = 0;
            allCoachCards.forEach(card => {
                const col = card.parentElement;
                const text = card.innerText.toLowerCase();
                const matchesSearch = text.includes(searchQuery);
                const matchesCategory = currentCategory === 'all' || text.includes(currentCategory.toLowerCase());

                if (matchesSearch && matchesCategory) {
                    col.style.display = '';
                    activeCount++;
                } else {
                    col.style.display = 'none';
                }
            });

            // Auto-expand the "more coaches" section if we are actively searching or filtering
            if ((searchQuery.length > 0 || currentCategory !== 'all') && moreCoaches) {
                if (!moreCoaches.classList.contains('show')) {
                    moreCoaches.classList.add('show');
                    if (viewAllBtn) viewAllBtn.innerHTML = 'Show Less Coaches';
                }
            }
        }

        // Search Input Event
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                filterCoaches();
            });
        }

        // Category Filter Event (handles both pills and dropdown items)
        categoryFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                currentCategory = e.currentTarget.getAttribute('data-category');

                // Update dropdown label if a dropdown item was clicked
                if (e.currentTarget.classList.contains('dropdown-item')) {
                    const label = document.getElementById('categoryDropdownLabel');
                    if (label) label.innerText = e.currentTarget.innerText;
                }

                // Reset active styling on all filters
                categoryFilters.forEach(f => {
                    if (f.classList.contains('category-pill')) {
                        f.style.borderColor = '#e0e0e0';
                        f.classList.remove('text-primary');
                    } else {
                        f.classList.remove('active');
                    }
                });

                // Set active styling on the clicked filter
                if (e.currentTarget.classList.contains('category-pill')) {
                    e.currentTarget.style.borderColor = '#0d6efd';
                    e.currentTarget.classList.add('text-primary');
                } else {
                    e.currentTarget.classList.add('active');
                }

                filterCoaches();
            });
        });
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>
