// Mood-Based Playlist Generator - Main JavaScript

$(document).ready(function() {
    // Update energy level display
    $('#energy_level').on('input', function() {
        $('#energy_value').text($(this).val());
    });

    // Handle form submission
    $('#moodForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');

        $.ajax({
            url: 'recommend.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayResults(response);
                    $('html, body').animate({
                        scrollTop: $('#results').offset().top - 100
                    }, 500);
                } else {
                    alert(response.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function displayResults(data) {
    let html = `
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle me-2"></i>${data.playlist_name}</h5>
        </div>
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-primary" onclick="sharePlaylist('${data.share_url}')">
                <i class="fas fa-share-alt me-2"></i>Bagikan Playlist
            </button>
        </div>
        <div class="list-group">
    `;

    data.songs.forEach(function(song, index) {
        const thumbnail = song.thumbnail || 'https://via.placeholder.com/80x80?text=🎵';
        
        html += `
            <div class="list-group-item">
                <div class="song-item">
                    <img src="${thumbnail}" alt="${song.title}" class="song-thumbnail" onerror="this.src='https://via.placeholder.com/80x80?text=🎵'">
                    <div class="song-info">
                        <h6 class="mb-1">${song.title}</h6>
                        <p class="mb-1 text-muted"><small>${song.artist}</small></p>
                        <span class="badge bg-secondary">${song.mood}</span>
                    </div>
                    <div class="ms-3">
        `;
        
        if (song.spotify_link) {
            html += `<a href="${song.spotify_link}" target="_blank" class="btn btn-sm btn-success me-2">
                <i class="fab fa-spotify"></i> Spotify
            </a>`;
        }
        
        if (song.youtube_link) {
            html += `<a href="${song.youtube_link}" target="_blank" class="btn btn-sm btn-danger">
                <i class="fab fa-youtube"></i> YouTube
            </a>`;
        }
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    $('#playlistContent').html(html);
    $('#results').fadeIn();
}

function sharePlaylist(url) {
    // Cek apakah Web Share API didukung
    if (navigator.share) {
        navigator.share({
            title: 'Mood-Based Playlist',
            text: 'Lihat playlist rekomendasi saya!',
            url: url
        }).catch(function(error) {
            // Jika user cancel atau error, fallback ke clipboard
            copyToClipboard(url);
        });
    } else {
        // Fallback ke clipboard
        copyToClipboard(url);
    }
}

function copyToClipboard(text) {
    // Method 1: Modern Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showShareSuccess();
        }).catch(function() {
            // Fallback ke method lama
            fallbackCopyToClipboard(text);
        });
    } else {
        // Fallback ke method lama
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    // Method 2: Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-999999px';
    textarea.style.top = '-999999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showShareSuccess();
        } else {
            showShareError();
        }
    } catch (err) {
        showShareError();
    } finally {
        document.body.removeChild(textarea);
    }
}

function showShareSuccess() {
    // Buat toast notification yang tidak langsung hilang
    const toast = document.createElement('div');
    toast.className = 'share-toast';
    toast.innerHTML = '<i class="fas fa-check-circle me-2"></i>Link playlist berhasil disalin!';
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    // Animate out setelah 3 detik
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function showShareError() {
    const toast = document.createElement('div');
    toast.className = 'share-toast share-toast-error';
    toast.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Gagal menyalin link. Silakan salin manual.';
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

