// Confirmation for delete actions
function confirmDelete(postId, title) {
    if (confirm('Are you sure you want to delete "' + title + '"?\nThis action cannot be undone.')) {
        // Check if we're already in the blog folder
        var currentPath = window.location.pathname;
        if (currentPath.includes('/blog/')) {
            window.location.href = 'delete.php?id=' + postId;
        } else {
            window.location.href = 'blog/delete.php?id=' + postId;
        }
    }
    return false;
}

// Simple Markdown to HTML converter
function parseMarkdown(markdown) {
    let html = markdown;
    
    // Headers
    html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');
    
    // Bold
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    
    // Italic
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
    
    // Links
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
    
    // Line breaks
    html = html.replace(/\n\n/g, '</p><p>');
    html = html.replace(/\n/g, '<br>');
    
    // Lists
    html = html.replace(/^\- (.+)$/gim, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
    
    // Wrap in paragraphs if not already
    if (!html.startsWith('<h') && !html.startsWith('<ul')) {
        html = '<p>' + html + '</p>';
    }
    
    return html;
}

// Preview markdown in real-time (optional feature)
function setupMarkdownPreview() {
    const contentTextarea = document.getElementById('content');
    const previewDiv = document.getElementById('markdown-preview');
    
    if (contentTextarea && previewDiv) {
        contentTextarea.addEventListener('input', function() {
            previewDiv.innerHTML = parseMarkdown(this.value);
        });
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#e74c3c';
            } else {
                input.style.borderColor = '#e0e0e0';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
}

// Auto-hide alerts after 5 seconds
function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

// Character counter for textarea
function addCharCounter(textareaId, maxLength = 5000) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    
    const counter = document.createElement('div');
    counter.style.textAlign = 'right';
    counter.style.color = '#999';
    counter.style.fontSize = '0.9rem';
    counter.style.marginTop = '0.5rem';
    
    textarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length} / ${maxLength} characters`;
        counter.style.color = remaining < 100 ? '#e74c3c' : '#999';
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    autoHideAlerts();
    validateForm('login-form');
    validateForm('register-form');
    validateForm('blog-form');
    addCharCounter('content');
});

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    return strength;
}

// Add password strength indicator
function addPasswordStrengthIndicator(passwordInputId) {
    const passwordInput = document.getElementById(passwordInputId);
    if (!passwordInput) return;
    
    const indicator = document.createElement('div');
    indicator.className = 'password-strength';
    indicator.style.height = '5px';
    indicator.style.borderRadius = '3px';
    indicator.style.marginTop = '0.5rem';
    indicator.style.transition = 'all 0.3s';
    
    passwordInput.parentNode.appendChild(indicator);
    
    passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        const colors = ['#e74c3c', '#e67e22', '#f39c12', '#2ecc71', '#27ae60'];
        const widths = ['20%', '40%', '60%', '80%', '100%'];
        
        if (this.value.length > 0) {
            indicator.style.backgroundColor = colors[strength - 1] || colors[0];
            indicator.style.width = widths[strength - 1] || widths[0];
        } else {
            indicator.style.width = '0%';
        }
    });
}







































