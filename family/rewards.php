<?php
require_once __DIR__ . '/../components/family-auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../classes/Family.php';
require_once __DIR__ . '/../classes/Points.php';

$path_prefix = "../";
$page_title = "Rewards";

$familyId = $family_id;
$familyMembers = $familyId ? Family::getMembersByFamilyId($familyId) : [];

// Check if user is the family head
$isHead = (isset($_SESSION['user']['role']) && strtolower($_SESSION['user']['role']) === 'family-head');

$myPointsData = Points::getPoints($_SESSION['user']['id']);
$myPoints = isset($myPointsData['data']['balance']) ? $myPointsData['data']['balance'] : 0;

include $path_prefix . 'components/family-header.php';
include $path_prefix . 'components/family-sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-light">
    <?php include $path_prefix . 'components/family-navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1400px; margin: 0 auto;">

        <!-- Header & Points Summary -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0 text-dark">Rewards</h2>
            <div class="d-flex align-items-center px-3 py-2 rounded-pill shadow-sm cursor-pointer" onclick="openSelectUserModal('vault')" style="background: linear-gradient(135deg, #FFD700, #FDB931); border: 2px solid #FFF3E0; transform: translateY(-2px); transition: transform 0.2s;" id="points-container">
                <div class="d-flex align-items-center justify-content-center bg-white rounded-circle me-2 shadow-sm" style="width: 28px; height: 28px; overflow: hidden;" id="points-avatar-container">
                    <i class="fa-solid fa-star text-warning" style="font-size: 0.85rem;" id="points-avatar-icon"></i>
                </div>
                <div class="d-flex flex-column lh-1 me-2 text-start">
                    <span class="text-white fw-bold" style="font-size: 0.7rem;" id="points-user-name">Select User</span>
                </div>
                <span class="fw-bold text-white fs-5" id="user-points-display" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">0</span>
                <span class="ms-1 fw-bold text-white fs-6" style="opacity: 0.9;">pts</span>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs border-bottom mb-4" id="rewardsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-medium text-dark px-4 py-3" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab">
                    <i class="fa-solid fa-store me-2"></i> Redeem Store
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-medium text-dark px-4 py-3" id="vault-tab" data-bs-toggle="tab" data-bs-target="#vault" type="button" role="tab">
                    <i class="fa-solid fa-box-open me-2"></i> My Reward Vault
                </button>
            </li>
        </ul>

        <div class="tab-content" id="rewardsTabsContent">
            <!-- Redeem Store Tab -->
            <div class="tab-pane fade show active" id="store" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-secondary mb-0">Available Rewards</h5>
                </div>
                <div class="row g-3" id="store-rewards-container">
                    <!-- Dynamic rewards will be loaded here -->
                </div>
            </div>

            <!-- My Reward Vault Tab -->
            <div class="tab-pane fade" id="vault" role="tabpanel">
                <h5 class="fw-bold text-secondary mb-3">My Redeemed Rewards</h5>
                <div class="row g-3" id="my-vault-container">
                    <!-- Dynamic vault items will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
    }

    .nav-tabs .nav-link:hover {
        border-color: #dee2e6;
    }

    .nav-tabs .nav-link.active {
        border: none;
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd !important;
        background-color: transparent;
    }
</style>

