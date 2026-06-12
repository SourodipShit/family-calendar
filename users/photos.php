<?php
$path_prefix = "../";
$page_title = "Family Photos";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1600px; margin: 0 auto;">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Family Photos</h3>
                <p class="text-muted mb-0">Capture and share your favorite moments.</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#approvePhotosModal">
                    <i class="fa-solid fa-check-to-slot me-2"></i> Approve Photos
                    <span class="badge rounded-pill bg-danger text-white ms-2" style="display: none;">0</span>
                </button>
                <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                    <i class="fa-solid fa-plus me-2"></i> Upload Photo
                </button>
            </div>
        </div>

        <!-- Photos Feed -->
        <div id="photosFeedContainer">
            <!-- Dynamically loaded -->
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="uploadPhotoModalLabel">Upload Photo</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="border rounded-4 border-dashed p-5 text-center bg-light mb-3 cursor-pointer" style="border-style: dashed !important; border-width: 2px !important;">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-primary mb-3"></i>
                    <h5>Drag and drop your photos here</h5>
                    <p class="text-muted small">or click to browse from your computer</p>
                    <input type="file" class="d-none" id="fileUpload" multiple accept="image/*">
                    <button class="btn btn-outline-primary mt-2 rounded-3" onclick="document.getElementById('fileUpload').click()">Browse Files</button>
                </div>
                <!-- Album selection removed as requested -->
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark shadow-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm" id="btnSubmitUpload">Upload Photos</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Photos Modal -->
<div class="modal fade" id="approvePhotosModal" tabindex="-1" aria-labelledby="approvePhotosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bold" id="approvePhotosModalLabel">Pending Photos <span class="badge bg-danger rounded-pill fs-7 align-middle ms-2" id="pendingCountBadge">0</span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 p-3 bg-light rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark fw-bold fs-7"><i class="fa-solid fa-hard-drive me-2 text-primary"></i>Storage Used</span>
                        <span class="text-muted fs-7 fw-medium">200 MB / <span class="text-dark">500 MB</span></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <p class="text-muted mb-3 fs-7">The following photos require your approval before they are visible to the rest of the family.</p>

                <div class="row g-3" id="pendingPhotosContainer">
                    <!-- Dynamically populated by JS -->
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-medium rounded-3" data-bs-dismiss="modal">Close</button>
                <div id="bulkActionsContainer" class="d-none">
                    <button type="button" class="btn btn-outline-danger px-4 py-2 fw-medium rounded-3 shadow-sm me-2">Reject Selected</button>
                    <button type="button" class="btn btn-success px-4 py-2 fw-medium rounded-3 shadow-sm">Approve Selected</button>
                </div>
                <div id="defaultActionContainer">
                    <button type="button" class="btn btn-primary px-4 py-2 fw-medium rounded-3 shadow-sm">Approve All</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Photo Lightbox -->
<div id="photoLightbox" class="lightbox-container" aria-hidden="true">
    <!-- Top toolbar -->
    <div class="lightbox-toolbar">
        <button class="lightbox-btn" id="lightboxClose" title="Close (Esc)"><i class="fa-solid fa-arrow-left"></i></button>
        <div class="lightbox-info">
            <span id="lightboxDate" class="lightbox-date"></span>
            <span id="lightboxCaption" class="lightbox-caption-text"></span>
        </div>
        <div class="lightbox-actions">
            <button class="lightbox-btn" title="Share"><i class="fa-solid fa-share-nodes"></i></button>
            <button class="lightbox-btn" title="Info"><i class="fa-solid fa-circle-info"></i></button>
            <button class="lightbox-btn" title="More options"><i class="fa-solid fa-ellipsis-vertical"></i></button>
        </div>
    </div>

    <!-- Image Area -->
    <div class="lightbox-content-area" id="lightboxContentArea">
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev"><i class="fa-solid fa-chevron-left"></i></button>

        <div class="lightbox-image-wrapper">
            <div class="lightbox-loader" id="lightboxLoader">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <img id="lightboxImage" src="" alt="Fullscreen Photo" class="lightbox-img">
        </div>

        <button class="lightbox-nav lightbox-next" id="lightboxNext"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
</div>

