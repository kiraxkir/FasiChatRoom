function setRole(btn) {
  document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#login-form');
  const errorBox = document.querySelector('#login-error');

  if (!form) {
    return;
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    errorBox.style.display = 'none';
    errorBox.textContent = '';

    const formData = new FormData(form);

    try {
      const response = await fetch('/api/login', {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();
      if (!response.ok || !data.success) {
        errorBox.textContent = data.error || 'Identifiants invalides.';
        errorBox.style.display = 'block';
        return;
      }

      const redirectMap = {
        etudiant: 'dashboard_etudiant.html',
        enseignant: 'dashboard_enseignant.html',
        assistant: 'dashboard_enseignant.html',
        doyen: 'dashboard_admin.html',
        vicedoyen: 'dashboard_vicedoyen.html',
        apparitaire: 'dashboard_apparitaire.html',
      };

      const redirectUrl = redirectMap[data.role] || 'dashboard_etudiant.html';
      window.location.href = redirectUrl;
    } catch (error) {
      errorBox.textContent = 'Erreur de connexion. Veuillez réessayer.';
      errorBox.style.display = 'block';
    }
  });
});

// Checkbox toggle
document.querySelectorAll('.checkbox-wrap').forEach(wrap => {
  wrap.addEventListener('click', () => {
    const cb = wrap.querySelector('input');
    cb.checked = !cb.checked;
  });
});