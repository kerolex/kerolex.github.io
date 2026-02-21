/**
 * News Filter Module
 * Handles filtering, pagination, and year grouping for news items
 */

document.addEventListener('DOMContentLoaded', function() {
  const PAGE_SIZE = 10;
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  const filterBtns = Array.from(document.querySelectorAll('.filter-btn[data-filter]'));
  const archiveEl = document.getElementById('newsArchive');

  // State management
  let currentFilter = 'all';
  let currentPage = 0;
  let currentList = [];

  /**
   * Get all news items from DOM
   */
  const getAllNewsItems = () => Array.from(document.querySelectorAll('.news-item'));

  /**
   * Extract year from news item
   */
  const getItemYear = (item) => {
    // Try data-year attribute first
    if (item.dataset.year) return item.dataset.year;
    
    // Try datetime attribute on news-date element
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
      // Remove existing badge first
      const existingBadge = item.querySelector('.news-badge');
      if (existingBadge) existingBadge.remove();

      // Add new badge if recent
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
   * Group news items by year into <details> elements
   */
  const groupNewsByYear = () => {
    const feed = document.querySelector('.news-feed');
    if (!feed) return;

    const items = getAllNewsItems();
    if (items.length === 0) return;

    // Group items by year
    const yearMap = items.reduce((acc, item) => {
      const year = getItemYear(item);
      if (!acc[year]) acc[year] = [];
      acc[year].push(item);
      return acc;
    }, {});

    // Sort years in descending order
    const sortedYears = Object.keys(yearMap).sort((a, b) => b - a);

    // Clear feed and rebuild with grouped structure
    feed.innerHTML = '';

    sortedYears.forEach((year, index) => {
      const details = document.createElement('details');
      details.className = 'year-group';
      details.open = index === 0; // Open first year by default

      const summary = document.createElement('summary');
      summary.textContent = `${year} (${yearMap[year].length})`;
      details.appendChild(summary);

      // Add items to details element
      yearMap[year].forEach(item => {
        details.appendChild(item);
      });

      feed.appendChild(details);
    });

    addNewBadges();
  };

  /**
   * Hide all news items
   */
  const hideAllItems = () => {
    getAllNewsItems().forEach(item => item.classList.add('hidden'));
  };

  /**
   * Show items from list with pagination
   */
  const showItemsPage = (list, startIndex, count) => {
    const itemsToShow = list.slice(startIndex, startIndex + count);
    itemsToShow.forEach(item => item.classList.remove('hidden'));
    return itemsToShow.length;
  };

  /**
   * Update load more button visibility
   */
  const updateLoadMoreButton = () => {
    if (!loadMoreBtn) return;
    
    const totalItems = currentList.length;
    const shownItems = getAllNewsItems().filter(item => !item.classList.contains('hidden')).length;
    
    loadMoreBtn.style.display = totalItems > shownItems ? 'block' : 'none';
    
    // Update button label for screen readers
    if (totalItems > shownItems) {
      const remaining = totalItems - shownItems;
      loadMoreBtn.setAttribute('aria-label', `Load ${remaining} more news items`);
    }
  };

  /**
   * Initialize: Group by year, setup filters, and show initial items
   */
  const initializeNewsDisplay = () => {
    groupNewsByYear();
    
    // Get ALL items (unfiltered) and show last 10
    const allItems = getAllNewsItems();
    hideAllItems();
    
    // Show last 10 items of ALL news (not filtered)
    const startIndex = Math.max(0, allItems.length - PAGE_SIZE);
    showItemsPage(allItems, startIndex, PAGE_SIZE);
    
    // Set currentList to all items for load more functionality
    currentList = allItems;
    currentPage = Math.ceil((startIndex + PAGE_SIZE) / PAGE_SIZE);

    updateLoadMoreButton();
  };

  /**
   * Load More button handler - loads older items (backwards pagination)
   */
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      // Calculate which items are currently visible
      const visibleItems = getAllNewsItems().filter(i => !i.classList.contains('hidden'));
      const firstVisibleIndex = currentList.indexOf(visibleItems[0]);
      
      // Load PAGE_SIZE items before the first visible item
      const startIndex = Math.max(0, firstVisibleIndex - PAGE_SIZE);
      const endIndex = firstVisibleIndex;
      
      // Show items in the range
      currentList.slice(startIndex, endIndex).forEach(item => {
        item.classList.remove('hidden');
      });

      updateLoadMoreButton();

      // Announce to screen readers
      if (startIndex === 0) {
        this.setAttribute('aria-label', 'All news loaded');
      } else {
        const remaining = startIndex;
        this.setAttribute('aria-label', `Load ${remaining} more older news items`);
      }
    });
  }

  /**
   * Build archive and add event listeners
   */
  if (archiveEl) {
    // Count items by year
    const yearCounts = {};
    getAllNewsItems().forEach(item => {
      const year = getItemYear(item);
      yearCounts[year] = (yearCounts[year] || 0) + 1;
    });

    // Create archive list
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

    archiveEl.innerHTML = '<h3>Archive by Year</h3>';
    archiveEl.appendChild(ul);

    /**
     * Archive link click handler
     */
    ul.addEventListener('click', (event) => {
      const link = event.target.closest('a');
      if (!link) return;

      event.preventDefault();
      const year = link.dataset.year;

      // Find the corresponding details element
      const detailsElements = document.querySelectorAll('.year-group');
      const targetDetails = Array.from(detailsElements).find(details => {
        const summary = details.querySelector('summary');
        return summary && summary.textContent.startsWith(year);
      });

      if (!targetDetails) return;

      // Close all, open target
      detailsElements.forEach(details => { details.open = false; });
      targetDetails.open = true;

      // Smooth scroll to details
      targetDetails.scrollIntoView({ behavior: 'smooth', block: 'start' });

      // Update filtered list and pagination - show first 10 items from that year
      currentList = Array.from(targetDetails.querySelectorAll('.news-item'));
      currentPage = 0;

      hideAllItems();
      
      // Show first 10 items from selected year
      showItemsPage(currentList, 0, PAGE_SIZE);
      currentPage = 1;

      updateLoadMoreButton();

      // Announce action to screen readers
      link.setAttribute('aria-pressed', 'true');
      setTimeout(() => link.removeAttribute('aria-pressed'), 500);
    });
  }

  /**
   * Filter button handlers
   */
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const filterValue = this.dataset.filter.toLowerCase();

      // Update active button state
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      // Get all items from DOM (respects current grouping)
      const allItems = getAllNewsItems();

      // Filter items
      if (filterValue === 'all') {
        currentList = allItems;
        // For "All" filter, show last 10 items (initial behavior)
        currentPage = 0;
        hideAllItems();
        const startIndex = Math.max(0, currentList.length - PAGE_SIZE);
        showItemsPage(currentList, startIndex, PAGE_SIZE);
        currentPage = Math.ceil((startIndex + PAGE_SIZE) / PAGE_SIZE);
      } else {
        currentList = allItems.filter(item => {
          const category = (item.dataset.category || '').toLowerCase();
          return category === filterValue;
        });
        // For specific filters, show first 10 items
        currentPage = 0;
        hideAllItems();
        showItemsPage(currentList, 0, PAGE_SIZE);
        currentPage = 1;
      }

      updateLoadMoreButton();

      // Scroll to feed and announce
      const feed = document.querySelector('.news-feed');
      if (feed) {
        feed.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      // Screen reader announcement
      const count = currentList.length;
      this.setAttribute('aria-pressed', 'true');
      setTimeout(() => this.removeAttribute('aria-pressed'), 300);
    });
  });

  /**
   * Initialize on DOM ready
   */
  initializeNewsDisplay();
});