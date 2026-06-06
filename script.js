// ===== PHP API CLIENT =====
class PHPApiClient {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
    }

    async get(endpoint, params = {}) {
        const query = new URLSearchParams(params).toString();
        const url = `${this.baseUrl}${endpoint}${query ? '?' + query : ''}`;
        
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    async post(endpoint, data = {}) {
        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    // Get all projects
    async getProjects() {
        return this.get('api.php', { action: 'get-projects' });
    }

    // Get single project
    async getProject(id) {
        return this.get('api.php', { action: 'get-project', id });
    }

    // Send contact message
    async sendMessage(data) {
        return this.post('contact.php', data);
    }

    // Get statistics
    async getStats() {
        return this.get('api.php', { action: 'stats' });
    }

    // Ping server
    async ping() {
        return this.get('api.php', { action: 'ping' });
    }
}

// ===== FORM HANDLER =====
class FormHandler {
    constructor(formId, apiClient) {
        this.form = document.getElementById(formId);
        this.api = apiClient;
        this.isSubmitting = false;

        if (this.form) {
            this.init();
        }
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.attachInputListeners();
    }

    attachInputListeners() {
        const inputs = this.form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('focus', () => this.clearError(input.name));
        });
    }

    validateField(field) {
        const name = field.name;
        const value = field.value.trim();
        const errors = {};

        if (!value) {
            errors[name] = `${this.getFieldLabel(name)} tidak boleh kosong`;
        } else if (name === 'email' && !this.isValidEmail(value)) {
            errors[name] = 'Format email tidak valid';
        } else if (name === 'phone' && !this.isValidPhone(value)) {
            errors[name] = 'Format telepon tidak valid (minimal 10 digit)';
        } else if (name === 'message' && value.length < 10) {
            errors[name] = 'Pesan minimal 10 karakter';
        }

        if (Object.keys(errors).length > 0) {
            this.showError(name, errors[name]);
            return false;
        } else {
            this.clearError(name);
            return true;
        }
    }

    validateForm() {
        const inputs = this.form.querySelectorAll('[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}Error`);
        const formGroup = document.querySelector(`input[name="${fieldName}"], textarea[name="${fieldName}"]`).closest('.form-group');

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
        if (formGroup) {
            formGroup.classList.add('error');
        }
    }

    clearError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}Error`);
        const formGroup = document.querySelector(`input[name="${fieldName}"], textarea[name="${fieldName}"]`)?.closest('.form-group');

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }
        if (formGroup) {
            formGroup.classList.remove('error');
        }
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^(\+62|0)[0-9]{9,12}$/.test(phone.replace(/[\s\-]/g, ''));
    }

    getFieldLabel(name) {
        const labels = {
            name: 'Nama',
            email: 'Email',
            phone: 'Telepon',
            subject: 'Subjek',
            message: 'Pesan'
        };
        return labels[name] || name;
    }

    async handleSubmit(e) {
        e.preventDefault();

        if (this.isSubmitting) return;
        if (!this.validateForm()) {
            Notification.show('Silakan periksa kembali form Anda', 'error');
            return;
        }

        this.isSubmitting = true;
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';

        const formData = {
            name: this.form.name.value,
            email: this.form.email.value,
            phone: this.form.phone.value,
            subject: this.form.subject.value,
            message: this.form.message.value
        };

        const response = await this.api.sendMessage(formData);

        this.isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;

        const messageElement = document.getElementById('formMessage');

        if (response.success) {
            messageElement.textContent = response.message;
            messageElement.className = 'form-message success';
            this.form.reset();
            Notification.show('Pesan Anda telah dikirim! Terima kasih 🎉', 'success', 4000);
            ConfettiAnimation.triggerConfetti();
            setTimeout(() => {
                messageElement.textContent = '';
            }, 5000);
        } else {
            messageElement.textContent = response.message;
            messageElement.className = 'form-message error';
            Notification.show('Gagal mengirim pesan. Silakan coba lagi.', 'error');
        }
    }
}

// ===== API ANIMASI TERINTEGRASI =====
class ApiAnimatedLoader {
    static show(message = 'Memuat...') {
        const loader = document.createElement('div');
        loader.id = 'apiLoader';
        loader.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            z-index: 9999;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: zoomIn 0.3s ease-out;
        `;

        loader.innerHTML = `
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid #e5e7eb;
                border-top: 4px solid #2563eb;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            "></div>
            <p style="color: #666; font-weight: 500;">${message}</p>
        `;

        document.body.appendChild(loader);
    }

    static hide() {
        const loader = document.getElementById('apiLoader');
        if (loader) {
            loader.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => loader.remove(), 300);
        }
    }
}

