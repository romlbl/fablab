let exportModalId = 0;

class ExportModal extends HTMLElement {
  connectedCallback() {
    if (this.dataset.ready) return;
    this.dataset.ready = 'true';
    const fields = [
      ['Machines', 'Toutes les machines', 'Bambulab X2d|MAKERBOT|TROTEC|GOSCAN'],
      ['Projets', 'Tous les projets', 'Résin Less|Autre projet|Projet sans description'],
      ['Clients', 'Tous les clients', 'Résin Less|Mairie Douai|Fablab'],
      ['Opérateurs', 'Tous les opérateurs', 'Jorge|Hadrien|Romain|Sylvain|Fabrice'],
      ['Consommables', 'Tous les consommables', 'PLA noir|PLA orange|PETG transparent'],
      ['Catégories', 'Tous les types', 'CAO|MO|Impression 3D FDM|Découpe laser'],
    ];
    this.innerHTML = `
      <div
        class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overscroll-contain bg-slate-950/50 p-4 backdrop-blur-[3px] sm:p-6"
        role="dialog"
        aria-modal="true"
      >
        <div class="my-auto w-full max-w-xl space-y-5 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-modal sm:space-y-6 sm:p-8">
          <div class="flex items-start justify-between gap-3">
            <h2 class="text-xl font-bold tracking-tight text-slate-900" data-title>
              Exporter les jobs
            </h2>
            <button
              type="button"
              aria-label="Fermer"
              data-close
              class="-mr-2 -mt-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-slate-400 sm:m-0 sm:h-auto sm:w-auto sm:p-1.5 hover:bg-slate-100 hover:text-slate-700"
            >
              <span class="text-2xl font-light leading-none">×</span>
            </button>
          </div>

          <form class="space-y-5">
            <div>
              <span class="mb-2.5 block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Filtres d'export
              </span>
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                ${fields.map(([label, placeholder, options], index) => `
                  <div class="relative space-y-1.5" data-select data-options="${options}">
                    <label class="block text-xs font-semibold text-slate-700" for="export-${index}">
                      ${label}
                    </label>
                    <div class="flex min-h-[44px] w-full flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1.5 sm:min-h-[38px]">
                      <input
                        id="export-${index}"
                        placeholder="${placeholder}"
                        aria-label="Rechercher ${label.toLocaleLowerCase()}"
                        autocomplete="off"
                        data-input
                        class="min-w-[100px] flex-1 border-0 bg-transparent p-0 text-base font-medium text-slate-700 sm:text-xs outline-none focus:ring-0 placeholder:text-slate-400"
                      >
                      <div class="flex flex-wrap items-center gap-1.5" data-selected></div>
                    </div>
                    <div
                      class="absolute left-0 right-0 top-full z-50 mt-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card"
                      data-suggestions
                    ></div>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="space-y-2.5 pt-1">
              <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Période
              </span>
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <label class="space-y-1.5 text-xs font-semibold text-slate-700">
                  Plage prédéfinie
                  <select name="period" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-base sm:py-2 sm:text-xs">
                    <option>Toutes les dates</option>
                    <option>Aujourd'hui</option>
                    <option>Cette semaine</option>
                    <option>Ce mois-ci</option>
                    <option>Période personnalisée</option>
                  </select>
                </label>
                <label class="space-y-1.5 text-xs font-semibold text-slate-700">
                  Du
                  <input name="dateStart" type="date" class="mt-1 min-w-0 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-base sm:py-2 sm:text-xs">
                </label>
                <label class="space-y-1.5 text-xs font-semibold text-slate-700">
                  Au
                  <input name="dateEnd" type="date" class="mt-1 min-w-0 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-base sm:py-2 sm:text-xs">
                </label>
              </div>
            </div>

            <div class="space-y-2.5 pt-1">
              <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Format du fichier
              </span>
              <div class="grid grid-cols-2 gap-3">
                <label class="flex cursor-pointer items-center gap-2 rounded-2xl border-2 border-[#10203E] bg-[#10203E]/5 p-3">
                  <input checked name="format" type="radio" value="xlsx">
                  <span class="text-xs font-bold">Excel (.xlsx)</span>
                </label>
                <label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-slate-200 p-3">
                  <input name="format" type="radio" value="csv">
                  <span class="text-xs font-semibold">CSV (.csv)</span>
                </label>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
              <button
                type="button"
                data-close
                class="flex-1 rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 sm:flex-none sm:py-2.5"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="flex-1 rounded-xl bg-[#10203E] px-6 py-3 text-sm font-bold text-white hover:bg-[#004e60] sm:flex-none sm:py-2.5"
              >
                Exporter
              </button>
            </div>
          </form>
        </div>
      </div>
    `;
    this.modal = this.firstElementChild;
    const titleId = `export-modal-title-${++exportModalId}`;
    this.querySelector('[data-title]').id = titleId;
    this.modal.setAttribute('aria-labelledby', titleId);
    this.querySelectorAll('[data-close]').forEach((button) => button.addEventListener('click', () => this.close()));
    this.modal.addEventListener('click', (event) => { if (event.target === this.modal) this.close(); });
    this.handleDocumentClick = (event) => {
      this.querySelectorAll('[data-suggestions]').forEach((suggestions) => {
        if (!suggestions.parentElement.contains(event.target)) {
          suggestions.classList.add('hidden');
        }
      });
    };
    document.addEventListener('click', this.handleDocumentClick);
    this.querySelector('form').addEventListener('submit', (event) => {
      event.preventDefault();
      this.dispatchEvent(new CustomEvent('export-submit', { bubbles: true, detail: this.value }));
    });
    this.querySelectorAll('[data-select]').forEach((select) => this.setupSelect(select));
  }

  setupSelect(select) {
    const input = select.querySelector('[data-input]');
    const selectedContainer = select.querySelector('[data-selected]');
    const suggestions = select.querySelector('[data-suggestions]');
    const selected = new Set();
    const render = () => {
      selectedContainer.replaceChildren();
      selected.forEach((value) => {
        const pill = document.createElement('span');
        pill.className = 'inline-flex items-center gap-1 rounded-full bg-[#10203E]/10 px-2.5 py-1 text-xs font-semibold text-[#10203E]';
        pill.textContent = value;
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = '-m-1 p-1 leading-none';
        remove.textContent = '×';
        remove.setAttribute('aria-label', `Retirer ${value}`);
        remove.addEventListener('click', () => { selected.delete(value); render(); });
        pill.append(remove);
        selectedContainer.append(pill);
      });
      const query = input.value.trim().toLocaleLowerCase();
      suggestions.replaceChildren();
      select.dataset.options.split('|').filter((option) => !selected.has(option) && option.toLocaleLowerCase().includes(query)).forEach((option) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'block w-full px-3.5 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50 sm:py-2 hover:text-[#10203E]';
        button.textContent = option;
        button.addEventListener('click', () => { selected.add(option); input.value = ''; render(); input.focus(); });
        suggestions.append(button);
      });
      suggestions.classList.toggle('hidden', !suggestions.children.length);
    };
    input.addEventListener('focus', render);
    input.addEventListener('input', render);
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' && suggestions.firstElementChild) { event.preventDefault(); suggestions.firstElementChild.click(); }
      if (event.key === 'Backspace' && !input.value && selected.size) { selected.delete(Array.from(selected).pop()); render(); }
      if (event.key === 'Escape') suggestions.classList.add('hidden');
    });
  }

  get value() {
    return Object.fromEntries(new FormData(this.querySelector('form')).entries());
  }

  open() {
    this.hideSuggestions();
    this.modal.classList.remove('hidden');
    this.modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }

  close() {
    this.hideSuggestions();
    this.modal.classList.add('hidden');
    this.modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  hideSuggestions() {
    this.querySelectorAll('[data-suggestions]').forEach((suggestions) => {
      suggestions.classList.add('hidden');
    });
  }
}

customElements.define('export-modal', ExportModal);