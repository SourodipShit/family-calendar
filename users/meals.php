<?php
$path_prefix = "../";
$page_title = "Meals";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';

$family_id = $_SESSION['user']['families'][0]['id'] ?? 1;
?>
<script>
    const FAMILY_ID = <?php echo $family_id; ?>;
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
</style>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4" style="max-width: 1400px; margin: 0 auto;">

        <div class="row g-4">
            <div class="col-12">
                <!-- Family Members Row -->
                <div class="d-flex flex-wrap align-items-center gap-0 mb-4 family-members-list">
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
                                id="date-picker-btn"><i class="fa-regular fa-calendar me-2"></i> May 12 – May
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
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto">Sat<br><span
                                        class="text-muted fw-normal fs-8">May 17</span></div>
                                <div class="meal-day-cell border-bottom-0 py-3 h-auto"
                                    style="border-right: none;">Sun<br><span
                                        class="text-muted fw-normal fs-8">May 18</span></div>
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
                            <div class="bg-white rounded d-inline-block p-2 me-3 fs-5 shadow-sm text-primary">💡
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
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm">
                            <i class="fa-solid fa-utensils fa-4x mb-4 opacity-25"></i>
                            <h3>Your Saved Meals</h3>
                            <p>Browse your favorite recipes and custom meals here.</p>
                            <button class="btn btn-primary mt-3 rounded-3 px-4">Browse Recipes</button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="groceries" role="tabpanel">
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm">
                            <i class="fa-solid fa-basket-shopping fa-4x mb-4 opacity-25"></i>
                            <h3>Full Grocery List</h3>
                            <p>Manage your entire shopping list for the week.</p>
                            <button class="btn btn-primary mt-3 rounded-3 px-4">Print List</button>
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
                        <div class="text-muted small mb-3">12 items</div>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 60%;"
                                    aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="small text-muted fw-bold">60%</div>
                        </div>

                        <div class="grocery-category">Produce</div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox" checked>
                            <span class="item-name">Bananas</span>
                        </div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox">
                            <span class="item-name">Avocados</span>
                        </div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox">
                            <span class="item-name">Spinach</span>
                        </div>

                        <div class="grocery-category">Dairy</div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox" checked>
                            <span class="item-name">Greek Yogurt</span>
                        </div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox">
                            <span class="item-name">Milk</span>
                        </div>

                        <div class="grocery-category">Pantry</div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox" checked>
                            <span class="item-name">Oats</span>
                        </div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox">
                            <span class="item-name">Pasta</span>
                        </div>
                        <div class="grocery-item">
                            <input class="form-check-input" type="checkbox">
                            <span class="item-name">Rice</span>
                        </div>

                        <!-- Hidden items that expand -->
                        <div class="collapse" id="moreGroceryItems">
                            <div class="grocery-item">
                                <input class="form-check-input" type="checkbox">
                                <span class="item-name">Blueberries</span>
                            </div>
                            <div class="grocery-item">
                                <input class="form-check-input" type="checkbox" checked>
                                <span class="item-name">Chicken Breast</span>
                            </div>
                            <div class="grocery-item">
                                <input class="form-check-input" type="checkbox">
                                <span class="item-name">Bell Peppers</span>
                            </div>
                            <div class="grocery-item">
                                <input class="form-check-input" type="checkbox">
                                <span class="item-name">Quinoa</span>
                            </div>
                            <div class="grocery-item">
                                <input class="form-check-input" type="checkbox">
                                <span class="item-name">Almond Milk</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <a href="#moreGroceryItems"
                                class="text-primary text-decoration-none fw-bold small d-flex align-items-center justify-content-between"
                                data-bs-toggle="collapse" role="button" aria-expanded="false"
                                aria-controls="moreGroceryItems" id="toggleFullList">
                                <span class="button-text">View Full List</span> <i
                                    class="fa-solid fa-chevron-down fs-8 ms-2 transition-transform"></i>
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
    document.addEventListener('DOMContentLoaded', function() {
        let currentDate = new Date(); // Start with today

        // Helper to format date as YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        // Helper to get week range (Monday to Sunday)
        const getWeekRange = (date) => {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday
            const monday = new Date(d.setDate(diff));
            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);
            return {
                start: monday,
                end: sunday
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
            const dateRangeText = `${range.start.toLocaleDateString('en-US', options)} – ${range.end.toLocaleDateString('en-US', options)}, ${range.end.toLocaleDateString('en-US', yearOptions)}`;
            document.getElementById('date-picker-btn').innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${dateRangeText}`;

            // Update Planner Header
            const headerCells = document.querySelectorAll('#planner-header .meal-day-cell');
            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            headerCells.forEach((cell, index) => {
                const dayDate = new Date(range.start);
                dayDate.setDate(range.start.getDate() + index);
                cell.innerHTML = `${days[index]}<br><span class="text-muted fw-normal fs-8">${dayDate.toLocaleDateString('en-US', options)}</span>`;
            });

            fetchMeals(startStr, endStr);
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

                        cell.innerHTML = `
                            <div class="meal-card">
                                <div class="dropdown position-absolute" style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                    <i class="fa-solid fa-ellipsis options-btn" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="font-size: 0.8rem;">
                                        <li><a class="dropdown-item py-2 edit-meal-link" href="#" data-meal='${JSON.stringify(meal).replace(/'/g, "&apos;")}'><i class="fa-solid fa-pen-to-square me-2 text-muted"></i> Edit meal</a></li>
                                        <li><a class="dropdown-item py-2 text-danger delete-meal-link" href="#" data-id="${meal.id}"><i class="fa-solid fa-trash-can me-2"></i> Remove</a></li>
                                    </ul>
                                </div>
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

        // Add Meal Button Click
        document.getElementById('add-meal-btn').addEventListener('click', () => {
            document.getElementById('mealForm').reset();
            document.getElementById('mealId').value = '';
            document.getElementById('mealModalLabel').textContent = 'Add New Meal';
            document.getElementById('saveMealBtn').textContent = 'Save Meal';
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

        // Initialize UI
        updateUI();
    });
</script>

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

                    <div class="mb-4">
                        <label for="mealName" class="form-label fw-semibold text-dark fs-7">Meal Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-utensils"></i></span>
                            <input type="text" class="form-control border-0 px-1 py-2 text-dark fw-medium" id="mealName" name="name" placeholder="e.g. Grilled Salmon with Asparagus" required>
                        </div>
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

                    <div class="mb-0">
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

<?php include $path_prefix . 'components/footer.php'; ?>