// ===== PARTIKEL ANIMASI =====
class ParticleAnimation {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.canvas.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        `;
        document.body.insertBefore(this.canvas, document.body.firstChild);

        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.init();
    }

    init() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        this.createParticles();
        this.animate();

        window.addEventListener('resize', () => {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        });
    }

    createParticles() {
        for (let i = 0; i < 50; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                size: Math.random() * 2 + 1,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                opacity: Math.random() * 0.5 + 0.2
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        this.particles.forEach((p, i) => {
            p.x += p.vx;
            p.y += p.vy;

            if (p.x < 0 || p.x > this.canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > this.canvas.height) p.vy *= -1;

            this.ctx.fillStyle = `rgba(37, 99, 235, ${p.opacity})`;
            this.ctx.beginPath();
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fill();

            // Draw connections
            this.particles.forEach((p2, j) => {
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 100) {
                    this.ctx.strokeStyle = `rgba(37, 99, 235, ${0.1 * (1 - distance / 100)})`;
                    this.ctx.lineWidth = 0.5;
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.stroke();
                }
            });
        });

        requestAnimationFrame(() => this.animate());
    }
}

// ===== ANIMASI TYPING TEXT =====
class TypingAnimation {
    constructor(element, text, speed = 50) {
        this.element = element;
        this.text = text;
        this.speed = speed;
        this.index = 0;
        this.animate();
    }

    animate() {
        if (this.index < this.text.length) {
            this.element.textContent += this.text[this.index];
            this.index++;
            setTimeout(() => this.animate(), this.speed);
        }
    }
}

// ===== ANIMASI FLOATING ELEMENTS =====
class FloatingAnimation {
    constructor() {
        this.init();
    }

    init() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes floating {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            @keyframes floatingRotate {
                0% { transform: rotate(0deg) translateY(0px); }
                50% { transform: rotate(5deg) translateY(-15px); }
                100% { transform: rotate(0deg) translateY(0px); }
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.7; transform: scale(1.05); }
            }
            
            @keyframes glow {
                0%, 100% { box-shadow: 0 0 5px rgba(37, 99, 235, 0.3); }
                50% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.8); }
            }
            
            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-50px); }
                to { opacity: 1; transform: translateX(0); }
            }
            
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(50px); }
                to { opacity: 1; transform: translateX(0); }
            }
            
            @keyframes zoomIn {
                from { opacity: 0; transform: scale(0.8); }
                to { opacity: 1; transform: scale(1); }
            }
            
            @keyframes rotateIn {
                from { opacity: 0; transform: rotate(-10deg); }
                to { opacity: 1; transform: rotate(0deg); }
            }
            
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            .floating { animation: floating 3s ease-in-out infinite; }
            .floating-rotate { animation: floatingRotate 4s ease-in-out infinite; }
            .pulse { animation: pulse 2s ease-in-out infinite; }
            .glow { animation: glow 2s ease-in-out infinite; }
            .bounce { animation: bounce 2s ease-in-out infinite; }
        `;
        document.head.appendChild(style);
    }

    applyFloating(element) {
        element.classList.add('floating');
    }

    applyGlow(element) {
        element.classList.add('glow');
    }
}

