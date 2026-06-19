const API_LIST = '/api/valve/list';
const API_CREATE = '/api/valve/create';
const annoncesGrid = document.getElementById('annonces-grid');
const annonceTemplate = document.getElementById('annonce-template');
const searchInput = document.getElementById('search-input');
const filterChips = document.querySelectorAll('.filter-chip');
const heroTotal = document.getElementById('stat-total');
const heroUrgent = document.getElementById('stat-urgent');
const heroConvocations = document.getElementById('stat-convocations');
const modalError = document.getElementById('modal-error');

let annonces = [];
let currentCategory = 'all';
let currentSearch = '';

const categoryMap = {
  urgent: { label: 'URGENT', icon: '🚨', color: '#ef4444', bg: 'rgba(239,68,68,0.12)', priority: '⚠' },
  convocation: { label: 'CONVOCATION', icon: '📅', color: '#d97706', bg: 'rgba(245,158,11,0.12)' },
  information: { label: 'INFORMATION', icon: '📢', color: '#16a34a', bg: 'rgba(34,197,94,0.12)' },
  academique: { label: 'ACADÉMIQUE', icon: '🎓', color: '#6366f1', bg: 'rgba(99,102,241,0.12)' },
};

function getCategoryStyle(category) {
  return categoryMap[category] || categoryMap.information;
}

function getInitials(name) {
  if (!name) return '??';
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(word => word[0].toUpperCase())
    .join('');
}

function formatDate(value) {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function renderAnnonces() {
  const filtered = annonces.filter(item => {
    const matchesCategory = currentCategory === 'all' || item.category === currentCategory;
    const sourceText = `${item.title} ${item.content} ${item.author_name} ${item.category} ${item.target_role}`.toLowerCase();
    const matchesSearch = currentSearch === '' || sourceText.includes(currentSearch);
    return matchesCategory && matchesSearch;
  });

  annoncesGrid.innerHTML = '';

  if (filtered.length === 0) {
    annoncesGrid.innerHTML = '<div class="empty-state">Aucune annonce ne correspond à votre recherche.</div>';
    updateStats();
    return;
  }

  filtered.forEach(item => {
    const entry = annonceTemplate.content.cloneNode(true);
    const iconNode = entry.querySelector('.ac-cat-icon');
    const labelNode = entry.querySelector('.ac-cat-label');
    const titleNode = entry.querySelector('.ac-title');
    const priorityNode = entry.querySelector('.ac-priority');
    const textNode = entry.querySelector('.ac-text');
    const authorNode = entry.querySelector('.ac-author-name');
    const authorAva = entry.querySelector('.ac-author-ava');
    const dateNode = entry.querySelector('.ac-date');
    const data = getCategoryStyle(item.category);

    iconNode.textContent = data.icon;
    iconNode.style.background = data.bg;
    labelNode.textContent = data.label;
    labelNode.style.color = data.color;
    titleNode.textContent = item.title;
    textNode.textContent = item.content;
    authorNode.textContent = item.author_name || 'Anonyme';
    authorAva.textContent = getInitials(item.author_name || item.target_role || 'Annonce');
    dateNode.textContent = formatDate(item.created_at);

    if (item.category === 'urgent') {
      priorityNode.textContent = data.priority;
      priorityNode.style.background = 'rgba(239,68,68,0.1)';
      priorityNode.style.color = data.color;
    } else {
      priorityNode.remove();
    }

    annoncesGrid.appendChild(entry);
  });

  updateStats();
}

function updateStats() {
  const total = annonces.length;
  const urgent = annonces.filter(item => item.category === 'urgent').length;
  const convocations = annonces.filter(item => item.category === 'convocation').length;

  if (heroTotal) heroTotal.textContent = String(total);
  if (heroUrgent) heroUrgent.textContent = String(urgent);
  if (heroConvocations) heroConvocations.textContent = String(convocations);
}

function setActiveFilter(category) {
  currentCategory = category;
  filterChips.forEach(chip => {
    chip.classList.toggle('active', chip.dataset.category === category);
  });
  renderAnnonces();
}

function openModal() {
  document.getElementById('modal').classList.add('open');
  if (modalError) modalError.style.display = 'none';
}

function closeModal() {
  document.getElementById('modal').classList.remove('open');
}

function closeModalOutside(e) {
  if (e.target === document.getElementById('modal')) closeModal();
}

async function loadAnnonces() {
  try {
    const response = await fetch(API_LIST);
    if (!response.ok) throw new Error('Impossible de charger les annonces');
    annonces = await response.json();
    renderAnnonces();
  } catch (error) {
    annoncesGrid.innerHTML = '<div class="empty-state">Erreur de chargement des annonces. Rechargez la page.</div>';
    console.error(error);
  }
}

async function publishAnnonce() {
  const title = document.getElementById('annonce-title').value.trim();
  const category = document.getElementById('annonce-category').value;
  const targetRole = document.getElementById('annonce-target-role').value;
  const content = document.getElementById('annonce-content').value.trim();

  if (!title || !category || !content) {
    if (modalError) {
      modalError.textContent = 'Veuillez remplir le titre, la catégorie et le contenu.';
      modalError.style.display = 'block';
    }
    return;
  }

  const formData = new FormData();
  formData.append('titre', title);
  formData.append('categorie', category);
  formData.append('target_role', targetRole);
  formData.append('contenu', content);

  try {
    const response = await fetch(API_CREATE, {
      method: 'POST',
      body: formData,
    });

    const json = await response.json();
    if (!response.ok || !json.success) {
      throw new Error(json.error || 'Impossible de publier l’annonce.');
    }

    closeModal();
    document.getElementById('annonce-title').value = '';
    document.getElementById('annonce-category').value = '';
    document.getElementById('annonce-target-role').value = 'all';
    document.getElementById('annonce-content').value = '';
    if (modalError) modalError.style.display = 'none';

    await loadAnnonces();
    alert('Annonce publiée avec succès sur le Valve !');
  } catch (error) {
    if (modalError) {
      modalError.textContent = error.message;
      modalError.style.display = 'block';
    }
    console.error(error);
  }
}

filterChips.forEach(chip => {
  chip.addEventListener('click', () => setActiveFilter(chip.dataset.category));
});

if (searchInput) {
  searchInput.addEventListener('input', () => {
    currentSearch = searchInput.value.trim().toLowerCase();
    renderAnnonces();
  });
}

loadAnnonces();
