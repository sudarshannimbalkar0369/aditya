const hero = document.getElementById('hero');
const cards = Array.from(document.querySelectorAll('.movie-card'));
const modal = document.getElementById('teaserModal');
const teaserFrame = document.getElementById('teaserFrame');
const closeModalBtn = document.getElementById('closeModal');
const filterButtons = document.querySelectorAll('#filters button');
const searchInput = document.getElementById('searchInput');

if (MOVIES.length > 0 && hero) {
  hero.style.backgroundImage = `url(${MOVIES[0].poster_url})`;
}

const visibleState = new Map(cards.map((card) => [card, true]));

function renderVisibility() {
  cards.forEach((card) => {
    card.style.display = visibleState.get(card) ? '' : 'none';
  });
}

function updateDynamicBackground() {
  const visibleCards = cards.filter((card) => visibleState.get(card));
  if (visibleCards.length === 0 || !hero) return;

  let nearest = visibleCards[0];
  let nearestDistance = Number.POSITIVE_INFINITY;

  visibleCards.forEach((card) => {
    const rect = card.getBoundingClientRect();
    const centerDelta = Math.abs(rect.top + rect.height / 2 - window.innerHeight / 2);
    if (centerDelta < nearestDistance) {
      nearestDistance = centerDelta;
      nearest = card;
    }
  });

  if (nearest?.dataset.bg) {
    hero.style.backgroundImage = `url(${nearest.dataset.bg})`;
const movieGrid = document.getElementById('movieGrid');

if (MOVIES.length > 0) {
  hero.style.backgroundImage = `url(${MOVIES[0].poster_url})`;
}

function getNearestCardByViewport() {
  let nearest = cards[0];
  let nearestDistance = Number.POSITIVE_INFINITY;
  cards.forEach((card) => {
    const rect = card.getBoundingClientRect();
    const center = Math.abs(rect.top + rect.height / 2 - window.innerHeight / 2);
    if (center < nearestDistance) {
      nearestDistance = center;
      nearest = card;
    }
  });
  return nearest;
}

function updateDynamicBackground() {
  const card = getNearestCardByViewport();
  if (!card) return;
  const bg = card.dataset.bg;
  if (bg) {
    hero.style.backgroundImage = `url(${bg})`;

  }
}

window.addEventListener('scroll', updateDynamicBackground);

cards.forEach((card, idx) => {
  card.style.animationDelay = `${idx * 35}ms`;
cards.forEach((card) => {

  const teaser = card.dataset.teaser;
  const preview = card.querySelector('.preview-video');

  if (preview) {
    card.addEventListener('mouseenter', () => preview.play().catch(() => null));
    card.addEventListener('mouseleave', () => {
      preview.pause();
      preview.currentTime = 0;
    });
  }

  card.addEventListener('click', (e) => {
    if (e.target.closest('.watchlist-btn')) return;
    if (!modal || !teaserFrame) return;
    teaserFrame.src = teaser + (teaser.includes('?') ? '&' : '?') + 'autoplay=1';
    modal.classList.remove('hidden');
  });
});

closeModalBtn?.addEventListener('click', () => {
=======
closeModalBtn.addEventListener('click', () => {

  modal.classList.add('hidden');
  teaserFrame.src = '';
});

modal?.addEventListener('click', (event) => {
modal.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.classList.add('hidden');
    teaserFrame.src = '';
  }
});

let selectedCategory = 'All';

function applyFilters() {
  const query = (searchInput?.value || '').trim().toLowerCase();

  cards.forEach((card) => {
    const categoryMatch = selectedCategory === 'All' || card.dataset.category === selectedCategory;
    const titleMatch = card.dataset.title.includes(query);
    visibleState.set(card, categoryMatch && titleMatch);
  });

  renderVisibility();
  updateDynamicBackground();
}

filterButtons.forEach((btn) => {
  btn.addEventListener('click', () => {
    filterButtons.forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    selectedCategory = btn.dataset.category;
    applyFilters();
  });
});

searchInput?.addEventListener('input', applyFilters);

document.querySelectorAll('.watchlist-btn').forEach((btn) => {
  btn.addEventListener('click', async (e) => {
    e.stopPropagation();

    if (!IS_LOGGED_IN) {
      window.location.href = 'login.php';
      return;
    }

    const category = btn.dataset.category;

    cards.forEach((card) => {
      const match = category === 'All' || card.dataset.category === category;
      card.style.display = match ? '' : 'none';
    });
  });
});

document.querySelectorAll('.watchlist-btn').forEach((btn) => {
  btn.addEventListener('click', async (e) => {
    e.stopPropagation();
    const movieId = btn.dataset.id;
    const res = await fetch('toggle_watchlist.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `movie_id=${encodeURIComponent(movieId)}`,
    });

    const data = await res.json();
    if (data.status === 'added') {

      btn.textContent = 'Saved';
      btn.textContent = 'In Watchlist';
      btn.classList.add('added');
    } else if (data.status === 'removed') {
      btn.textContent = '+ Watchlist';
      btn.classList.remove('added');
    }
  });
});

document.getElementById('scrollLeft')?.addEventListener('click', () => {
  movieGrid.scrollBy({ left: -400, behavior: 'smooth' });
});

document.getElementById('scrollRight')?.addEventListener('click', () => {
  movieGrid.scrollBy({ left: 400, behavior: 'smooth' });
});
