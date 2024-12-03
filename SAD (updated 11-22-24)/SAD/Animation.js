// Animation.js

let currentIndex = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;

function changeSlide() {
    // Remove 'active' class from the current slide
    slides[currentIndex].classList.remove('active');
    
    // Move to the next slide
    currentIndex = (currentIndex + 1) % totalSlides;
    
    // Add 'active' class to the new current slide
    slides[currentIndex].classList.add('active');
}

// Initial highlight for the first slide
slides[currentIndex].classList.add('active');

// Change slides every 5 seconds
setInterval(changeSlide, 5000); // Adjust the interval time as needed