// ===== ANIMASI LOADING =====
class LoadingAnimation {
    static createLoader() {
        const loader = document.createElement('div');
        loader.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        `;

        loader.innerHTML = `
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid #e5e7eb;
                border-top: 4px solid #2563eb;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            "></div>
        `;

        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(loader);
        return loader;
    }

    static removeLoader(loader) {
        loader.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => loader.remove(), 300);
    }
}

// ===== PARALLAX ANIMASI =====
class AdvancedParallax {
    constructor() {
        this.elements = document.querySelectorAll('[data-parallax]');
        this.init();
    }

    init() {
        window.addEventListener('scroll', () => this.update());
    }

    update() {
        this.elements.forEach(element => {
            const speed = element.dataset.parallax || 0.5;
            const yPos = window.scrollY * speed;
            element.style.transform = `translateY(${yPos}px)`;
        });
    }
}

// ===== ANIMASI TEXT REVEAL =====
class TextReveal {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
        const text = this.element.textContent;
        this.element.textContent = '';

        const chars = text.split('');
        chars.forEach((char, index) => {
            const span = document.createElement('span');
            span.textContent = char;
            span.style.cssText = `
                display: inline-block;
                opacity: 0;
                animation: fadeInUp 0.8s ease-out ${index * 0.05}s forwards;
            `;
            this.element.appendChild(span);
        });
    }
}

// ===== ANIMASI HOVER CARD =====
class HoverCardAnimation {
    constructor() {
        this.init();
    }

    init() {
        const cards = document.querySelectorAll('.project-card, .stat-card, .hero-card');

        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.animation = 'none';
                void this.offsetWidth; // Trigger reflow
                this.style.animation = 'pulse 0.6s ease-out';
            });
        });
    }
}

// ===== MENU ANIMASI =====
class MenuAnimation {
    constructor() {
        this.createStyles();
    }

    createStyles() {
        const style = document.createElement('style');
        style.textContent = `
            nav a {
                position: relative;
                overflow: hidden;
            }
            
            nav a::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: -100%;
                width: 100%;
                height: 2px;
                background: #2563eb;
                transition: left 0.3s ease;
            }
            
            nav a:hover::before {
                left: 0;
            }
            
            nav a::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 0;
                height: 100%;
                background: rgba(37, 99, 235, 0.1);
                transition: width 0.3s ease;
            }
            
            nav a:hover::after {
                width: 100%;
            }
        `;
        document.head.appendChild(style);
    }
}

// ===== SCROLL PROGRESS BAR =====
class ScrollProgressBar {
    constructor() {
        this.createBar();
        this.init();
    }

    createBar() {
        const bar = document.createElement('div');
        bar.id = 'scrollProgressBar';
        bar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            z-index: 1001;
            width: 0%;
            transition: width 0.2s ease;
        `;
        document.body.appendChild(bar);
    }

    init() {
        window.addEventListener('scroll', () => {
            const scrollPercentage = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            document.getElementById('scrollProgressBar').style.width = scrollPercentage + '%';
        });
    }
}

// ===== KONFETTI ANIMASI =====
class ConfettiAnimation {
    static createConfetti() {
        const confetti = document.createElement('div');
        confetti.style.cssText = `
            position: fixed;
            width: 10px;
            height: 10px;
            background: #2563eb;
            pointer-events: none;
            z-index: 999;
        `;

        const startX = Math.random() * window.innerWidth;
        confetti.style.left = startX + 'px';
        confetti.style.top = '-10px';

        document.body.appendChild(confetti);

        let x = startX;
        let y = 0;
        let vx = (Math.random() - 0.5) * 4;
        let vy = Math.random() * 3 + 2;

        const animate = () => {
            y += vy;
            x += vx;
            vy += 0.1; // Gravity
            confetti.style.left = x + 'px';
            confetti.style.top = y + 'px';
            confetti.style.opacity = 1 - (y / window.innerHeight);

            if (y < window.innerHeight) {
                requestAnimationFrame(animate);
            } else {
                confetti.remove();
            }
        };

        animate();
    }

    static triggerConfetti() {
        for (let i = 0; i < 30; i++) {
            setTimeout(() => this.createConfetti(), i * 30);
        }
    }
}

