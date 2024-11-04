const menu = document.querySelector('#mobile-menu');
const menuLinks = document.querySelector('.navbar__menu');

menu.addEventListener('click', function(){
    menu.classList.toggle('is-active');
    menuLinks.classList.toggle('active');
});




let currentIndex = 0; // Track the current index of the displayed cards
const totalCards = 9; // Total number of cards
const cardsToShow = 3; // How many cards you want to display at once

function updateSlider() {
    const sliderContainer = document.querySelector('.slider-container');
    // Calculate the offset based on the current index
    const offset = -(currentIndex * (100 / cardsToShow));
    sliderContainer.style.transform = `translateX(${offset}%)`;
}

function slideRight() {
    currentIndex++;
    if (currentIndex > totalCards - cardsToShow) {
        currentIndex = 0; // Wrap around to the start
    }
    updateSlider();
}

function slideLeft() {
    currentIndex--;
    if (currentIndex < 0) {
        currentIndex = totalCards - cardsToShow; // Go to the end
    }
    updateSlider();
}