<!-- Select User Modal -->
<div class="modal fade" id="selectUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-0 justify-content-center">
                <h5 class="modal-title fw-bold" id="selectUserModalTitle">Who is this for?</h5>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <?php foreach ($familyMembers as $member):
                        $avatarUrl = !empty($member['image']) ? $member['image'] : 'https://ui-avatars.com/api/?name=' . urlencode($member['nickname'] ?? $member['name']) . '&background=random&color=fff';
                        $displayName = htmlspecialchars(!empty($member['nickname']) ? $member['nickname'] : $member['name']);
                    ?>
                        <div class="text-center position-relative cursor-pointer avatar-selector opacity-75 hover-opacity-100"
                            onclick="selectUserAndContinue(<?= $member['id'] ?>, '<?= addslashes($displayName) ?>', '<?= $avatarUrl ?>')">
                            <img src="<?= $avatarUrl ?>" class="rounded-circle border border-transparent border-2 p-1 avatar-img" width="50" height="50" style="object-fit: cover;">
                            <div class="fs-8 mt-1 text-dark avatar-name" style="font-size: 0.75rem;"><?= $displayName ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    let userPoints = 0;
    const isHead = <?php echo $isHead ? 'true' : 'false'; ?>;

    let pendingAction = null; // 'redeem' or 'vault'
    let pendingRewardId = null;
    let pendingRewardPrice = null;
    let currentSelectedUserId = null;
    let currentSelectedUserName = null;

    function openSelectUserModal(action, rewardId = null, price = null) {
        pendingAction = action;
        pendingRewardId = rewardId;
        pendingRewardPrice = price;
        
        document.getElementById('selectUserModalTitle').innerText = action === 'redeem' ? 'Who is redeeming this?' : 'Select Member';
        
        let modal = new bootstrap.Modal(document.getElementById('selectUserModal'));
        modal.show();
    }

    function selectUserAndContinue(userId, userName, avatarUrl) {
        currentSelectedUserId = userId;
        currentSelectedUserName = userName;
        
        let modalEl = document.getElementById('selectUserModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        
        // Update top right header
        document.getElementById('points-user-name').innerText = userName;
        document.getElementById('points-avatar-container').innerHTML = `<img src="${avatarUrl}" class="w-100 h-100" style="object-fit: cover;">`;
        
        fetchUserPoints(userId).then(() => {
            if (pendingAction === 'redeem') {
                processRedeem();
            } else if (pendingAction === 'vault') {
                fetchMyVault();
            }
        });
    }

    function fetchUserPoints(userId = currentSelectedUserId) {
        if (!userId) return Promise.resolve();
        return fetch('../api/family/rewards.php?action=get_points&user_id=' + userId)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    userPoints = res.data.balance || 0;
                    document.getElementById('user-points-display').innerText = userPoints;
                }
            });
    }

    function fetchStoreRewards() {
        fetch('../api/family/rewards.php?action=list')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderStore(res.data);
                }
            });
    }

    function renderStore(rewards) {
        const container = document.getElementById('store-rewards-container');
        container.innerHTML = '';
        if (rewards.length === 0) {
            container.innerHTML = '<div class="col-12"><p class="text-muted">No rewards available.</p></div>';
            return;
        }
        rewards.forEach(reward => {
            const imgSrc = reward.image ? reward.image : 'https://placehold.co/300x300?text=Reward';
            container.innerHTML += `
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm overflow-hidden position-relative rounded-4" style="aspect-ratio: 1/1; cursor: pointer; transition: transform 0.2s;">
                        <img src="${imgSrc}" alt="${reward.title}" class="w-100 h-100" style="object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-2 d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%); height: 70%;">
                            <h6 class="fw-bold text-white mb-2 text-truncate text-center" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">${reward.title}</h6>
                            <button onclick="redeemReward(${reward.id}, ${reward.price})" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold text-dark" style="font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">${reward.price} pts</button>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function fetchMyVault() {
        if (!currentSelectedUserId) {
            const container = document.getElementById('my-vault-container');
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-users fa-3x text-muted mb-3 opacity-50"></i>
                    <p class="text-muted">Please choose a member to view their reward vault.</p>
                    <button class="btn btn-outline-primary rounded-pill px-4 mt-2" onclick="openSelectUserModal('vault')">Select Member</button>
                </div>
            `;
            return;
        }
        fetch('../api/family/rewards.php?action=my_vault&user_id=' + currentSelectedUserId)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderMyVault(res.data);
                }
            });
    }

    function renderMyVault(vaults) {
        const container = document.getElementById('my-vault-container');
        container.innerHTML = '';
        if (vaults.length === 0) {
            container.innerHTML = '<div class="col-12"><p class="text-muted">No redeemed rewards yet.</p></div>';
            return;
        }
        vaults.forEach(vault => {
            const imgSrc = vault.image ? vault.image : 'https://placehold.co/300x300?text=Reward';
            const badge = vault.status === 'pending' ?
                '<span class="badge bg-warning text-dark shadow-sm" style="font-size: 0.6rem;">Pending</span>' :
                '<span class="badge bg-success shadow-sm" style="font-size: 0.6rem;">Completed</span>';
            const filterClass = vault.status === 'completed' ? 'filter: grayscale(50%);' : '';

            const date = new Date(vault.redeemed_at).toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric'
            });

            container.innerHTML += `
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm overflow-hidden position-relative rounded-4" style="aspect-ratio: 1/1;">
                        <img src="${imgSrc}" class="w-100 h-100" style="object-fit: cover; ${filterClass}">
                        <div class="position-absolute top-0 end-0 p-1">
                            ${badge}
                        </div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-2 d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%); height: 70%;">
                            <h6 class="fw-bold text-white mb-0 text-truncate text-center" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8); font-size: 0.9rem;">${vault.title}</h6>
                            <small class="text-white-50 text-center fw-medium" style="font-size: 0.65rem;">${date}</small>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function fetchFamilyVault() {
        if (!isHead) return;
        fetch('../api/family/rewards.php?action=family_vault')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderFamilyVault(res.data);
                }
            });
    }

    function renderFamilyVault(vaults) {
        const pendingContainer = document.getElementById('manage-vault-pending-container');
        const completedContainer = document.getElementById('manage-vault-completed-container');
        if (!pendingContainer || !completedContainer) return;
        pendingContainer.innerHTML = '';
        completedContainer.innerHTML = '';

        const pendings = vaults.filter(v => v.status === 'pending');
        const completeds = vaults.filter(v => v.status === 'completed');

        if (pendings.length === 0) {
            pendingContainer.innerHTML = '<div class="col-12"><p class="text-muted">No pending rewards.</p></div>';
        }
        if (completeds.length === 0) {
            completedContainer.innerHTML = '<div class="col-12"><p class="text-muted">No completed rewards.</p></div>';
        }

        pendings.forEach(vault => {
            const imgSrc = vault.image ? vault.image : 'https://placehold.co/300x300?text=Reward';
            const userImg = vault.user_image ? (vault.user_image.startsWith('http') ? vault.user_image : vault.user_image) : `https://ui-avatars.com/api/?name=${encodeURIComponent(vault.name || 'User')}&background=0d6efd&color=fff`;

            pendingContainer.innerHTML += `
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm overflow-hidden position-relative rounded-4 border border-warning" style="aspect-ratio: 1/1;">
                        <img src="${imgSrc}" class="w-100 h-100" style="object-fit: cover;">
                        <div class="position-absolute top-0 start-0 p-1">
                            <img src="${userImg}" alt="${vault.name}" class="rounded-circle shadow-sm border border-2 border-white" style="width: 24px; height: 24px; object-fit: cover;" title="${vault.name}">
                        </div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-2 d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%); height: 75%;">
                            <h6 class="fw-bold text-white mb-1 text-truncate text-center" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8); font-size: 0.85rem;">${vault.title}</h6>
                            <button onclick="fulfillReward(${vault.id})" class="btn btn-success btn-sm w-100 rounded-pill fw-bold text-white py-0" style="font-size: 0.75rem; box-shadow: 0 2px 4px rgba(0,0,0,0.3); height: 26px;"><i class="fa-solid fa-check"></i> Fulfill</button>
                        </div>
                    </div>
                </div>
            `;
        });

        completeds.forEach(vault => {
            const imgSrc = vault.image ? vault.image : 'https://placehold.co/300x300?text=Reward';
            const userImg = vault.user_image ? (vault.user_image.startsWith('http') ? vault.user_image : vault.user_image) : `https://ui-avatars.com/api/?name=${encodeURIComponent(vault.name || 'User')}&background=198754&color=fff`;
            const date = new Date(vault.completed_at).toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric'
            });

            completedContainer.innerHTML += `
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm overflow-hidden position-relative rounded-4 border border-success" style="aspect-ratio: 1/1;">
                        <img src="${imgSrc}" class="w-100 h-100" style="object-fit: cover; filter: grayscale(40%);">
                        <div class="position-absolute top-0 start-0 p-1">
                            <img src="${userImg}" alt="${vault.name}" class="rounded-circle shadow-sm border border-2 border-white" style="width: 24px; height: 24px; object-fit: cover;" title="${vault.name}">
                        </div>
                        <div class="position-absolute top-0 end-0 p-1">
                            <span class="badge bg-success shadow-sm" style="font-size: 0.6rem;">Done</span>
                        </div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-2 d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%); height: 75%;">
                            <h6 class="fw-bold text-white mb-0 text-truncate text-center" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8); font-size: 0.85rem;">${vault.title}</h6>
                            <small class="text-white-50 text-center fw-medium" style="font-size: 0.65rem;">${date}</small>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    function redeemReward(rewardId, price) {
        openSelectUserModal('redeem', rewardId, price);
    }

    function processRedeem() {
        if (!currentSelectedUserId) return;
        
        if (userPoints < pendingRewardPrice) {
            showAlert('Not enough points to redeem this reward!', 'error');
            return;
        }
        if (!confirm('Are you sure you want to redeem this reward for ' + currentSelectedUserName + ' for ' + pendingRewardPrice + ' points?')) return;

        // Optimistic UI Update: Make it feel instant
        const previousPoints = userPoints;
        userPoints -= pendingRewardPrice;
        document.getElementById('user-points-display').innerText = userPoints;

        fetch('../api/family/rewards.php?action=redeem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reward_id: pendingRewardId,
                    user_id: currentSelectedUserId
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert('Reward redeemed successfully!', 'success');
                    if (res.new_balance !== undefined) {
                        userPoints = res.new_balance;
                        document.getElementById('user-points-display').innerText = userPoints;
                    }
                    fetchMyVault(); // Silently update the vault in background
                } else {
                    // Revert optimistic update
                    userPoints = previousPoints;
                    document.getElementById('user-points-display').innerText = userPoints;
                    showAlert(res.message || 'Failed to redeem reward', 'error');
                }
            })
            .catch(() => {
                userPoints = previousPoints;
                document.getElementById('user-points-display').innerText = userPoints;
                showAlert('A network error occurred. Please try again.', 'error');
            });
    }

    function fulfillReward(redeemId) {
        if (!confirm('Mark this reward as fulfilled?')) return;

        fetch('../api/family/rewards.php?action=complete_redemption', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    redeem_id: redeemId
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showAlert('Reward fulfilled!', 'success');
                    fetchFamilyVault(); // refresh tab
                } else {
                    showAlert(res.message || 'Failed to fulfill reward', 'error');
                }
            });
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        fetchStoreRewards();
        fetchMyVault();
        fetchFamilyVault();
    });
</script>
<?php include $path_prefix . 'components/family-footer.php'; ?>
