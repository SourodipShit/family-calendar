<?php
$path_prefix = "../";
$page_title = "Family Photos";
include $path_prefix . 'components/family-header.php';
include $path_prefix . 'components/family-sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/family-navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1600px; margin: 0 auto;">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Family Photos</h3>
                <p class="text-muted mb-0">Capture and share your favorite moments.</p>
            </div>
        </div>

        <!-- Photos Feed -->
        <div id="photosFeedContainer">
            <!-- Dynamically loaded -->
        </div>

    </div>
</div>

<!-- Fullscreen Photo Lightbox -->
<div id="photoLightbox" class="lightbox-container" aria-hidden="true">
    <div class="lightbox-toolbar">
        <button class="lightbox-btn" id="lightboxClose" title="Close (Esc)"><i class="fa-solid fa-arrow-left"></i></button>
        <div class="lightbox-info">
            <span id="lightboxDate" class="lightbox-date"></span>
            <span id="lightboxCaption" class="lightbox-caption-text"></span>
        </div>
        <div class="lightbox-actions">
            <button class="lightbox-btn" title="Download" id="lightboxDownload" style="display:none;"><i class="fa-solid fa-download"></i></button>
            <button class="lightbox-btn" title="Delete" id="lightboxDelete" style="display:none;"><i class="fa-solid fa-trash"></i></button>
            <button class="lightbox-btn" title="Info" id="lightboxInfoBtn"><i class="fa-solid fa-circle-info"></i></button>
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

    <!-- Info Panel (Off-canvas) -->
    <div id="lightboxInfoPanel" class="lightbox-info-panel">
        <div class="info-panel-header">
            <h5 class="m-0">Info</h5>
            <button class="btn-close btn-close-white" id="infoPanelClose"></button>
        </div>
        <div class="info-panel-body">
            <h6 class="text-white-50 mb-1">Details</h6>
            <div class="d-flex align-items-center mb-4">
                <i class="fa-solid fa-image me-3 text-white-50 fs-4"></i>
                <div>
                    <div id="infoOriginalName" class="text-white fw-medium text-break">filename.jpg</div>
                    <div id="infoResolution" class="text-white-50 small">1920 × 1080</div>
                    <div id="infoSize" class="text-white-50 small">1.2 MB</div>
                </div>
            </div>

            <h6 class="text-white-50 mb-1 mt-4">Uploaded By</h6>
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-user-circle me-3 text-white-50 fs-3"></i>
                <div>
                    <div id="infoUploader" class="text-white fw-medium">User Name</div>
                    <div id="infoUploadDate" class="text-white-50 small">Oct 24, 2023</div>
                </div>
            </div>
        </div>
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

    /* Lightbox Info Panel */
    .lightbox-info-panel {
        position: absolute;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100%;
        background-color: #212121;
        z-index: 10002;
        transition: right 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: flex;
        flex-direction: column;
        border-left: 1px solid #444;
    }

    .lightbox-container.info-open .lightbox-info-panel {
        right: 0;
    }

    .info-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #444;
        color: white;
    }

    .info-panel-body {
        padding: 20px;
        color: white;
        overflow-y: auto;
    }

    /* Shift content area when info is open */
    .lightbox-content-area {
        transition: margin-right 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .lightbox-container.info-open .lightbox-content-area {
        margin-right: 350px;
    }

    .lightbox-container.info-open .lightbox-toolbar {
        padding-right: 366px;
        /* 350 + 16 */
    }

    @media (max-width: 768px) {
        .lightbox-info-panel {
            width: 100%;
            right: -100%;
        }

        .lightbox-container.info-open .lightbox-content-area {
            margin-right: 0;
        }

        .lightbox-container.info-open .lightbox-toolbar {
            padding-right: 16px;
        }
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
                const res = await fetch('../api/family/photos.php?action=getByFamily');
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

                    let meta = {};
                    try {
                        if (photo.metadata) meta = JSON.parse(photo.metadata);
                    } catch (e) {}

                    photosData.push({
                        id: photo.id,
                        src: photo.photo,
                        alt: 'Photo by ' + (photo.user ? photo.user.name : 'Unknown'),
                        caption: 'Uploaded by ' + (photo.user ? photo.user.name : 'Unknown'),
                        date: dateText,
                        originalName: meta.original_name || photo.photo.split('/').pop(),
                        width: meta.width || 'Unknown',
                        height: meta.height || 'Unknown',
                        sizeBytes: photo.file_size || 0,
                        uploader: photo.user ? photo.user.name : 'Unknown',
                        fullDate: new Date(photo.created_at).toLocaleString()
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

        // --- Lightbox Logic ---
        function openLightbox(index) {
            currentIndex = index;
            lightbox.classList.add('active');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            resetIdleTimer();
            loadImage(currentIndex);
        }

        btnPrev.addEventListener('click', prevPhoto);
        btnNext.addEventListener('click', nextPhoto);
        btnClose.addEventListener('click', closeLightbox);

        // Info panel toggle
        const infoPanel = document.getElementById('lightboxInfoPanel');
        const infoPanelClose = document.getElementById('infoPanelClose');
        const infoBtn = document.getElementById('lightboxInfoBtn');

        if (infoBtn) {
            infoBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                lightbox.classList.toggle('info-open');
            });
        }

        if (infoPanelClose) {
            infoPanelClose.addEventListener('click', (e) => {
                e.stopPropagation();
                lightbox.classList.remove('info-open');
            });
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            lightbox.setAttribute('aria-hidden', 'true');
            lightbox.classList.remove('info-open');
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

            // Update Info Panel
            document.getElementById('infoOriginalName').textContent = data.originalName || 'Unknown file';
            document.getElementById('infoResolution').textContent = (data.width !== 'Unknown' && data.height !== 'Unknown') ? `${data.width} × ${data.height}` : 'Unknown resolution';
            document.getElementById('infoSize').textContent = data.sizeBytes > 0 ? (data.sizeBytes / (1024 * 1024)).toFixed(2) + ' MB' : 'Unknown size';
            document.getElementById('infoUploader').textContent = data.uploader || 'Unknown';
            document.getElementById('infoUploadDate').textContent = data.fullDate || 'Unknown Date';

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

<?php include $path_prefix . 'components/family-footer.php'; ?>
