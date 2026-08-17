<script>
let container = null;
document.addEventListener('DOMContentLoaded', function() {
    container = document.getElementById('adminBracketContainer');
    const headerBar = document.getElementById('adminRoundHeadersBar');

    // ----------------------------------------------------
    // Restore Scroll Position seamlessly (NO MORE RESETTING SCROLL ON ACTION!)
    // ----------------------------------------------------
    if (container) {
        const savedLeft = sessionStorage.getItem('admin_bracket_scroll_left');
        const savedTop = sessionStorage.getItem('admin_bracket_scroll_top');
        if (savedLeft !== null && savedTop !== null) {
            container.scrollLeft = parseFloat(savedLeft);
            container.scrollTop = parseFloat(savedTop);
            sessionStorage.removeItem('admin_bracket_scroll_left');
            sessionStorage.removeItem('admin_bracket_scroll_top');
        }
    }

    // Show flash message from previous action if exists
    const flashMsg = sessionStorage.getItem('admin_bracket_flash_msg');
    if (flashMsg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flashMsg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        sessionStorage.removeItem('admin_bracket_flash_msg');
    }

    // Helper function to save scroll state and reload page
    function saveScrollAndReload() {
        if (container) {
            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
        }
        window.location.reload();
    }

    if (container && headerBar) {
        // Sync header horizontal scrolling
        container.addEventListener('scroll', function() {
            headerBar.scrollLeft = container.scrollLeft;
        });

        // Drag to scroll
        let isDown = false;
        let startX, startY;
        let scrollLeft, scrollTop;

        container.addEventListener('mousedown', (e) => {
            // Do not drag if clicking on native scrollbar
            if (e.offsetX > container.clientWidth || e.offsetY > container.clientHeight) return;
            // Do not drag if clicking on a button inside the card (e.g. quick win)
            if (e.target.closest('button')) return;

            isDown = true;
            window.isDraggingBracket = false;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            startY = e.pageY - container.offsetTop;
            scrollLeft = container.scrollLeft;
            scrollTop = container.scrollTop;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'default';
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'default';
            setTimeout(() => { window.isDraggingBracket = false; }, 50);
        });
        
        container.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const y = e.pageY - container.offsetTop;
            const walkX = (x - startX) * 1.5;
            const walkY = (y - startY) * 1.5;
            
            if (Math.abs(walkX) > 5 || Math.abs(walkY) > 5) {
                window.isDraggingBracket = true;
            }

            container.scrollLeft = scrollLeft - walkX;
            container.scrollTop = scrollTop - walkY;
        });
    }

    // Modal Form AJAX submit handler
    const form = document.getElementById('editMatchForm');
    const modalEl = document.getElementById('editMatchModal');
    const modal = new bootstrap.Modal(modalEl);

    // Reset Match Button Handler
    const btnResetMatch = document.getElementById('btnResetMatch');
    if (btnResetMatch) {
        btnResetMatch.addEventListener('click', function() {
            const matchId = document.getElementById('modalMatchId').value;
            if (!matchId) return;

            Swal.fire({
                title: 'Reset Pertandingan?',
                text: 'Semua skor akan di-nol-kan dan status dikembalikan ke awal. Bagan di babak selanjutnya yang terpengaruh juga akan dibersihkan secara otomatis.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('match_id', matchId);
                    formData.append('team1_score', '0');
                    formData.append('team2_score', '0');
                    formData.append('status', 'upcoming');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch("{{ route('admin.season.bracket.update-match', $season->id) }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            modal.hide();
                            sessionStorage.setItem('admin_bracket_flash_msg', 'Pertandingan berhasil direset.');
                            saveScrollAndReload();
                        } else {
                            Swal.fire('Gagal', res.message || 'Gagal meriset pertandingan.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi saat meriset pertandingan.', 'error');
                    });
                }
            });
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = new FormData(form);
        const submitBtn = document.getElementById('btnSaveMatch');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        fetch("{{ route('admin.season.bracket.update-match', $season->id) }}", {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Hasil';
            
            if (res.success) {
                modal.hide();
                sessionStorage.setItem('admin_bracket_flash_msg', res.message || 'Pertandingan berhasil disimpan.');
                saveScrollAndReload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: res.message || 'Gagal menyimpan perubahan tanding.'
                });
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Hasil';
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Error',
                text: 'Terjadi kegagalan koneksi atau error di sisi server.'
            });
        });
    });

    // ----------------------------------------------------
    // Drag and Drop Auto-Scroll boundaries logic
    // ----------------------------------------------------
    let autoScrollInterval = null;
    let scrollXSpeed = 0;
    let scrollYSpeed = 0;

    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (!draggedElement) return;

        const rect = container.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        
        const edgeSize = 65; // Distance in pixels from edge to trigger scroll
        const maxSpeed = 22; // Maximum scroll speed
        
        scrollXSpeed = 0;
        scrollYSpeed = 0;
        
        // Vertical Scroll boundaries
        if (mouseY < edgeSize) {
            scrollYSpeed = -Math.max(3, maxSpeed * (1 - mouseY / edgeSize));
        } else if (mouseY > rect.height - edgeSize) {
            const dist = rect.height - mouseY;
            scrollYSpeed = Math.max(3, maxSpeed * (1 - dist / edgeSize));
        }
        
        // Horizontal Scroll boundaries
        if (mouseX < edgeSize) {
            scrollXSpeed = -Math.max(3, maxSpeed * (1 - mouseX / edgeSize));
        } else if (mouseX > rect.width - edgeSize) {
            const dist = rect.width - mouseX;
            scrollXSpeed = Math.max(3, maxSpeed * (1 - dist / edgeSize));
        }
        
        // Handle trigger interval
        if (scrollXSpeed !== 0 || scrollYSpeed !== 0) {
            if (!autoScrollInterval) {
                autoScrollInterval = setInterval(() => {
                    container.scrollLeft += scrollXSpeed;
                    container.scrollTop += scrollYSpeed;
                }, 16);
            }
        } else {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    });

    const stopDragScroll = () => {
        if (autoScrollInterval) {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    };

    container.addEventListener('dragend', stopDragScroll);
    container.addEventListener('drop', stopDragScroll);

    // ----------------------------------------------------
    // Click-to-Swap Teams (Mode Tukar Posisi Tim Babak 1)
    // ----------------------------------------------------
    let isSwapModeActive = false;
    let firstSelectedTeamRow = null;

    const toggleSwapBtn = document.getElementById('toggleSwapModeBtn');
    const swapText = document.getElementById('swapModeText');

    if (toggleSwapBtn) {
        toggleSwapBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            isSwapModeActive = !isSwapModeActive;
            if (isSwapModeActive) {
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning', 'shadow');
                if (swapText) swapText.textContent = 'Mode Tukar (AKTIF)';
                
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                toast.fire({
                    icon: 'warning',
                    title: 'Mode Tukar Posisi AKTIF',
                    text: 'Klik Tim A lalu Klik Tim B di Babak 1 untuk menukar posisi.'
                });
            } else {
                this.classList.remove('btn-warning', 'shadow');
                this.classList.add('btn-outline-warning');
                if (swapText) swapText.textContent = 'Tukar Posisi';
                clearSwapSelection();
            }
        });
    }

    function clearSwapSelection() {
        if (firstSelectedTeamRow) {
            firstSelectedTeamRow.classList.remove('team-row-swap-selected');
            firstSelectedTeamRow = null;
        }
    }

    document.addEventListener('click', function(e) {
        // Only act if swap mode is ON OR if a team is already selected for swap
        if (!isSwapModeActive && !firstSelectedTeamRow) return;

        const teamRow = e.target.closest('.team-row[data-round="1"]');
        if (e.target.closest('.btn-quick-win') || e.target.closest('button')) return;

        if (!teamRow) {
            if (!e.target.closest('#toggleSwapModeBtn')) {
                clearSwapSelection();
            }
            return;
        }

        // STOP propagation so edit match modal DOES NOT OPEN during swap selection!
        e.stopPropagation();
        e.preventDefault();

        const matchId = teamRow.dataset.matchId;
        const slot = teamRow.dataset.slot;

        if (!matchId || !slot) return;

        const teamName = teamRow.querySelector('.team-name')?.textContent || 'Tim';

        if (!firstSelectedTeamRow) {
            firstSelectedTeamRow = teamRow;
            teamRow.classList.add('team-row-swap-selected');

            const toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
            toast.fire({
                icon: 'info',
                title: `Tim "${teamName}" Dipilih!`,
                text: 'Klik tim kedua di Babak 1 untuk menukar posisi.'
            });
        } else if (firstSelectedTeamRow === teamRow) {
            clearSwapSelection();
        } else {
            const team1Row = firstSelectedTeamRow;
            const team2Row = teamRow;

            const team1Name = team1Row.querySelector('.team-name')?.textContent || 'Tim 1';
            const team2Name = team2Row.querySelector('.team-name')?.textContent || 'Tim 2';

            const m1_id = team1Row.dataset.matchId;
            const slot1 = team1Row.dataset.slot;
            const m2_id = team2Row.dataset.matchId;
            const slot2 = team2Row.dataset.slot;

            clearSwapSelection();

            Swal.fire({
                title: 'Tukar Posisi Tim?',
                html: `Anda akan menukar posisi <strong>${team1Name}</strong> dengan <strong>${team2Name}</strong> di Babak 1.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tukar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();

                    fetch("{{ route('admin.season.bracket.swap-teams', $season->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            match1_id: m1_id,
                            slot1: slot1,
                            match2_id: m2_id,
                            slot2: slot2
                        })
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                saveScrollAndReload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menukar posisi tim karena masalah koneksi.'
                        });
                    });
                }
            });
        }
    }, true);

    // ----------------------------------------------------
    // Drag and Drop (Rearrange Seeding inside Round 1)
    // ----------------------------------------------------
    let draggedElement = null;

    const draggableRows = document.querySelectorAll('.team-row[draggable="true"]');
    
    draggableRows.forEach(row => {
        row.addEventListener('dragstart', function(e) {
            draggedElement = this;
            e.dataTransfer.effectAllowed = 'move';
            this.style.opacity = '0.5';
        });

        row.addEventListener('dragend', function() {
            draggedElement = null;
            this.style.opacity = '1';
            draggableRows.forEach(r => r.classList.remove('drag-over'));
        });

        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (draggedElement && draggedElement !== this) {
                this.classList.add('drag-over');
            }
        });

        row.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        row.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (!draggedElement || draggedElement === this) return;

            const m1_id = draggedElement.dataset.matchId;
            const slot1 = draggedElement.dataset.slot;
            const m2_id = this.dataset.matchId;
            const slot2 = this.dataset.slot;

            Swal.fire({
                title: 'Tukar Posisi Tim?',
                text: "Anda akan menukar posisi tim ini di Babak 1.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tukar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    
                    fetch("{{ route('admin.season.bracket.swap-teams', $season->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            match1_id: m1_id,
                            slot1: slot1,
                            match2_id: m2_id,
                            slot2: slot2
                        })
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                saveScrollAndReload(); // Use smooth scroll reload
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menukar posisi tim karena masalah koneksi.'
                        });
                    });
                }
            });
        });
    });

    // ----------------------------------------------------
    // Search Engine & Theme Switcher inside Admin View
    // ----------------------------------------------------
    const adminSearchInput = document.getElementById('adminTeamSearch');
    const toggleSearchModeBtn = document.getElementById('toggleSearchModeBtn');
    let searchMode = 'name'; // 'name' or 'wa'

    if (toggleSearchModeBtn) {
        toggleSearchModeBtn.addEventListener('click', function() {
            if (searchMode === 'name') {
                searchMode = 'wa';
                toggleSearchModeBtn.innerHTML = '<i class="bi bi-whatsapp"></i> No. WA';
                adminSearchInput.placeholder = 'Cari nomor WA kapten...';
            } else {
                searchMode = 'name';
                toggleSearchModeBtn.innerHTML = '<i class="bi bi-person-fill"></i> Nama';
                adminSearchInput.placeholder = 'Cari nama tim...';
            }
            // Trigger input event to re-evaluate search with new mode
            adminSearchInput.dispatchEvent(new Event('input'));
        });
    }

    adminSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.match-card').forEach(card => card.classList.remove('search-focus-glow'));

        if (!query) return;

        let selector = `.team-row[data-team-name*="${query}"]`;
        if (searchMode === 'wa') {
            selector = `.team-row[data-team-wa*="${query}"]`;
        }

        const matchRows = document.querySelectorAll(selector);
        let firstCard = null;
        matchRows.forEach(matchRow => {
            const targetCard = matchRow.closest('.match-card');
            if (targetCard) {
                targetCard.classList.add('search-focus-glow');
                if (!firstCard) firstCard = targetCard;
            }
        });

        if (firstCard) {
            const containerRect = container.getBoundingClientRect();
            const cardRect = firstCard.getBoundingClientRect();
            
            const relativeLeft = cardRect.left - containerRect.left + container.scrollLeft;
            const targetScrollLeft = relativeLeft - (containerRect.width / 2) + (cardRect.width / 2);

            const relativeTop = cardRect.top - containerRect.top + container.scrollTop;
            const targetScrollTop = relativeTop - (containerRect.height / 2) + (cardRect.height / 2);

            container.scrollTo({
                left: targetScrollLeft,
                top: targetScrollTop,
                behavior: 'smooth'
            });
        }
    });

    // Theme Switcher Logic
    const toggleBracketThemeSwitch = document.getElementById('toggleBracketThemeSwitch');
    const bracketCardContainer = document.getElementById('bracketCardContainer');

    if (toggleBracketThemeSwitch && bracketCardContainer) {
        // Default theme is dark
        const savedTheme = localStorage.getItem('admin_bracket_theme') || 'dark';
        
        if (savedTheme === 'dark') {
            toggleBracketThemeSwitch.checked = true;
            bracketCardContainer.classList.add('theme-dark');
            bracketCardContainer.classList.remove('theme-light');
        } else {
            toggleBracketThemeSwitch.checked = false;
            bracketCardContainer.classList.add('theme-light');
            bracketCardContainer.classList.remove('theme-dark');
        }

        toggleBracketThemeSwitch.addEventListener('change', function() {
            if (this.checked) {
                bracketCardContainer.classList.add('theme-dark');
                bracketCardContainer.classList.remove('theme-light');
                localStorage.setItem('admin_bracket_theme', 'dark');
            } else {
                bracketCardContainer.classList.add('theme-light');
                bracketCardContainer.classList.remove('theme-dark');
                localStorage.setItem('admin_bracket_theme', 'light');
            }
        });
    }

    // ----------------------------------------------------
    // Team Path Highlighting on Hover (Challonge-style)
    // ----------------------------------------------------
    (function initTeamPathHighlight() {
        const container = document.getElementById('adminBracketContainer');
        if (!container) return;

        function clearHighlight() {
            document.querySelectorAll('.team-path-highlight').forEach(el => el.classList.remove('team-path-highlight'));
            document.querySelectorAll('.match-path-highlight').forEach(el => el.classList.remove('match-path-highlight'));
            document.querySelectorAll('.connector-line.highlighted, .connector-line.line-path-highlight').forEach(el => el.classList.remove('highlighted', 'line-path-highlight'));
        }

        function highlightPath(teamId) {
            clearHighlight();
            if (!teamId) return;

            const teamRows = document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`);
            const teamMatchesByRound = {};

            teamRows.forEach(row => {
                row.classList.add('team-path-highlight');
                const card = row.closest('.match-card');
                if (card) {
                    card.classList.add('match-path-highlight');
                    const parts = card.id ? card.id.split('_') : [];
                    if (parts.length >= 4) {
                        const rNum = parseInt(parts[2]);
                        const mNum = parseInt(parts[3]);
                        teamMatchesByRound[rNum] = mNum;
                    }
                }
            });

            // Highlight connector lines for rounds where team appears
            Object.keys(teamMatchesByRound).forEach(rStr => {
                const rNum = parseInt(rStr);
                const mNum = teamMatchesByRound[rNum];
                const line = document.getElementById(`line_${rNum}_${mNum}`);
                if (line) {
                    line.classList.add('line-path-highlight', 'highlighted');
                }
            });
        }

        container.addEventListener('mouseover', function(e) {
            const teamRow = e.target.closest('.team-row[data-team-id]');
            if (!teamRow) return;
            const teamId = teamRow.getAttribute('data-team-id');
            if (teamId) highlightPath(teamId);
        });

        container.addEventListener('mouseleave', clearHighlight, true);
    })();

    // ----------------------------------------------------
    // Search & Filter inside YMD Slots Modal
    // ----------------------------------------------------
    const modalYmdSearch = document.getElementById('modalYmdSearch');
    if (modalYmdSearch) {
        modalYmdSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('#modalYmdTable tbody tr').forEach(row => {
                const firstCell = row.querySelector('td:first-child');
                const secondCellInput = row.querySelector('input[type="text"]');
                
                const slotName = firstCell ? firstCell.textContent.toLowerCase() : '';
                const renameVal = secondCellInput ? secondCellInput.value.toLowerCase() : '';

                if (slotName.includes(query) || renameVal.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ----------------------------------------------------
    // LIVE Real-Time Polling (Sync database updates without refresh)
    // Optimized with Page Visibility API to save bandwidth
    // ----------------------------------------------------
    let pollingInterval = null;

    function fetchLatestBracketData() {
        const isModalOpen = document.querySelectorAll('.modal.show').length > 0;
        const isDragging = draggedElement !== null;

        // Only poll if no modal is active and admin is not currently dragging a row
        if (!isModalOpen && !isDragging) {
            fetch("{{ route('public.season.bracket.data', \App\Http\Controllers\BracketController::encodeId($season->id)) }}")
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.matches) {
                        res.matches.forEach(m => {
                            const card = document.getElementById(`card_m_${m.round_number}_${m.match_number}`);
                            if (card) {
                                // 1. Update time / live status badge
                                const timeSpan = card.querySelector('.match-card-time');
                                if (timeSpan) {
                                    if (m.status === 'live') {
                                        timeSpan.innerHTML = '<span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem; animation: pulse 1s infinite alternate;">LIVE</span>';
                                    } else {
                                        timeSpan.innerHTML = `<i class="bi bi-clock"></i> ${m.match_time || '20:00 WIB'}`;
                                    }
                                }

                                // 2. Update border class
                                if (m.status === 'live') {
                                    card.classList.add('border-primary');
                                } else {
                                    card.classList.remove('border-primary');
                                }

                                // 3. Update Team 1 row details
                                const row1 = card.querySelector('.team-row[data-slot="1"]');
                                if (row1) {
                                    row1.dataset.teamId = m.team1_id || '';
                                    row1.dataset.teamName = m.team1_name ? m.team1_name.toLowerCase() : '';
                                    
                                    row1.className = `team-row ${m.winner_id && m.winner_id === m.team1_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team1_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = row1.querySelector('.team-name');
                                    if (nameSpan) {
                                        nameSpan.className = m.team1_name ? 'team-name text-dark fw-semibold' : 'team-name text-muted italic';
                                        nameSpan.textContent = m.team1_name || 'Belum Ada Tim';
                                    }
                                    const scoreBox = row1.querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team1_score;
                                }

                                // 4. Update Team 2 row details
                                const row2 = card.querySelector('.team-row[data-slot="2"]');
                                if (row2) {
                                    row2.dataset.teamId = m.team2_id || '';
                                    row2.dataset.teamName = m.team2_name ? m.team2_name.toLowerCase() : '';
                                    
                                    row2.className = `team-row ${m.winner_id && m.winner_id === m.team2_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team2_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = row2.querySelector('.team-name');
                                    if (nameSpan) {
                                        if (m.team2_name) {
                                            nameSpan.className = 'team-name text-dark fw-semibold';
                                            nameSpan.textContent = m.team2_name;
                                        } else {
                                            if (m.round_number === 1) {
                                                nameSpan.className = 'team-name text-success fw-bold';
                                                nameSpan.textContent = 'BYE (Lolos)';
                                            } else {
                                                nameSpan.className = 'team-name text-muted italic';
                                                nameSpan.textContent = 'Belum Ada Tim';
                                            }
                                        }
                                    }
                                    const scoreBox = row2.querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team2_score;
                                }

                                // 5. Update open modal click handler payload
                                card.setAttribute('onclick', `openEditMatchModal(${JSON.stringify({
                                    id: m.id,
                                    team1_name: m.team1_name || 'TBD',
                                    team2_name: m.team2_name || 'TBD',
                                    team1_score: m.team1_score,
                                    team2_score: m.team2_score,
                                    match_time: m.match_time || '20:00 WIB',
                                    status: m.status,
                                    team1_exists: !!m.team1_id,
                                    team2_exists: !!m.team2_id
                                })})`);
                            }
                        });
                    }
                })
                .catch(err => console.log("Realtime sync issue:", err));
        }
    }

    function startPolling() {
        if (!pollingInterval) {
            pollingInterval = setInterval(fetchLatestBracketData, 4000);
        }
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    // Stop polling when tab is inactive
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            fetchLatestBracketData();
            startPolling();
        }
    });

    startPolling();
});

