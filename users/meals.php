<?php
$path_prefix = "../";
$page_title = "Meals";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';

$family_id = $_SESSION['user']['active_family_id'] ?? 1;

$use_recipes_in_meal = false;
if (isset($_SESSION['user']['active_family']['settings'])) {
    $settings_json = $_SESSION['user']['active_family']['settings'];
    $settings = is_string($settings_json) ? json_decode($settings_json, true) : $settings_json;
    if (is_array($settings) && !empty($settings['use_recipes_in_meal'])) {
        $use_recipes_in_meal = filter_var($settings['use_recipes_in_meal'], FILTER_VALIDATE_BOOLEAN);
    }
}
?>
<script>
    const FAMILY_ID = <?php echo $family_id; ?>;
    const USE_RECIPES_IN_MEAL = <?php echo $use_recipes_in_meal ? 'true' : 'false'; ?>;
</script>

<style>
    /* Modal Styles */
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
        transition: all 0.3s ease;
    }

    .border-dashed:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    }

    .fs-7 {
        font-size: 0.85rem;
    }

    .fs-8 {
        font-size: 0.75rem;
    }

    .fs-9 {
        font-size: 0.65rem;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .meal-card {
        transition: transform 0.2s ease, shadow 0.2s ease;
    }

    .meal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .options-btn {
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .meal-card:hover .options-btn {
        opacity: 1;
    }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }

        #grocery-list-page,
        #grocery-list-page * {
            visibility: visible;
        }

        #grocery-list-page {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .no-print {
            display: none !important;
        }
    }

    /* Sidebar Grocery Styles */
    .sidebar-grocery-category {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--bs-primary);
        margin-top: 1.5rem;
        margin-bottom: 0.4rem;
        padding: 0.2rem 0.6rem;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-radius: 4px;
        border-bottom: 3px solid var(--bs-primary);
        display: block;
    }

    .sidebar-grocery-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .sidebar-grocery-item:last-child {
        border-bottom: none;
    }

    .sidebar-grocery-item .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0;
        cursor: pointer;
    }

    .sidebar-grocery-item .item-name {
        font-size: 0.95rem;
        font-weight: 500;
        margin-left: 0.75rem;
        color: #333;
        cursor: pointer;
    }

    .sidebar-grocery-item .form-check-input:checked+.item-name {
        text-decoration: line-through;
        opacity: 0.6;
    }

    /* Main Grocery List Item Size */
    .grocery-item-label {
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .grocery-qty-text {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .grocery-unit-text {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    /* Rating Styles */
    .rating-badge {
        position: absolute;
        bottom: 0.5rem;
        left: 0.5rem;
        background: rgba(255, 255, 255, 0.9);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 5;
    }

    .star-rating {
        color: #ffc107;
    }

    .favorite-btn-active {
        color: #ff4757 !important;
    }

    .rating-stars-input i {
        font-size: 1.5rem;
        cursor: pointer;
        color: #ddd;
        transition: color 0.2s;
    }

    .rating-stars-input i.active {
        color: #ffc107;
    }
</style>

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
                        -ms-overflow-style: none;  /* IE and Edge */
                        scrollbar-width: none;  /* Firefox */
                    }
                </style>
                <!-- Family Members Row -->
                <div class="d-flex flex-nowrap align-items-center gap-0 mb-4 family-members-list overflow-x-auto">
                    <!-- Populated by JS: Dad, Mom, Emma, Liam, Ava, Grandma, Grandpa, Add -->
                </div>

            </div>
            <!-- Main Meals Area -->
            <div class="col-lg-9">

                <!-- Tabs -->
                <div class="mb-4 border-bottom">
                    <ul class="nav nav-tabs border-0" id="mealTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link active border-0 border-bottom border-primary border-3 bg-transparent fw-bold text-primary px-4 pb-3"
                                id="planner-tab" data-bs-toggle="tab" data-bs-target="#planner" type="button"
                                role="tab">Meal Planner</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link border-0 bg-transparent fw-medium text-muted px-4 pb-3"
                                id="mymeals-tab" data-bs-toggle="tab" data-bs-target="#mymeals" type="button"
                                role="tab">My Meals</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link border-0 bg-transparent fw-medium text-muted px-4 pb-3"
                                id="groceries-tab" data-bs-toggle="tab" data-bs-target="#groceries"
                                type="button" role="tab">Groceries</button>
                        </li>
                    </ul>
                </div>

                <!-- Meal Controls -->
                <div class="d-lg-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="btn-group border rounded-3 overflow-hidden shadow-sm">
                            <button class="btn btn-white border-0 py-2 px-3" id="btn-prev"><i
                                    class="fa-solid fa-chevron-left text-muted"></i></button>
                            <button class="btn btn-white border-0 py-2 px-3 fw-bold border-start border-end"
                                id="date-picker-btn"><i class="fa-regular fa-calendar me-2"></i> May 12 â€“ May
                                18, 2024</button>
                            <button class="btn btn-white border-0 py-2 px-3" id="btn-next"><i
                                    class="fa-solid fa-chevron-right text-muted"></i></button>
                        </div>
                        <button class="btn btn-white border rounded-3 py-2 px-4 fw-medium shadow-sm"
                            id="btn-today">Today</button>
                    </div>
                    <div class="d-flex gap-3 mt-2 mt-lg-0">
                        <button id="add-meal-btn"
                            class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#mealModal">
                            <i class="fa-solid fa-plus me-2"></i> Add Meal
                        </button>
                        <button id="new-list-btn"
                            class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center d-none"
                            data-bs-toggle="modal" data-bs-target="#groceryListModal">
                            <i class="fa-solid fa-plus me-2"></i> New List
                        </button>
                        <button
                            class="btn btn-white border rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center">
                            <i class="fa-solid fa-gear me-2"></i> Meal Settings
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="mealTabsContent">
                    <!-- Meal Planner Tab -->
                    <div class="tab-pane fade show active" id="planner" role="tabpanel">
                        <div class="bg-white border mob-overflows rounded-3 overflow-hidden">
                            <!-- Planner Header -->
                            <div id="planner-header"
                                class="meal-planner-grid border-bottom fw-bold text-center bg-light bg-opacity-50">
                                <div class="meal-time-cell border-bottom-0 py-3"></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Sun<br><span
                                        class="text-muted fw-normal fs-8">May 11</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Mon<br><span
                                        class="text-muted fw-normal fs-8">May 12</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Tue<br><span
                                        class="text-muted fw-normal fs-8">May 13</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Wed<br><span
                                        class="text-muted fw-normal fs-8">May 14</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Thu<br><span
                                        class="text-muted fw-normal fs-8">May 15</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Fri<br><span
                                        class="text-muted fw-normal fs-8">May 16</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto"
                                    style="border-right: none;">Sat<br><span
                                        class="text-muted fw-normal fs-8">May 17</span></div>
                            </div>

                            <!-- Breakfast Row -->
                            <div class="meal-planner-grid meals-card-div" id="breakfast-row">
                                <div class="meal-time-cell">
                                    <span class="iu-breakfast yelows">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="29"
                                            height="29" fill="rgba(254,186,9,1)">
                                            <path
                                                d="M12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6C15.3137 6 18 8.68629 18 12C18 15.3137 15.3137 18 12 18ZM11 1H13V4H11V1ZM11 20H13V23H11V20ZM3.51472 4.92893L4.92893 3.51472L7.05025 5.63604L5.63604 7.05025L3.51472 4.92893ZM16.9497 18.364L18.364 16.9497L20.4853 19.0711L19.0711 20.4853L16.9497 18.364ZM19.0711 3.51472L20.4853 4.92893L18.364 7.05025L16.9497 5.63604L19.0711 3.51472ZM5.63604 16.9497L7.05025 18.364L4.92893 20.4853L3.51472 19.0711L5.63604 16.9497ZM23 11V13H20V11H23ZM4 11V13H1V11H4Z">
                                            </path>
                                        </svg>
                                    </span>
                                    <div class="fw-bold fs-8">Breakfast</div>
                                </div>
                                <!-- Days will be populated by JS -->
                            </div>

                            <!-- Lunch Row -->
                            <div class="meal-planner-grid" id="lunch-row">
                                <div class="meal-time-cell">
                                    <span class="iu-breakfast grens">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="29"
                                            height="29" fill="rgba(30,134,31,1)">
                                            <path
                                                d="M12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6C15.3137 6 18 8.68629 18 12C18 15.3137 15.3137 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16ZM11 1H13V4H11V1ZM11 20H13V23H11V20ZM3.51472 4.92893L4.92893 3.51472L7.05025 5.63604L5.63604 7.05025L3.51472 4.92893ZM16.9497 18.364L18.364 16.9497L20.4853 19.0711L19.0711 20.4853L16.9497 18.364ZM19.0711 3.51472L20.4853 4.92893L18.364 7.05025L16.9497 5.63604L19.0711 3.51472ZM5.63604 16.9497L7.05025 18.364L4.92893 20.4853L3.51472 19.0711L5.63604 16.9497ZM23 11V13H20V11H23ZM4 11V13H1V11H4Z">
                                            </path>
                                        </svg>
                                    </span>
                                    <div class="fw-bold fs-8">Lunch</div>
                                </div>
                                <!-- Days will be populated by JS -->
                            </div>

                            <!-- Dinner Row -->
                            <div class="meal-planner-grid" id="dinner-row">
                                <div class="meal-time-cell">
                                    <span class="iu-breakfast dinners">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="29"
                                            height="29" fill="rgba(85,33,214,1)">
                                            <path
                                                d="M10 7C10 10.866 13.134 14 17 14C18.9584 14 20.729 13.1957 21.9995 11.8995C22 11.933 22 11.9665 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C12.0335 2 12.067 2 12.1005 2.00049C10.8043 3.27098 10 5.04157 10 7ZM4 12C4 16.4183 7.58172 20 12 20C15.0583 20 17.7158 18.2839 19.062 15.7621C18.3945 15.9187 17.7035 16 17 16C12.0294 16 8 11.9706 8 7C8 6.29648 8.08133 5.60547 8.2379 4.938C5.71611 6.28423 4 8.9417 4 12Z">
                                            </path>
                                        </svg>
                                    </span>
                                    <div class="fw-bold fs-8">Dinner</div>
                                </div>
                                <!-- Days will be populated by JS -->
                            </div>

                            <!-- Snacks Row -->
                            <div class="meal-planner-grid" id="snacks-row">
                                <div class="meal-time-cell" style="border-bottom: none;">

                                    <span class="iu-breakfast dinner2s">
                                        <i class="fa-solid fa-apple-whole text-warning mb-2 fs-4"></i>
                                    </span>
                                    <div class="fw-bold fs-8">Snacks</div>
                                </div>
                                <!-- Days will be populated by JS -->
                            </div>
                        </div>

                        <!-- Motivational Banner -->
                        <div class="mt-4 p-4 rounded-4 d-lg-flex align-items-lg-center"
                            style="background-color: #f0f7ff;">
                            <div class="bg-white rounded d-inline-block p-2 me-3 fs-5 shadow-sm text-primary">ðŸ’¡
                            </div>
                            <div class="flex-grow-1 my-2 my-lg-0">
                                <span class="fw-bold">Plan ahead for success!</span> Planning your meals in
                                advance saves time and reduces stress during busy weekdays.
                            </div>
                            <div>
                                <button
                                    class="btn btn-white border rounded-3 px-4 py-2 fw-medium shadow-sm text-primary">View
                                    Tips</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="mymeals" role="tabpanel">
                        <div class="row g-4" id="favorites-container">
                            <!-- Favorites will be rendered here -->
                        </div>
                        <div id="no-favorites-message" class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm d-none">
                            <i class="fa-solid fa-heart fa-4x mb-4 opacity-25"></i>
                            <h3>No Favorites Yet</h3>
                            <p>Meals you mark as favorite will appear here for quick access.</p>
                            <button class="btn btn-primary mt-3 rounded-3 px-4" onclick="document.getElementById('planner-tab').click()">Browse Planner</button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="groceries" role="tabpanel">
                        <div id="grocery-list-page" class="bg-white border rounded-4 shadow-sm p-3 mx-auto" style="max-width: 850px; min-height: 500px;">
                            <!-- Header Actions -->
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom no-print">
                                <h5 class="fw-bold mb-0 text-primary d-flex align-items-center">
                                    <span class="bg-primary bg-opacity-10 p-1 rounded-2 me-2">
                                        <i class="fa-solid fa-basket-shopping text-primary fs-7"></i>
                                    </span>
                                    Full Grocery List
                                </h5>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-white border rounded-3 px-2 py-1 fw-medium shadow-sm d-flex align-items-center fs-8" onclick="window.print()">
                                        <i class="fa-solid fa-print me-1 text-muted"></i> Print
                                    </button>
                                    <button class="btn btn-white border rounded-3 px-2 py-1 fw-medium shadow-sm d-flex align-items-center fs-8" id="edit-list-btn">
                                        <i class="fa-solid fa-pen-to-square me-1 text-muted"></i> Edit List
                                    </button>
                                </div>
                            </div>

                            <!-- List Content -->
                            <div id="grocery-content-area" class="d-none">
                                <div class="text-center mb-3 mt-1">
                                    <h4 id="display-list-title" class="fw-bold mb-0 text-dark">Weekly Groceries</h4>
                                    <p id="display-list-dates" class="text-muted fw-medium mb-0 small">May 11 â€“ May 17, 2024</p>
                                </div>

                                <div id="display-items-list" class="row g-3">
                                    <!-- Grouped Items will be rendered here -->
                                </div>

                                <div id="display-list-notes-container" class="mt-4 pt-3 border-top d-none">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa-solid fa-note-sticky text-muted me-2 small"></i>
                                        <h6 class="fw-bold text-dark mb-0 small">Notes</h6>
                                    </div>
                                    <div class="p-2 bg-light rounded-3 border-start border-primary border-3">
                                        <p id="display-list-notes" class="mb-0 text-dark lh-sm" style="white-space: pre-wrap; font-size: 0.8rem;"></p>
                                    </div>
                                </div>

                                <div class="mt-4 text-center text-muted small border-top pt-2 opacity-50 no-print" style="font-size: 0.7rem;">
                                    Generated by Family Calendar
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div id="no-grocery-message" class="text-center py-5 my-5">
                                <div class="bg-light d-inline-flex p-4 rounded-circle mb-4">
                                    <i class="fa-solid fa-basket-shopping fa-3x text-muted opacity-25"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">No Grocery List Yet</h4>
                                <p class="text-muted mb-4 mx-auto" style="max-width: 300px;">We couldn't find a grocery list for this week. Create one to get started with your shopping.</p>
                                <button class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#groceryListModal">
                                    <i class="fa-solid fa-plus me-2"></i> Create Weekly List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-3">
                <!-- Grocery List Widget -->
                <div class="card border rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">Grocery List</h5>
                        <div class="text-muted small mb-3" id="sidebar-item-count">0 items</div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" id="sidebar-progress-bar" role="progressbar" style="width: 0%;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="small text-muted fw-bold" id="sidebar-progress-text">0%</div>
                        </div>

                        <div id="sidebar-grocery-items-container">
                            <!-- Populated by JS -->
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <a href="#" id="view-full-list-link"
                                class="text-primary text-decoration-none fw-bold small d-flex align-items-center justify-content-between">
                                <span class="button-text">View Full List</span> <i
                                    class="fa-solid fa-chevron-right fs-8 ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Meal Ideas Widget -->
                <div class="card border rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Meal Ideas</h5>
                            <a href="#" class="text-primary text-decoration-none small fw-bold">View All</a>
                        </div>

                        <div class="meal-ideas-list">
                            <div class="meal-idea-card">
                                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=100&h=100&auto=format&fit=crop"
                                    alt="Fajitas">
                                <div class="idea-info">
                                    <div class="idea-name">Sheet Pan Chicken Fajitas</div>
                                    <div class="idea-meta">30 min</div>
                                </div>
                            </div>
                            <div class="meal-idea-card">
                                <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=100&h=100&auto=format&fit=crop"
                                    alt="Rice">
                                <div class="idea-info">
                                    <div class="idea-name">Veggie Fried Rice</div>
                                    <div class="idea-meta">25 min</div>
                                </div>
                            </div>
                            <div class="meal-idea-card">
                                <img src="https://images.unsplash.com/photo-1473093226795-af9932fe5856?q=80&w=100&h=100&auto=format&fit=crop"
                                    alt="Pasta">
                                <div class="idea-info">
                                    <div class="idea-name">Chicken Pesto Pasta</div>
                                    <div class="idea-meta">20 min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personalized Suggestions banner -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden"
                    style="background: linear-gradient(135deg, #f8f0ff 0%, #f0e0ff 100%);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white rounded p-2 text-primary shadow-sm">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Get Personalized
                                    Suggestions</h6>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Based on your family
                                    preferences</p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-muted small"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let groceryCategories = [];

    const fetchGroceryCategories = () => {
        fetch(`../api/grocery_categories.php?action=list`)
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    groceryCategories = data.data;
                }
            })
            .catch(err => console.error('Error fetching categories:', err));
    };

    document.addEventListener('DOMContentLoaded', function() {
        fetchGroceryCategories();
        let currentDate = new Date(); // Start with today

        // Helper to format date as YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        // Helper to get week range (Sunday to Saturday)
        const getWeekRange = (date) => {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day; // Go back to Sunday (day 0)
            const sunday = new Date(d.setDate(diff));
            const saturday = new Date(sunday);
            saturday.setDate(sunday.getDate() + 6);
            return {
                start: sunday,
                end: saturday
            };
        };

        const updateUI = () => {
            const range = getWeekRange(currentDate);
            const startStr = formatDate(range.start);
            const endStr = formatDate(range.end);

            // Update date picker text
            const options = {
                month: 'short',
                day: 'numeric'
            };
            const yearOptions = {
                year: 'numeric'
            };
            const dateRangeText = `${range.start.toLocaleDateString('en-US', options)} â€“ ${range.end.toLocaleDateString('en-US', options)}, ${range.end.toLocaleDateString('en-US', yearOptions)}`;
            document.getElementById('date-picker-btn').innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${dateRangeText}`;

            // Update Planner Header
            const headerCells = document.querySelectorAll('#planner-header .meal-day-cell');
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            headerCells.forEach((cell, index) => {
                const dayDate = new Date(range.start);
                dayDate.setDate(range.start.getDate() + index);
                cell.innerHTML = `${days[index]}<br><span class="text-muted fw-normal fs-8">${dayDate.toLocaleDateString('en-US', options)}</span>`;
            });

            fetchMeals(startStr, endStr);
            fetchGroceries(startStr, endStr);
        };

        const fetchGroceries = (startDate, endDate) => {
            fetch(`../api/grocery.php?action=getByDate&startDate=${startDate}&endDate=${endDate}&family_id=${FAMILY_ID}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderGroceries(data.data);
                    } else {
                        document.getElementById('grocery-content-area').classList.add('d-none');
                        document.getElementById('no-grocery-message').classList.remove('d-none');
                    }
                })
                .catch(err => console.error('Error fetching groceries:', err));
        };

        const renderGroceries = (list) => {
            document.getElementById('grocery-content-area').classList.remove('d-none');
            document.getElementById('no-grocery-message').classList.add('d-none');

            document.getElementById('display-list-title').textContent = list.title;

            // Set up Edit button
            const editBtn = document.getElementById('edit-list-btn');
            editBtn.onclick = () => editGroceryList(list);

            const options = {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            };
            const start = new Date(list.week_start_date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
            const end = new Date(list.week_end_date).toLocaleDateString('en-US', options);
            document.getElementById('display-list-dates').textContent = `${start} â€“ ${end}`;

            if (list.notes && list.notes.trim() !== "") {
                document.getElementById('display-list-notes-container').classList.remove('d-none');
                document.getElementById('display-list-notes').textContent = list.notes;
            } else {
                document.getElementById('display-list-notes-container').classList.add('d-none');
            }

            const itemsList = document.getElementById('display-items-list');
            itemsList.innerHTML = '';
            itemsList.className = 'col-12'; // Single column for list view

            // Group items by category
            const categories = {};
            list.items.forEach(item => {
                const catName = item.category_name;
                if (!categories[catName]) categories[catName] = [];
                categories[catName].push(item);
            });

            for (const [catName, items] of Object.entries(categories)) {
                const catSection = document.createElement('div');
                catSection.className = 'mb-3'; // Tighter section spacing

                let itemsHtml = items.map(item => {
                    const qty = item.quantity ? `<span class="fw-bold text-dark grocery-qty-text">${item.quantity}</span>` : '';
                    const unit = item.unit ? `<span class="text-muted ms-1 grocery-unit-text">${item.unit}</span>` : '';
                    const qtyInfo = (qty || unit) ? `<div class="ms-auto ps-2 text-end">${qty} ${unit}</div>` : '';

                    return `
                        <div class="d-flex align-items-center py-2 border-bottom border-light opacity-85">
                            <div class="form-check mb-0 d-flex align-items-center">
                                <input class="form-check-input toggle-item-checkbox" type="checkbox" data-id="${item.id}" ${item.is_complete ? 'checked' : ''} style="width: 1.1rem; height: 1.1rem;">
                                <label class="form-check-label text-dark ms-2 grocery-item-label" style="line-height: 1;">${item.name}</label>
                            </div>
                            ${qtyInfo}
                        </div>
                    `;
                }).join('');

                catSection.innerHTML = `
                    <h6 class="fw-bold text-primary mb-1 mt-2 d-flex align-items-center border-bottom pb-1" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-caret-right fs-9 me-1"></i>
                        ${catName}
                    </h6>
                    <div class="category-items ps-1">
                        ${itemsHtml}
                    </div>
                `;
                itemsList.appendChild(catSection);
            }

            // Update Sidebar Widget
            renderSidebarGroceries(list);
        };

        const renderSidebarGroceries = (list) => {
            const sidebarContainer = document.getElementById('sidebar-grocery-items-container');
            const sidebarCount = document.getElementById('sidebar-item-count');
            const sidebarProgressBar = document.getElementById('sidebar-progress-bar');
            const sidebarProgressText = document.getElementById('sidebar-progress-text');
            const viewFullListLink = document.getElementById('view-full-list-link');

            if (!sidebarContainer) return;

            sidebarContainer.innerHTML = '';

            const totalItems = list.items.length;
            const completedItems = list.items.filter(i => i.is_complete).length;
            const progress = totalItems > 0 ? Math.round((completedItems / totalItems) * 100) : 0;

            sidebarCount.textContent = `${totalItems} items`;
            sidebarProgressBar.style.width = `${progress}%`;
            sidebarProgressBar.setAttribute('aria-valuenow', progress);
            sidebarProgressText.textContent = `${progress}%`;

            // Handle View Full List link
            viewFullListLink.onclick = (e) => {
                e.preventDefault();
                document.getElementById('groceries-tab').click();
                document.getElementById('grocery-list-page').scrollIntoView({
                    behavior: 'smooth'
                });
            };

            // Group by category for sidebar (limit to first few categories or items if needed, but here we show all)
            const categories = {};
            list.items.forEach(item => {
                const catName = item.category_name;
                if (!categories[catName]) categories[catName] = [];
                categories[catName].push(item);
            });

            // Show first 3 categories in sidebar for compactness, or all? Let's show all but maybe limit items per category
            for (const [catName, items] of Object.entries(categories)) {
                const catDiv = document.createElement('div');
                catDiv.className = 'sidebar-grocery-category';
                catDiv.textContent = catName;
                sidebarContainer.appendChild(catDiv);

                items.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'sidebar-grocery-item';
                    itemDiv.innerHTML = `
                        <input class="form-check-input toggle-item-checkbox" type="checkbox" data-id="${item.id}" ${item.is_complete ? 'checked' : ''}>
                        <span class="item-name">${item.name}</span>
                    `;
                    sidebarContainer.appendChild(itemDiv);
                });
            }
        };

        const editGroceryList = (list) => {
            document.getElementById('editListIdInput').value = list.id;
            document.getElementById('editListTitle').value = list.title;
            document.getElementById('editListStartDate').value = list.week_start_date;
            document.getElementById('editListEndDate').value = list.week_end_date;
            document.getElementById('editListNotes').value = list.notes || '';

            const itemsContainer = document.getElementById('edit-grocery-items-container');
            itemsContainer.innerHTML = '';

            list.items.forEach(item => {
                const itemRow = document.createElement('div');
                itemRow.className = 'grocery-item-row';
                itemRow.innerHTML = `
                    <div class="input-group border rounded-3 overflow-hidden shadow-sm mb-2">
                        <select class="form-select border-0 px-1 py-2 text-dark fw-medium border-end fs-8" name="categories[]" style="max-width: 80px; min-width: 80px;">
                            ${groceryCategories.map(cat => `<option value="${cat.id}" ${cat.id == item.grocery_category_id ? 'selected' : ''}>${cat.name}</option>`).join('')}
                        </select>
                        <input type="text" class="form-control border-0 px-2 py-2 text-dark fw-medium fs-8" name="items[]" value="${item.name}" placeholder="Item..." required>
                        <input type="number" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8 text-center" name="quantities[]" value="${item.quantity || ''}" placeholder="Qty" style="max-width: 50px; min-width: 50px;">
                        <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8" name="units[]" value="${item.unit || ''}" placeholder="Unit" style="max-width: 60px; min-width: 60px;">
                        <input type="hidden" name="is_complete[]" value="${item.is_complete ? 1 : 0}">
                        <button class="btn btn-white border-0 text-danger remove-item-btn border-start px-2" type="button"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                `;
                itemsContainer.appendChild(itemRow);
            });

            const modal = new bootstrap.Modal(document.getElementById('editGroceryListModal'));
            modal.show();
        };

        // Add item button logic for EDIT modal
        document.getElementById('edit-add-item-btn').addEventListener('click', function() {
            const itemsContainer = document.getElementById('edit-grocery-items-container');
            const itemRow = document.createElement('div');
            itemRow.className = 'grocery-item-row';

            let categoryOptions = groceryCategories.map(cat =>
                `<option value="${cat.id}">${cat.name}</option>`
            ).join('');

            itemRow.innerHTML = `
                <div class="input-group border rounded-3 overflow-hidden shadow-sm mb-2">
                    <select class="form-select border-0 px-1 py-2 text-dark fw-medium border-end fs-8" name="categories[]" style="max-width: 80px; min-width: 80px;">
                        ${categoryOptions}
                    </select>
                    <input type="text" class="form-control border-0 px-2 py-2 text-dark fw-medium fs-8" name="items[]" placeholder="Item..." required>
                    <input type="number" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8 text-center" name="quantities[]" placeholder="Qty" style="max-width: 50px; min-width: 50px;">
                    <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8" name="units[]" placeholder="Unit" style="max-width: 60px; min-width: 60px;">
                    <input type="hidden" name="is_complete[]" value="0">
                    <button class="btn btn-white border-0 text-danger remove-item-btn border-start px-2" type="button"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            `;
            itemsContainer.appendChild(itemRow);
        });

        const fetchFavorites = () => {
            fetch(`../api/favorite.php?action=list`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderFavorites(data.favorites, data.recipes);
                    } else {
                        console.error('Error fetching favorites:', data.message);
                        renderFavorites([], []); // Show 'No Favorites' message if it's an API error
                    }
                })
                .catch(err => {
                    console.error('Error fetching favorites:', err);
                    renderFavorites([], []); // Show 'No Favorites' message if it's a fetch error
                });
        };

        const renderFavorites = (favorites, recipes = []) => {
            const container = document.getElementById('favorites-container');
            const noFavs = document.getElementById('no-favorites-message');

            if ((!favorites || favorites.length === 0) && (!recipes || recipes.length === 0)) {
                container.innerHTML = '';
                noFavs.classList.remove('d-none');
                return;
            }

            noFavs.classList.add('d-none');
            let html = '';

            if (favorites && favorites.length > 0) {
                html += '<h5 class="w-100 mb-2 mt-3 fw-bold"><i class="fa-solid fa-heart text-danger me-2"></i> Favorite Meals</h5>';
                html += favorites.map(meal => {
                    const type = meal.type;
                    const fallbackImage = `../public/img/${type}.webp`;
                    let imageUrl = meal.image || fallbackImage;

                    const isFavorite = true; // They are in favorites list
                    const favIcon = 'fa-solid fa-heart text-danger';
                    const favText = 'Remove from favorites';

                    return `
                        <div class="col-md-4 col-lg-3">
                            <div class="meal-card position-relative bg-light rounded-4 overflow-hidden border shadow-sm h-100">
                                <div class="dropdown position-absolute" style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                    <i class="fa-solid fa-ellipsis options-btn bg-white rounded-circle p-2 shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" style="opacity: 1;"></i>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="font-size: 0.8rem;">
                                        <li><a class="dropdown-item py-2 edit-meal-link" href="#" data-meal='${JSON.stringify(meal).replace(/'/g, "&apos;")}'><i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit meal</a></li>
                                        <li><a class="dropdown-item py-2 toggle-favorite-link" href="#" data-id="${meal.id}" data-favorite="1"><i class="${favIcon} me-2"></i> ${favText}</a></li>
                                        <li><a class="dropdown-item py-2 rate-meal-link" href="#" data-id="${meal.id}" data-name="${meal.name.replace(/'/g, "&apos;")}"><i class="fa-regular fa-star me-2 text-warning"></i> Rate meal</a></li>
                                        ${meal.recipe_id ? `<li><a class="dropdown-item py-2" href="recipe-details.php?id=${meal.recipe_id}"><i class="fa-solid fa-list-ol me-2 text-info"></i> View prep steps</a></li>` : ''}
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger delete-meal-link" href="#" data-id="${meal.id}"><i class="fa-solid fa-trash-can me-2"></i> Remove</a></li>
                                    </ul>
                                </div>

                                <div class="p-3 bg-white" style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 5; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                    <div class="meal-name text-dark fw-bold mb-1">${meal.name}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-capitalize fs-9">${meal.type}</span>
                                        ${meal.average_rating > 0 ? `
                                            <div class="d-flex align-items-center text-white fs-8">
                                                <i class="fa-solid fa-star star-rating me-1"></i>
                                                <span>${meal.average_rating}</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                
                                <img src="${imageUrl}" class="w-100 h-100" style="object-fit: cover; min-height: 200px;" alt="${meal.name}" onerror="this.src='${fallbackImage}'">
                            </div>
                        </div>
                    `;
                }).join('');
            }

            if (recipes && recipes.length > 0) {
                if (favorites && favorites.length > 0) {
                    html += '<div class="w-100 my-4 border-top"></div>';
                }
                html += '<h5 class="w-100 mb-2 mt-3 fw-bold"><i class="fa-solid fa-book-open text-primary me-2"></i> Family Recipes</h5>';
                html += recipes.map(recipe => {
                    let imagePath = '';
                    if (recipe.image_url) {
                        imagePath = recipe.image_url.startsWith('http') || recipe.image_url.startsWith('../') ? recipe.image_url : '../' + recipe.image_url;
                    }
                    const fallbackImage = `../public/img/dinner.webp`; // Or something default
                    const imageUrl = imagePath || fallbackImage;

                    return `
                        <div class="col-md-4 col-lg-3">
                            <div class="meal-card position-relative bg-light rounded-4 overflow-hidden border shadow-sm h-100">
                                <div class="dropdown position-absolute" style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                    <i class="fa-solid fa-ellipsis options-btn bg-white rounded-circle p-2 shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" style="opacity: 1;"></i>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="font-size: 0.8rem;">
                                        <li><a class="dropdown-item py-2" href="recipe-details.php?id=${recipe.id}"><i class="fa-solid fa-list-ol me-2 text-info"></i> View prep steps</a></li>
                                    </ul>
                                </div>

                                <div class="p-3 bg-white" style="position: absolute; bottom: 0; left: 0; right: 0; z-index: 5; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                    <div class="meal-name text-dark fw-bold mb-1">${recipe.name}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-capitalize fs-9">Recipe</span>
                                        ${recipe.calories ? `<span class="badge bg-info text-capitalize fs-9">${recipe.calories} kcal</span>` : ''}
                                    </div>
                                </div>
                                
                                <img src="${imageUrl}" class="w-100 h-100" style="object-fit: cover; min-height: 200px;" alt="${recipe.name}" onerror="this.src='${fallbackImage}'">
                            </div>
                        </div>
                    `;
                }).join('');
            }

            container.innerHTML = html;
        };

        const fetchMeals = (startDate, endDate) => {
            const formData = new FormData();
            formData.append('startDate', startDate);
            formData.append('endDate', endDate);
            formData.append('family_id', FAMILY_ID);

            fetch(`${API_PATH}meals.php?action=getByDateRange`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMeals(data.meals, startDate);
                    } else {
                        console.error('Error fetching meals:', data.message);
                    }
                })
                .catch(error => console.error('Fetch error:', error));
        };

        const renderMeals = (meals, startDate) => {
            const types = ['breakfast', 'lunch', 'dinner', 'snacks'];
            const start = new Date(startDate);

            types.forEach(type => {
                const row = document.getElementById(`${type}-row`);
                // Clear existing day cells except the first one
                const existingCells = row.querySelectorAll('.meal-day-cell');
                existingCells.forEach(cell => cell.remove());

                for (let i = 0; i < 7; i++) {
                    const dayDate = new Date(start);
                    dayDate.setDate(start.getDate() + i);
                    const dateStr = formatDate(dayDate);

                    const meal = meals.find(m => m.type === type && m.date === dateStr);
                    const cell = document.createElement('div');
                    cell.className = 'meal-day-cell';
                    if (i === 6) cell.style.borderRight = 'none';
                    if (type === 'snacks') cell.style.borderBottom = 'none';

                    if (meal) {
                        let imageUrl = meal.image;
                        const fallbackImage = `../public/img/${type}.webp`;

                        // Check if image is null, empty or not existing (client-side we can only check null/empty easily)
                        if (!imageUrl || imageUrl.trim() === "") {
                            imageUrl = fallbackImage;
                        }

                        const isFavorite = parseInt(meal.is_favorite) > 0;
                        const favIcon = isFavorite ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart';
                        const favText = isFavorite ? 'Remove from favorites' : 'Add to favorites';

                        cell.innerHTML = `
                            <div class="meal-card position-relative">
                                <div class="dropdown position-absolute" style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                    <i class="fa-solid fa-ellipsis options-btn" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="font-size: 0.8rem;">
                                        <li><a class="dropdown-item py-2 edit-meal-link" href="#" data-meal='${JSON.stringify(meal).replace(/'/g, "&apos;")}'><i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit meal</a></li>
                                        <li><a class="dropdown-item py-2 toggle-favorite-link" href="#" data-id="${meal.id}" data-favorite="${isFavorite ? 1 : 0}"><i class="${favIcon} me-2"></i> ${favText}</a></li>
                                        <li><a class="dropdown-item py-2 rate-meal-link" href="#" data-id="${meal.id}" data-name="${meal.name.replace(/'/g, "&apos;")}"><i class="fa-regular fa-star me-2 text-warning"></i> Rate meal</a></li>
                                        ${meal.recipe_id ? `<li><a class="dropdown-item py-2" href="recipe-details.php?id=${meal.recipe_id}"><i class="fa-solid fa-list-ol me-2 text-info"></i> View prep steps</a></li>` : ''}
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger delete-meal-link" href="#" data-id="${meal.id}"><i class="fa-solid fa-trash-can me-2"></i> Remove</a></li>
                                    </ul>
                                </div>
                                
                                ${meal.average_rating > 0 ? `
                                <div class="rating-badge">
                                    <i class="fa-solid fa-star star-rating"></i>
                                    <span>${meal.average_rating}</span>
                                    <span class="text-muted" style="font-size: 0.65rem">(${meal.total_ratings})</span>
                                </div>
                                ` : ''}

                                <div class="meal-name">${meal.name}</div>
                                <img src="${imageUrl}" alt="${meal.name}" onerror="this.src='${fallbackImage}'">
                            </div>
                        `;
                    } else {
                        cell.innerHTML = `
                            <div class="add-meal-placeholder cursor-pointer" data-date="${dateStr}" data-type="${type}">
                                <i class="fa-solid fa-plus fs-3 text-muted opacity-50"></i>
                            </div>
                        `;
                    }
                    row.appendChild(cell);
                }
            });
        };

        // Handle Placeholder Click (Event Delegation)
        document.addEventListener('click', (e) => {
            const placeholder = e.target.closest('.add-meal-placeholder');
            if (placeholder) {
                const date = placeholder.getAttribute('data-date');
                const type = placeholder.getAttribute('data-type');

                document.getElementById('mealForm').reset();
                document.getElementById('mealId').value = '';
                document.getElementById('mealDate').value = date;
                document.getElementById('mealType').value = type;

                document.getElementById('mealModalLabel').textContent = 'Add New Meal';
                document.getElementById('saveMealBtn').textContent = 'Save Meal';
                document.getElementById('mealRecipeId').value = '';
                toggleRecipeSearch.checked = typeof USE_RECIPES_IN_MEAL !== 'undefined' ? USE_RECIPES_IN_MEAL : true;
                toggleRecipeSearch.dispatchEvent(new Event('change'));
                imagePreview.classList.add('d-none');
                uploadPlaceholder.classList.remove('d-none');

                const mealModal = new bootstrap.Modal(document.getElementById('mealModal'));
                mealModal.show();
            }
        });

        // Event Listeners
        document.getElementById('btn-prev').addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() - 7);
            updateUI();
        });

        document.getElementById('btn-next').addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() + 7);
            updateUI();
        });

        document.getElementById('btn-today').addEventListener('click', () => {
            currentDate = new Date();
            updateUI();
        });

        // Modal Image Preview
        const mealImageInput = document.getElementById('mealImage');
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        imageUploadArea.addEventListener('click', () => mealImageInput.click());

        mealImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.querySelector('img').src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Toggle Recipe Search Logic
        const toggleRecipeSearch = document.getElementById('toggleRecipeSearch');

        toggleRecipeSearch.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('mealRecipeId').value = '';
            } else {
                document.getElementById('mealRecipeId').value = '';
            }
        });

        // Recipe Autocomplete Logic for Meal Name
        const mealNameInput = document.getElementById('mealName');
        const recipeSuggestions = document.getElementById('recipeSuggestions');
        let debounceTimer;

        mealNameInput.addEventListener('input', function() {
            if (!toggleRecipeSearch.checked) return;

            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                recipeSuggestions.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`../api/recipe.php?action=getRecipies&count=10&filter[search]=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success' && data.data && data.data.length > 0) {
                            recipeSuggestions.innerHTML = data.data.map(recipe => {
                                let imagePath = '';
                                if (recipe.image_url) {
                                    imagePath = recipe.image_url.startsWith('http') || recipe.image_url.startsWith('../') ? recipe.image_url : '../' + recipe.image_url;
                                }
                                const imgStr = imagePath ? imagePath : `../public/img/${document.getElementById('mealType').value || 'dinner'}.webp`;
                                return `
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 cursor-pointer recipe-suggestion-item" 
                                           data-name="${recipe.name.replace(/"/g, '&quot;')}" 
                                           data-image="${imagePath}"
                                           data-recipe-id="${recipe.id}">
                                            <img src="${imgStr}" class="rounded me-2" style="width: 32px; height: 32px; object-fit: cover;" onerror="this.src='../public/img/dinner.webp'">
                                            <div class="text-truncate flex-grow-1">${recipe.name}</div>
                                            <small class="text-muted ms-2">${recipe.category || ''}</small>
                                        </a>
                                    </li>
                                `;
                            }).join('');
                            recipeSuggestions.style.display = 'block';
                        } else {
                            recipeSuggestions.style.display = 'none';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching recipes:', err);
                        recipeSuggestions.style.display = 'none';
                    });
            }, 300);
        });

        // Handle Suggestion Selection
        document.addEventListener('click', (e) => {
            const suggestionItem = e.target.closest('.recipe-suggestion-item');
            if (suggestionItem) {
                e.preventDefault();
                const name = suggestionItem.getAttribute('data-name');
                const image = suggestionItem.getAttribute('data-image');
                const recipeId = suggestionItem.getAttribute('data-recipe-id');

                document.getElementById('mealName').value = name;
                document.getElementById('mealExistingImage').value = image || '';
                document.getElementById('mealRecipeId').value = recipeId || '';

                if (image) {
                    imagePreview.querySelector('img').src = image;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                } else {
                    imagePreview.classList.add('d-none');
                    uploadPlaceholder.classList.remove('d-none');
                }

                recipeSuggestions.style.display = 'none';
            } else if (!e.target.closest('#mealName') && !e.target.closest('#recipeSuggestions')) {
                // Hide suggestions when clicking outside
                if (recipeSuggestions) {
                    recipeSuggestions.style.display = 'none';
                }
            }
        });

        // Add Meal Button Click
        document.getElementById('add-meal-btn').addEventListener('click', () => {
            document.getElementById('mealForm').reset();
            document.getElementById('mealId').value = '';
            document.getElementById('mealModalLabel').textContent = 'Add New Meal';
            document.getElementById('saveMealBtn').textContent = 'Save Meal';
            document.getElementById('mealRecipeId').value = '';
            toggleRecipeSearch.checked = typeof USE_RECIPES_IN_MEAL !== 'undefined' ? USE_RECIPES_IN_MEAL : true;
            toggleRecipeSearch.dispatchEvent(new Event('change'));
            imagePreview.classList.add('d-none');
            uploadPlaceholder.classList.remove('d-none');
        });

        // Edit Meal Click (Event Delegation)
        document.addEventListener('click', (e) => {
            if (e.target.closest('.edit-meal-link')) {
                e.preventDefault();
                const meal = JSON.parse(e.target.closest('.edit-meal-link').getAttribute('data-meal'));

                document.getElementById('mealId').value = meal.id;
                document.getElementById('mealName').value = meal.name;
                document.getElementById('mealType').value = meal.type;
                document.getElementById('mealDate').value = meal.date;
                document.getElementById('mealExistingImage').value = meal.image || '';
                document.getElementById('mealRecipeId').value = meal.recipe_id || '';

                if (meal.recipe_id) {
                    toggleRecipeSearch.checked = true;
                    toggleRecipeSearch.dispatchEvent(new Event('change'));
                } else {
                    toggleRecipeSearch.checked = false;
                    toggleRecipeSearch.dispatchEvent(new Event('change'));
                }

                document.getElementById('mealModalLabel').textContent = 'Edit Meal';
                document.getElementById('saveMealBtn').textContent = 'Update Meal';

                if (meal.image) {
                    imagePreview.querySelector('img').src = meal.image;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                } else {
                    imagePreview.classList.add('d-none');
                    uploadPlaceholder.classList.remove('d-none');
                }

                const mealModal = new bootstrap.Modal(document.getElementById('mealModal'));
                mealModal.show();
            }
        });

        // Delete Meal Click
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-meal-link')) {
                e.preventDefault();
                const id = e.target.closest('.delete-meal-link').getAttribute('data-id');

                if (confirm('Are you sure you want to delete this meal?')) {
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch(`${API_PATH}meals.php?action=delete`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showAlert(data.message || 'Meal removed successfully', 'success');
                                updateUI();
                            } else {
                                showAlert(data.message || 'Error deleting meal', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Delete error:', err);
                            showAlert('Connection error. Please try again.', 'error');
                        });
                }
            }
        });

        // Form Submission
        document.getElementById('mealForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = document.getElementById('mealId').value;
            const action = id ? 'update' : 'add';

            fetch(`${API_PATH}meals.php?action=${action}`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('mealModal')).hide();
                        showAlert(data.message || 'Meal saved successfully', 'success');
                        updateUI();
                        this.reset();
                    } else {
                        showAlert(data.message || 'Error saving meal', 'error');
                    }
                })
                .catch(err => {
                    console.error('Save error:', err);
                    showAlert('Connection error. Please try again.', 'error');
                });
        });

        // Handle Tab Switching for Buttons
        const mealTabs = document.getElementById('mealTabs');
        const addMealBtn = document.getElementById('add-meal-btn');
        const newListBtn = document.getElementById('new-list-btn');

        mealTabs.addEventListener('shown.bs.tab', function(event) {
            if (event.target.id === 'groceries-tab') {
                addMealBtn.classList.add('d-none');
                newListBtn.classList.remove('d-none');
            } else if (event.target.id === 'mymeals-tab') {
                addMealBtn.classList.add('d-none');
                newListBtn.classList.add('d-none');
                fetchFavorites();
            } else {
                addMealBtn.classList.remove('d-none');
                newListBtn.classList.add('d-none');
            }
        });

        // New List Button Click - Set Defaults
        newListBtn.addEventListener('click', () => {
            document.getElementById('listIdInput').value = ''; // Reset ID for new list
            const range = getWeekRange(currentDate);
            const startStr = formatDate(range.start);
            const endStr = formatDate(range.end);

            const options = {
                month: 'short',
                day: 'numeric'
            };
            const defaultTitle = `Groceries: ${range.start.toLocaleDateString('en-US', options)} - ${range.end.toLocaleDateString('en-US', options)}`;

            document.getElementById('listTitle').value = defaultTitle;
            document.getElementById('listStartDate').value = startStr;
            document.getElementById('listEndDate').value = endStr;
            document.getElementById('listNotes').value = '';

            // Reset items to one empty row
            resetGroceryItemsContainer();
        });

        const resetGroceryItemsContainer = () => {
            const itemsContainer = document.getElementById('grocery-items-container');
            let categoryOptions = groceryCategories.map(cat =>
                `<option value="${cat.id}">${cat.name}</option>`
            ).join('');

            itemsContainer.innerHTML = `
                <div class="grocery-item-row">
                    <div class="input-group border rounded-3 overflow-hidden shadow-sm mb-2">
                        <select class="form-select border-0 px-1 py-2 text-dark fw-medium border-end fs-8" name="categories[]" style="max-width: 80px; min-width: 80px;">
                            ${categoryOptions}
                        </select>
                        <input type="text" class="form-control border-0 px-2 py-2 text-dark fw-medium fs-8" name="items[]" placeholder="Item..." required>
                        <input type="number" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8 text-center" name="quantities[]" placeholder="Qty" style="max-width: 50px; min-width: 50px;">
                        <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8" name="units[]" placeholder="Unit" style="max-width: 60px; min-width: 60px;">
                        <button class="btn btn-white border-0 text-danger remove-item-btn border-start px-2" type="button"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            `;
        };

        // Add Grocery Item logic
        const addItemBtn = document.getElementById('add-item-btn');
        const itemsContainer = document.getElementById('grocery-items-container');

        if (addItemBtn) {
            addItemBtn.addEventListener('click', function() {
                const itemRow = document.createElement('div');
                itemRow.className = 'grocery-item-row mb-2';

                let categoryOptions = groceryCategories.map(cat =>
                    `<option value="${cat.id}">${cat.name}</option>`
                ).join('');

                itemRow.innerHTML = `
                    <div class="input-group border rounded-3 overflow-hidden shadow-sm mb-2">
                        <select class="form-select border-0 px-1 py-2 text-dark fw-medium border-end fs-8" name="categories[]" style="max-width: 80px; min-width: 80px;">
                            ${categoryOptions}
                        </select>
                        <input type="text" class="form-control border-0 px-2 py-2 text-dark fw-medium fs-8" name="items[]" placeholder="Item..." required>
                        <input type="number" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8 text-center" name="quantities[]" placeholder="Qty" style="max-width: 50px; min-width: 50px;">
                        <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8" name="units[]" placeholder="Unit" style="max-width: 60px; min-width: 60px;">
                        <button class="btn btn-white border-0 text-danger remove-item-btn border-start px-2" type="button"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                `;
                itemsContainer.appendChild(itemRow);
            });
        }

        // Remove Grocery Item logic (Create Modal)
        if (itemsContainer) {
            itemsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const rows = itemsContainer.querySelectorAll('.grocery-item-row');
                    if (rows.length > 1) {
                        e.target.closest('.grocery-item-row').remove();
                    }
                }
            });
        }

        // Remove Grocery Item logic (Edit Modal)
        const editItemsContainer = document.getElementById('edit-grocery-items-container');
        if (editItemsContainer) {
            editItemsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const rows = editItemsContainer.querySelectorAll('.grocery-item-row');
                    if (rows.length > 1) {
                        e.target.closest('.grocery-item-row').remove();
                    }
                }
            });
        }

        // Handle Grocery List Form Submission (CREATE)
        document.getElementById('groceryListForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const items = [];

            this.querySelectorAll('.grocery-item-row').forEach(row => {
                const name = row.querySelector('input[name="items[]"]').value.trim();
                if (name !== "") {
                    items.push({
                        name: name,
                        grocery_category_id: row.querySelector('select[name="categories[]"]').value,
                        quantity: row.querySelector('input[name="quantities[]"]').value || null,
                        unit: row.querySelector('input[name="units[]"]').value || null,
                        is_complete: row.querySelector('input[name="is_complete[]"]')?.value || 0
                    });
                }
            });

            const payload = {
                action: 'add',
                family_id: FAMILY_ID,
                title: formData.get('title'),
                week_start_date: formData.get('start_date'),
                week_end_date: formData.get('end_date'),
                notes: formData.get('notes'),
                items: items
            };

            fetch(`../api/grocery.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('groceryListModal')).hide();
                        showAlert(data.message || 'Grocery list created successfully', 'success');
                        this.reset();
                        updateUI();
                    } else {
                        showAlert(data.message || 'Error creating grocery list', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to connect to the server', 'danger');
                });
        });

        // Handle Grocery List Form Submission (EDIT)
        document.getElementById('editGroceryListForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const items = [];

            this.querySelectorAll('.grocery-item-row').forEach(row => {
                const name = row.querySelector('input[name="items[]"]').value.trim();
                if (name !== "") {
                    items.push({
                        name: name,
                        grocery_category_id: row.querySelector('select[name="categories[]"]').value,
                        quantity: row.querySelector('input[name="quantities[]"]').value || null,
                        unit: row.querySelector('input[name="units[]"]').value || null,
                        is_complete: row.querySelector('input[name="is_complete[]"]')?.value || 0
                    });
                }
            });

            const listId = document.getElementById('editListIdInput').value;
            const payload = {
                action: 'update',
                id: listId,
                family_id: FAMILY_ID,
                title: formData.get('title'),
                week_start_date: formData.get('start_date'),
                week_end_date: formData.get('end_date'),
                notes: formData.get('notes'),
                items: items
            };

            fetch(`../api/grocery.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('editGroceryListModal')).hide();
                        showAlert(data.message || 'Grocery list updated successfully', 'success');
                        updateUI();
                    } else {
                        showAlert(data.message || 'Error updating grocery list', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to connect to the server', 'danger');
                });
        });

        // Toggle Item Status logic
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('toggle-item-checkbox')) {
                const itemId = e.target.getAttribute('data-id');
                const isComplete = e.target.checked;

                fetch(`../api/grocery.php?action=toggleItem`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: itemId,
                            is_complete: isComplete
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Refresh UI to sync progress bars and counts
                            updateUI();
                        } else {
                            e.target.checked = !isComplete; // Revert if failed
                            showAlert(data.message || 'Error updating item status', 'danger');
                        }
                    })
                    .catch(err => {
                        console.error('Toggle error:', err);
                        e.target.checked = !isComplete; // Revert if failed
                        showAlert('Connection error', 'danger');
                    });
            }
        });

        // Toggle Favorite Click
        document.addEventListener('click', (e) => {
            const link = e.target.closest('.toggle-favorite-link');
            if (link) {
                e.preventDefault();
                const mealId = link.getAttribute('data-id');
                const isFavorite = link.getAttribute('data-favorite') === '1';
                const action = isFavorite ? 'remove' : 'add';

                const formData = new FormData();
                formData.append('meal_id', mealId);

                fetch(`../api/favorite.php?action=${action}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showAlert(data.message, 'success');
                            updateUI();
                        } else {
                            showAlert(data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Favorite error:', err);
                        showAlert('Connection error', 'error');
                    });
            }
        });

        // Rating Modal Logic
        const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
        const ratingStars = document.querySelectorAll('#ratingStarsContainer i');

        document.addEventListener('click', (e) => {
            const link = e.target.closest('.rate-meal-link');
            if (link) {
                e.preventDefault();
                const mealId = link.getAttribute('data-id');
                const mealName = link.getAttribute('data-name');

                document.getElementById('ratingMealId').value = mealId;
                document.getElementById('ratingMealName').textContent = mealName;
                document.getElementById('ratingValue').value = 0;

                // Reset stars
                ratingStars.forEach(s => s.classList.remove('active'));

                ratingModal.show();
            }
        });

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                document.getElementById('ratingValue').value = val;

                ratingStars.forEach(s => {
                    if (parseInt(s.getAttribute('data-value')) <= parseInt(val)) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });

        document.getElementById('submitRatingBtn').addEventListener('click', () => {
            const mealId = document.getElementById('ratingMealId').value;
            const rating = document.getElementById('ratingValue').value;

            if (rating == 0) {
                showAlert('Please select a rating', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('meal_id', mealId);
            formData.append('rating', rating);

            fetch(`../api/rating.php?action=save`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        ratingModal.hide();
                        showAlert(data.message, 'success');
                        updateUI();
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Rating error:', err);
                    showAlert('Connection error', 'error');
                });
        });

        // Initialize UI
        updateUI();
    });
</script>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Rate Meal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="ratingMealName" class="mb-3 fw-bold"></h6>
                <div class="rating-stars-input mb-3" id="ratingStarsContainer">
                    <i class="fa-solid fa-star" data-value="1"></i>
                    <i class="fa-solid fa-star" data-value="2"></i>
                    <i class="fa-solid fa-star" data-value="3"></i>
                    <i class="fa-solid fa-star" data-value="4"></i>
                    <i class="fa-solid fa-star" data-value="5"></i>
                </div>
                <input type="hidden" id="ratingMealId">
                <input type="hidden" id="ratingValue" value="0">
                <button type="button" class="btn btn-primary w-100 rounded-3 fw-bold" id="submitRatingBtn">Submit Rating</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Meal Modal -->
<div class="modal fade" id="mealModal" tabindex="-1" aria-labelledby="mealModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="mealModalLabel">Add New Meal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="mealForm" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" id="mealId" name="id">
                    <input type="hidden" id="mealFamilyId" name="family_id" value="<?php echo $family_id; ?>">
                    <input type="hidden" id="mealExistingImage" name="image">
                    <input type="hidden" id="mealRecipeId" name="recipe_id">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold text-dark fs-7">Search Recipes</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="toggleRecipeSearch" <?php echo $use_recipes_in_meal ? 'checked' : ''; ?>>
                        </div>
                    </div>

                    <div class="mb-4 position-relative">
                        <label for="mealName" class="form-label fw-semibold text-dark fs-7">Meal Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-utensils"></i></span>
                            <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="mealName" name="name" placeholder="e.g. Grilled Salmon with Asparagus" autocomplete="off" required>
                        </div>
                        <ul id="recipeSuggestions" class="dropdown-menu w-100 shadow-sm position-absolute" style="max-height: 200px; overflow-y: auto; top: 100%; z-index: 1050; display: none;"></ul>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="mealType" class="form-label fw-semibold text-dark fs-7">Meal Type <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-layer-group"></i></span>
                                <select class="form-select border-0 px-1 py-2 text-dark fw-medium" id="mealType" name="type" required>
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch">Lunch</option>
                                    <option value="dinner">Dinner</option>
                                    <option value="snacks">Snacks</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="mealDate" class="form-label fw-semibold text-dark fs-7">Date <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="mealDate" name="date" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0" id="imageUploadContainer">
                        <label for="mealImage" class="form-label fw-semibold text-dark fs-7">Meal Image</label>
                        <div class="border rounded-3 p-3 text-center bg-light bg-opacity-50 border-dashed cursor-pointer" id="imageUploadArea">
                            <input type="file" id="mealImage" name="image" class="d-none" accept="image/*">
                            <div id="imagePreview" class="mb-2 d-none">
                                <img src="" alt="Preview" class="img-fluid rounded-3 shadow-sm" style="max-height: 150px;">
                            </div>
                            <div id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up fs-2 text-primary mb-2"></i>
                                <p class="mb-0 fs-8 text-muted">Click to upload or drag & drop</p>
                                <p class="mb-0 fs-9 text-muted opacity-75">JPG, PNG or WEBP (Max 2MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" id="saveMealBtn">Save Meal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create New Grocery List Modal -->
<div class="modal fade" id="groceryListModal" tabindex="-1" aria-labelledby="groceryListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="groceryListModalLabel">Create New Grocery List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="groceryListForm">
                <input type="hidden" name="list_id" id="listIdInput">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="listTitle" class="form-label fw-semibold text-dark fs-7">List Title <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="listTitle" name="title" placeholder="e.g. Weekly Groceries" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="listStartDate" class="form-label fw-semibold text-dark fs-7">Start Date <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="listStartDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="listEndDate" class="form-label fw-semibold text-dark fs-7">End Date <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="listEndDate" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="listNotes" class="form-label fw-semibold text-dark fs-7">Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-note-sticky"></i></span>
                            <textarea class="form-control border-0 px-1 py-2 text-dark fw-medium" id="listNotes" name="notes" placeholder="Any special instructions..." rows="1"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold text-dark fs-7 mb-0">Items</label>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="add-item-btn">
                                <i class="fa-solid fa-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div id="grocery-items-container">
                            <!-- Dynamic items will be added here -->
                            <div class="grocery-item-row">
                                <div class="input-group border rounded-3 overflow-hidden shadow-sm mb-2">
                                    <select class="form-select border-0 px-1 py-2 text-dark fw-medium border-end fs-8" name="categories[]" style="max-width: 80px; min-width: 80px;">
                                        <?php
                                        require_once $path_prefix . 'classes/GroceryCategories.php';
                                        $cats = GroceryCategories::getByFamily($family_id)['data'] ?? [];
                                        foreach ($cats as $cat) {
                                            echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="text" class="form-control border-0 px-2 py-2 text-dark fw-medium fs-8" name="items[]" placeholder="Item..." required>
                                    <input type="number" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8 text-center" name="quantities[]" placeholder="Qty" style="max-width: 50px; min-width: 50px;">
                                    <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium border-start fs-8" name="units[]" placeholder="Unit" style="max-width: 60px; min-width: 60px;">
                                    <button class="btn btn-white border-0 text-danger remove-item-btn border-start px-2" type="button"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" id="saveListBtn">Create List</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Grocery List Modal -->
<div class="modal fade" id="editGroceryListModal" tabindex="-1" aria-labelledby="editGroceryListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editGroceryListModalLabel">Edit Grocery List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGroceryListForm">
                <input type="hidden" name="list_id" id="editListIdInput">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label for="editListTitle" class="form-label fw-semibold text-dark fs-7">List Title <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="editListTitle" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editListStartDate" class="form-label fw-semibold text-dark fs-7">Start Date <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="editListStartDate" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editListEndDate" class="form-label fw-semibold text-dark fs-7">End Date <span class="text-danger">*</span></label>
                            <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="editListEndDate" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editListNotes" class="form-label fw-semibold text-dark fs-7">Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-note-sticky"></i></span>
                            <textarea class="form-control border-0 px-1 py-2 text-dark fw-medium" id="editListNotes" name="notes" placeholder="Any special instructions..." rows="1"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold text-dark fs-7 mb-0">Items</label>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="edit-add-item-btn">
                            <i class="fa-solid fa-plus me-1"></i> Add Item
                        </button>
                    </div>

                    <div id="edit-grocery-items-container">
                        <!-- Dynamic items will be added here -->
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>
