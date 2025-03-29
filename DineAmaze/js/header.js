document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const navMenu = document.getElementById('nav-menu');
    const overlay = document.getElementById('overlay');
    const menuIcon = document.getElementById('menu-icon');
    
    mobileMenuBtn.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        overlay.style.display = navMenu.classList.contains('active') ? 'block' : 'none';
        
        // Add animation class
        menuIcon.classList.add('icon-transition');
        
        // Toggle between hamburger and cross icon with animation
        if (navMenu.classList.contains('active')) {
            menuIcon.style.animation = 'crossFade 0.3s forwards';
            setTimeout(() => {
                menuIcon.className = 'fi fi-br-cross icon-transition';
                menuIcon.classList.add('rotate');
            }, 150);
        } else {
            menuIcon.style.animation = 'iconFade 0.3s forwards';
            menuIcon.classList.remove('rotate');
            setTimeout(() => {
                menuIcon.className = 'fi fi-rr-bars-staggered icon-transition';
            }, 150);
        }
    });
    
    // Close menu when clicking on overlay
    overlay.addEventListener('click', function() {
        navMenu.classList.remove('active');
        overlay.style.display = 'none';
        
        menuIcon.style.animation = 'iconFade 0.3s forwards';
        menuIcon.classList.remove('rotate');
        setTimeout(() => {
            menuIcon.className = 'fi fi-rr-bars-staggered icon-transition';
        }, 150);
    });
    
    // Remove animation class after animation completes
    menuIcon.addEventListener('animationend', function() {
        menuIcon.classList.remove('icon-transition');
    });
});