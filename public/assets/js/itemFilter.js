/**
 * Reusable Filter Function
 * @param {string} category - The category name to filter by
 * @param {HTMLElement} btn - The button element clicked
 */
function filterMenu(category, btn) {
  const cards = document.querySelectorAll('.menu-card');
  const navButtons = btn.closest('nav').querySelectorAll('button, a');

  console.log(`Filtering by category: ${category}`);
  // 1. Update Button Styles (Visual Feedback)
  navButtons.forEach((b) => {
    // Reset all to secondary/inactive
    b.classList.remove('bg-[#2d5a27]', 'text-white');
    b.classList.add('bg-[#F0EDED]', 'text-[#2c2c2c]');
  });

  // Set clicked button to primary/active
  btn.classList.add('bg-[#2d5a27]', 'text-white');
  btn.classList.remove('bg-[#F0EDED]', 'text-[#2c2c2c]');

  // 2. Filter Cards
  cards.forEach((card) => {
    const cardCategory = card.getAttribute('data-category');

    if (category === 'all' || cardCategory === category) {
      card.style.display = 'flex';
      // Optional: Add a small fade-in animation
      card.classList.add('animate-fade-in');
    } else {
      card.style.display = 'none';
    }
  });
}
