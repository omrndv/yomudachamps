<script>
    // Copy to clipboard helper
    function copyText(textareaId) {
        const textarea = document.getElementById(textareaId);
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(textarea.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Teks Berhasil Disalin!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }).catch(err => alert("Gagal menyalin teks: " + err));
    }

    // Roomtour bracket random generator logic
    function generateRoomtour() {
        const roundsData = @json($startNumbers);
        const roundKeys = Object.keys(roundsData).map(Number).sort((a,b) => a-b);
        const roundsCount = roundKeys.length;
        
        let text = "*List Bracket yang masuk Live 📺*\n\n";
        
        // Randomly pick for each round starting from round 3 up to semifinals
        for (let i = 0; i < roundKeys.length; i++) {
            const rNum = roundKeys[i];
            
            // Skip Babak 1 and Babak 2
            if (rNum < 3) continue;
            
            // Final is skip since it's "Final" (handled automatically at bottom)
            if (rNum === roundsCount) continue;
            
            const start = roundsData[rNum];
            let nextStart = roundsData[rNum + 1];
            if (!nextStart) {
                nextStart = start + 1;
            }
            
            const matchCount = nextStart - start;
            
            // Choose one random bracket number between start and nextStart - 1
            const randomBracket = Math.floor(Math.random() * matchCount) + start;
            
            let roundLabel = `Babak ${rNum}`;
            if (rNum === roundsCount - 1) {
                roundLabel = "Semifinal";
            } else if (rNum === roundsCount - 2) {
                roundLabel = "Babak 5";
            }
            
            text += `${roundLabel} : Bracket ${randomBracket}\n`;
        }
        
        text += "Final\n\n";
        text += "*Note: yang masuk ke dalam bracket dengan no diatas, mimin yang invite*";
        
        document.getElementById('roomtourTextarea').value = text;
    }

    // Run generateRoomtour once on modal show to prefill
    document.getElementById('modalShareTemplates').addEventListener('show.bs.modal', generateRoomtour);
</script>
