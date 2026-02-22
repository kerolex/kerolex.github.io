/**
 * News Filter Module - SIMPLIFIED
 * Handles filtering, pagination, and year archive browsing
 * Removed confusing year grouping - shows flat list with year filtering option
 */

document.addEventListener('DOMContentLoaded', function() {
  const PAGE_SIZE = 10;
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  const filterBtns = Array.from(document.querySelectorAll('.filter-btn[data-filter]'));
  const archiveEl = document.getElementById('newsArchive');

  // State management
  let currentFilter = 'all';
  let currentYear = null; // null = all years
  let currentList = [];
  let shownCount = 0;

  /**
   * Get all news items from DOM
   */
  const getAllNewsItems = () => Array.from(document.querySelectorAll('.news-item'));

  /**
   * Extract year from news item
   */
  const getItemYear = (item) => {
    if (item.dataset.year) return item.dataset.year;
    const dateEl = item.querySelector('.news-date');
    if (dateEl?.getAttribute('datetime')) {
      return dateEl.getAttribute('datetime').slice(0, 4);
    }
    return 'Unknown';
  };

  /**
   * Check if item is recent (within X days)
   */
  const isRecentItem = (item, days = 30) => {
    const dateEl = item.querySelector('.news-date');
    const datetime = dateEl?.getAttribute('datetime');
    
    if (!datetime) return false;
    
    try {
      const itemDate = new Date(datetime).getTime();
      const daysDiff = (Date.now() - itemDate) / (1000 * 60 * 60 * 24);
      return daysDiff <= days;
    } catch (e) {
      return false;
    }
  };

  /**
   * Add NEW badge to recent items
   */
  const addNewBadges = () => {
    getAllNewsItems().forEach(item => {
      const existingBadge = item.querySelector('.news-badge');
      if (existingBadge) existingBadge.remove();

      if (isRecentItem(item)) {
        const badge = document.createElement('span');
        badge.className = 'news-badge';
        badge.textContent = 'NEW';
        badge.setAttribute('aria-label', 'Recently added');
        
        const heading = item.querySelector('h3');
        if (heading) {
          heading.appendChild(badge);
        }
      }
    });
  };

  /**
   * Hide all news items
   */
  const hideAllItems = () => {
    getAllNewsItems().forEach(item => item.classList.add('hidden'));
  };

  /**
   * Show specific number of items from current list
   */
  const showItemsCount = (count) => {
    let shown = 0;
    for (let i = 0; i < currentList.length && shown < count; i++) {
      currentList[i].classList.remove('hidden');
      shown++;
    }
  };

  /**
   * Update load more button visibility and label
   */
  const updateLoadMoreButton = () => {
    if (!loadMoreBtn) return;
    
    const totalItems = currentList.length;
    const visibleItems = currentList.filter(item => !item.classList.contains('hidden')).length;
    
    if (totalItems > visibleItems) {
      loadMoreBtn.style.display = 'block';
      const remaining = totalItems - visibleItems;
      loadMoreBtn.textContent = `Load More News (${remaining} remaining)`;
      loadMoreBtn.setAttribute('aria-label', `Load ${remaining} more news items`);
    } else {
      loadMoreBtn.style.display = 'none';
      if (totalItems > 0) {
        loadMoreBtn.setAttribute('aria-label', 'All news loaded');
      }
    }
  };

  /**
   * Initialize: Show last 10 items
   */
  const initializeNewsDisplay = () => {
    addNewBadges();
    
    // Get all items and show last 10
    const allItems = getAllNewsItems();
    currentList = allItems;
    
    hideAllItems();
    showItemsCount(PAGE_SIZE);
    shownCount = PAGE_SIZE;

    updateLoadMoreButton();
  };

  /**
   * Load More button - show next PAGE_SIZE items
   */
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      const nextCount = shownCount + PAGE_SIZE;
      showItemsCount(nextCount);
      shownCount = nextCount;
      updateLoadMoreButton();

      // Smooth scroll to newly loaded items
      if (currentList.length > shownCount - PAGE_SIZE) {
        const nextItem = currentList[shownCount - PAGE_SIZE];
        if (nextItem) {
          nextItem.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  }

  /**
   * Build year archive - simple link list (NO grouping)
   */
  if (archiveEl) {
    // Count items by year
    const yearCounts = {};
    getAllNewsItems().forEach(item => {
      const year = getItemYear(item);
      yearCounts[year] = (yearCounts[year] || 0) + 1;
    });

    // Create archive list
    // const wrapper = document.createElement('div');
    const heading = document.createElement('h3');
    heading.textContent = 'Browse by Year';
    // wrapper.appendChild(heading);

    const ul = document.createElement('ul');
    const sortedYears = Object.keys(yearCounts).sort((a, b) => b - a);

    sortedYears.forEach(year => {
      const li = document.createElement('li');
      const a = document.createElement('a');
      a.href = '#';
      a.textContent = `${year} (${yearCounts[year]})`;
      a.dataset.year = year;
      a.setAttribute('role', 'button');
      a.setAttribute('aria-label', `Show news from ${year}`);
      li.appendChild(a);
      ul.appendChild(li);
    });

    // wrapper.className = 'news-archive-wrapper';
    // wrapper.appendChild(ul);
    archiveEl.innerHTML = '';
    // archiveEl.appendChild(wrapper);
    archiveEl.className = 'news-archive-wrapper';
    archiveEl.appendChild(heading);
    archiveEl.appendChild(ul);

    /**
     * Year link click handler - filter by year
     */
    ul.addEventListener('click', (event) => {
      const link = event.target.closest('a');
      if (!link) return;

      event.preventDefault();
      const year = link.dataset.year;

      // Reset filters to "All" if year is selected
      filterBtns.forEach(b => b.classList.remove('active'));
      const allBtn = filterBtns.find(b => b.dataset.filter === 'all');
      if (allBtn) allBtn.classList.add('active');

      // Filter items by year
      const allItems = getAllNewsItems();
      currentList = allItems.filter(item => getItemYear(item) === year);
      
      currentYear = year;
      shownCount = 0;

      hideAllItems();
      showItemsCount(PAGE_SIZE);
      shownCount = PAGE_SIZE;

      updateLoadMoreButton();

      // Scroll to news feed
      const feed = document.querySelector('.news-feed');
      if (feed) {
        feed.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      // Update active state on archive links
      document.querySelectorAll('[data-year]').forEach(el => {
        el.classList.remove('active');
      });
      link.classList.add('active');
    });
  }

  /**
   * Filter button handlers - by category
   */
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const filterValue = this.dataset.filter.toLowerCase();

      // Update active button state
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      // Get all items
      const allItems = getAllNewsItems();

      // Filter by category and year
      if (filterValue === 'all') {
        currentList = currentYear 
          ? allItems.filter(item => getItemYear(item) === currentYear)
          : allItems;
      } else {
        currentList = allItems.filter(item => {
          const category = (item.dataset.category || '').toLowerCase();
          const year = getItemYear(item);
          const yearMatch = currentYear ? year === currentYear : true;
          return category === filterValue && yearMatch;
        });
      }

      shownCount = 0;
      hideAllItems();
      showItemsCount(PAGE_SIZE);
      shownCount = PAGE_SIZE;

      updateLoadMoreButton();

      // Scroll to feed
      const feed = document.querySelector('.news-feed');
      if (feed) {
        feed.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /**
   * Initialize on DOM ready
   */
  initializeNewsDisplay();
});