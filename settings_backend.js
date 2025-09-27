document.getElementById('avatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatarPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('avatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.type === 'image/gif' && !document.body.classList.contains('premium-user')) {
            alert('GIF avatars are available only for premium users');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatarPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('banner')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.type === 'image/gif' && !document.body.classList.contains('premium-user')) {
            alert('GIF banners are available only for premium users');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('.banner-preview').style.backgroundImage = `url('${event.target.result}')`;
        };
        reader.readAsDataURL(file);
    }
});

function selectTheme(theme) {
    document.getElementById('selectedTheme').value = theme;
    document.querySelectorAll('.theme-preview').forEach(el => {
        el.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
}

document.getElementById('banner')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.querySelector('.banner-preview').style.backgroundImage = `url('${event.target.result}')`;
        };
        reader.readAsDataURL(file);
    }
});

function showPremiumModal() {
    document.getElementById('premiumModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}