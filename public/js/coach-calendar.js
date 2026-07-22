document.addEventListener('DOMContentLoaded', function () {
    // 1. Globals & Elements
    let currentView = 'view-week';
    let activeFamilyFilter = 'all';
    let allEvents = [];
    let currentDate = new Date();

    // Top Filter Selection
    document.querySelectorAll('.family-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.family-filter-btn').forEach(b => {
                b.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10', 'fw-bold');
                b.classList.add('border-transparent');
            });
            btn.classList.add('border-primary', 'bg-primary', 'bg-opacity-10', 'fw-bold');
            btn.classList.remove('border-transparent');

            activeFamilyFilter = btn.getAttribute('data-family-id');
            if (window.calendarInstance) window.calendarInstance.refetchEvents();
            refreshCalendarUI();
        });
    });

    // Custom View Renderers Helpers
    function getWeekRange(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day;
        const sunday = new Date(d.setDate(diff));
        const saturday = new Date(sunday);
        saturday.setDate(sunday.getDate() + 6);
        return { start: sunday, end: saturday };
    }

    function formatTime(decimalHour) {
        if (!decimalHour) return '';
        if (typeof decimalHour === 'string' && decimalHour.includes(':')) {
            const dateObj = new Date(decimalHour.replace(' ', 'T'));
            let h = dateObj.getHours();
            let m = dateObj.getMinutes();
            let suffix = h >= 12 ? 'PM' : 'AM';
            if (h > 12) h -= 12;
            if (h === 0) h = 12;
            return `${h}:${m.toString().padStart(2, '0')} ${suffix}`;
        }

        let h = Math.floor(decimalHour);
        let m = Math.round((decimalHour - h) * 60);
        let suffix = h >= 12 ? 'PM' : 'AM';
        if (h > 12) h -= 12;
        if (h === 0) h = 12;
        return `${h}:${m.toString().padStart(2, '0')} ${suffix}`;
    }

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
        if (!hex) return '#ffffff';
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

    function handleEventClick(evt) {
        const status = evt.tracking_status;
        if (status === 'family_completed' || status === 'coach_approved' || status === 'reopened') {
            document.getElementById('reviewEventId').value = evt.id;
            document.getElementById('reviewFeedback').value = evt.tracking_feedback || '';
            if (status === 'reopened') document.getElementById('statusReopen').checked = true;
            else document.getElementById('statusApprove').checked = true;
            const modal = new bootstrap.Modal(document.getElementById('coachReviewModal'));
            modal.show();
        } else {
            if (typeof showAlert === 'function') showAlert('This task is pending for the family.', 'info');
            else alert('This task is pending for the family.');
        }
    }

    // View Renderers
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

    function renderTimedEventsWeek() {
        const container = document.getElementById('week-events-list-container');
        if (!container) return;
        container.innerHTML = '';
        container.className = 'd-flex flex-column w-100 gap-2 p-3 bg-white';

        const week = getWeekRange(currentDate);
        const weekStart = new Date(week.start);
        weekStart.setHours(0, 0, 0, 0);
        const weekEnd = new Date(week.end);
        weekEnd.setHours(23, 59, 59, 999);

        const currentWeekEvents = allEvents.filter(e => {
            if (activeFamilyFilter !== 'all' && e.family_id.toString() !== activeFamilyFilter) return false;
            if (e.is_all_day) return false;
            const startD = new Date(e.startStr.split('T')[0] + 'T00:00:00');
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0] + 'T00:00:00') : new Date(startD);
            endD.setHours(23, 59, 59, 999);
            return startD <= weekEnd && endD >= weekStart;
        });

        currentWeekEvents.sort((a, b) => a.startHourDecimal - b.startHourDecimal);
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(week.start);
            dayDate.setDate(dayDate.getDate() + i);
            dayDate.setHours(0, 0, 0, 0);
            const isToday = dayDate.toDateString() === new Date().toDateString();

            const dayEvents = currentWeekEvents.filter(e => {
                const startD = new Date(e.startStr.split('T')[0] + 'T00:00:00');
                startD.setHours(0, 0, 0, 0);
                const endD = e.endStr ? new Date(e.endStr.split('T')[0] + 'T00:00:00') : new Date(startD);
                endD.setHours(0, 0, 0, 0);
                return dayDate >= startD && dayDate <= endD;
            });

            const row = document.createElement('div');
            row.className = 'd-flex border-bottom pb-3 pt-2';

            const leftCol = document.createElement('div');
            leftCol.className = 'd-flex flex-column align-items-center fw-bold text-muted me-3';
            leftCol.style.width = '50px';
            leftCol.innerHTML = `
                <span class="fs-7 text-dark">${daysOfWeek[i]}</span>
                <span class="fs-5 mt-1 ${isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : 'text-dark fw-normal'}" 
                      style="${isToday ? 'width: 32px; height: 32px;' : ''}">${dayDate.getDate()}</span>
            `;

            const rightCol = document.createElement('div');
            rightCol.className = 'flex-fill d-flex flex-column gap-2';

            if (dayEvents.length > 0) {
                dayEvents.forEach(evt => {
                    const block = document.createElement('div');
                    block.className = `event-block position-relative rounded p-2 shadow-sm w-100 d-flex align-items-center justify-content-between`;
                    
                    let baseColor = evt.colorCode || '#0d6efd';
                    const bgColor = lightenColor(baseColor, 92);
                    block.style.backgroundColor = bgColor;
                    block.style.borderLeft = `4px solid ${baseColor}`;

                    const startStr = formatTime(evt.startStr);
                    const endStr = evt.endStr ? formatTime(evt.endStr) : '';

                    let statusBadge = '';
                    if (evt.tracking_status === 'family_completed') {
                        statusBadge = `<span class="badge ms-2" style="background-color:#0dcaf0; color:#fff; font-size:0.65rem;">Needs Review</span>`;
                    } else if (evt.tracking_status === 'coach_approved') {
                        statusBadge = `<span class="badge ms-2" style="background-color:#198754; color:#fff; font-size:0.65rem;">Approved</span>`;
                    } else if (evt.tracking_status === 'reopened') {
                        statusBadge = `<span class="badge ms-2" style="background-color:#dc3545; color:#fff; font-size:0.65rem;">Reopened</span>`;
                    }

                    block.innerHTML = `
                        <div class="d-flex flex-column" style="z-index: 1;">
                            <span class="fw-bold text-dark fs-7" style="line-height: 1.2;">${evt.title}${statusBadge}</span>
                            <span class="text-muted mt-1" style="font-size: 0.75rem;">${startStr}${endStr ? ' - ' + endStr : ''}</span>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                             style="width: 24px; height: 24px; background-color: ${baseColor}; font-size: 0.7rem; z-index: 1;" title="${evt.family_name}">
                            <i class="fa-solid fa-people-roof"></i>
                        </div>
                    `;
                    block.style.cursor = 'pointer';
                    block.addEventListener('click', () => handleEventClick(evt));
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

    function renderAllDayEventsWeek() {
        const allDayContainer = document.getElementById('all-day-events-container');
        if (!allDayContainer) return;
        allDayContainer.innerHTML = '';

        const week = getWeekRange(currentDate);
        const weekStart = new Date(week.start);
        weekStart.setHours(0, 0, 0, 0);
        const weekEnd = new Date(week.end);
        weekEnd.setHours(23, 59, 59, 999);

        const currentWeekAllDayEvents = allEvents.filter(e => {
            if (activeFamilyFilter !== 'all' && e.family_id.toString() !== activeFamilyFilter) return false;
            if (!e.is_all_day) return false;
            const startD = new Date(e.startStr.split('T')[0] + 'T00:00:00');
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0] + 'T00:00:00') : new Date(startD);
            endD.setHours(23, 59, 59, 999);
            return startD <= weekEnd && endD >= weekStart;
        });

        currentWeekAllDayEvents.forEach(evt => {
            const div = document.createElement('div');
            div.className = `all-day-event p-1 ps-2 pe-2 rounded me-2 mb-1 d-inline-flex align-items-center`;
            let baseColor = evt.colorCode || '#0d6efd';

            div.style.backgroundColor = lightenColor(baseColor, 92);
            div.style.color = baseColor;
            div.style.borderLeft = `4px solid ${baseColor}`;
            div.style.position = 'relative';
            
            let statusBadge = '';
            if (evt.tracking_status === 'family_completed') {
                statusBadge = `<span class="badge ms-2" style="background-color:#0dcaf0; color:#fff; font-size:0.65rem;">Needs Review</span>`;
            } else if (evt.tracking_status === 'coach_approved') {
                statusBadge = `<span class="badge ms-2" style="background-color:#198754; color:#fff; font-size:0.65rem;">Approved</span>`;
            } else if (evt.tracking_status === 'reopened') {
                statusBadge = `<span class="badge ms-2" style="background-color:#dc3545; color:#fff; font-size:0.65rem;">Reopened</span>`;
            }

            div.innerHTML = `<div class="text-truncate fs-8 fw-medium" style="position:relative; z-index:1;"><i class="fa-solid fa-calendar-day"></i> ${evt.title} (${evt.family_name}) ${statusBadge}</div>`;
            div.style.cursor = 'pointer';
            div.addEventListener('click', () => handleEventClick(evt));
            allDayContainer.appendChild(div);
        });
    }

    function renderDayView(date) {
        const container = document.getElementById('day-view-container');
        if (!container) return;
        container.innerHTML = '';

        const targetDate = new Date(date);
        targetDate.setHours(0, 0, 0, 0);

        const dayEvents = allEvents.filter(e => {
            if (activeFamilyFilter !== 'all' && e.family_id.toString() !== activeFamilyFilter) return false;
            const startD = new Date(e.startStr.split('T')[0] + 'T00:00:00');
            startD.setHours(0, 0, 0, 0);
            const endD = e.endStr ? new Date(e.endStr.split('T')[0] + 'T00:00:00') : new Date(startD);
            endD.setHours(0, 0, 0, 0);
            return targetDate >= startD && targetDate <= endD;
        });

        const dayAllDayEvents = dayEvents.filter(e => e.is_all_day);
        const dayTimedEvents = dayEvents.filter(e => !e.is_all_day).sort((a, b) => a.startHourDecimal - b.startHourDecimal);

        // All Day Events
        dayAllDayEvents.forEach(evt => {
            const allDayRow = document.createElement('div');
            allDayRow.className = 'day-view-row d-flex align-items-center p-3 border-bottom bg-light bg-opacity-25';

            let baseColor = evt.colorCode || '#0d6efd';
            const bgColor = lightenColor(baseColor, 92);
            
            let statusBadge = '';
            if (evt.tracking_status === 'family_completed') statusBadge = `<span class="badge ms-2" style="background-color:#0dcaf0; color:#fff; font-size:0.65rem;">Needs Review</span>`;
            else if (evt.tracking_status === 'coach_approved') statusBadge = `<span class="badge ms-2" style="background-color:#198754; color:#fff; font-size:0.65rem;">Approved</span>`;
            else if (evt.tracking_status === 'reopened') statusBadge = `<span class="badge ms-2" style="background-color:#dc3545; color:#fff; font-size:0.65rem;">Reopened</span>`;

            allDayRow.innerHTML = `
                <div class="row-left d-flex align-items-center" style="width: 150px;">
                    <i class="fa-solid fa-circle text-primary fs-7 me-3"></i>
                    <span class="fw-bold text-dark">All Day</span>
                </div>
                <div class="row-time" style="width: 100px;"></div>
                <div class="row-content flex-grow-1 d-flex gap-2">
                    <div class="all-day-event p-2 rounded" style="background-color: ${bgColor}; color: ${baseColor}; border-left: 4px solid ${baseColor}; width: 100%;">
                        <i class="fa-solid fa-calendar-day me-1"></i> <strong>${evt.title}</strong> ${statusBadge}
                    </div>
                </div>
                <div class="row-meta text-muted fs-7 ps-3 d-flex align-items-center">
                    <i class="fa-solid fa-people-roof me-1"></i> ${evt.family_name}
                </div>
            `;
            const eventDiv = allDayRow.querySelector('.all-day-event');
            if (eventDiv) {
                eventDiv.style.cursor = 'pointer';
                eventDiv.addEventListener('click', () => handleEventClick(evt));
            }
            container.appendChild(allDayRow);
        });

        // Timed Events
        if (dayTimedEvents.length === 0 && dayAllDayEvents.length === 0) {
            const noEvents = document.createElement('div');
            noEvents.className = 'p-5 text-center text-muted';
            noEvents.innerHTML = '<i class="fa-regular fa-calendar-xmark fs-1 mb-3 d-block opacity-25"></i> No events scheduled for this day.';
            container.appendChild(noEvents);
        } else {
            dayTimedEvents.forEach(evt => {
                const row = document.createElement('div');
                row.className = 'day-view-row d-flex align-items-center p-3 border-bottom';

                const startStr = formatTime(evt.startStr);
                let baseColor = evt.colorCode || '#0d6efd';
                const bgColor = lightenColor(baseColor, 92);
                
                let statusBadge = '';
                if (evt.tracking_status === 'family_completed') statusBadge = `<span class="badge ms-2" style="background-color:#0dcaf0; color:#fff; font-size:0.65rem;">Needs Review</span>`;
                else if (evt.tracking_status === 'coach_approved') statusBadge = `<span class="badge ms-2" style="background-color:#198754; color:#fff; font-size:0.65rem;">Approved</span>`;
                else if (evt.tracking_status === 'reopened') statusBadge = `<span class="badge ms-2" style="background-color:#dc3545; color:#fff; font-size:0.65rem;">Reopened</span>`;

                row.innerHTML = `
                    <div class="row-left d-flex align-items-center" style="width: 150px;">
                        <i class="fa-regular fa-clock text-muted fs-6 me-3"></i>
                        <span class="fw-bold text-muted">${startStr}</span>
                    </div>
                    <div class="row-content flex-grow-1">
                        <div class="event-block position-relative" style="background-color: ${bgColor}; border-left: 4px solid ${baseColor}; cursor: pointer; padding: 0.5rem; border-radius: 4px;">
                            <div class="event-time" style="color: ${baseColor}; font-size:0.8rem; font-weight:bold;"><i class="fa-regular fa-clock me-1"></i> ${startStr}</div>
                            <div class="event-title mt-1" style="color: ${getContrastColor(bgColor) === 'white' ? '#fff' : '#1a1a1a'}; font-weight:600;">${evt.title} ${statusBadge}</div>
                        </div>
                    </div>
                    <div class="row-meta text-muted fs-7 d-flex align-items-center justify-content-end" style="width: 200px;">
                        <span class="me-2 fw-medium text-dark">${evt.family_name}</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white bg-secondary shadow-sm border border-2 border-white" style="width: 32px; height: 32px;"><i class="fa-solid fa-people-roof fs-6"></i></div>
                    </div>
                `;
                const eventBlock = row.querySelector('.event-block');
                if (eventBlock) {
                    eventBlock.addEventListener('click', () => handleEventClick(evt));
                }
                container.appendChild(row);
            });
        }
    }

    function refreshCalendarUI() {
        if (currentView === 'view-week') {
            updateWeekHeaders();
            renderTimedEventsWeek();
            renderAllDayEventsWeek();
        } else if (currentView === 'view-day') {
            renderDayView(currentDate);
        }
    }

    // 2. FullCalendar Initialization
    const calendarEl = document.getElementById('full-calendar-container');
    if (calendarEl) {
        window.calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            height: 700,
            allDaySlot: true,
            slotMinTime: '06:00:00',
            slotMaxTime: '22:00:00',
            events: function (info, successCallback, failureCallback) {
                const start = info.startStr.split('T')[0];
                const end = info.endStr.split('T')[0];

                fetch(API_PATH + `coach_actions.php?action=get_coach_events&start=${start}&end=${end}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) throw new Error(data.error);

                        // Store events globally and map properties
                        allEvents = data.map(e => {
                            const startT = (typeof e.startHour === 'string' && e.startHour.includes(' ')) ? e.startHour.replace(' ', 'T') : e.startHour;
                            const endT = e.endHour ? (typeof e.endHour === 'string' && e.endHour.includes(' ') ? e.endHour.replace(' ', 'T') : e.endHour) : startT;
                            let startHourDecimal = 0;
                            if (startT && startT.includes('T')) {
                                const d = new Date(startT);
                                startHourDecimal = d.getHours() + (d.getMinutes() / 60);
                            } else {
                                startHourDecimal = parseFloat(e.startHour) || 0;
                            }
                            return {
                                ...e,
                                startHourDecimal: startHourDecimal,
                                startStr: startT,
                                endStr: endT
                            };
                        });

                        refreshCalendarUI();

                        const filtered = data.filter(e => {
                            if (activeFamilyFilter === 'all') return true;
                            return e.family_id.toString() === activeFamilyFilter;
                        });

                        const mappedEvents = filtered.map(e => ({
                            id: e.id,
                            title: e.title,
                            start: e.startHour,
                            end: e.endHour,
                            allDay: e.is_all_day,
                            backgroundColor: e.colorCode,
                            borderColor: e.colorCode,
                            extendedProps: {
                                tracking_status: e.tracking_status,
                                family_name: e.family_name,
                                feedback: e.tracking_feedback,
                                colorCode: e.colorCode
                            }
                        }));

                        successCallback(mappedEvents);
                    })
                    .catch(err => {
                        console.error('Error loading events:', err);
                        failureCallback(err);
                    });
            },
            eventContent: function (info) {
                const props = info.event.extendedProps;
                const isAllDay = info.event.allDay;
                const baseColor = props.colorCode || '#0d6efd';

                const bgColor = lightenColor(baseColor, 92);

                let statusBadge = '';
                if (props.tracking_status === 'family_completed') {
                    statusBadge = `<span class="badge ms-1" style="background-color:#0dcaf0; color:#fff; font-size:0.65rem;">Needs Review</span>`;
                } else if (props.tracking_status === 'coach_approved') {
                    statusBadge = `<span class="badge ms-1" style="background-color:#198754; color:#fff; font-size:0.65rem;">Approved</span>`;
                } else if (props.tracking_status === 'reopened') {
                    statusBadge = `<span class="badge ms-1" style="background-color:#dc3545; color:#fff; font-size:0.65rem;">Reopened</span>`;
                }

                if (isAllDay || info.view.type === 'dayGridMonth') {
                    return {
                        html: `
                            <div class="p-1 rounded overflow-hidden text-truncate w-100" style="background-color: ${bgColor}; color: ${baseColor}; border-left: 3px solid ${baseColor}; font-size: 0.75rem;">
                                <strong>${info.event.title}</strong> (${props.family_name}) ${statusBadge}
                            </div>
                        `
                    };
                }

                return {
                    html: `
                        <div class="p-2 h-100 rounded overflow-hidden shadow-sm" style="background-color: ${bgColor}; color: #333; border-left: 4px solid ${baseColor}; font-size: 0.8rem; display: flex; flex-direction: column;">
                            <div class="fw-bold" style="color: ${baseColor};"><i class="fa-regular fa-clock me-1"></i> ${info.timeText}</div>
                            <div class="fw-bold text-dark mt-1 text-wrap" style="line-height: 1.2;">${info.event.title}</div>
                            <div class="text-muted text-truncate mt-1" style="font-size: 0.75rem;"><i class="fa-solid fa-people-roof me-1"></i> ${props.family_name}</div>
                            <div class="mt-auto pt-1">${statusBadge}</div>
                        </div>
                    `
                };
            },
            eventClick: function (info) {
                handleEventClick(info.event.extendedProps);
            }
        });
        window.calendarInstance.render();
    }

    // 3. Setup UI switching (Day / Week / Month)
    const viewButtons = document.querySelectorAll('.toggle-view-btn');
    const views = {
        'view-day': document.getElementById('view-day'),
        'view-week': document.getElementById('view-week'),
        'view-month': document.getElementById('view-month')
    };

    function switchView(viewId) {
        currentView = viewId;

        viewButtons.forEach(btn => {
            if (btn.getAttribute('data-target') === viewId) {
                btn.classList.remove('btn-white', 'text-muted');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.add('btn-white', 'text-muted');
                btn.classList.remove('btn-primary');
            }
        });

        if (viewId === 'view-month') {
            views['view-month'].classList.remove('d-none');
            views['view-week'].classList.add('d-none');
            views['view-day'].classList.add('d-none');
            if (window.calendarInstance) {
                setTimeout(() => {
                    window.calendarInstance.updateSize();
                    window.calendarInstance.render();
                }, 100);
            }
        } else if (viewId === 'view-week') {
            views['view-month'].classList.add('d-none');
            views['view-week'].classList.remove('d-none');
            views['view-day'].classList.add('d-none');
        } else if (viewId === 'view-day') {
            views['view-month'].classList.add('d-none');
            views['view-week'].classList.add('d-none');
            views['view-day'].classList.remove('d-none');
        }

        refreshCalendarUI();
        updateDateDisplay();
    }

    viewButtons.forEach(btn => {
        btn.addEventListener('click', () => switchView(btn.getAttribute('data-target')));
    });

    // 4. Date Picker and Navigation Controls
    function updateDateDisplay() {
        const btn = document.getElementById('date-picker-btn');
        if (!btn || !window.calendarInstance) return;

        if (currentView === 'view-month') {
            btn.innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })} <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>`;
        } else if (currentView === 'view-week') {
            const week = getWeekRange(currentDate);
            btn.innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${week.start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${week.end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>`;
        } else {
            btn.innerHTML = `<i class="fa-regular fa-calendar me-2"></i> ${currentDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })} <i class="fa-solid fa-chevron-down ms-2 fs-7"></i>`;
        }
    }

    $('#date-picker-btn').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e) {
        currentDate = e.date;
        if (window.calendarInstance) window.calendarInstance.gotoDate(e.date);
        refreshCalendarUI();
        updateDateDisplay();
    });

    document.getElementById('btn-today').addEventListener('click', () => {
        currentDate = new Date();
        if (window.calendarInstance) window.calendarInstance.today();
        refreshCalendarUI();
        updateDateDisplay();
    });

    document.getElementById('btn-prev').addEventListener('click', () => {
        if (currentView === 'view-month') {
            if (window.calendarInstance) window.calendarInstance.prev();
            currentDate = window.calendarInstance.getDate();
        } else if (currentView === 'view-week') {
            currentDate.setDate(currentDate.getDate() - 7);
            if (window.calendarInstance) window.calendarInstance.gotoDate(currentDate);
        } else if (currentView === 'view-day') {
            currentDate.setDate(currentDate.getDate() - 1);
            if (window.calendarInstance) window.calendarInstance.gotoDate(currentDate);
        }
        refreshCalendarUI();
        updateDateDisplay();
    });

    document.getElementById('btn-next').addEventListener('click', () => {
        if (currentView === 'view-month') {
            if (window.calendarInstance) window.calendarInstance.next();
            currentDate = window.calendarInstance.getDate();
        } else if (currentView === 'view-week') {
            currentDate.setDate(currentDate.getDate() + 7);
            if (window.calendarInstance) window.calendarInstance.gotoDate(currentDate);
        } else if (currentView === 'view-day') {
            currentDate.setDate(currentDate.getDate() + 1);
            if (window.calendarInstance) window.calendarInstance.gotoDate(currentDate);
        }
        refreshCalendarUI();
        updateDateDisplay();
    });

    // --- Upload Plan Form Handler ---
    const uploadForm = document.getElementById('uploadPlanForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(API_PATH + 'coach_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (typeof showAlert === 'function') showAlert(data.message, 'success');
                        else alert(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('uploadPlanModal')).hide();
                        this.reset();
                        if (window.calendarInstance) window.calendarInstance.refetchEvents();
                    } else {
                        if (typeof showAlert === 'function') showAlert(data.message, 'error');
                        else alert(data.message);
                    }
                })
                .catch(err => console.error('Error uploading plan:', err));
        });
    }

    // --- Review Form Handler ---
    const reviewForm = document.getElementById('coachReviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(API_PATH + 'coach_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (typeof showAlert === 'function') showAlert(data.message, 'success');
                        else alert(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('coachReviewModal')).hide();
                        this.reset();
                        if (window.calendarInstance) window.calendarInstance.refetchEvents();
                    } else {
                        if (typeof showAlert === 'function') showAlert(data.message, 'error');
                        else alert(data.message);
                    }
                })
                .catch(err => console.error('Error reviewing task:', err));
        });
    }

    // Initialize display
    switchView('view-week');
});

