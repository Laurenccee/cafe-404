window.confirmDelete = function (id, name) {
  if (confirm(`Are you sure you want to delete "${name}"?`)) {
    window.location.href = `/cafe_404/menu/delete?id=${id}`;
  }
};

document.addEventListener('DOMContentLoaded', () => {
  console.log('Filter Script Initialized');

  const searchInput = document.querySelector('input[name="search"]');
  const categoryInput = document.querySelector('input[name="category"]');
  const availabilityInput = document.querySelector(
    'input[name="availability"]',
  );

  const gridContainer = document.querySelector('main .grid-cols-1');
  const paginationContainer = document.querySelector(
    '.flex.items-center.justify-between.pt-6',
  );

  async function updateMenu(pageNum = 1) {
    const params = new URLSearchParams();

    if (searchInput?.value) params.set('search', searchInput.value);
    if (categoryInput?.value) params.set('category', categoryInput.value);
    if (availabilityInput?.value)
      params.set('availability', availabilityInput.value);
    params.set('page', pageNum);

    const targetUrl = `${window.location.pathname}?${params.toString()}`;
    console.log('Fetching:', targetUrl);

    try {
      if (gridContainer) gridContainer.style.opacity = '0.5';

      const response = await fetch(targetUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      const newGrid = doc.querySelector('main .grid-cols-1');
      const newPagination = doc.querySelector(
        '.flex.items-center.justify-between.pt-6',
      );

      if (newGrid && gridContainer) {
        gridContainer.innerHTML = newGrid.innerHTML;
      }
      if (newPagination && paginationContainer) {
        paginationContainer.innerHTML = newPagination.innerHTML;
      }

      window.history.pushState({}, '', targetUrl);
      if (window.lucide) window.lucide.createIcons();
    } catch (error) {
      console.error('AJAX Error:', error);
    } finally {
      if (gridContainer) gridContainer.style.opacity = '1';
    }
  }

  let timer;
  searchInput?.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => updateMenu(1), 300);
  });

  const observer = new MutationObserver((mutations) => {
    console.log('Filter changed via ComboBox');
    updateMenu(1);
  });

  const observerOptions = { attributes: true, attributeFilter: ['value'] };
  if (categoryInput) observer.observe(categoryInput, observerOptions);
  if (availabilityInput) observer.observe(availabilityInput, observerOptions);

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