// ----------------------------------------------------
// Save Jam Main per Babak
// ----------------------------------------------------
function saveRoundTime(roundNum) {
    const inputVal = document.getElementById(`roundTime_${roundNum}`).value;
    const totalRounds = {{ count($rounds) }};
    let roundLabel = `Babak ${roundNum}`;
    if (roundNum === totalRounds) {
        roundLabel = "Grand Final";
    } else if (roundNum === totalRounds - 1 && totalRounds > 1) {
        roundLabel = "Semifinal";
    }

    Swal.fire({
        title: 'Ubah Jadwal Babak?',
        text: `Semua pertandingan di ${roundLabel} akan diubah jadwalnya menjadi: "${inputVal}". Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.update-round-times', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    round_number: roundNum,
                    match_time: inputVal
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (typeof saveScrollAndReload === 'function') {
                            saveScrollAndReload();
                        } else {
                            const container = document.getElementById('adminBracketContainer');
                            if (container) {
                                sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                                sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                            }
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal mengubah jadwal karena masalah koneksi.'
                });
            });
        }
    });
}

// Official Yomuda Fast Tour Schedule Matrix
function applyOfficialYomudaPreset() {
    const totalRounds = {{ count($rounds) }};
    const officialStartTimes = [
        "20:00 WIB", // Babak 1
        "20:40 WIB", // Babak 2
        "21:15 WIB", // Babak 3
        "21:50 WIB", // Babak 4
        "22:20 WIB", // Babak 5
    ];

    for (let r = 1; r <= totalRounds; r++) {
        const input = document.getElementById(`roundTime_${r}`);
        if (!input) continue;

        let timeStr = "";
        if (r === totalRounds) {
            timeStr = "23:20 WIB"; // Grand Final / Bronze
        } else if (r === totalRounds - 1 && totalRounds > 1) {
            timeStr = "22:50 WIB"; // Semifinal
        } else {
            const idx = r - 1;
            timeStr = officialStartTimes[idx] || officialStartTimes[officialStartTimes.length - 1];
        }

        input.value = timeStr;
    }

    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500
    });
    toast.fire({
        icon: 'success',
        title: 'Preset Resmi Yomuda Diterapkan!',
        text: 'Klik "Simpan Semua Jam Babak" untuk menyimpan ke database.'
    });
}

// Batch Save All Round Times (1-Click)
function saveAllRoundTimes() {
    const totalRounds = {{ count($rounds) }};
    const promises = [];

    Swal.fire({
        title: 'Menyimpan Jam Semua Babak...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    for (let r = 1; r <= totalRounds; r++) {
        const input = document.getElementById(`roundTime_${r}`);
        if (!input) continue;
        const val = input.value;

        promises.push(
            fetch("{{ route('admin.season.bracket.update-round-times', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    round_number: r,
                    match_time: val
                })
            })
        );
    }

    Promise.all(promises)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Semua jam babak berhasil disimpan!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                if (typeof saveScrollAndReload === 'function') {
                    saveScrollAndReload();
                } else {
                    window.location.reload();
                }
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal menyimpan jam babak.'
            });
        });
}

// ----------------------------------------------------
// Save / Rename YMD Slot Team
// ----------------------------------------------------
function renameYmdSlot(teamId, oldName) {
    const inputVal = document.getElementById(`ymdRenameInput_${teamId}`).value.trim();
    const priceVal = document.getElementById(`ymdPriceInput_${teamId}`).value.trim();
    const parsedPrice = parseInt(priceVal) || 0;

    if (!inputVal) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nama tim baru tidak boleh kosong!'
        });
        return;
    }

    Swal.fire({
        title: 'Ganti Nama Slot?',
        text: `Ubah slot "${oldName}" menjadi "${inputVal}" dengan harga Rp ${parsedPrice.toLocaleString('id-ID')}? Perubahan akan langsung terlihat di bagan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.rename-ymd-slot', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    team_id: teamId,
                    new_name: inputVal,
                    price: parsedPrice
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const container = document.getElementById('adminBracketContainer');
                        if (container) {
                            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal mengubah nama slot karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Delete All YMD Slots
// ----------------------------------------------------
function deleteAllYmdSlots() {
    Swal.fire({
        title: 'Hapus Semua Slot YMD?',
        text: "Semua tim placeholder berawalan YMD- di season ini akan dihapus dari database dan bagan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.delete-all-ymd-slots', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghapus slot karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Win All YMD Slots to Round 2
// ----------------------------------------------------
function winAllYmdSlots() {
    Swal.fire({
        title: 'Loloskan Semua Slot YMD?',
        text: "Semua pertandingan yang berisi slot YMD- di Babak 1 & Babak 2 akan otomatis dimenangkan (skor 1-0) dan diloloskan hingga ke Babak 3.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Loloskan Ke Babak 3!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.win-ymd-slots', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memenangkan slot YMD karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Toggle Bronze Match (Juara 3 & 4)
// ----------------------------------------------------
function toggleBronzeMatchSetting(switchEl) {
    const active = switchEl.checked;
    Swal.showLoading();

    fetch("{{ route('admin.season.bracket.toggle-bronze-match', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            active: active
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            switchEl.checked = !active; // revert
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: res.message
            });
        }
    })
    .catch(err => {
        switchEl.checked = !active; // revert
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal mengubah pengaturan Bronze Match karena masalah koneksi.'
        });
    });
}

// ----------------------------------------------------
// Bulk Add YMD Slots
// ----------------------------------------------------
function bulkAddYmdSlots() {
    const count = parseInt(document.getElementById('ymdAddCount').value);
    
    if (isNaN(count) || count < 1 || count > 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Jumlah Invalid',
            text: 'Silakan masukkan jumlah slot YMD antara 1 s/d 100.'
        });
        return;
    }

    Swal.fire({
        title: 'Tambah Slot YMD?',
        text: `Anda akan membuat ${count} slot placeholder YMD baru ke database untuk season ini.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Tambahkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.add-ymd-slots', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    count: count
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const container = document.getElementById('adminBracketContainer');
                        if (container) {
                            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menambahkan slot YMD karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Copy Teams list to Clipboard
// ----------------------------------------------------
function copyTeamsList() {
    const area = document.getElementById('teamsListArea');
    area.select();
    area.setSelectionRange(0, 99999);
    document.execCommand('copy');

    Swal.fire({
        icon: 'success',
        title: 'Disalin!',
        text: 'Daftar nama tim berhasil disalin ke clipboard.',
        timer: 1500,
        showConfirmButton: false
    });
}

// Function to populate and open Edit Modal
function openEditMatchModal(match) {
    if (window.isDraggingBracket) return;
    document.getElementById('modalMatchId').value = match.id;
    document.getElementById('modalT1Name').textContent = match.team1_name;
    document.getElementById('modalT2Name').textContent = match.team2_name;
    document.getElementById('modalT1Score').value = match.team1_score;
    document.getElementById('modalT2Score').value = match.team2_score;
    document.getElementById('modalMatchTime').value = match.match_time;

    const alertEl = document.getElementById('modalIncompleteAlert');
    const input1 = document.getElementById('modalT1Score');
    const input2 = document.getElementById('modalT2Score');
    const btnSave = document.getElementById('btnSaveMatch');

    const btnReset = document.getElementById('btnResetMatch');

    if (!match.team1_exists && !match.team2_exists) {
        alertEl.classList.remove('d-none');
        alertEl.textContent = 'Pertandingan kosong (kedua tim belum ditentukan) tidak dapat diubah skornya.';
        input1.disabled = true;
        input2.disabled = true;
        btnSave.disabled = true;
        if (btnReset) btnReset.disabled = true;
    } else {
        alertEl.classList.add('d-none');
        input1.disabled = false;
        input2.disabled = false;
        
        input1.readOnly = !match.team1_exists;
        input2.readOnly = !match.team2_exists;
        
        // Styling abu-abu agar terlihat pasif
        if (!match.team1_exists) {
            input1.style.opacity = '0.6';
            input1.style.backgroundColor = '#e9ecef';
        } else {
            input1.style.opacity = '1';
            input1.style.backgroundColor = '#ffffff';
        }
        
        if (!match.team2_exists) {
            input2.style.opacity = '0.6';
            input2.style.backgroundColor = '#e9ecef';
        } else {
            input2.style.opacity = '1';
            input2.style.backgroundColor = '#ffffff';
        }
        
        btnSave.disabled = false;
        if (btnReset) btnReset.disabled = false;
    }

    const modal = new bootstrap.Modal(document.getElementById('editMatchModal'));
    modal.show();
}

// ----------------------------------------------------
// ----------------------------------------------------
// Bracket Visibility Toggle
// ----------------------------------------------------
document.getElementById('toggleBracketVisibility')?.addEventListener('change', function() {
    const label = document.getElementById('bracketVisibilityLabel');
    const isChecked = this.checked;
    
    fetch(`/admin/dashboard/{{ $season->id }}/bracket/toggle-visibility`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (res.is_bracket_visible) {
                label.className = 'text-success';
                label.textContent = '🟢 Bracket Terlihat oleh Peserta';
            } else {
                label.className = 'text-danger';
                label.textContent = '🔴 Bracket Tersembunyi dari Peserta';
            }
        }
    })
    .catch(err => {
        // Revert on error
        this.checked = !isChecked;
        console.error('Toggle visibility error:', err);
    });
});

// Admin Live Chat Dashboard Scripting
// ----------------------------------------------------
const threadsList = document.getElementById('adminChatThreadsList');
const activeThreadTitle = document.getElementById('adminActiveThreadTitle');
const threadSessionTokenInput = document.getElementById('adminThreadSessionToken');
const adminChatMessagesBody = document.getElementById('adminChatMessagesBody');
const adminReplyInput = document.getElementById('adminReplyInput');
const adminBtnReplySend = document.getElementById('adminBtnReplySend');
const adminGlobalUnreadBadge = document.getElementById('adminGlobalUnreadBadge');

let activeThreadToken = null;
let activeThreadName = null;
let adminLastMessageId = 0;
let adminThreadsInterval = null;
let adminMessagesInterval = null;
let adminChatTab = 'active';

// Thread List Styling helpers
function renderThreadListHTML(threads) {
    if (!threads || threads.length === 0) {
        threadsList.innerHTML = `<div class="text-center text-secondary py-5 small">Tidak ada percakapan ${adminChatTab === 'archived' ? 'diarsip' : 'aktif'}.</div>`;
        return;
    }

    let listHTML = '';
    threads.forEach(t => {
        const isSelected = activeThreadToken === t.sender_session_token;
        const activeClass = isSelected ? 'bg-secondary bg-opacity-25 border-start border-3 border-warning' : '';
        const unreadBadge = t.unread_count > 0 ? `<span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.55rem;">${t.unread_count}</span>` : '';
        
        // Truncate message
        let textTruncated = t.last_message || '';
        if (textTruncated.length > 22) {
            textTruncated = textTruncated.substring(0, 20) + '...';
        }
        
        listHTML += `
            <div class="p-3 border-bottom border-secondary border-opacity-10 cursor-pointer ${activeClass}" style="cursor: pointer;" onclick="selectChatThread('${t.sender_session_token}', '${t.sender_name}')">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-white">${t.sender_name}</span>
                    ${unreadBadge}
                </div>
                <div class="small text-secondary mt-1 text-truncate">${t.last_message_is_admin ? 'Anda: ' : ''}${textTruncated}</div>
            </div>
        `;
    });
    threadsList.innerHTML = listHTML;
}

window.selectChatThread = function(token, name) {
    activeThreadToken = token;
    activeThreadName = name;
    activeThreadTitle.textContent = `Percakapan dengan ${name}`;
    threadSessionTokenInput.textContent = token;
    adminReplyInput.disabled = false;
    adminBtnReplySend.disabled = false;
    
    // Enable attachment buttons
    const adminBtnAttach = document.getElementById('adminBtnAttach');
    if (adminBtnAttach) adminBtnAttach.disabled = false;

    // Show delete button
    const adminBtnDeleteThread = document.getElementById('adminBtnDeleteThread');
    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'inline-block';
    
    // Show archive or unarchive button depending on tab
    const adminBtnArchiveThread = document.getElementById('adminBtnArchiveThread');
    const adminBtnUnarchiveThread = document.getElementById('adminBtnUnarchiveThread');
    
    if (adminChatTab === 'active') {
        if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'inline-block';
        if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
    } else {
        if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
        if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'inline-block';
    }

    adminReplyInput.focus();

    // Reset last message id to reload messages correctly
    adminLastMessageId = 0;
    adminChatMessagesBody.innerHTML = '<div class="text-center text-secondary py-5 small"><i class="bi bi-arrow-repeat spin"></i> Memuat pesan...</div>';

    // Mark as read immediately
    fetch(`/admin/dashboard/{{ $season->id }}/chat/read/${token}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    fetchThreadMessages();
    fetchAdminChatThreads(); // refresh list to clear badge count
};

// Bind actions on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    const adminBtnDeleteThread = document.getElementById('adminBtnDeleteThread');
    const adminBtnArchiveThread = document.getElementById('adminBtnArchiveThread');
    const adminBtnUnarchiveThread = document.getElementById('adminBtnUnarchiveThread');
    const adminBtnClearAllChats = document.getElementById('adminBtnClearAllChats');
    const adminBtnAttach = document.getElementById('adminBtnAttach');
    const adminFileInput = document.getElementById('adminFileInput');
    const adminTabActive = document.getElementById('adminTabActive');
    const adminTabArchived = document.getElementById('adminTabArchived');

    // Tab Switching Bindings
    if (adminTabActive && adminTabArchived) {
        adminTabActive.addEventListener('click', () => {
            adminChatTab = 'active';
            adminTabActive.className = 'btn btn-warning btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            adminTabArchived.className = 'btn btn-outline-secondary text-white btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            fetchAdminChatThreads();
        });

        adminTabArchived.addEventListener('click', () => {
            adminChatTab = 'archived';
            adminTabActive.className = 'btn btn-outline-secondary text-white btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            adminTabArchived.className = 'btn btn-warning btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            fetchAdminChatThreads();
        });
    }

    // 1. Delete thread
    if (adminBtnDeleteThread) {
        adminBtnDeleteThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            if (!confirm(`Apakah Anda yakin ingin menghapus seluruh riwayat chat dengan ${activeThreadName} beserta berkas gambar yang dikirim?`)) return;
            
            fetch(`/admin/dashboard/{{ $season->id }}/chat/delete/${activeThreadToken}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    adminBtnDeleteThread.style.display = 'none';
                    if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-chat-dots" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan berhasil dihapus.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 2. Archive thread
    if (adminBtnArchiveThread) {
        adminBtnArchiveThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            if (!confirm(`Apakah Anda yakin ingin mengarsipkan percakapan dengan ${activeThreadName} untuk meredam spam?`)) return;

            fetch(`/admin/dashboard/{{ $season->id }}/chat/archive/${activeThreadToken}`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-archive" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan diarsipkan.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 3. Unarchive thread
    if (adminBtnUnarchiveThread) {
        adminBtnUnarchiveThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            
            fetch(`/admin/dashboard/{{ $season->id }}/chat/unarchive/${activeThreadToken}`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan dikembalikan ke pesan aktif.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 4. Clear all chats (entire season)
    if (adminBtnClearAllChats) {
        adminBtnClearAllChats.addEventListener('click', () => {
            if (!confirm("PERINGATAN! Anda yakin ingin menghapus SELURUH riwayat obrolan dan gambar media di season ini? Tindakan ini tidak dapat dibatalkan.")) return;

            fetch(`/admin/dashboard/{{ $season->id }}/chat/clear-all`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-trash" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Seluruh chat berhasil dibersihkan.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 5. Admin upload file attachments
    if (adminBtnAttach && adminFileInput) {
        adminBtnAttach.addEventListener('click', () => adminFileInput.click());
        adminFileInput.addEventListener('change', function() {
            if (this.files && this.files[0] && activeThreadToken) {
                const file = this.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert("Ukuran berkas maksimal 2MB!");
                    return;
                }

                const formData = new FormData();
                formData.append('image', file);

                fetch(`/admin/dashboard/{{ $season->id }}/chat/upload/${activeThreadToken}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        fetchThreadMessages();
                        fetchAdminChatThreads();
                    } else {
                        alert("Gagal mengunggah: " + res.message);
                    }
                })
                .catch(err => console.log("Upload error:", err));
            }
        });
    }
});

let previousGlobalUnread = -1;

function playNotificationSound() {
    const now = Date.now();
    const lastGlobalSound = parseInt(localStorage.getItem('yomuda_global_last_sound_time') || '0', 10);
    
    // Cross-tab lock: If ANY tab played audio in the last 4 seconds, skip playing in other tabs
    if (now - lastGlobalSound < 4000) return;
    
    localStorage.setItem('yomuda_global_last_sound_time', now.toString());

    try {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        
        // Synth Chime (Ting sound)
        const osc = context.createOscillator();
        const gain = context.createGain();
        
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, context.currentTime); // A5 note
        osc.frequency.exponentialRampToValueAtTime(1320, context.currentTime + 0.1); // Sweep up to E6 note
        
        gain.gain.setValueAtTime(0.12, context.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.6);
        
        osc.connect(gain);
        gain.connect(context.destination);
        
        osc.start();
        osc.stop(context.currentTime + 0.6);
    } catch (e) {
        console.log("AudioContext blocked or not supported yet:", e);
    }
}

// Polling Laporan Hasil Laga (Match Reports)
let previousPendingReportsCount = -1;

function pollAdminMatchReports() {
    fetch("{{ route('admin.season.match-reports.poll', $season->id) }}")
        .then(r => r.json())
        .then(res => {
            if (res.reports) {
                const pendingCount = res.reports.filter(r => r.status === 'PENDING').length;

                if (previousPendingReportsCount !== -1 && pendingCount > previousPendingReportsCount) {
                    playNotificationSound();
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: 'Laporan Hasil Laga',
                        text: 'Ada laporan hasil tanding baru yang butuh verifikasi!'
                    });
                }
                previousPendingReportsCount = pendingCount;
            }
        })
        .catch(err => console.log("Report polling error:", err));
}

// Start report polling every 10 seconds
setInterval(pollAdminMatchReports, 10000);
pollAdminMatchReports();

function fetchAdminChatThreads() {
    fetch("{{ route('admin.season.chat.threads', $season->id) }}?status=" + adminChatTab)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.threads) {
                renderThreadListHTML(res.threads);
                
                // Calculate global unread count
                let globalUnread = 0;
                res.threads.forEach(t => {
                    globalUnread += parseInt(t.unread_count || 0);
                });

                // Play sound if unread count increases (new thread or new message in closed thread)
                if (previousGlobalUnread !== -1 && globalUnread > previousGlobalUnread) {
                    playNotificationSound();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'info',
                        title: 'Pesan Live Chat Baru',
                        text: 'Ada pesan live chat baru dari peserta turnamen.'
                    });
                }
                previousGlobalUnread = globalUnread;

                if (globalUnread > 0) {
                    adminGlobalUnreadBadge.textContent = globalUnread;
                    adminGlobalUnreadBadge.style.display = 'inline-block';
                } else {
                    adminGlobalUnreadBadge.style.display = 'none';
                }
            }
        })
        .catch(err => console.log("Threads load issue:", err));
}

function fetchThreadMessages() {
    if (!activeThreadToken) return;

    fetch(`/admin/dashboard/{{ $season->id }}/chat/messages/${activeThreadToken}`)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.messages) {
                let renderList = false;
                if (adminLastMessageId === 0) {
                    adminChatMessagesBody.innerHTML = '';
                    renderList = true;
                }

                res.messages.forEach(msg => {
                    if (msg.id > adminLastMessageId) {
                        // Play sound on new incoming user message in active open thread
                        if (adminLastMessageId > 0 && !msg.is_admin) {
                            playNotificationSound();
                        }

                        const bubble = document.createElement('div');
                        bubble.className = `p-2 rounded-3 text-white small ${msg.is_admin ? 'bg-secondary bg-opacity-50 align-self-end text-end' : 'bg-dark border border-secondary border-opacity-25 align-self-start'}`;
                        bubble.style.maxWidth = '80%';
                        
                        let displayContent = msg.message;
                        if (msg.message.startsWith('[IMAGE]:')) {
                            const imgUrl = msg.message.substring(8);
                            displayContent = `<img src="${imgUrl}" class="img-fluid rounded-3 my-1" style="max-height: 150px; cursor: pointer; display: block;" onclick="window.open('${imgUrl}', '_blank')" onload="this.closest('.modal-body').querySelector('.d-flex.flex-column').scrollTop = this.closest('.modal-body').querySelector('.d-flex.flex-column').scrollHeight">`;
                        }

                        bubble.innerHTML = `
                            <div class="fw-bold" style="font-size: 0.65rem; color: ${msg.is_admin ? '#cbd5e1' : '#f59e0b'};">${msg.is_admin ? 'Anda (Admin)' : msg.sender_name}</div>
                            <div class="mt-1">${displayContent}</div>
                        `;
                        adminChatMessagesBody.appendChild(bubble);
                        adminLastMessageId = msg.id;
                        renderList = true;
                    }
                });

                if (renderList) {
                    setTimeout(() => {
                        adminChatMessagesBody.scrollTop = adminChatMessagesBody.scrollHeight;
                    }, 80);
                }
            }
        })
        .catch(err => console.log("Messages load issue:", err));
}

function sendAdminReply() {
    const text = adminReplyInput.value.trim();
    if (!text || !activeThreadToken) return;

    adminReplyInput.value = '';

    fetch("{{ route('admin.season.chat.reply', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sender_session_token: activeThreadToken,
            message: text
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            fetchThreadMessages();
            fetchAdminChatThreads();
        }
    })
    .catch(err => console.log("Reply issue:", err));
}

adminBtnReplySend.addEventListener('click', sendAdminReply);
adminReplyInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendAdminReply();
    }
});

// Setup admin listeners
document.getElementById('modalAdminLiveChat').addEventListener('show.bs.modal', () => {
    fetchAdminChatThreads();
    adminThreadsInterval = setInterval(fetchAdminChatThreads, 4000);
    adminMessagesInterval = setInterval(fetchThreadMessages, 3000);
});

document.getElementById('modalAdminLiveChat').addEventListener('hide.bs.modal', () => {
    if (adminThreadsInterval) clearInterval(adminThreadsInterval);
    if (adminMessagesInterval) clearInterval(adminMessagesInterval);
});

// Initialize polling for thread badge counts (global badge)
setInterval(fetchAdminChatThreads, 15000);
fetchAdminChatThreads();
</script>
