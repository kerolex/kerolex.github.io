/* publications.js
 * Fetches publications.json and renders the publications list, wiring up
 * abstract/bibtex toggles, Altmetric badges, category/year filters and
 * "Load more" pagination.
 *
 * To add, edit, or remove a publication, only publications.json needs to
 * change -- this file and publications.html do not need to be touched.
 */

const PAGE_SIZE = 10;

let allPublications = [];   // raw data from JSON
let currentCategoryFilter = 'all';
let currentYearFilter = 'all';
let currentList = [];       // filtered + sorted publication objects for current view
let shownCount = 0;

const container = document.getElementById('publications-container');
const loadMoreBtn = document.getElementById('loadMoreBtn');

/* ---------- Rendering ---------- */

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function renderLinks(pub, uid) {
  return pub.links.map(link => {
    if (link.action === 'abstract') {
      return `<a href="#" onclick="toggleAbstract(event, 'abstract_${uid}')">${escapeHtml(link.label)}</a>`;
    }
    if (link.action === 'bibtex') {
      return `<a href="#" onclick="toggleBib(event, 'bib_${uid}')">${escapeHtml(link.label)}</a>`;
    }
    return `<a href="${escapeHtml(link.url)}" target="_blank">${escapeHtml(link.label)}</a>`;
  }).join('\n');
}

function renderPublication(pub, index) {
  const uid = pub.id || `pub${index}`;
  const hasAbstract = !!pub.abstract;
  const hasBib = !!pub.bibtex;
  const hasBadge = !!(pub.doi || pub.arxivId);

  const article = document.createElement('article');
  article.className = 'publication-item';
  article.setAttribute('data-filter', pub.filter || '');
  article.setAttribute('data-year', pub.year || '');

  article.innerHTML = `
    <img src="${escapeHtml(pub.image || '')}" alt="${escapeHtml(pub.imageAlt || '')}" class="publication-image">
    <div class="publication-content">
      <div class="publication-title">${pub.title || ''}</div>
      <div class="publication-venue">${pub.venue || ''}</div>
      <div class="publication-authors">${pub.authors || ''}</div>
      <div class="publication-links">${renderLinks(pub, uid)}</div>
      ${hasAbstract ? `<div id="abstract_${uid}" class="abstract">${pub.abstract}</div>` : ''}
      ${hasBib ? `<div id="bib_${uid}" class="bib">${escapeHtml(pub.bibtex)}</div>` : ''}
      ${pub.thesisInfo ? `<div class="thesis-info">${pub.thesisInfo}</div>` : ''}
      ${hasBadge ? `
      <div class="publication-badge">
        <div data-badge-type="medium-donut"
             ${pub.doi ? `data-doi="${escapeHtml(pub.doi)}"` : ''}
             ${pub.arxivId ? `data-arxiv-id="${escapeHtml(pub.arxivId)}"` : ''}
             data-condensed="true" data-hide-no-mentions="true" data-hide-less-than="0"
             data-badge-popover="left" class="altmetric-embed"></div>
      </div>` : ''}
    </div>
  `;

  return article;
}

function renderAllPublications() {
  container.innerHTML = '';
  allPublications.forEach((pub, index) => {
    container.appendChild(renderPublication(pub, index));
  });

  // Re-render Altmetric badges now that they exist in the DOM
  if (window._altmetric_embed_init) {
    window._altmetric_embed_init();
  } else if (window.Altmetric) {
    // fallback: some Altmetric embed versions auto re-scan on load
  }
}

/* ---------- Toggle abstract / bibtex ---------- */

function toggleAbstract(e, id) {
  e.preventDefault();
  const elem = document.getElementById(id);
  if (elem) elem.classList.toggle('visible');
}

function toggleBib(e, id) {
  e.preventDefault();
  const elem = document.getElementById(id);
  if (elem) elem.classList.toggle('visible');
}

/* ---------- Filtering & pagination ---------- */

function attachCategoryFilterListeners() {
  const categorySection = document.querySelector('.filter-section');
  if (!categorySection) return;
  categorySection.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const filter = this.getAttribute('data-filter');
      categorySection.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      currentCategoryFilter = filter;
      updatePublicationsList();
    });
  });
}