<style>
    /* Grid Layout similar to Google Photos */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        grid-auto-rows: 220px;
        gap: 8px;
    }

    .photo-item-wide {
        grid-column: span 2;
    }

    .photo-item {
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        background-color: #f0f0f0;
    }

    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .photo-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
        color: white;
        padding: 30px 15px 12px;
        font-size: 0.85rem;
        font-weight: 500;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .photo-item:hover img {
        transform: scale(1.03);
    }

    .photo-item:hover .photo-overlay {
        opacity: 1;
    }

    /* Lightbox Styles */
    .lightbox-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #000;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .lightbox-container.active {
        opacity: 1;
        visibility: visible;
    }

    /* Toolbar */
    .lightbox-toolbar {
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 10001;
        transition: opacity 0.3s ease;
    }

    .lightbox-container.idle .lightbox-toolbar {
        opacity: 0;
        pointer-events: none;
    }

    .lightbox-info {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        margin-left: 20px;
        color: white;
    }

    .lightbox-date {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .lightbox-caption-text {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .lightbox-actions {
        display: flex;
        gap: 8px;
    }

    .lightbox-btn {
        background: transparent;
        color: white;
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background 0.2s;
    }

    .lightbox-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Content Area */
    .lightbox-content-area {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .lightbox-image-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .lightbox-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .lightbox-loader.active {
        opacity: 1;
    }

    .lightbox-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        z-index: 2;
        opacity: 0;
        transform: scale(0.98);
        transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        user-select: none;
    }

    .lightbox-img.loaded {
        opacity: 1;
        transform: scale(1);
    }

    /* Nav Buttons */
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: none;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background 0.2s, opacity 0.3s ease, transform 0.2s ease;
        z-index: 10000;
    }

    .lightbox-container.idle .lightbox-nav {
        opacity: 0;
        pointer-events: none;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-50%) scale(1.05);
    }

    .lightbox-prev {
        left: 20px;
    }

    .lightbox-next {
        right: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photosFeedContainer = document.getElementById('photosFeedContainer');
        const pendingPhotosContainer = document.getElementById('pendingPhotosContainer');
        const fileUpload = document.getElementById('fileUpload');
        const btnSubmitUpload = document.getElementById('btnSubmitUpload');
        const pendingCountBadge = document.getElementById('pendingCountBadge');

        const lightbox = document.getElementById('photoLightbox');
        const lightboxImg = document.getElementById('lightboxImage');
        const lightboxCaption = document.getElementById('lightboxCaption');
        const lightboxDate = document.getElementById('lightboxDate');
        const lightboxLoader = document.getElementById('lightboxLoader');
        const btnClose = document.getElementById('lightboxClose');
        const btnPrev = document.getElementById('lightboxPrev');
        const btnNext = document.getElementById('lightboxNext');
        const contentArea = document.getElementById('lightboxContentArea');

        let currentIndex = 0;
        let photosData = [];
        let idleTimer = null;
        let touchStartX = 0;
        let touchEndX = 0;

        // Load data on start
        loadPhotosFeed();
        loadPendingPhotos();

        function formatDate(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        }

        async function loadPhotosFeed() {
            try {
                const res = await fetch('../api/photos.php?action=getByFamily');
                const result = await res.json();
                if (result.status === 'success') {
                    renderPhotosFeed(result.data);
                }
            } catch (err) {
                console.error(err);
            }
        }

        function renderPhotosFeed(photos) {
            photosFeedContainer.innerHTML = '';
            photosData = [];
            if (photos.length === 0) {
                photosFeedContainer.innerHTML = '<p class="text-muted text-center py-5">No photos available.</p>';
                return;
            }

            // Group by date
            const groups = {};
            photos.forEach(p => {
                const d = formatDate(p.created_at);
                if (!groups[d]) groups[d] = [];
                groups[d].push(p);
            });

            let globalIndex = 0;
            for (const dateText in groups) {
                const groupDiv = document.createElement('div');
                groupDiv.className = 'photo-date-group mb-5';

                const title = document.createElement('h5');
                title.className = 'fw-medium text-dark mb-3 ps-1 fs-6';
                title.textContent = dateText;
                groupDiv.appendChild(title);

                const grid = document.createElement('div');
                grid.className = 'photo-grid';

                groups[dateText].forEach((photo, i) => {
                    // Populate Lightbox array
                    const itemIndex = globalIndex++;
                    photosData.push({
                        src: photo.photo,
                        alt: 'Photo by ' + (photo.user ? photo.user.name : 'Unknown'),
                        caption: 'Uploaded by ' + (photo.user ? photo.user.name : 'Unknown'),
                        date: dateText
                    });

                    const item = document.createElement('div');
                    item.className = 'photo-item';
                    item.setAttribute('data-index', itemIndex);

                    item.innerHTML = `
                        <img src="${photo.photo}" alt="Photo">
                        <div class="photo-overlay">${photosData[itemIndex].caption}</div>
                    `;

                    item.addEventListener('click', () => openLightbox(itemIndex));
                    grid.appendChild(item);
                });

                groupDiv.appendChild(grid);
                photosFeedContainer.appendChild(groupDiv);
            }
        }

        async function loadPendingPhotos() {
            try {
                const res = await fetch('../api/photos.php?action=getPending');
                const result = await res.json();
                if (result.status === 'success') {
                    renderPendingPhotos(result.data);
                }
            } catch (err) {
                console.error(err);
            }
        }

        function renderPendingPhotos(photos) {
            pendingPhotosContainer.innerHTML = '';
            pendingCountBadge.textContent = photos.length;
            const approveBadge = document.querySelector('.btn-outline-primary .badge');
            approveBadge.textContent = photos.length;

            if (photos.length > 0) {
                approveBadge.style.display = 'inline-block';
            } else {
                approveBadge.style.display = 'none';
            }

            if (photos.length === 0) {
                pendingPhotosContainer.innerHTML = '<div class="col-12"><p class="text-muted text-center py-4">No pending photos.</p></div>';
                document.getElementById('bulkActionsContainer').classList.add('d-none');
                document.getElementById('defaultActionContainer').classList.add('d-none');
                return;
            }

            document.getElementById('defaultActionContainer').classList.remove('d-none');

            photos.forEach(photo => {
                const col = document.createElement('div');
                col.className = 'col-lg-3 col-md-4 col-sm-6';
                col.innerHTML = `
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                        <div class="position-absolute top-0 start-0 p-2" style="z-index: 10;">
                            <input class="form-check-input photo-approval-checkbox" type="checkbox" value="${photo.id}" aria-label="Select photo" style="width: 1.25rem; height: 1.25rem; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-color: #dee2e6;">
                        </div>
                        <img src="${photo.photo}" class="card-img-top" style="height: 110px; object-fit: cover;" alt="Pending">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="${photo.user?.image ? photo.user.image : 'https://ui-avatars.com/api/?name=' + photo.user?.name}" class="rounded-circle me-2" width="24" height="24" alt="User">
                                    <span class="fw-medium text-dark fs-7">${photo.user?.name || 'Unknown'}</span>
                                </div>
                                <span class="text-muted" style="font-size: 0.7rem;">${formatDate(photo.created_at)}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success flex-grow-1 rounded-pill fw-medium btn-sm" onclick="approvePhoto(${photo.id})"><i class="fa-solid fa-check"></i></button>
                                <button class="btn btn-outline-danger flex-grow-1 rounded-pill fw-medium btn-sm" onclick="rejectPhoto(${photo.id})"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                pendingPhotosContainer.appendChild(col);
            });

            bindCheckboxLogic();
        }

        // Upload functionality
        fileUpload.addEventListener('change', () => {
            if (fileUpload.files.length > 0) {
                const count = fileUpload.files.length;
                document.querySelector('#uploadPhotoModal h5').textContent = count + ' file(s) selected';
                document.querySelector('#uploadPhotoModal p.text-muted').textContent = 'Ready to upload';
            } else {
                document.querySelector('#uploadPhotoModal h5').textContent = 'Drag and drop your photos here';
                document.querySelector('#uploadPhotoModal p.text-muted').textContent = 'or click to browse from your computer';
            }
        });

        btnSubmitUpload.addEventListener('click', async () => {
            const files = fileUpload.files;
            if (files.length === 0) return alert('Please select files to upload.');

            btnSubmitUpload.disabled = true;
            btnSubmitUpload.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';

            let uploadedCount = 0;
            for (let i = 0; i < files.length; i++) {
                const formData = new FormData();
                formData.append('file', files[i]);
                try {
                    const res = await fetch('../api/photos.php?action=upload', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        uploadedCount++;
                    }
                } catch (err) {
                    console.error('Upload error', err);
                }
            }

            btnSubmitUpload.disabled = false;
            btnSubmitUpload.innerHTML = 'Upload Photos';
            fileUpload.value = '';
            document.querySelector('#uploadPhotoModal h5').textContent = 'Drag and drop your photos here';
            document.querySelector('#uploadPhotoModal p.text-muted').textContent = 'or click to browse from your computer';

            const modalEl = document.getElementById('uploadPhotoModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            if (uploadedCount > 0) {
                showAlert(`${uploadedCount} photo(s) uploaded and waiting for approval`, 'success');
            } else {
                showAlert('Failed to upload photos', 'danger');
            }

            loadPendingPhotos();
            loadPhotosFeed();
        });

        // Approve / Reject actions
        window.approvePhoto = async (id, skipAlert = false) => {
            const formData = new FormData();
            formData.append('id', id);
            await fetch('../api/photos.php?action=approve', {
                method: 'POST',
                body: formData
            });
            loadPendingPhotos();
            loadPhotosFeed();
            if (!skipAlert) showAlert('Photo approved successfully', 'success');
        };

        window.rejectPhoto = async (id, skipAlert = false) => {
            if (!skipAlert) {
                if (!confirm('Are you sure you want to delete this pending photo?')) return;
            }
            const formData = new FormData();
            formData.append('id', id);
            await fetch('../api/photos.php?action=delete', {
                method: 'POST',
                body: formData
            });
            loadPendingPhotos();
            if (!skipAlert) showAlert('Photo deleted successfully', 'success');
        };

        const bulkActions = document.getElementById('bulkActionsContainer');
        const defaultActions = document.getElementById('defaultActionContainer');

        function bindCheckboxLogic() {
            const approveCheckboxes = document.querySelectorAll('.photo-approval-checkbox');
            approveCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const checkedCount = document.querySelectorAll('.photo-approval-checkbox:checked').length;
                    if (checkedCount > 0) {
                        bulkActions.classList.remove('d-none');
                        defaultActions.classList.add('d-none');
                    } else {
                        bulkActions.classList.add('d-none');
                        defaultActions.classList.remove('d-none');
                    }
                });
            });
        }

        // Bulk Approve / Reject / Approve All buttons
        document.querySelector('#defaultActionContainer .btn-primary').addEventListener('click', async () => {
            const checkboxes = document.querySelectorAll('.photo-approval-checkbox');
            if (checkboxes.length === 0) return;
            if (!confirm('Are you sure you want to approve all photos?')) return;
            for (const cb of checkboxes) {
                await window.approvePhoto(cb.value, true);
            }
            showAlert(checkboxes.length + ' photo(s) approved', 'success');
        });

        document.querySelector('#bulkActionsContainer .btn-success').addEventListener('click', async () => {
            const checked = document.querySelectorAll('.photo-approval-checkbox:checked');
            if (checked.length === 0) return;
            for (const cb of checked) {
                await window.approvePhoto(cb.value, true);
            }
            showAlert(checked.length + ' photo(s) approved', 'success');
        });

        document.querySelector('#bulkActionsContainer .btn-outline-danger').addEventListener('click', async () => {
            const checked = document.querySelectorAll('.photo-approval-checkbox:checked');
            if (checked.length === 0) return;
            if (!confirm('Are you sure you want to delete selected photos?')) return;
            for (const cb of checked) {
                await window.rejectPhoto(cb.value, true);
            }
            showAlert(checked.length + ' photo(s) deleted', 'success');
        });

        // --- Lightbox Logic ---
        function openLightbox(index) {
            currentIndex = index;
            lightbox.classList.add('active');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            resetIdleTimer();
            loadImage(currentIndex);
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            clearTimeout(idleTimer);

            setTimeout(() => {
                lightboxImg.src = '';
                lightboxImg.classList.remove('loaded');
            }, 300);
        }

        function loadImage(index) {
            const data = photosData[index];
            if (!data) return;

            lightboxImg.classList.remove('loaded');
            lightboxLoader.classList.add('active');

            lightboxCaption.textContent = data.caption;
            lightboxDate.textContent = data.date;

            btnPrev.style.visibility = index > 0 ? 'visible' : 'hidden';
            btnNext.style.visibility = index < photosData.length - 1 ? 'visible' : 'hidden';

            const tempImg = new Image();
            tempImg.onload = () => {
                lightboxImg.src = tempImg.src;
                lightboxImg.alt = data.alt;
                lightboxLoader.classList.remove('active');
                requestAnimationFrame(() => lightboxImg.classList.add('loaded'));
                preloadAdjacentImages(index);
            };
            tempImg.src = data.src;
        }

        function preloadAdjacentImages(index) {
            if (index > 0) new Image().src = photosData[index - 1].src;
            if (index < photosData.length - 1) new Image().src = photosData[index + 1].src;
        }

        function prevPhoto(e) {
            if (e) e.stopPropagation();
            if (currentIndex > 0) {
                currentIndex--;
                resetIdleTimer();
                loadImage(currentIndex);
            }
        }

        function nextPhoto(e) {
            if (e) e.stopPropagation();
            if (currentIndex < photosData.length - 1) {
                currentIndex++;
                resetIdleTimer();
                loadImage(currentIndex);
            }
        }

        btnPrev.addEventListener('click', prevPhoto);
        btnNext.addEventListener('click', nextPhoto);
        btnClose.addEventListener('click', closeLightbox);

        function resetIdleTimer() {
            lightbox.classList.remove('idle');
            clearTimeout(idleTimer);
            idleTimer = setTimeout(() => lightbox.classList.add('idle'), 3000);
        }

        lightbox.addEventListener('mousemove', resetIdleTimer);
        lightbox.addEventListener('click', resetIdleTimer);

        contentArea.addEventListener('click', (e) => {
            if (e.target === contentArea || e.target.classList.contains('lightbox-image-wrapper')) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (!lightbox.classList.contains('active')) return;
            resetIdleTimer();
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevPhoto();
            if (e.key === 'ArrowRight') nextPhoto();
        });

        contentArea.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
            resetIdleTimer();
        }, {
            passive: true
        });

        contentArea.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX < touchStartX - 50) nextPhoto();
            if (touchEndX > touchStartX + 50) prevPhoto();
        }, {
            passive: true
        });

    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>