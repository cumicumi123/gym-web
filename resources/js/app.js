import { initCharts } from './charts';

document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (navToggle && mobileMenu) {
        navToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    const weightInput = document.getElementById('input-berat');
    const heightInput = document.getElementById('input-tinggi');
    const bmiValue = document.getElementById('bmi-value');
    const bmiCategory = document.getElementById('bmi-category');
    const bmiFill = document.getElementById('bmi-fill');

    const categoryFor = (bmi) => {
        if (bmi < 18.5) return 'Kurang';
        if (bmi < 25) return 'Normal';
        if (bmi < 30) return 'Berlebih';
        return 'Obesitas';
    };

    const updateBmi = () => {
        if (!weightInput || !heightInput || !bmiValue) return;

        const weight = Number.parseFloat(weightInput.value);
        const heightCm = Number.parseFloat(heightInput.value);

        if (!Number.isFinite(weight) || !Number.isFinite(heightCm) || heightCm <= 0) {
            bmiValue.textContent = '—';
            if (bmiCategory) bmiCategory.textContent = 'Belum dihitung';
            if (bmiFill) bmiFill.style.width = '0%';
            return;
        }

        const bmi = weight / ((heightCm / 100) ** 2);
        bmiValue.textContent = bmi.toFixed(1).replace('.', ',');
        if (bmiCategory) bmiCategory.textContent = categoryFor(bmi);
        if (bmiFill) {
            const width = Math.min(100, Math.max(2, ((bmi - 12) / (40 - 12)) * 100));
            bmiFill.style.width = `${width}%`;
        }
    };

    if (weightInput && heightInput) {
        weightInput.addEventListener('input', updateBmi);
        heightInput.addEventListener('input', updateBmi);
        updateBmi();
    }

    const form = document.querySelector('[data-predict-form]');
    if (form) {
        form.addEventListener('submit', () => {
            const button = form.querySelector('[data-submit-button]');
            if (button) {
                button.disabled = true;
                button.dataset.originalText = button.textContent.trim();
                button.textContent = 'Menghitung rekomendasi...';
            }
        });
    }

    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach((item) => {
        item.addEventListener('toggle', () => {
            if (!item.open) return;
            faqItems.forEach((other) => {
                if (other !== item) other.open = false;
            });
        });
    });

    const dumbbellStage = document.querySelector('[data-dumbbell-stage]');
    const dumbbellRotator = document.querySelector('[data-dumbbell-rotator]');

    if (dumbbellStage && dumbbellRotator) {
        const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        let rotX = -6;
        let rotY = 22;
        let velocityX = 0;
        let velocityY = 0;
        let dragging = false;
        let moved = false;
        let lastX = 0;
        let lastY = 0;

        const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

        const renderDumbbell = () => {
            dumbbellRotator.style.transform = `rotateX(${rotX}deg) rotateY(${rotY}deg) rotateZ(-5deg)`;
        };

        const spinDumbbell = () => {
            if (motionQuery.matches) {
                rotY += 60;
                renderDumbbell();
                return;
            }

            velocityY = 20;
            velocityX = 0;
        };

        const frame = () => {
            if (!dragging) {
                rotY += velocityY;
                rotX = clamp(rotX + velocityX, -80, 80);
                velocityY *= 0.94;
                velocityX *= 0.94;

                if (!motionQuery.matches && Math.abs(velocityY) < 0.02) {
                    rotY += 0.14;
                }

                renderDumbbell();
            }

            window.requestAnimationFrame(frame);
        };

        dumbbellStage.addEventListener('pointerdown', (event) => {
            dragging = true;
            moved = false;
            lastX = event.clientX;
            lastY = event.clientY;
            velocityX = 0;
            velocityY = 0;
            dumbbellStage.setPointerCapture?.(event.pointerId);
        });

        dumbbellStage.addEventListener('pointermove', (event) => {
            if (!dragging) {
                return;
            }

            const dx = event.clientX - lastX;
            const dy = event.clientY - lastY;

            if (Math.abs(dx) + Math.abs(dy) > 2) {
                moved = true;
            }

            lastX = event.clientX;
            lastY = event.clientY;

            rotY += dx * 0.55;
            rotX = clamp(rotX - dy * 0.45, -80, 80);
            velocityY = dx * 0.35;
            velocityX = -dy * 0.25;

            renderDumbbell();
        });

        const stopDragging = () => {
            if (!dragging) {
                return;
            }

            dragging = false;

            if (!moved) {
                spinDumbbell();
            }
        };

        dumbbellStage.addEventListener('pointerup', stopDragging);
        dumbbellStage.addEventListener('pointercancel', () => {
            dragging = false;
        });
        dumbbellStage.addEventListener('lostpointercapture', () => {
            dragging = false;
        });

        dumbbellStage.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                spinDumbbell();
                return;
            }

            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                event.preventDefault();
                rotY += event.key === 'ArrowLeft' ? -18 : 18;
                renderDumbbell();
            }
        });

        renderDumbbell();
        window.requestAnimationFrame(frame);
    }

    const revealElements = document.querySelectorAll('.reveal');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!reduceMotion && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 },
        );

        revealElements.forEach((element) => observer.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('is-visible'));
    }

    initCharts();
});