// ===== TEMA GELAP / TERANG =====
class ThemeManager {
    constructor() {
        this.isDark = localStorage.getItem('theme') === 'dark';
        this.init();
    }

    init() {
        this.applyTheme();
        this.createToggleButton();
    }

    applyTheme() {
        if (this.isDark) {
            document.documentElement.style.colorScheme = 'dark';
            document.body.style.background = '#1a1a2e';
            document.body.style.color = '#e0e0e0';
        } else {
            document.documentElement.style.colorScheme = 'light';
            document.body.style.background = '#f7f9fc';
            document.body.style.color = '#1f2937';
        }
    }

    createToggleButton() {
        const header = document.querySelector('.site-header');
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'theme-toggle';
        toggleBtn.innerHTML = this.isDark ? '☀️' : '🌙';
        toggleBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            transition: transform 0.3s ease;
        `;

        toggleBtn.addEventListener('click', () => this.toggle());
        toggleBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'rotate(20deg) scale(1.1)';
        });
        toggleBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'rotate(0deg) scale(1)';
        });

        header.querySelector('.header-inner').appendChild(toggleBtn);
    }

    toggle() {
        this.isDark = !this.isDark;
        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        this.applyTheme();
        document.querySelector('.theme-toggle').innerHTML = this.isDark ? '☀️' : '🌙';
        ConfettiAnimation.triggerConfetti();
    }
}

// ===== NOTIFIKASI SISTEM =====
class Notification {
    static show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
}

// ===== MODAL UNTUK DETAIL PROYEK =====
class ProjectModal {
    constructor() {
        this.createModal();
        this.attachEventListeners();
    }

    createModal() {
        const modal = document.createElement('div');
        modal.id = 'projectModal';
        modal.style.cssText = `
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 999;
            animation: fadeIn 0.3s ease;
        `;

        modal.innerHTML = `
            <div style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 40px;
                border-radius: 12px;
                max-width: 600px;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                animation: zoomIn 0.3s ease;
            ">
                <button style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                " onclick="document.getElementById('projectModal').style.display='none'">×</button>
                
                <h2 id="modalTitle"></h2>
                <p id="modalDescription"></p>
                <div id="modalDetails"></div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="button" onclick="Notification.show('Proyek berhasil ditambahkan!', 'success'); ConfettiAnimation.triggerConfetti();">Lihat Proyek</button>
                    <button class="button button-secondary" onclick="document.getElementById('projectModal').style.display='none'">Tutup</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
    }

