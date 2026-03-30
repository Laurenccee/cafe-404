/**
 * Item Filter Handler for Cafe 404
 * This script handles real-time AJAX filtering for the menu.
 */

// 1. GLOBAL SCOPE: Functions called by HTML 'onclick' attributes
window.confirmDelete = function (id, name) {
  if (confirm(`Are you sure you want to delete "${name}"?`)) {
    window.location.href = `/cafe_404/menu/delete?id=${id}`;
  }
};

document.addEventListener('DOMContentLoaded', () => {
  console.log('Filter Script Initialized');

  // 2. SELECTORS - We use 'name' attributes as they are most reliable
  const searchInput = document.querySelector('input[name="search"]');
  const categoryInput = document.querySelector('input[name="category"]');
  const availabilityInput = document.querySelector(
    'input[name="availability"]',
  );

  // The container that wraps the menu items (must match your PHP class)
  const gridContainer = document.querySelector('main .grid-cols-1');
  const paginationContainer = document.querySelector(
    '.flex.items-center.justify-between.pt-6',
  );

  // 3. THE AJAX FETCH FUNCTION
  async function updateMenu(pageNum = 1) {
    const params = new URLSearchParams();

    // Collect current values
    if (searchInput?.value) params.set('search', searchInput.value);
    if (categoryInput?.value) params.set('category', categoryInput.value);
    if (availabilityInput?.value)
      params.set('availability', availabilityInput.value);
    params.set('page', pageNum);

    const targetUrl = `${window.location.pathname}?${params.toString()}`;
    console.log('Fetching:', targetUrl);

    try {
      // Visual feedback: Dim the grid while loading
      if (gridContainer) gridContainer.style.opacity = '0.5';

      const response = await fetch(targetUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // Find the new content in the response
      const newGrid = doc.querySelector('main .grid-cols-1');
      const newPagination = doc.querySelector(
        '.flex.items-center.justify-between.pt-6',
      );

      // Swap the HTML
      if (newGrid && gridContainer) {
        gridContainer.innerHTML = newGrid.innerHTML;
      }
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      // Update URL bar and refresh Lucide icons
      window.history.pushState({}, '', targetUrl);
      if (window.lucide) window.lucide.createIcons();
    } catch (error) {
      console.error('AJAX Error:', error);
    } finally {
      if (gridContainer) gridContainer.style.opacity = '1';
    }
  }

  // 4. EVENT LISTENERS

  // A. Search Input (Debounced 300ms)
  let timer;
  searchInput?.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => updateMenu(1), 300);
  });

  // B. Mutation Observer for ComboBoxFields (Hidden Inputs)
  const observer = new MutationObserver((mutations) => {
    console.log('Filter changed via ComboBox');
    updateMenu(1);
  });

  const observerOptions = { attributes: true, attributeFilter: ['value'] };
  if (categoryInput) observer.observe(categoryInput, observerOptions);
  if (availabilityInput) observer.observe(availabilityInput, observerOptions);

  // C. Pagination Clicks
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href*="page="]');
    if (link) {
      e.preventDefault();
      const url = new URL(link.href, window.location.origin);
      const page = url.searchParams.get('page') || 1;
      updateMenu(page);
    }
  });
});
