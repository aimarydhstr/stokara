(() => {
  const reopenedModal = document.querySelector('[data-reopen-modal]');
  if (reopenedModal && window.bootstrap) bootstrap.Modal.getOrCreateInstance(reopenedModal).show();
  const toggle = document.querySelector('[data-sidebar-toggle]');
  const closeSidebar = () => { document.body.classList.remove('sidebar-open'); toggle?.setAttribute('aria-expanded', 'false'); };
  toggle?.addEventListener('click', () => { const opened = document.body.classList.toggle('sidebar-open'); toggle.setAttribute('aria-expanded', String(opened)); });
  document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeSidebar();
    if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
      const search = document.querySelector('.global-search input');
      if (search) { event.preventDefault(); search.focus(); }
    }
  });
  document.querySelectorAll('[data-auto-submit]').forEach(select => select.addEventListener('change', () => select.form.requestSubmit()));
  document.querySelector('[data-password-toggle]')?.addEventListener('click', event => {
    const button = event.currentTarget;
    const input = document.querySelector('#password');
    const show = input.type === 'password'; input.type = show ? 'text' : 'password';
    button.setAttribute('aria-pressed', String(show)); button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
  });
  document.querySelectorAll('form').forEach(form => form.addEventListener('submit', event => {
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) { event.preventDefault(); return; }
    if (!form.checkValidity()) return;
    const button = form.querySelector('[data-loading-text]');
    if (button) { button.disabled = true; button.textContent = button.dataset.loadingText; }
  }));
  window.addEventListener('pageshow', () => document.querySelectorAll('[data-loading-text]').forEach(button => { if (button.disabled) { button.disabled = false; button.textContent = button.dataset.originalText || 'Simpan'; } }));
  document.querySelectorAll('[data-loading-text]').forEach(button => button.dataset.originalText = button.textContent.trim());
  const form = document.querySelector('#movementForm');
  if (form) {
    const select = form.querySelector('#item_id'); const quantity = form.querySelector('#quantity');
    const put = (selector, text) => { const el = document.querySelector(selector); if (el) el.textContent = text; };
    const render = () => {
      const option = select.selectedOptions[0]; const selected = Boolean(select.value);
      const stock = Number(option?.dataset.stock || 0); const amount = Number(quantity.value || 0); const unit = option?.dataset.unit || 'Unit';
      const outgoing = form.dataset.movementType === 'out'; const after = outgoing ? stock - amount : stock + amount;
      const format = value => `${new Intl.NumberFormat('id-ID').format(value)} ${unit}`;
      put('[data-preview-name]', selected ? option.dataset.name : 'Belum ada barang dipilih');
      put('[data-preview-sku]', selected ? option.dataset.sku : 'Pilih barang untuk melihat ringkasan.');
      put('[data-unit-label]', unit); put('[data-preview-before]', selected ? format(stock) : '—');
      put('[data-preview-quantity]', selected ? `${outgoing ? '−' : '+'}${format(amount)}` : '—');
      put('[data-preview-after]', selected ? format(after) : '—');
      const invalid = selected && outgoing && amount > stock;
      document.querySelector('[data-preview-error]').hidden = !invalid;
      document.querySelector('[data-preview-after]').classList.toggle('text-danger', invalid);
      quantity.setAttribute('max', outgoing && selected ? stock : 1000000000);
      quantity.setCustomValidity(invalid ? 'Jumlah keluar melebihi stok tersedia.' : '');
    };
    select.addEventListener('change', render); quantity.addEventListener('input', render); render();
  }
})();