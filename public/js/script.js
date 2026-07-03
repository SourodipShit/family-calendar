document.addEventListener('DOMContentLoaded', () => {

    // 1. Family Members Data
    let familyMembers = [];
    let activeMemberFilter = 'all';

    fetch(API_PATH + 'getMembers.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            // Map database fields to script expectations
            familyMembers = data.map(member => {
                // Determine color class based on name/role for legacy compatibility
                let colorClass = 'liam';
                const name = member.name.toLowerCase();
                if (member.role === 'family-head' || name.includes('dad')) colorClass = 'dad';
                else if (name.includes('mom')) colorClass = 'mom';
                else if (name.includes('emma')) colorClass = 'emma';
                else if (name.includes('ava')) colorClass = 'ava';
                else if (name.includes('grandma')) colorClass = 'grandma';

                return {
                    ...member,
                    id: member.id,
                    // Use name as ID for filter compatibility if it matches hardcoded events
                    filterId: name.includes('dad') ? 'dad' :
                        name.includes('mom') ? 'mom' :
                            name.includes('emma') ? 'emma' :
                                name.includes('liam') ? 'liam' :
                                    name.includes('ava') ? 'ava' : member.id,
                    avatar: member.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}&background=random`,
                    colorClass: colorClass,
                    colorCode: member.color || '#0d6efd'
                };
            });

            renderMembers();
            renderLegend();
            populateModalMembers();
            loadEventTypes();
        })
        .catch(error => console.error('Error fetching family members:', error));

    function getDisplayName(member) {
        let settings = window.familySettings || {};
        
        // Handle case where settings might be a string
        if (typeof settings === 'string' && settings.trim() !== '') {
            try {
                settings = JSON.parse(settings);
            } catch (e) {
                console.error('Error parsing family settings:', e);
                settings = {};
            }
        }

        const showNicknames = settings.show_nicknames === true || settings.show_nicknames === "true" || settings.show_nicknames === 1 || settings.show_nicknames === "1";

        if (showNicknames) {
            if (member.nickname && member.nickname.trim() !== '') {
                return member.nickname;
            } else if (member.name) {
                // If nickname is null, show first part of name
                return member.name.trim().split(' ')[0];
            }
        }
        return member.name || '';
    }

    function renderMembers() {
        const membersContainer = document.querySelector('.family-members-list');
        if (!membersContainer) return;

        membersContainer.innerHTML = '';

        // Add "All" Filter
        const allDiv = document.createElement('div');
        allDiv.className = `family-member-filter ${activeMemberFilter === 'all' ? 'active' : ''}`;
        allDiv.setAttribute('data-member', 'all');
        allDiv.innerHTML = `
            <div class="avatar-wrapper position-relative">
                <div class="rounded-circle border border-2 border-white shadow-sm d-flex align-items-center justify-content-center bg-light text-primary fw-bold" style="width: 60px; height: 60px; font-size: 1.2rem;">
                    ALL
                </div>
                <span class="name text-dark">Everyone</span>
            </div>
        `;
        membersContainer.appendChild(allDiv);

        familyMembers.forEach(member => {
            const div = document.createElement('div');
            const memberFilterId = member.name.toLowerCase();
            div.className = `family-member-filter ${activeMemberFilter === memberFilterId ? 'active' : ''}`;
            div.setAttribute('data-member', memberFilterId);
            
            let badgeOrEdit = member.is_external ? 
                `<span class="position-absolute top-0 end-0 bg-white text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold" style="width: 22px; height: 22px; font-size: 11px; transform: translate(10%, -10%); border: 1px solid #eee; z-index: 10;">E</span>` 
                : 
                (window.CURRENT_USER_ROLE === 'family-head' ? `<a href="../users/edit-member.php?id=${member.id}" class="edit-member-btn position-absolute top-0 end-0 bg-white rounded-circle shadow-sm text-primary" style="width: 22px; height: 22px; font-size: 11px; display: flex; align-items: center; justify-content: center; transform: translate(10%, -10%); border: 1px solid #eee; z-index: 10;">
                    <i class="fa-solid fa-pen"></i>
                </a>` : '');

            div.innerHTML = `
                <div class="avatar-wrapper position-relative">
                    <img src="${member.avatar}" alt="${member.name}" width="60" height="60" class="rounded-circle border border-2 border-white shadow-sm">
                    ${badgeOrEdit}
                    <span class="name text-dark">${getDisplayName(member)}</span>
                </div>
            `;
            membersContainer.appendChild(div);
        });

        // Add Button for Members
        if (window.CURRENT_USER_ROLE === 'family-head') {
            const addDiv = document.createElement('div');
            addDiv.className = 'family-member-filter-add';
            addDiv.innerHTML = `
                <div class="avatar-wrapper">
                    <button class="add-avatar-btn" onclick="window.location.href='../users/add-member.php'">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    <span class="name text-muted">Add</span>
                </div>
            `;
            membersContainer.appendChild(addDiv);
        }

        // Re-initialize Filter Logic
        const filters = membersContainer.querySelectorAll('.family-member-filter');
        filters.forEach(filter => {
            filter.addEventListener('click', () => {
                const memberId = filter.getAttribute('data-member');
                if (!memberId) return;

                activeMemberFilter = memberId;
                filters.forEach(f => f.classList.remove('active'));
                filter.classList.add('active');

                applyFilters();
            });
        });
    }

    function applyFilters() {
        const memberId = activeMemberFilter;
        
        // Apply to meals
        document.querySelectorAll('.meal-card-wrapper').forEach(wrapper => {
            // Meals currently don't have member data, so we show them all or handle 'all'
            wrapper.style.display = 'block'; 
        });

        document.querySelectorAll('.event-block').forEach(block => {
            const blockMembers = block.getAttribute('data-members') ? JSON.parse(block.getAttribute('data-members')) : ['all'];
            if (memberId === 'all' || blockMembers.includes(memberId)) {
                block.style.opacity = '1';
                block.style.pointerEvents = 'auto';
                block.classList.remove('filtered-out');
            } else {
                block.style.opacity = '0.15';
                block.style.pointerEvents = 'none';
                block.classList.add('filtered-out');
            }
        });

        document.querySelectorAll('.all-day-event').forEach(evt => {
            const evtMembers = evt.getAttribute('data-members') ? JSON.parse(evt.getAttribute('data-members')) : ['all'];
            if (memberId === 'all' || evtMembers.includes(memberId)) {
                evt.style.opacity = '1';
                evt.style.display = 'inline-flex';
            } else {
                evt.style.opacity = '0.15';
                evt.style.display = 'none';
            }
        });

        // Update FullCalendar if it exists
        if (window.calendarInstance) {
            window.calendarInstance.refetchEvents();
        }
    }

    function renderLegend() {
        const legendContainer = document.querySelector('.legend-container');
        if (!legendContainer) return;

        legendContainer.innerHTML = '';
        familyMembers.forEach(member => {
            const item = document.createElement('div');
            item.className = 'legend-item';
            const color = member.colorCode || '#0d6efd';
            item.innerHTML = `<span class="legend-dot" style="background-color: ${color}"></span> ${getDisplayName(member)}`;
            legendContainer.appendChild(item);
        });
    }

    function populateModalMembers(containerElement, inputsContainerId) {
        const modalMembersContainer = containerElement || document.getElementById('modal-members-container');
        const hiddenInputsContainer = document.getElementById(inputsContainerId || 'hidden-member-inputs');
        if (!modalMembersContainer) return;

        modalMembersContainer.innerHTML = '';
        if (hiddenInputsContainer) hiddenInputsContainer.innerHTML = ''; // Clear inputs

        familyMembers.forEach((member, index) => {
            const div = document.createElement('div');
            // By default, select the first one, others unselected
            const isSelected = index === 0;
            div.className = `text-center position-relative cursor-pointer avatar-selector ${isSelected ? 'selected opacity-100' : 'opacity-75 hover-opacity-100'}`;
            div.setAttribute('data-member-id', member.id);
            div.setAttribute('data-id', member.id);
            div.classList.add('avatar-wrapper');

            const avatarUrl = member.avatar;
            const borderClass = isSelected ? 'border-primary' : 'border-transparent';
            const checkIconClass = isSelected ? '' : 'd-none';
            const nameWeight = isSelected ? 'fw-medium' : '';

            div.innerHTML = `
                <img src="${avatarUrl}" class="rounded-circle border ${borderClass} border-2 p-1 avatar-img" width="44" height="44" alt="${member.name}">
                <i class="fa-solid fa-circle-check text-primary position-absolute bg-white rounded-circle check-icon ${checkIconClass}" style="top: -2px; right: -2px; font-size: 14px;"></i>
                <div class="fs-8 mt-1 text-dark avatar-name ${nameWeight}" style="font-size: 0.7rem;">${getDisplayName(member)}</div>
            `;

            div.addEventListener('click', () => {
                const wasSelected = div.classList.contains('selected');
                
                if (wasSelected) {
                    // Unselect
                    div.classList.remove('selected', 'opacity-100');
                    div.classList.add('opacity-75');
                    div.querySelector('.avatar-img').classList.remove('border-primary');
                    div.querySelector('.avatar-img').classList.add('border-transparent');
                    div.querySelector('.check-icon').classList.add('d-none');
                    div.querySelector('.avatar-name').classList.remove('fw-medium');
                } else {
                    // Select
                    div.classList.remove('opacity-75');
                    div.classList.add('selected', 'opacity-100');
                    div.querySelector('.avatar-img').classList.remove('border-transparent');
                    div.querySelector('.avatar-img').classList.add('border-primary');
                    div.querySelector('.check-icon').classList.remove('d-none');
                    div.querySelector('.avatar-name').classList.add('fw-medium');
                }
                
                updateHiddenInputs(modalMembersContainer, hiddenInputsContainer);
            });

            modalMembersContainer.appendChild(div);
        });
        
        // Initial sync of hidden inputs
        updateHiddenInputs(modalMembersContainer, hiddenInputsContainer);
    }
    
    function updateHiddenInputs(modalMembersContainer, hiddenInputsContainer) {
        if (!hiddenInputsContainer) return;
        hiddenInputsContainer.innerHTML = '';
        modalMembersContainer.querySelectorAll('.avatar-selector.selected').forEach(selectedDiv => {
            const id = selectedDiv.getAttribute('data-id');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'member_ids[]';
            input.value = id;
            hiddenInputsContainer.appendChild(input);
        });
    }

    function loadEventTypes() {
        const eventTypeSelect = document.getElementById('eventType');
        if (!eventTypeSelect) return;

        fetch(API_PATH + 'event_types.php?action=list')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    eventTypeSelect.innerHTML = '';
                    data.data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        option.dataset.allowMultipleDay = type.allow_multiple_day || 0;
                        eventTypeSelect.appendChild(option);
                    });
                    
                    // Trigger change to set initial state
                    eventTypeSelect.dispatchEvent(new Event('change'));
                }
            })
            .catch(err => console.error('Error loading event types:', err));

        // Event listener for event type change
        eventTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const endDateField = document.getElementById('eventEndDate');
            const inputGroup = document.getElementById('endDateInputGroup');
            const endDateContainer = document.getElementById('endDateContainer');
            
            if (endDateField && selectedOption) {
                if (selectedOption.dataset.allowMultipleDay == "1") {
                    endDateField.disabled = false;
                    if (inputGroup) {
                        inputGroup.classList.remove('bg-light', 'opacity-50');
                        inputGroup.classList.add('bg-white');
                    }
                    if (endDateContainer) {
                        endDateContainer.classList.remove('opacity-50');
                    }
                } else {
                    endDateField.disabled = true;
                    endDateField.value = '';
                    if (inputGroup) {
                        inputGroup.classList.remove('bg-white');
                        inputGroup.classList.add('bg-light');
                        inputGroup.classList.remove('opacity-50'); // remove from here if it was previously added
                    }
                    if (endDateContainer) {
                        endDateContainer.classList.add('opacity-50');
                    }
                }
            }
        });
    }

    // Fix Bootstrap Tabs (manual fallback if Bootstrap JS fails)
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            const tabList = btn.closest('.nav-tabs');
            if (tabList) {
                tabList.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active', 'text-primary', 'border-primary', 'border-3');
                    link.classList.add('text-muted');
                });
            }

            btn.classList.add('active', 'text-primary', 'border-primary', 'border-3');
            btn.classList.remove('text-muted');

            const targetId = btn.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                const tabContent = targetPane.closest('.tab-content');
                if (tabContent) {
                    tabContent.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });
                }
                targetPane.classList.add('show', 'active');
            }
        });
    });



    // 2. DATA ENTRY POINTS (DYNAMIC TRANSITION)
    // ---------------------------------------------------------
    // To make this dynamic:
    // 1. Create an API endpoint (e.g., api/getEvents.php) that returns these arrays.
    // 2. Use fetch() to get the data and replace these static arrays.
    // 3. Call the rendering functions (initCalendar, renderDayView, etc.) after fetch.

    // --- DYNAMIC EVENTS LOADING ---
    let events = [];
    let allDayEvents = [];

    function loadEvents() {
        // Load events for a broad range (current month +/- 1 month)
        const now = new Date();
        const start = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().split('T')[0];
        const end = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];

        fetch(API_PATH + `events.php?action=getEvents&start=${start}&end=${end}&_=${new Date().getTime()}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) throw new Error(data.error);

                const mapped = data.map(e => {
                    const startT = e.startHour.replace(' ', 'T');
                    const endT = e.endHour.replace(' ', 'T');
                    const d = new Date(startT);

                    return {
                        ...e,
                        day: d.getDay(),
                        startHour: d.getHours() + (d.getMinutes() / 60),
                        startStr: startT,
                        endStr: endT,
                        countdown: e.countdown
                    };
                });

                events = mapped.filter(e => !e.is_all_day);
                allDayEvents = mapped.filter(e => e.is_all_day);

                // Update UI
                refreshCalendarUI();

                // Refresh FullCalendar if initialized
                if (window.calendarInstance) {
                    window.calendarInstance.refetchEvents();
                }
            })
            .catch(err => console.error('Error fetching events:', err));
    }

    loadEvents();

    // --- MEALS DATA ---
    let allMeals = [];
    let mealsData = [];

    function getWeekRange(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day; // Sunday is 0
        const sunday = new Date(d.setDate(diff));
        const saturday = new Date(sunday);
        saturday.setDate(sunday.getDate() + 6);
        return { start: sunday, end: saturday };
    }

    function loadMeals() {
        const now = new Date();
        const start = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().split('T')[0];
        const end = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];

        const formData = new FormData();
        formData.append('startDate', start);
        formData.append('endDate', end);

        fetch(API_PATH + 'meals.php?action=getByDateRange', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    allMeals = data.meals;
                    refreshCalendarUI();
                }
            })
            .catch(err => console.error('Error fetching meals:', err));
    }

    loadMeals();

    function updateMealsData() {
        const week = getWeekRange(currentDate);
        const mealTypes = [
            { type: 'breakfast', label: 'Breakfast', icon: 'fa-solid fa-sun', color: 'warning', time: '7:00 AM' },
            { type: 'lunch', label: 'Lunch', icon: 'fa-solid fa-cloud-sun', color: 'success', time: '12:30 PM' },
            { type: 'dinner', label: 'Dinner', icon: 'fa-solid fa-moon', color: 'danger', time: '6:30 PM' }
        ];

        mealsData = mealTypes.map(mt => {
            const menu = [];
            for (let i = 0; i < 7; i++) {
                const dayDate = new Date(week.start);
                dayDate.setDate(week.start.getDate() + i);
                const dateStr = dayDate.toISOString().split('T')[0];

                const meal = allMeals.find(m => m.type === mt.type && m.date === dateStr);
                if (meal) {
                    menu.push({ name: meal.name, img: meal.image || `../public/img/${mt.type}.webp`, type: mt.type });
                } else {
                    menu.push({ name: 'Not planned', img: `../public/img/unknown.png`, empty: true, type: mt.type });
                }
            }
            return {
                type: mt.label,
                icon: mt.icon,
                color: mt.color,
                time: mt.time,
                menu: menu
            };
        });
    }

    function updateWeekHeaders() {
        const week = getWeekRange(currentDate);
        const headerCells = document.querySelectorAll('#view-week .header-row .day-col');
        if (headerCells.length === 0) return;

        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const options = { month: 'short', day: 'numeric' };

        headerCells.forEach((cell, index) => {
            const dayDate = new Date(week.start);
            dayDate.setDate(week.start.getDate() + index);

            const isToday = dayDate.toDateString() === new Date().toDateString();

            if (isToday) {
                cell.className = 'day-col flex-fill border-end p-3 text-primary';
                if (index === 6) cell.className = 'day-col flex-fill p-3 text-primary';
                cell.innerHTML = `<span class="fw-bold">${days[index]}</span><br><span class="d-inline-block bg-primary text-white rounded-circle mt-1 fw-bold" style="width: 30px; height: 30px; line-height: 30px;">${dayDate.getDate()}</span>`;
            } else {
                cell.className = 'day-col flex-fill border-end p-3';
                if (index === 6) cell.className = 'day-col flex-fill p-3';
                cell.innerHTML = `${days[index]}<br><span class="fw-normal">${dayDate.toLocaleDateString('en-US', options)}</span>`;
            }
        });
    }

    // 3. UI RENDERING FUNCTIONS
    // ---------------------------------------------------------
    function setupEditModal(evt) {
        // Populate edit modal
        document.getElementById('editEventId').value = evt.id;
        document.getElementById('editEventTitle').value = evt.title;
        document.getElementById('editEventLocation').value = evt.location || '';
        
        if (evt.startStr) {
            document.getElementById('editEventDate').value = evt.startStr.split('T')[0];
        }
        if (evt.endStr) {
            document.getElementById('editEventEndDate').value = evt.endStr.split('T')[0];
        }
        
        const countdownEl = document.getElementById('editEventCountdown');
        if (countdownEl) {
            countdownEl.checked = (evt.countdown == 1 || evt.countdown === true);
        }
        
        if (!evt.is_all_day && !evt.isAllDay && evt.startHour !== undefined) {
            document.getElementById('editEventAllDay').checked = false;
            let mStr = Math.round((evt.startHour - Math.floor(evt.startHour)) * 60).toString().padStart(2, '0');
            let hStr = Math.floor(evt.startHour).toString().padStart(2, '0');
            document.getElementById('editEventStartTime').value = hStr + ':' + mStr;
            
            if (evt.duration) {
                let eH = evt.startHour + evt.duration;
                let emStr = Math.round((eH - Math.floor(eH)) * 60).toString().padStart(2, '0');
                let ehStr = Math.floor(eH).toString().padStart(2, '0');
                document.getElementById('editEventEndTime').value = ehStr + ':' + emStr;
            }
        } else {
            document.getElementById('editEventAllDay').checked = true;
        }
        
        // Populate event types if not already done
        const typeSelect = document.getElementById('editEventType');
        if (typeSelect && typeSelect.options.length <= 1) {
            typeSelect.innerHTML = document.getElementById('eventType').innerHTML;
        }
        
        // Populate members using the same function but different container
        populateModalMembers(document.getElementById('edit-modal-members-container'), 'edit-hidden-member-inputs');
        
        // Set the correct selected member visually
        setTimeout(() => {
            const memberAvatars = document.getElementById('edit-modal-members-container').querySelectorAll('.avatar-wrapper');
            const eventMemberNames = evt.members ? evt.members.map(m => m.member.toLowerCase()) : [];
            
            memberAvatars.forEach(av => {
                const avatarName = av.querySelector('img').getAttribute('alt').toLowerCase();
                if (eventMemberNames.includes(avatarName)) {
                    av.classList.add('selected', 'opacity-100');
                    av.classList.remove('opacity-75');
                    av.querySelector('.avatar-img').classList.add('border-primary');
                    av.querySelector('.avatar-img').classList.remove('border-transparent');
                    av.querySelector('.check-icon').classList.remove('d-none');
                    av.querySelector('.avatar-name').classList.add('fw-medium');
                } else {
                    av.classList.remove('selected', 'opacity-100');
                    av.classList.add('opacity-75');
                    av.querySelector('.avatar-img').classList.remove('border-primary');
                    av.querySelector('.avatar-img').classList.add('border-transparent');
                    av.querySelector('.check-icon').classList.add('d-none');
                    av.querySelector('.avatar-name').classList.remove('fw-medium');
                }
            });
            
            // Re-sync hidden inputs after changing selections
            const modalContainer = document.getElementById('edit-modal-members-container');
            const hiddenContainer = document.getElementById('edit-hidden-member-inputs');
            if (typeof updateHiddenInputs === 'function') {
                updateHiddenInputs(modalContainer, hiddenContainer);
            }
        }, 100);
        
        // Show edit modal
        const editModal = new bootstrap.Modal(document.getElementById('editEventModal'));
        editModal.show();
    }

    function openEventModal(evt) {
        let dateStr = '';
        let timeStr = 'All Day';
        const dateOpts = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };

        if (evt.startStr) {
            const sd = new Date(evt.startStr.split('T')[0]);
            dateStr = sd.toLocaleDateString('en-US', dateOpts);
            
            if (evt.endStr && evt.endStr.split('T')[0] !== evt.startStr.split('T')[0]) {
                const ed = new Date(evt.endStr.split('T')[0]);
                dateStr += ' - ' + ed.toLocaleDateString('en-US', dateOpts);
            }
        }

        if (!evt.is_all_day && !evt.isAllDay && evt.startHour !== undefined) {
            timeStr = formatTime(evt.startHour);
            if (evt.duration) {
                timeStr += ' - ' + formatTime(evt.startHour + evt.duration);
            }
        }
        
        const isMobile = window.innerWidth < 992; // Bootstrap lg breakpoint

        if (isMobile) {
            const container = document.getElementById('mobileEventDetailsContainer');
            if (container) {
                document.getElementById('mobileEventTitle').textContent = evt.title;
                
                let subtitle = '';
                if (dateStr) subtitle += dateStr;
                if (evt.location) {
                    subtitle += (subtitle ? '  ' : '') + evt.location;
                }
                document.getElementById('mobileEventSubtitle').textContent = subtitle;
                
                const editContainer = document.getElementById('mobileEventEditContainer');
                const btnEdit = document.getElementById('mobileBtnEditEvent');
                const btnDelete = document.getElementById('mobileBtnDeleteEvent');
                
                if (evt.created_by == window.CURRENT_USER_ID) {
                    editContainer.style.setProperty('display', 'flex', 'important');
                    btnEdit.onclick = function(e) {
                        e.preventDefault();
                        container.classList.add('d-none');
                        setupEditModal(evt);
                    };
                    btnDelete.onclick = function(e) {
                        e.preventDefault();
                        if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                            window.location.href = `../helpers/deleteEvent.php?id=${evt.id}`;
                        }
                    };
                } else {
                    editContainer.style.setProperty('display', 'none', 'important');
                }
                
                container.classList.remove('d-none');
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }
        }

        // Desktop behavior
        document.getElementById('viewEventTitle').textContent = evt.title;
        document.getElementById('viewEventLocation').textContent = evt.location || 'No location specified';
        
        // Handle multiple members
        if (evt.members && evt.members.length > 0) {
            const memberNames = evt.members.map(m => getDisplayName({name: m.member, nickname: m.member_nickname})).join(', ');
            document.getElementById('viewEventMember').textContent = memberNames;
        } else {
            document.getElementById('viewEventMember').textContent = 'Unassigned';
        }
        
        const dateEl = document.getElementById('viewEventDate');
        if (dateEl) dateEl.textContent = dateStr;

        document.getElementById('viewEventTime').textContent = timeStr;
        
        const editContainer = document.getElementById('viewEventEditContainer');
        const btnEdit = document.getElementById('btnEditEvent');
        const btnDelete = document.getElementById('btnDeleteEvent');
        if (evt.created_by == window.CURRENT_USER_ID) {
            editContainer.style.setProperty('display', 'flex', 'important');
            
            // Set up Edit button
            btnEdit.onclick = function(e) {
                e.preventDefault();
                // Close view modal
                const viewModalEl = document.getElementById('viewEventModal');
                let viewModal = bootstrap.Modal.getInstance(viewModalEl);
                if (!viewModal) {
                    viewModal = new bootstrap.Modal(viewModalEl);
                }
                viewModal.hide();
                setupEditModal(evt);
            };

            // Set up Delete button
            if (btnDelete) {
                btnDelete.onclick = function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                        window.location.href = `../helpers/deleteEvent.php?id=${evt.id}`;
                    }
                };
            }
        } else {
            editContainer.style.setProperty('display', 'none', 'important');
        }
        
        const viewModalEl = document.getElementById('viewEventModal');
        let modal = bootstrap.Modal.getInstance(viewModalEl);
        if (!modal) {
            modal = new bootstrap.Modal(viewModalEl);
        }
        modal.show();
    }

    function refreshCalendarUI() {
        updateWeekHeaders();
        updateMealsData();
        renderMealsWeek();
        renderTimedEventsWeek();
        renderAllDayEventsWeek();
        renderCountdownEventsWeek();
        renderCountdownEventsMonth();
        renderTop3Countdowns();
        if (currentView === 'view-day') renderDayView(currentDate);
        if (window.calendarInstance) window.calendarInstance.refetchEvents();
        
        // Re-apply member filters after UI refresh
        applyFilters();
    }

    // 4. Meals Rendering (Week View)
    function renderMealsWeek() {
        const mealsContainer = document.getElementById('meals-container');
        if (!mealsContainer) return;
        mealsContainer.innerHTML = '';
        mealsData.forEach(mealType => {
            const row = document.createElement('div');
            row.className = 'calendar-row d-flex border-bottom';
            let cellsHtml = `
                <div class="time-col border-end p-2 d-flex align-items-center fw-bold text-dark fs-7">
                    <i class="${mealType.icon} text-${mealType.color} me-2"></i> ${mealType.type}
                </div>
            `;
            mealType.menu.forEach(item => {
                const imgOp = item.empty ? 'opacity: 0.5;' : '';
                const textCls = item.empty ? 'text-muted fst-italic' : 'text-dark fw-bold';
                const defaultImg = item.empty ? '../public/img/unknown.png' : `../public/img/${item.type}.webp`;
                cellsHtml += `
                    <div class="day-col flex-fill p-2">
                        <div class="meal-card-wrapper">
                            <div class="meal-card border-0">
                                <div class="d-flex align-items-center gap-1">
                                    <img src="${item.img}" alt="${item.name}" class="meal-recipe-img" width="30" height="30" 
                                         style="object-fit: cover; border-radius: 4px; ${imgOp}" 
                                         onerror="this.src='${defaultImg}';">
                                    <div class="meal-title ${textCls} fs-7 lh-sm" style="font-size: 0.75rem;">${item.name}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            row.innerHTML = cellsHtml;
            mealsContainer.appendChild(row);
        });
    }

    // 4. Time Grid Setup
    // 4. (Removed Time Grid Setup)

    // 5. Helper Functions
    function formatTime(decimalHour) {
        let h = Math.floor(decimalHour);
        let m = Math.round((decimalHour - h) * 60);
        let suffix = h >= 12 ? 'PM' : 'AM';
        if (h > 12) h -= 12;
        if (h === 0) h = 12;
        return `${h}:${m.toString().padStart(2, '0')} ${suffix}`;
    }

    // Color Utility Helpers
    function hexToRgb(hex) {
        if (!hex) return { r: 0, g: 0, b: 0 };
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : { r: 0, g: 0, b: 0 };
    }

    function lightenColor(hex, percent) {
        const rgb = hexToRgb(hex);
        const r = Math.round(rgb.r + (255 - rgb.r) * (percent / 100));
        const g = Math.round(rgb.g + (255 - rgb.g) * (percent / 100));
        const b = Math.round(rgb.b + (255 - rgb.b) * (percent / 100));
        return `rgb(${r}, ${g}, ${b})`;
    }

    function getContrastColor(hex) {
        let r, g, b;
        if (hex.startsWith('rgb')) {
            const parts = hex.match(/\d+/g);
            r = parseInt(parts[0]);
            g = parseInt(parts[1]);
            b = parseInt(parts[2]);
        } else {
            const rgb = hexToRgb(hex);
            r = rgb.r; g = rgb.g; b = rgb.b;
        }
        const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return (yiq >= 128) ? 'black' : 'white';
    }

    // 6. Render Events (Week View)
    function createEventBlockHtml(evt, showLocation = true) {
        const startStr = formatTime(evt.startHour);
        
        let baseColor = evt.colorCode || '#0d6efd';
        let memberNames = 'Unassigned';
        
        if (evt.members && evt.members.length > 0) {
            memberNames = evt.members.map(m => getDisplayName({name: m.member, nickname: m.member_nickname})).join(', ');
            if (evt.members.length > 1) {
                baseColor = '#6f42c1'; // Purple for shared events
            }
        }
        
        const bgColor = lightenColor(baseColor, 92);
        const textColor = getContrastColor(bgColor);

        return `
            <div class="event-time" style="color: ${baseColor}"><i class="fa-regular fa-clock me-1"></i> ${startStr}</div>
            <div class="event-title" style="color: ${textColor === 'white' ? '#fff' : '#1a1a1a'}">${evt.title}</div>
            ${showLocation && evt.location ? `<div class="event-location"><i class="fa-solid fa-location-dot me-1"></i> ${evt.location}</div>` : ''}
            <div class="event-assigned" style="color: ${baseColor}">${memberNames}</div>
        `;
    }

    // 6. Render Timed Events (Week View)
    function renderTimedEventsWeek() {
        const container = document.getElementById('week-events-list-container');
        if (!container) return;
        container.innerHTML = '';
        container.className = 'd-flex flex-column w-100 gap-2 p-3 bg-white';

        const week = getWeekRange(currentDate);
        // Normalize week start/end to midnight for accurate comparison
        const weekStart = new Date(week.start);
        weekStart.setHours(0, 0, 0, 0);
        const weekEnd = new Date(week.end);
        weekEnd.setHours(23, 59, 59, 999);

        const currentWeekEvents = events.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(23, 59, 59, 999);
            return startD <= weekEnd && endD >= weekStart;
        });

        // Sort events chronologically
        currentWeekEvents.sort((a, b) => a.startHour - b.startHour);

        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(week.start);
            dayDate.setDate(dayDate.getDate() + i);
            dayDate.setHours(0, 0, 0, 0);
            const isToday = dayDate.toDateString() === new Date().toDateString();

            const dayEvents = currentWeekEvents.filter(e => {
                const startD = new Date(e.startStr.split('T')[0]);
                startD.setHours(0, 0, 0, 0);
                const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
                endD.setHours(0, 0, 0, 0);
                return dayDate >= startD && dayDate <= endD;
            });

            const row = document.createElement('div');
            row.className = 'd-flex border-bottom pb-3 pt-2';

            // Left side: Day label
            const leftCol = document.createElement('div');
            leftCol.className = 'd-flex flex-column align-items-center fw-bold text-muted me-3';
            leftCol.style.width = '50px';
            leftCol.innerHTML = `
                <span class="fs-7 text-dark">${daysOfWeek[i]}</span>
                <span class="fs-5 mt-1 ${isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : 'text-dark fw-normal'}" 
                      style="${isToday ? 'width: 32px; height: 32px;' : ''}">${dayDate.getDate()}</span>
            `;

            // Right side: Events list
            const rightCol = document.createElement('div');
            rightCol.className = 'flex-fill d-flex flex-column gap-2';

            if (dayEvents.length > 0) {
                dayEvents.forEach(evt => {
                    const block = document.createElement('div');
                    block.className = `event-block position-relative rounded p-2 shadow-sm w-100 d-flex align-items-center justify-content-between`;
                    
                    let baseColor = evt.colorCode || '#0d6efd';
                    let memberNamesList = [];
                    let initialStr = 'U';
                    
                    if (evt.members && evt.members.length > 0) {
                        memberNamesList = evt.members.map(m => m.member.toLowerCase());
                        initialStr = getDisplayName({name: evt.members[0].member, nickname: evt.members[0].member_nickname}).charAt(0).toUpperCase();
                        if (evt.members.length > 1) {
                            baseColor = '#6f42c1';
                            initialStr = '' + evt.members.length; // Number of members
                        }
                    }
                    
                    block.setAttribute('data-members', JSON.stringify(memberNamesList));

                    const bgColor = lightenColor(baseColor, 92);
                    block.style.backgroundColor = bgColor;
                    block.style.borderLeft = `4px solid ${baseColor}`;

                    const startStr = formatTime(evt.startHour);
                    const endStr = formatTime(evt.startHour + evt.duration);

                    block.innerHTML = `
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark fs-7" style="line-height: 1.2;">${evt.title}</span>
                            <span class="text-muted mt-1" style="font-size: 0.75rem;">${startStr} - ${endStr}</span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                             style="width: 24px; height: 24px; background-color: ${baseColor}; font-size: 0.7rem;" title="Assigned: ${evt.members ? evt.members.map(m => m.member).join(', ') : 'Unassigned'}">
                            ${initialStr}
                        </div>
                    `;
                    block.style.cursor = 'pointer';
                    block.addEventListener('click', () => openEventModal(evt));
                    rightCol.appendChild(block);
                });
            } else {
                rightCol.innerHTML = '<div class="text-muted fst-italic fs-7 mt-2">No scheduled events</div>';
            }

            row.appendChild(leftCol);
            row.appendChild(rightCol);
            container.appendChild(row);
        }
    }

    // 7. Render All Day Events (Week View)
    function renderAllDayEventsWeek() {
        const allDayContainer = document.getElementById('all-day-events-container');
        if (!allDayContainer) return;
        allDayContainer.innerHTML = '';

        const week = getWeekRange(currentDate);
        // Normalize week start/end to midnight for accurate comparison
        const weekStart = new Date(week.start);
        weekStart.setHours(0, 0, 0, 0);
        const weekEnd = new Date(week.end);
        weekEnd.setHours(23, 59, 59, 999);

        const currentWeekAllDayEvents = allDayEvents.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(23, 59, 59, 999);
            return startD <= weekEnd && endD >= weekStart;
        });

        currentWeekAllDayEvents.forEach(evt => {
            const div = document.createElement('div');
            div.className = `all-day-event`;

            let baseColor = evt.colorCode || '#0d6efd';
            let memberNamesList = [];
            if (evt.members && evt.members.length > 0) {
                memberNamesList = evt.members.map(m => m.member.toLowerCase());
                if (evt.members.length > 1) {
                    baseColor = '#6f42c1';
                }
            }

            div.style.backgroundColor = lightenColor(baseColor, 92);
            div.style.color = baseColor;
            div.style.borderLeft = `4px solid ${baseColor}`;

            div.innerHTML = `<i class="fa-solid fa-calendar-day"></i> ${evt.title}`;
            div.setAttribute('data-members', JSON.stringify(memberNamesList));
            div.style.cursor = 'pointer';
            div.addEventListener('click', () => openEventModal(evt));
            allDayContainer.appendChild(div);
        });
    }

    // 7.5 Render Countdown Events
    function renderCountdownEventsWeek() {
        const countdownContainer = document.getElementById('calendar-countdown-container');
        const countdownRow = document.querySelector('.countdown-row');
        if (!countdownContainer || !countdownRow) return;
        countdownContainer.innerHTML = '';

        const allEvents = [...events, ...allDayEvents];
        const countdownEventsList = allEvents.filter(e => e.countdown == 1 || e.countdown === true);

        const now = new Date();
        now.setHours(0, 0, 0, 0);

        const upcomingCountdowns = countdownEventsList.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            return startD >= now;
        });

        upcomingCountdowns.sort((a, b) => new Date(a.startStr.split('T')[0]) - new Date(b.startStr.split('T')[0]));

        if (upcomingCountdowns.length > 0) {
            countdownRow.classList.remove('d-none');
            countdownRow.classList.add('d-flex');
            
            upcomingCountdowns.forEach(evt => {
                const div = document.createElement('div');
                div.className = `countdown-event-badge d-inline-flex align-items-center rounded px-2 py-1 me-2 fs-8`;
                
                const baseColor = evt.colorCode || '#0d6efd';
                div.style.backgroundColor = lightenColor(baseColor, 92);
                div.style.color = baseColor;
                div.style.borderLeft = `4px solid ${baseColor}`;
                
                const eventDate = new Date(evt.startStr.replace(' ', 'T'));
                const actualNow = new Date().getTime();
                const diffTime = eventDate.getTime() - actualNow;
                
                let dayText = '';
                if (diffTime > 0 && diffTime <= 48 * 60 * 60 * 1000) {
                    dayText = `<span class="live-countdown-timer fw-bold me-2" data-target="${eventDate.getTime()}">--:--:--</span>`;
                } else {
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    dayText = `<span class="fw-bold me-2">${diffDays <= 0 ? 'Today' : (diffDays === 1 ? '1 day' : diffDays + ' days')}</span>`;
                }

                div.innerHTML = `${dayText} <span style="opacity: 0.85;">| <i class="fa-solid fa-calendar-day mx-1"></i> ${evt.title}</span>`;
                div.setAttribute('data-member', evt.member);
                div.style.cursor = 'pointer';
                div.addEventListener('click', () => openEventModal(evt));
                countdownContainer.appendChild(div);
            });
        } else {
            countdownRow.classList.remove('d-flex');
            countdownRow.classList.add('d-none');
        }
    }

    function renderCountdownEventsMonth() {
        const countdownContainer = document.getElementById('month-countdown-container');
        const countdownRow = document.querySelector('.countdown-row-month');
        if (!countdownContainer || !countdownRow) return;
        countdownContainer.innerHTML = '';

        const allEvents = [...events, ...allDayEvents];
        const countdownEventsList = allEvents.filter(e => e.countdown == 1 || e.countdown === true);

        const now = new Date();
        now.setHours(0, 0, 0, 0);

        const upcomingCountdowns = countdownEventsList.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            return startD >= now;
        });

        upcomingCountdowns.sort((a, b) => new Date(a.startStr.split('T')[0]) - new Date(b.startStr.split('T')[0]));

        if (upcomingCountdowns.length > 0) {
            countdownRow.classList.remove('d-none');
            countdownRow.classList.add('d-flex');
            
            upcomingCountdowns.forEach(evt => {
                const div = document.createElement('div');
                div.className = `countdown-event-badge d-inline-flex align-items-center rounded px-2 py-1 me-2 fs-8`;
                
                const baseColor = evt.colorCode || '#0d6efd';
                div.style.backgroundColor = lightenColor(baseColor, 92);
                div.style.color = baseColor;
                div.style.borderLeft = `4px solid ${baseColor}`;
                
                const eventDate = new Date(evt.startStr.replace(' ', 'T'));
                const actualNow = new Date().getTime();
                const diffTime = eventDate.getTime() - actualNow;
                
                let dayText = '';
                if (diffTime > 0 && diffTime <= 48 * 60 * 60 * 1000) {
                    dayText = `<span class="live-countdown-timer fw-bold me-2" data-target="${eventDate.getTime()}">--:--:--</span>`;
                } else {
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    dayText = `<span class="fw-bold me-2">${diffDays <= 0 ? 'Today' : (diffDays === 1 ? '1 day' : diffDays + ' days')}</span>`;
                }

                div.innerHTML = `${dayText} <span style="opacity: 0.85;">| <i class="fa-solid fa-calendar-day mx-1"></i> ${evt.title}</span>`;
                div.setAttribute('data-member', evt.member);
                div.style.cursor = 'pointer';
                div.addEventListener('click', () => openEventModal(evt));
                countdownContainer.appendChild(div);
            });
        } else {
            countdownRow.classList.remove('d-flex');
            countdownRow.classList.add('d-none');
        }
    }

    function renderTop3Countdowns() {
        const countdownLists = document.querySelectorAll('.countdown-list');
        if (countdownLists.length === 0) return;

        const allEvents = [...events, ...allDayEvents];
        const countdownEventsList = allEvents.filter(e => e.countdown == 1 || e.countdown === true);

        const now = new Date();
        now.setHours(0, 0, 0, 0);

        const upcomingCountdowns = countdownEventsList.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            return startD >= now;
        });

        upcomingCountdowns.sort((a, b) => new Date(a.startStr.split('T')[0]) - new Date(b.startStr.split('T')[0]));
        const top3 = upcomingCountdowns.slice(0, 3);

        countdownLists.forEach(list => {
            list.innerHTML = '';
            if (top3.length === 0) {
                list.innerHTML = '<div class="text-muted text-center fs-7 py-3">No upcoming countdowns</div>';
                return;
            }

            top3.forEach((evt, idx) => {
                const eventDate = new Date(evt.startStr.replace(' ', 'T'));
                const actualNow = new Date().getTime();
                const diffTime = eventDate.getTime() - actualNow;
                
                let timeDisplayHTML = '';
                if (diffTime > 0 && diffTime <= 48 * 60 * 60 * 1000) {
                    timeDisplayHTML = `
                        <h4 class="mb-0 fw-bold live-countdown-timer fs-5" data-target="${eventDate.getTime()}">--:--:--</h4>
                        <small class="text-muted" style="font-size: 0.7rem;">Time Left</small>
                    `;
                } else {
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    timeDisplayHTML = `
                        <h4 class="mb-0 fw-bold">${diffDays <= 0 ? 0 : diffDays}</h4>
                        <small class="text-muted" style="font-size: 0.7rem;">${diffDays <= 1 ? 'Day' : 'Days'}</small>
                    `;
                }

                // Give different icons based on index
                const icons = ['fa-car-side text-primary', 'fa-cake-candles text-danger', 'fa-suitcase-rolling text-success'];
                const bgColors = ['bg-primary bg-opacity-10', 'bg-danger bg-opacity-10', 'bg-success bg-opacity-10'];
                
                const iconClass = icons[idx % icons.length];
                const bgClass = bgColors[idx % bgColors.length];
                
                const dateOpts = { month: 'short', day: 'numeric', year: 'numeric' };
                const formattedDate = eventDate.toLocaleDateString('en-US', dateOpts);

                const itemHTML = `
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded p-2 me-3 fs-3 d-flex align-items-center justify-content-center ${bgClass}" style="width: 45px; height: 45px;">
                            <i class="fa-solid ${iconClass} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-truncate" style="max-width: 140px;">${evt.title}</h6>
                            <small class="text-muted">${formattedDate}</small>
                        </div>
                        <div class="text-end ps-2">
                            ${timeDisplayHTML}
                        </div>
                    </div>
                `;
                
                const wrapper = document.createElement('div');
                wrapper.innerHTML = itemHTML.trim();
                const element = wrapper.firstElementChild;
                element.style.cursor = 'pointer';
                element.addEventListener('click', () => {
                    // if it's the index page, open modal
                    if (typeof openEventModal === 'function') openEventModal(evt);
                });
                
                list.appendChild(element);
            });
        });
    }

    // 6. View Toggling & Navigation State
    let savedView = localStorage.getItem('default_calendar_view');
    let currentView = savedView ? 'view-' + savedView : 'view-week';
    let currentDate = new Date(); // Today

    const toggleBtns = document.querySelectorAll('.toggle-view-btn');
    const views = {
        'view-day': document.getElementById('view-day'),
        'view-week': document.getElementById('view-week'),
        'view-month': document.getElementById('view-month')
    };

    function updateActiveView(targetId) {
        currentView = targetId;

        // Update button styles
        toggleBtns.forEach(b => {
            const isMatch = b.getAttribute('data-target') === targetId;
            b.classList.toggle('btn-primary', isMatch);
            b.classList.toggle('btn-white', !isMatch);
            b.classList.toggle('text-muted', !isMatch);
        });

        // Toggle visibility
        Object.keys(views).forEach(id => {
            if (views[id]) {
                views[id].classList.toggle('d-none', id !== targetId);
            }
        });

        // Refresh specific view content
        if (targetId === 'view-day') {
            renderDayView(currentDate);
        } else if (targetId === 'view-month' && window.calendarInstance) {
            setTimeout(() => {
                window.calendarInstance.render();
                window.calendarInstance.gotoDate(currentDate);
            }, 50);
        }

        updateDateText(currentDate);
    }

    if (toggleBtns.length > 0) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const targetId = e.currentTarget.getAttribute('data-target');
                updateActiveView(targetId);
            });
        });
    }

    function renderDayView(date) {
        const container = document.getElementById('day-view-container');
        if (!container) return;
        container.innerHTML = '';

        // Get day of week (0-6)
        const dayIndex = date.getDay();

        // 0. Render Countdowns (Upcoming)
        const allEventsList = [...events, ...allDayEvents];
        const countdownEventsList = allEventsList.filter(e => e.countdown == 1 || e.countdown === true);
        const todayAtMidnight = new Date();
        todayAtMidnight.setHours(0, 0, 0, 0);

        const upcomingCountdowns = countdownEventsList.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            return startD >= todayAtMidnight;
        });

        if (upcomingCountdowns.length > 0) {
            upcomingCountdowns.sort((a, b) => new Date(a.startStr.split('T')[0]) - new Date(b.startStr.split('T')[0]));
            
            const countdownRow = document.createElement('div');
            countdownRow.className = 'day-view-row d-flex align-items-center p-3 border-bottom bg-white';
            
            countdownRow.innerHTML = `
                <div class="row-left d-flex align-items-center" style="width: 150px;">
                    <i class="fa-solid fa-hourglass-half text-primary fs-5 me-3"></i>
                    <span class="fw-bold text-dark">Countdowns</span>
                </div>
                <div class="row-content flex-grow-1 d-flex flex-wrap gap-2 countdown-badges-container">
                    <!-- Badges populated dynamically -->
                </div>
            `;
            container.appendChild(countdownRow);
            
            const badgesContainer = countdownRow.querySelector('.countdown-badges-container');
            
            upcomingCountdowns.forEach(evt => {
                const div = document.createElement('div');
                div.className = `countdown-event-badge d-inline-flex align-items-center rounded px-2 py-1 fs-8`;
                
                const baseColor = evt.colorCode || '#0d6efd';
                div.style.backgroundColor = lightenColor(baseColor, 92);
                div.style.color = baseColor;
                div.style.borderLeft = `4px solid ${baseColor}`;
                
                const eventDate = new Date(evt.startStr.replace(' ', 'T'));
                const actualNow = new Date().getTime();
                const diffTime = eventDate.getTime() - actualNow;
                
                let dayText = '';
                if (diffTime > 0 && diffTime <= 48 * 60 * 60 * 1000) {
                    dayText = `<span class="live-countdown-timer fw-bold me-2" data-target="${eventDate.getTime()}">--:--:--</span>`;
                } else {
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    dayText = `<span class="fw-bold me-2">${diffDays <= 0 ? 'Today' : (diffDays === 1 ? '1 day' : diffDays + ' days')}</span>`;
                }

                div.innerHTML = `${dayText} <span style="opacity: 0.85;">| <i class="fa-solid fa-calendar-day mx-1"></i> ${evt.title}</span>`;
                div.setAttribute('data-member', evt.member);
                div.style.cursor = 'pointer';
                div.addEventListener('click', () => openEventModal(evt));
                badgesContainer.appendChild(div);
            });
        }

        // 1. Render Meals for the Day
        const mealsForRow = mealsData.map(m => ({
            type: m.type,
            icon: m.icon,
            color: m.color,
            time: m.time,
            item: m.menu[dayIndex] || m.menu[0]
        }));

        mealsForRow.forEach(meal => {
            const row = document.createElement('div');
            row.className = 'day-view-row meal-card-wrapper d-flex align-items-center p-3 border-bottom';
            const defaultImg = meal.item.empty ? '../public/img/unknown.png' : `../public/img/${meal.item.type}.webp`;
            row.innerHTML = `
                    <div class="row-left d-flex align-items-center" style="width: 150px;">
                        <i class="${meal.icon} text-${meal.color} fs-5 me-3"></i>
                        <span class="fw-bold text-dark">${meal.type}</span>
                    </div>
                    <div class="row-time text-muted fs-7" style="width: 100px;">${meal.time}</div>
                    <div class="row-content flex-grow-1 d-flex align-items-center">
                        <img src="${meal.item.img}" class="meal-recipe-img me-3 rounded" width="50" height="50" 
                             style="object-fit: cover; ${meal.item.empty ? 'opacity:0.5;' : ''}"
                             onerror="this.src='${defaultImg}';">
                        <span class="${meal.item.empty ? 'text-muted fst-italic' : 'text-dark fw-medium'}">${meal.item.name}</span>
                    </div>
                    <div class="row-meta text-muted fs-7">Added by Mom</div>
                    <div class="ms-3 cursor-pointer"><i class="fa-solid fa-ellipsis text-muted"></i></div>
                `;
            container.appendChild(row);
        });

        // 2. All Day Events for this day
        const targetDate = new Date(date);
        targetDate.setHours(0, 0, 0, 0);

        const dayAllDayEvents = allDayEvents.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(0, 0, 0, 0);
            return targetDate >= startD && targetDate <= endD;
        });
        dayAllDayEvents.forEach(evt => {
            const allDayRow = document.createElement('div');
            allDayRow.className = 'day-view-row d-flex align-items-center p-3 border-bottom bg-light bg-opacity-25';

            let baseColor = evt.colorCode || '#0d6efd';
            if (evt.members && evt.members.length > 1) {
                baseColor = '#6f42c1';
            }
            const bgColor = lightenColor(baseColor, 92);

            allDayRow.innerHTML = `
                <div class="row-left d-flex align-items-center" style="width: 150px;">
                    <i class="fa-solid fa-circle text-primary fs-7 me-3"></i>
                    <span class="fw-bold text-dark">All Day</span>
                </div>
                <div class="row-time" style="width: 100px;"></div>
                <div class="row-content flex-grow-1 d-flex gap-2">
                    <div class="all-day-event" data-members='${JSON.stringify(evt.members ? evt.members.map(m => m.member.toLowerCase()) : [])}' style="background-color: ${bgColor}; color: ${baseColor}; border-left: 4px solid ${baseColor};">
                        <i class="fa-solid fa-calendar-day me-1"></i> ${evt.title}
                    </div>
                </div>
                <div class="row-meta text-muted fs-7 ps-3 d-flex align-items-center">
                    <i class="fa-solid fa-user me-1"></i> ${evt.members && evt.members.length > 0 ? evt.members.map(m => getDisplayName({name: m.member, nickname: m.member_nickname})).join(', ') : 'Unassigned'}
                </div>
            `;
            const eventDiv = allDayRow.querySelector('.all-day-event');
            if (eventDiv) {
                eventDiv.style.cursor = 'pointer';
                eventDiv.addEventListener('click', () => openEventModal(evt));
            }
            container.appendChild(allDayRow);
        });

        // 3. Time-based Events for this day
        const dayEvents = events.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(0, 0, 0, 0);
            return targetDate >= startD && targetDate <= endD;
        });
        dayEvents.sort((a, b) => a.startHour - b.startHour);

        if (dayEvents.length === 0) {
            const noEvents = document.createElement('div');
            noEvents.className = 'p-5 text-center text-muted';
            noEvents.innerHTML = '<i class="fa-regular fa-calendar-xmark fs-1 mb-3 d-block opacity-25"></i> No events scheduled for this day.';
            container.appendChild(noEvents);
        } else {
            dayEvents.forEach(evt => {
                const row = document.createElement('div');
                row.className = 'day-view-row d-flex align-items-center p-3 border-bottom';

                const startStr = formatTime(evt.startHour);
                let baseColor = evt.colorCode || '#0d6efd';
                if (evt.members && evt.members.length > 1) {
                    baseColor = '#6f42c1';
                }
                const bgColor = lightenColor(baseColor, 92);

                let memberMetaHtml = '';
                if (evt.members && evt.members.length > 1) {
                    memberMetaHtml = `<span class="me-2 fw-medium text-dark">${evt.members.length} Members</span>`;
                } else if (evt.members && evt.members.length === 1) {
                    const memberName = getDisplayName({name: evt.members[0].member, nickname: evt.members[0].member_nickname});
                    memberMetaHtml = `<span class="me-2 fw-medium text-dark">${memberName}</span>
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(memberName)}&background=random" class="rounded-circle shadow-sm border border-2 border-white" width="32" height="32" title="${memberName}">`;
                } else {
                    memberMetaHtml = `<span class="me-2 fw-medium text-dark">Unassigned</span>`;
                }

                row.innerHTML = `
                    <div class="row-left d-flex align-items-center" style="width: 150px;">
                        <i class="fa-regular fa-clock text-muted fs-6 me-3"></i>
                        <span class="fw-bold text-muted">${startStr}</span>
                    </div>
                    <div class="row-content flex-grow-1">
                        <div class="event-block position-relative" data-members='${JSON.stringify(evt.members ? evt.members.map(m => m.member.toLowerCase()) : [])}' style="background-color: ${bgColor}; border-left: 4px solid ${baseColor};">
                            ${createEventBlockHtml(evt, true)}
                        </div>
                    </div>
                    <div class="row-meta text-muted fs-7 d-flex align-items-center justify-content-end" style="width: 200px;">
                        <div class="avatar-stack d-flex align-items-center">
                            ${memberMetaHtml}
                        </div>
                    </div>
                `;
                const eventBlock = row.querySelector('.event-block');
                if (eventBlock) {
                    eventBlock.style.cursor = 'pointer';
                    eventBlock.addEventListener('click', () => openEventModal(evt));
                }
                container.appendChild(row);
            });
        }
        
        // Apply filters to newly rendered day view content
        applyFilters();
    }


    // Generate dynamic dates for current month
    const today = new Date();
    const y = today.getFullYear();
    const m = String(today.getMonth() + 1).padStart(2, '0');

    function showMobileDayDetails(dateStr) {
        const targetDate = new Date(dateStr);
        targetDate.setHours(0, 0, 0, 0);
        
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        document.getElementById('mobileMonthDayTitle').textContent = targetDate.toLocaleDateString(undefined, options);

        const container = document.getElementById('mobileMonthDayEventsList');
        container.innerHTML = ''; 

        const dayAllDayEvents = allDayEvents.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(0, 0, 0, 0);
            return targetDate >= startD && targetDate <= endD;
        });

        const dayEvents = events.filter(e => {
            const startD = new Date(e.startStr.split('T')[0]);
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0]) : new Date(startD);
            endD.setHours(0, 0, 0, 0);
            return targetDate >= startD && targetDate <= endD;
        }).sort((a, b) => a.startHour - b.startHour);

        const filteredAllDay = dayAllDayEvents.filter(e => {
            if (activeMemberFilter === 'all') return true;
            if (e.members) {
                return e.members.some(m => m.member.toLowerCase() === activeMemberFilter);
            }
            return false;
        });

        const filteredTimed = dayEvents.filter(e => {
            if (activeMemberFilter === 'all') return true;
            if (e.members) {
                return e.members.some(m => m.member.toLowerCase() === activeMemberFilter);
            }
            return false;
        });

        if (filteredAllDay.length === 0 && filteredTimed.length === 0) {
            const noEvents = document.createElement('div');
            noEvents.className = 'p-4 text-center text-muted';
            noEvents.innerHTML = '<i class="fa-regular fa-calendar-xmark fs-3 mb-2 d-block opacity-50"></i> <span style="font-size:0.9rem">No events scheduled</span>';
            container.appendChild(noEvents);
        } else {
            filteredAllDay.forEach(evt => {
                const allDayRow = document.createElement('div');
                allDayRow.className = 'd-flex align-items-center p-3 border-bottom bg-light bg-opacity-25';

                let baseColor = evt.colorCode || '#0d6efd';
                if (evt.members && evt.members.length > 1) {
                    baseColor = '#6f42c1';
                }
                const bgColor = lightenColor(baseColor, 92);

                allDayRow.innerHTML = `
                    <div class="row-content flex-grow-1 d-flex gap-2">
                        <div class="all-day-event w-100 m-0" style="background-color: ${bgColor}; color: ${baseColor}; border-left: 4px solid ${baseColor};">
                            <i class="fa-solid fa-calendar-day me-1"></i> ${evt.title}
                        </div>
                    </div>
                `;
                const eventDiv = allDayRow.querySelector('.all-day-event');
                if (eventDiv) {
                    eventDiv.style.cursor = 'pointer';
                    eventDiv.addEventListener('click', () => openEventModal(evt));
                }
                container.appendChild(allDayRow);
            });

            filteredTimed.forEach(evt => {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center p-3 border-bottom';

                let baseColor = evt.colorCode || '#0d6efd';
                if (evt.members && evt.members.length > 1) {
                    baseColor = '#6f42c1';
                }
                const bgColor = lightenColor(baseColor, 92);

                row.innerHTML = `
                    <div class="row-content flex-grow-1 w-100">
                        <div class="event-block position-relative m-0" style="background-color: ${bgColor}; border-left: 4px solid ${baseColor};">
                            ${createEventBlockHtml(evt, true)}
                        </div>
                    </div>
                `;
                const eventDiv = row.querySelector('.event-block');
                if (eventDiv) {
                    eventDiv.style.cursor = 'pointer';
                    eventDiv.addEventListener('click', () => openEventModal(evt));
                }
                container.appendChild(row);
            });
        }

        const detailsContainer = document.getElementById('mobileMonthDayDetailsContainer');
        detailsContainer.classList.remove('d-none');
        
        detailsContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // 8. Initialize FullCalendar for Month View
    const calendarEl = document.getElementById('full-calendar-container');
    if (calendarEl) {
        window.calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            height: 'auto',
            events: function (info, successCallback) {
                const timed = events.map(e => ({
                    title: e.title,
                    start: e.startStr,
                    end: e.endStr,
                    className: `fc-event-dynamic`,
                    extendedProps: {
                        id: e.id,
                        created_by: e.created_by,
                        member: e.member,
                        member_nickname: e.member_nickname,
                        members: e.members,
                        colorCode: e.colorCode,
                        startHour: e.startHour,
                        duration: e.duration,
                        location: e.location,
                        countdown: e.countdown,
                        isAllDay: false
                    }
                }));

                const allDay = allDayEvents.map(e => ({
                    title: e.title,
                    start: e.startStr.split('T')[0],
                    allDay: true,
                    className: `fc-event-dynamic`,
                    extendedProps: {
                        id: e.id,
                        created_by: e.created_by,
                        member: e.member,
                        member_nickname: e.member_nickname,
                        members: e.members,
                        colorCode: e.colorCode,
                        location: e.location,
                        countdown: e.countdown,
                        isAllDay: true
                    }
                }));

                const combined = [...timed, ...allDay];
                const filtered = combined.filter(e => {
                    if (activeMemberFilter === 'all') return true;
                    if (e.extendedProps.members) {
                        return e.extendedProps.members.some(m => m.member.toLowerCase() === activeMemberFilter);
                    }
                    return false;
                });
                successCallback(filtered);
            },
            eventClick: function(info) {
                if (window.innerWidth < 992) {
                    return; // Handled by dateClick due to pointer-events: none on mobile month events
                }
                const props = info.event.extendedProps;
                openEventModal({
                    title: info.event.title,
                    location: props.location,
                    member: props.member,
                    member_nickname: props.member_nickname,
                    members: props.members,
                    startHour: props.startHour,
                    duration: props.duration,
                    isAllDay: props.isAllDay,
                    created_by: props.created_by,
                    countdown: props.countdown,
                    id: props.id,
                    startStr: info.event.startStr,
                    endStr: info.event.endStr
                });
            },
            dateClick: function(info) {
                if (window.innerWidth < 992) {
                    showMobileDayDetails(info.dateStr);
                }
            },
            eventContent: function (info) {
                const props = info.event.extendedProps;
                let baseColor = props.colorCode || '#0d6efd';
                if (props.members && props.members.length > 1) {
                    baseColor = '#6f42c1';
                }
                const bgColor = lightenColor(baseColor, 92);

                if (props.isAllDay) {
                    return {
                        html: `
                            <div class="all-day-event" style="background-color: ${bgColor}; color: ${baseColor}; border-left: 4px solid ${baseColor}; box-shadow:none; padding: 2px 8px;">
                                <i class="fa-solid fa-calendar-day me-1"></i> ${info.event.title}
                            </div>
                        `
                    };
                }

                // Use the same structure as event-block
                const evtData = {
                    startHour: props.startHour,
                    title: info.event.title,
                    member: props.member,
                    members: props.members,
                    colorCode: props.colorCode,
                    location: props.location
                };

                return {
                    html: `
                        <div class="event-block" style="background-color: ${bgColor}; border-left: 4px solid ${baseColor};">
                            ${createEventBlockHtml(evtData, false)}
                        </div>
                    `
                };
            }
        });
    }

    // 9. Initialize Datepicker Universally
    const $datePicker = $('#date-picker-btn');
    const dateBtnText = document.getElementById('date-picker-btn');

    function updateDateText(date) {
        if (dateBtnText) {
            if (currentView === 'view-week') {
                const range = getWeekRange(date);
                const options = { month: 'short', day: 'numeric' };
                const yearOptions = { year: 'numeric' };
                dateBtnText.innerHTML = '<i class="fa-regular fa-calendar me-2"></i> ' +
                    `${range.start.toLocaleDateString('en-US', options)} – ${range.end.toLocaleDateString('en-US', options)}, ${range.end.toLocaleDateString('en-US', yearOptions)} <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>`;
            } else {
                const options = { month: 'short', day: 'numeric', year: 'numeric' };
                dateBtnText.innerHTML = '<i class="fa-regular fa-calendar me-2"></i> ' + date.toLocaleDateString('en-US', options) + ' <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>';
            }
        }
    }

    if ($datePicker.length) {
        $datePicker.datepicker({
            format: 'MM dd, yyyy',
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function (e) {
            if (window.calendarInstance) {
                window.calendarInstance.gotoDate(e.date);
            }
            updateDateText(e.date);
        });
    }

    // 10. Navigation Buttons Unification
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnToday = document.getElementById('btn-today');

    function navigate(direction) {
        if (currentView === 'view-month' && window.calendarInstance) {
            if (direction === 'prev') window.calendarInstance.prev();
            else if (direction === 'next') window.calendarInstance.next();
            else window.calendarInstance.today();
            currentDate = window.calendarInstance.getDate();
        } else if (currentView === 'view-day') {
            const days = direction === 'prev' ? -1 : (direction === 'next' ? 1 : 0);
            if (days === 0) currentDate = new Date();
            else currentDate.setDate(currentDate.getDate() + days);
            renderDayView(currentDate);
        } else {
            // Week view navigation
            const days = direction === 'prev' ? -7 : (direction === 'next' ? 7 : 0);
            if (days === 0) currentDate = new Date();
            else currentDate.setDate(currentDate.getDate() + days);
            refreshCalendarUI();
        }
        updateDateText(currentDate);
    }

    if (btnPrev) btnPrev.addEventListener('click', () => navigate('prev'));
    if (btnNext) btnNext.addEventListener('click', () => navigate('next'));
    if (btnToday) btnToday.addEventListener('click', () => navigate('today'));

    // 10.5 Handle Save Event (Handled by normal PHP form submission now)

    // 11. Modal Avatar Selection Logic (Handled in populateModalMembers for dynamic elements)

    // 12. Wizard Navigation Logic (Family Setup)
    const setupWizardContainer = document.getElementById('setup-wizard-container');
    if (setupWizardContainer) {
        const steps = [
            document.getElementById('step1-content'),
            document.getElementById('step2-content'),
            document.getElementById('step3-content')
        ];
        const navSteps = [
            document.getElementById('nav-step-1'),
            document.getElementById('nav-step-2'),
            document.getElementById('nav-step-3')
        ];

        function showStep(stepIndex) {
            // Hide all steps
            steps.forEach(step => {
                if (step) step.classList.add('d-none');
            });

            // Show target step
            if (steps[stepIndex]) steps[stepIndex].classList.remove('d-none');

            // Update progress bar
            navSteps.forEach((navStep, idx) => {
                if (!navStep) return;

                const circle = navStep.querySelector('.step-circle');
                const title = navStep.querySelector('.step-title');
                const status = navStep.querySelector('.step-status');

                if (idx < stepIndex) {
                    // Completed
                    navStep.classList.remove('opacity-50');
                    circle.classList.remove('bg-primary', 'bg-secondary');
                    circle.classList.add('bg-success');
                    circle.innerHTML = '<i class="fa-solid fa-check"></i>';
                    title.classList.remove('text-primary');
                    title.classList.add('text-dark');
                    status.textContent = 'Completed';
                    status.classList.remove('text-primary', 'd-none');
                    status.classList.add('text-muted');
                } else if (idx === stepIndex) {
                    // In Progress
                    navStep.classList.remove('opacity-50');
                    circle.classList.remove('bg-secondary', 'bg-success');
                    circle.classList.add('bg-primary');
                    circle.innerHTML = (idx + 1).toString();
                    title.classList.remove('text-dark');
                    title.classList.add('text-primary');
                    status.textContent = 'In Progress';
                    status.classList.remove('text-muted', 'd-none');
                    status.classList.add('text-primary');
                } else {
                    // Pending
                    navStep.classList.add('opacity-50');
                    circle.classList.remove('bg-primary', 'bg-success');
                    circle.classList.add('bg-secondary');
                    circle.innerHTML = (idx + 1).toString();
                    title.classList.remove('text-primary');
                    title.classList.add('text-dark');
                    status.textContent = 'Pending';
                    status.classList.remove('text-primary', 'd-none');
                    status.classList.add('text-muted');
                }
            });
        }

        // Attach event listeners to buttons
        const btnNextList = document.querySelectorAll('.btn-next');
        const btnPrevList = document.querySelectorAll('.btn-prev');

        btnNextList.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const nextStep = parseInt(e.currentTarget.getAttribute('data-next')) - 1;
                showStep(nextStep);
            });
        });

        btnPrevList.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const prevStep = parseInt(e.currentTarget.getAttribute('data-prev')) - 1;
                showStep(prevStep);
            });
        });

        // 13. Appointment Types Card Selection Logic
        const apptCards = document.querySelectorAll('.appt-type-card');
        apptCards.forEach(card => {
            card.addEventListener('click', function (e) {
                // If the user clicked directly on the checkbox, let the default behavior happen but style the card
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                }

                const checkbox = this.querySelector('input[type="checkbox"]');
                if (checkbox.checked) {
                    this.classList.add('selected', 'border-primary', 'bg-primary', 'bg-opacity-10');
                    this.classList.remove('bg-white');
                    this.querySelector('.rounded-circle').classList.remove('bg-light');
                    this.querySelector('.rounded-circle').classList.add('bg-white');
                } else {
                    this.classList.remove('selected', 'border-primary', 'bg-primary', 'bg-opacity-10');
                    this.classList.add('bg-white');
                    this.querySelector('.rounded-circle').classList.add('bg-light');
                    this.querySelector('.rounded-circle').classList.remove('bg-white');
                }
            });
        });
    }

    // Mobile Month View Details Close Button
    const closeMobileMonthDayBtn = document.getElementById('closeMobileMonthDayBtn');
    if (closeMobileMonthDayBtn) {
        closeMobileMonthDayBtn.addEventListener('click', () => {
            document.getElementById('mobileMonthDayDetailsContainer').classList.add('d-none');
        });
    }

    // Grocery List Toggle Text
    const groceryCollapse = document.getElementById('moreGroceryItems');
    const toggleFullListBtn = document.getElementById('toggleFullList');
    if (groceryCollapse && toggleFullListBtn) {
        const btnText = toggleFullListBtn.querySelector('.button-text');
        groceryCollapse.addEventListener('shown.bs.collapse', () => {
            btnText.textContent = 'Show Less';
        });
        groceryCollapse.addEventListener('hidden.bs.collapse', () => {
            btnText.textContent = 'View Full List';
        });
    }

    // Initial render
    renderMealsWeek();
    renderTimedEventsWeek();
    renderAllDayEventsWeek();
    updateActiveView(currentView);

    // Timer to update live countdowns
    setInterval(() => {
        const now = new Date().getTime();
        document.querySelectorAll('.live-countdown-timer').forEach(el => {
            const targetTime = parseInt(el.getAttribute('data-target'), 10);
            if (isNaN(targetTime)) return;
            const diff = targetTime - now;
            if (diff > 0 && diff <= 48 * 60 * 60 * 1000) {
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((diff % (1000 * 60)) / 1000);
                el.textContent = `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            } else if (diff <= 0) {
                el.textContent = '00:00:00';
            }
        });
    }, 1000);

});
