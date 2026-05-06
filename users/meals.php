<?php
$path_prefix = "../";
$page_title = "Meals";
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
                        <button
                            class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm d-flex align-items-center">
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
                            <div
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
                            <div class="meal-planner-grid meals-card-div">
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
                                <div class="meal-day-cell">
                                    <div class="meal-card">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Oatmeal with Berries</div>
                                        <img src="https://images.unsplash.com/photo-1517673132405-a56a62b18caf?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Oatmeal" data-member="dad">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="mom">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Greek Yogurt with Granola</div>
                                        <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Yogurt">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="emma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Avocado Toast</div>
                                        <img src="https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Toast">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="liam">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Pancakes & Fruit</div>
                                        <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7bb7445?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Pancakes">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="ava">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Scrambled Eggs</div>
                                        <img src="https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Eggs">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="grandma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Smoothie Bowl</div>
                                        <img src="https://images.unsplash.com/photo-1546039907-7fa05f864c02?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Smoothie">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-right: none;">
                                    <div class="meal-card" data-member="grandpa">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Breakfast Burrito</div>
                                        <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Burrito">
                                    </div>
                                </div>
                            </div>

                            <!-- Lunch Row -->
                            <div class="meal-planner-grid">
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
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="dad">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Grilled Cheese & Tomato Soup</div>
                                        <img src="https://images.unsplash.com/photo-1547592166-23ac45744acd?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Soup">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="mom">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Chicken Caesar Salad</div>
                                        <img src="https://images.unsplash.com/photo-1550304943-4f24f54ddde9?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Salad">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="emma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Turkey Wrap</div>
                                        <img src="https://images.unsplash.com/photo-1533470192478-9993ca0a8308?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Wrap">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="liam">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Pasta Salad</div>
                                        <img src="https://images.unsplash.com/photo-1543339308-43e59d6b73a6?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Pasta">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="ava">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Quesadilla & Salsa</div>
                                        <img src="https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Quesadilla">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="grandma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Leftovers</div>
                                        <img src="https://images.unsplash.com/photo-1563379091339-03b21bc4a4f8?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Leftovers">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-right: none;">
                                    <div class="add-meal-placeholder">
                                        <i class="fa-solid fa-plus fs-3"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Dinner Row -->
                            <div class="meal-planner-grid">
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
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="dad">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Baked Salmon & Veggies</div>
                                        <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Salmon">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="mom">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Taco Night</div>
                                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Tacos">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="emma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Lemon Chicken & Rice</div>
                                        <img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Chicken">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="liam">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Spaghetti & Meatballs</div>
                                        <img src="https://images.unsplash.com/photo-1551183053-bf91a1d81141?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Spaghetti">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="ava">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Stir Fry & Rice</div>
                                        <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Stir Fry">
                                    </div>
                                </div>
                                <div class="meal-day-cell">
                                    <div class="meal-card" data-member="grandma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Homemade Pizza</div>
                                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Pizza">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-right: none;">
                                    <div class="add-meal-placeholder">
                                        <i class="fa-solid fa-plus fs-3"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Snacks Row -->
                            <div class="meal-planner-grid">
                                <div class="meal-time-cell" style="border-bottom: none;">

                                    <span class="iu-breakfast dinner2s">
                                        <i class="fa-solid fa-apple-whole text-warning mb-2 fs-4"></i>
                                    </span>
                                    <div class="fw-bold fs-8">Snacks</div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Apple & Peanut Butter</div>
                                        <img src="https://images.unsplash.com/photo-1576675466969-38eeae4b41f6?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Apple">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card" data-member="dad">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Carrot Sticks & Hummus</div>
                                        <img src="https://images.unsplash.com/photo-1541533338070-2ec2b5a3ccc4?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Carrot">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card" data-member="emma">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Banana Smoothie</div>
                                        <img src="https://images.unsplash.com/photo-1525385133336-24426fe13bc7?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Smoothie">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card" data-member="liam">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Cheese & Crackers</div>
                                        <img src="https://images.unsplash.com/photo-1541280910158-c4e14f9c94a3?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Cheese">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card" data-member="ava">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Trail Mix</div>
                                        <img src="https://images.unsplash.com/photo-1514944288352-fffbb99f0bdf?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Trail Mix">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-bottom: none;">
                                    <div class="meal-card" data-member="mom">
                                        <div class="dropdown position-absolute"
                                            style="top: 0.5rem; right: 0.5rem; z-index: 10;">
                                            <i class="fa-solid fa-ellipsis options-btn"
                                                data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                                style="font-size: 0.8rem;">
                                                <li><a class="dropdown-item py-2" href="#"><i
                                                            class="fa-solid fa-pen-to-square me-2 text-muted"></i>
                                                        Edit meal</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i
                                                            class="fa-solid fa-trash-can me-2"></i> Remove</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="meal-name">Yogurt & Berries</div>
                                        <img src="https://images.unsplash.com/photo-1488477181946-6428a0291777?q=80&w=200&h=150&auto=format&fit=crop"
                                            alt="Yogurt">
                                    </div>
                                </div>
                                <div class="meal-day-cell" style="border-right: none; border-bottom: none;">
                                    <div class="add-meal-placeholder">
                                        <i class="fa-solid fa-plus fs-3"></i>
                                    </div>
                                </div>
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

<?php include $path_prefix . 'components/footer.php'; ?>