function attachYearFilterListeners() {
  const yearSection = document.getElementById('year-filters');
  if (!yearSection) return;
  yearSection.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const filter = this.getAttribute('data-filter');
      yearSection.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      currentYearFilter = filter;
      updatePublicationsList();
    });
  });
}

function hideAllPublications() {
  document.querySelectorAll('.publication-item').forEach(item => {
    item.classList.add('hidden');
    item.style.order = '0';
  });
}

function showPublicationsCount(count) {
  let shown = 0;
  for (let i = 0; i < currentList.length && shown < count; i++) {
    currentList[i].classList.remove('hidden');
    currentList[i].style.order = i.toString();
    shown++;
  }
}

function updateLoadMoreButton() {
  if (!loadMoreBtn) return;

  const totalItems = currentList.length;
  const visibleItems = currentList.filter(item => !item.classList.contains('hidden')).length;

  if (totalItems > visibleItems) {
    loadMoreBtn.style.display = 'block';
    const remaining = totalItems - visibleItems;
    loadMoreBtn.textContent = `Load More Publications (${remaining} remaining)`;
  } else {
    loadMoreBtn.style.display = 'none';
  }
}

function updatePublicationsList() {
  const allItems = document.querySelectorAll('.publication-item');
  currentList = [];

  allItems.forEach(item => {
    const itemCategory = item.getAttribute('data-filter');
    const itemYear = item.getAttribute('data-year');

    let matches = true;
    if (currentCategoryFilter !== 'all' && itemCategory !== currentCategoryFilter) matches = false;
    if (currentYearFilter !== 'all' && itemYear !== currentYearFilter) matches = false;

    if (matches) currentList.push(item);
  });

  // Sort by year, descending
  currentList.sort((a, b) => {
    const yearA = parseInt(a.getAttribute('data-year')) || 0;
    const yearB = parseInt(b.getAttribute('data-year')) || 0;
    return yearB - yearA;
  });

  shownCount = 0;
  hideAllPublications();
  showPublicationsCount(PAGE_SIZE);
  shownCount = PAGE_SIZE;
  updateLoadMoreButton();
}

function initLoadMoreButton() {
  if (!loadMoreBtn) return;
  loadMoreBtn.addEventListener('click', function () {
    const nextCount = shownCount + PAGE_SIZE;
    showPublicationsCount(nextCount);
    shownCount = nextCount;
    updateLoadMoreButton();

    if (currentList.length > shownCount - PAGE_SIZE) {
      const nextItem = currentList[shownCount - PAGE_SIZE];
      if (nextItem) nextItem.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
}

function initYearFilters() {
  const yearFiltersContainer = document.getElementById('year-filters');
  if (!yearFiltersContainer || yearFiltersContainer.children.length > 0) return;

  const years = new Set();
  document.querySelectorAll('.publication-item').forEach(item => {
    const year = item.getAttribute('data-year');
    if (year) years.add(year);
  });

  const sortedYears = Array.from(years).sort((a, b) => b - a);

  const allBtn = document.createElement('button');
  allBtn.className = 'filter-btn active';
  allBtn.setAttribute('data-filter', 'all');
  allBtn.textContent = 'All Years';
  yearFiltersContainer.appendChild(allBtn);

  sortedYears.forEach(year => {
    const btn = document.createElement('button');
    btn.className = 'filter-btn';
    btn.setAttribute('data-filter', year);
    btn.textContent = year;
    yearFiltersContainer.appendChild(btn);
  });

  attachYearFilterListeners();
}

/* ---------- Init ---------- */

async function loadPublications() {
  try {
    const response = await fetch('publications.json');
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    allPublications = await response.json();
  } catch (err) {
    console.error('Failed to load publications.json:', err);
    if (container) {
      container.innerHTML = '<p>Sorry, publications could not be loaded right now. Please try again later.</p>';
    }
    return;
  }

  renderAllPublications();
  initYearFilters();
  attachCategoryFilterListeners();
  attachYearFilterListeners();
  initLoadMoreButton();
  updatePublicationsList();
}

const currentYearEl = document.getElementById('currentYear');
if (currentYearEl) currentYearEl.textContent = new Date().getFullYear();

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadPublications);
} else {
  loadPublications();
}