<script>
    document.getElementById('formManualWinners').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("{{ route('admin.season.bracket.update-manual-winners', $season->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Gagal menyimpan data.', 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'));
    });

    function resetManualWinners() {
        if (!confirm('Hapus inputan juara manual dan kembali gunakan hasil otomatis bracket?')) return;
        document.getElementById('inputManualJuara1').value = '';
        document.getElementById('inputManualJuara2').value = '';
        document.getElementById('inputManualJuara3').value = '';
        document.getElementById('inputManualJuara4').value = '';
        document.getElementById('formManualWinners').dispatchEvent(new Event('submit'));
    }

    // ----------------------------------------------------
    // Quick 1-Click Winner JS Action
    // ----------------------------------------------------
    function quickWinMatch(matchId, winnerId, teamName) {
        Swal.fire({
            title: 'Loloskan Tim ini?',
            text: `Anda akan memberikan kemenangan otomatis untuk ${teamName}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Loloskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Determine scores (1-0 for the winner)
                const team1Id = document.querySelector(`#card_m_1_${matchId} .team-row[data-slot="1"]`)?.dataset.teamId;
                let team1Score = 0;
                let team2Score = 0;
                
                if (team1Id == winnerId) {
                    team1Score = 1;
                } else {
                    team2Score = 1;
                }

                const formData = new FormData();
                formData.append('match_id', matchId);
                formData.append('team1_score', team1Score);
                formData.append('team2_score', team2Score);
                formData.append('status', 'finished');
                formData.append('_token', '{{ csrf_token() }}');

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch("{{ route('admin.season.bracket.update-match', $season->id) }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        sessionStorage.setItem('admin_bracket_flash_msg', `${teamName} telah diloloskan.`);
                        saveScrollAndReload();
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan sistem.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                });
            }
        });
    }

    // ----------------------------------------------------
    // Filter Babak / Round Tab Focus Logic
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.round-filter-btn');
        const roundCols = document.querySelectorAll('.bracket-round');
        const roundHeaders = document.querySelectorAll('.round-header-item');
        const bContainer = document.getElementById('adminBracketContainer');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetRound = this.getAttribute('data-round');

                // Update button active states
                filterBtns.forEach(b => {
                    b.classList.remove('active', 'btn-warning');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('active', 'btn-warning');

                if (targetRound === 'all') {
                    roundCols.forEach(col => col.style.display = '');
                    roundHeaders.forEach(h => h.style.display = '');
                } else {
                    roundCols.forEach(col => {
                        if (col.getAttribute('data-round-col') === targetRound) {
                            col.style.display = '';
                        } else {
                            col.style.display = 'none';
                        }
                    });

                    roundHeaders.forEach(h => {
                        if (h.getAttribute('data-round-header') === targetRound) {
                            h.style.display = '';
                        } else {
                            h.style.display = 'none';
                        }
                    });
                }

                if (bContainer) {
                    bContainer.scrollLeft = 0;
                    bContainer.scrollTop = 0;
                }
            });
        });
    });
</script>