    attachEventListeners() {
        const cards = document.querySelectorAll('.project-card');
        cards.forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', () => {
                const title = card.querySelector('h3').textContent;
                const desc = card.querySelector('p').textContent;
                this.show(title, desc);
            });

            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-8px)';
                this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.2)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
    }

    show(title, description) {
        const modal = document.getElementById('projectModal');
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalDetails').innerHTML = `
            <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-top: 15px;">
                <p><strong>📅 Tahun:</strong> 2026</p>
                <p><strong>🛠️ Tools:</strong> HTML, CSS, JavaScript</p>
                <p><strong>⭐ Rating:</strong> 5/5</p>
            </div>
        `;
        modal.style.display = 'block';
    }
}

// ===== COUNTER ANIMASI =====
class CounterAnimation {
    static animateCounters() {
        const statCards = document.querySelectorAll('.stat-card h3');

        statCards.forEach(element => {
            const target = parseInt(element.textContent);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const counter = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + '+';
                    clearInterval(counter);
                } else {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 16);
        });
    }
}

// ===== NAVIGASI LANCAR =====
class SmoothNavigation {
    constructor() {
        this.navLinks = document.querySelectorAll('nav a');
        this.init();
    }

    init() {
        this.navLinks.forEach(link => {
            link.addEventListener('click', (e) => this.handleClick(e));
        });

        window.addEventListener('scroll', () => this.updateActiveLink());
    }

    handleClick(e) {
        e.preventDefault();
        const targetId = e.target.getAttribute('href');
        const targetSection = document.querySelector(targetId);

        if (targetSection) {
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    updateActiveLink() {
        let current = '';
        const sections = document.querySelectorAll('section');

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        this.navLinks.forEach(link => {
            link.style.color = '';
            link.style.borderBottom = '';

            if (link.getAttribute('href') === '#' + current) {
                link.style.color = '#3b82f6';
                link.style.borderBottom = '2px solid #3b82f6';
                link.style.paddingBottom = '2px';
            }
        });
    }
}

// ===== SCROLL REVEAL ANIMASI =====
class ScrollReveal {
    constructor() {
        this.elements = document.querySelectorAll('.section, .project-card, .stat-card, .button');
        this.init();
    }

    init() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        this.elements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(element);
        });
    }
}

// ===== BUTTON INTERAKTIF =====
class ButtonManager {
    constructor() {
        this.init();
    }

    init() {
        const buttons = document.querySelectorAll('.button');

        buttons.forEach(button => {
            button.style.transition = 'all 0.3s ease';
            button.style.position = 'relative';
            button.style.overflow = 'hidden';

            button.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            });

            button.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });

            button.addEventListener('click', function (e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });
    }
}

// ===== KEYBOARD SHORTCUTS =====
class KeyboardShortcuts {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K untuk search (placeholder)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                Notification.show('Fitur search sedang dikembangkan 🚀', 'info');
            }

            // Ctrl/Cmd + / untuk bantuan
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                Notification.show('⌨️ Shortcut: Ctrl+K untuk search', 'info');
            }
        });
    }
}

// ===== INISIALISASI SEMUA FITUR =====
document.addEventListener('DOMContentLoaded', function () {
    // Tambahkan animasi keyframe
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .project-card {
            transition: all 0.3s ease;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);

    // Inisialisasi API Client
    const apiClient = new PHPApiClient();
    
    // Inisialisasi Form Handler
    const formHandler = new FormHandler('contactForm', apiClient);

    // Inisialisasi semua modul
    const particles = new ParticleAnimation();
    const floating = new FloatingAnimation();
    const theme = new ThemeManager();
    const modal = new ProjectModal();
    const nav = new SmoothNavigation();
    const reveal = new ScrollReveal();
    const buttons = new ButtonManager();
    const shortcuts = new KeyboardShortcuts();
    const progressBar = new ScrollProgressBar();
    const menuAnim = new MenuAnimation();
    const hoverCard = new HoverCardAnimation();

    // Animasi counter saat section about terlihat
    const aboutSection = document.querySelector('#about');
    const observerAbout = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                CounterAnimation.animateCounters();
                observerAbout.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    if (aboutSection) observerAbout.observe(aboutSection);

    // Test PHP API Connection
    apiClient.ping().then(response => {
        console.log('📡 PHP Server Status:', response);
        if (response.success) {
            console.log('%c✅ PHP Backend Connected!', 'color: #10b981; font-weight: bold;');
        }
    }).catch(err => {
        console.warn('⚠️ PHP Backend not available (local testing):', err);
    });
    
    // Load projects dari PHP
    apiClient.getProjects().then(response => {
        if (response.success) {
            console.log('📦 Projects loaded from PHP:', response.data);
        }
    }).catch(err => {
        console.warn('Projects loading failed:', err);
    });

    // Tampilkan notifikasi sambutan dengan animasi
    setTimeout(() => {
        Notification.show('👋 Selamat datang di Portofolio Kyy!', 'info', 2500);
    }, 500);

    // Trigger confetti untuk effect spesial
    setTimeout(() => {
        ConfettiAnimation.triggerConfetti();
    }, 3000);

    // Log informasi developer
    console.log('%c🎨 Portofolio Kyy - Powered by Advanced JavaScript + PHP Backend', 
        'color: #3b82f6; font-size: 14px; font-weight: bold;');
    console.log('%c📡 Full-Stack Integration: Frontend JS + PHP API + Form Validation', 
        'color: #2563eb; font-size: 12px;');
    console.log('%c✨ Features: Dark Mode, Animations, Forms, API Integration', 
        'color: #2563eb; font-size: 12px;');
});
