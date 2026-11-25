const jobModalTemplate = `
  <div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overscroll-contain bg-slate-950/50 p-4 backdrop-blur-[3px] sm:p-6" role="dialog" aria-modal="true">
    <div class="my-auto flex max-h-[calc(100dvh-2rem)] w-full max-w-2xl flex-col overflow-hidden sm:max-h-[calc(100dvh-3rem)] rounded-3xl border border-slate-200/90 bg-white shadow-modal">
      <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4 sm:px-8 sm:py-5">
        <h2 class="text-lg font-bold tracking-tight text-slate-900" data-title>Ajouter un job</h2>
        <button type="button" aria-label="Fermer la modale" data-close class="-mr-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 sm:mr-0 sm:h-9 sm:w-9">
          <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
      </div>
      <form class="custom-scroll space-y-5 overflow-y-auto overscroll-contain p-5 font-body sm:space-y-6 sm:p-8">
        <div>
          <label for="" data-description-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Description du job</label>
          <input type="text" name="description" data-description class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all placeholder:text-slate-400 hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="relative" data-autocomplete data-options="Bambulab X2d|MAKERBOT|TROTEC|GOSCAN">
            <label for="" data-machine-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Machine</label>
            <input type="text" name="machine" autocomplete="off" data-machine class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            <div class="absolute left-0 right-0 top-full z-50 mt-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div>
          </div>
          <div class="relative" data-autocomplete data-options="Résin Less|Autre projet|Projet sans description">
            <label for="" data-project-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Projet</label>
            <input type="text" name="project" autocomplete="off" data-project class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            <div class="absolute left-0 right-0 top-full z-50 mt-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div>
          </div>
          <div class="relative" id="chp_client" data-autocomplete data-options="Résin Less|Mairie Douai|Fablab">
            <label for="" data-client-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Clients</label>
            <input type="text" name="client" autocomplete="off" data-client class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            <div class="absolute left-0 right-0 top-full z-50 mt-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div>
          </div>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
          <div id="champ_debut">
            <label for="" data-start-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Début</label>
            <input type="datetime-local" name="startDate" data-start-date class="min-w-0 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
          </div>
          <div>
            <span class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Durée</span>
            <div class="flex gap-2">
              <label class="sr-only" for="" data-duration-hours-label>Heures</label>
              <input type="number" name="durationHours" data-duration-hours min="0" step="1" placeholder="h" aria-label="Durée en heures" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
              <label class="sr-only" for="" data-duration-minutes-label>Minutes</label>
              <input type="number" name="durationMinutes" data-duration-minutes min="0" max="59" step="1" placeholder="min" aria-label="Durée en minutes" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            </div>
          </div>
          <div id="champ_fin">
            <label for="" data-end-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Fin</label>
            <input type="datetime-local" name="endDate" data-end-date aria-label="Date et heure de fin" class="min-w-0 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
          </div>
        </div>
        <div class="space-y-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:p-5">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 font-sans">Consommables &amp; Quantités</label>
          <div class="space-y-2.5" data-consumables></div>
          <button type="button" data-add-consumable class="inline-flex items-center space-x-1.5 rounded-xl border border-dashed border-[#10203E]/40 bg-white px-3 py-2.5 text-sm font-semibold text-[#10203E] sm:py-1.5 sm:text-xs transition-all hover:border-[#10203E] hover:bg-slate-100/80">
            <span class="material-symbols-outlined text-[16px]">add</span><span>Ajouter un consommable</span>
          </button>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="relative" data-autocomplete data-options="Hadrien|Romain|Jorge|Sylvain|Fabrice">
            <label for="" data-operator-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Opérateur</label>
            <input type="text" name="operator" autocomplete="off" data-operator class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            <div class="absolute bottom-full left-0 right-0 z-50 mb-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div>
          </div>
          <div class="relative" data-autocomplete data-options="MO|CAO|Impression 3D FDM|Découpe laser">
            <label for="" data-type-label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">Catégorie</label>
            <input type="text" name="type" autocomplete="off" data-type class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15">
            <div class="absolute bottom-full left-0 right-0 z-50 mb-1.5 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div>
          </div>
        </div>
      </form>
      <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/80 px-6 py-4 sm:px-8">
        <button type="button" data-close class="flex-1 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-200/60 hover:text-slate-900 sm:flex-none sm:py-2.5 sm:text-xs">Annuler</button>
        <button type="submit" class="flex-1 rounded-xl bg-[#10203E] px-5 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-[#004e60] active:scale-95 sm:flex-none sm:py-2.5 sm:text-xs">Créer le job</button>
      </div>
    </div>
  </div>`;

let jobModalId = 0;

class JobModal extends HTMLElement {
  connectedCallback() {
    if (this.dataset.ready) return;
    this.dataset.ready = 'true';
    this.innerHTML = jobModalTemplate;
    this.modal = this.firstElementChild;
    this.modal.setAttribute('aria-labelledby', `job-modal-title-${++jobModalId}`);
    const title = this.querySelector('[data-title]');
    title.id = `job-modal-title-${jobModalId}`;
    const form = this.querySelector('form');
    form.id = `job-modal-form-${jobModalId}`;
    this.querySelector('button[type="submit"]').setAttribute('form', form.id);
    this.querySelectorAll('label').forEach((label, index) => {
      const input = label.nextElementSibling?.matches('input')
        ? label.nextElementSibling
        : label.parentElement.querySelector('input');
      if (input) {
        input.id = `job-modal-${jobModalId}-${index}`;
        label.htmlFor = input.id;
      }
    });
    this.consumables = this.querySelector('[data-consumables]');
    this.addConsumable = this.querySelector('[data-add-consumable]');
    this.startDate = this.querySelector('[data-start-date]');
    this.durationHours = this.querySelector('[data-duration-hours]');
    this.durationMinutes = this.querySelector('[data-duration-minutes]');
    this.endDate = this.querySelector('[data-end-date]');
    this.startDate.value = this.toLocalDateTimeValue(new Date());
    [this.startDate, this.durationHours, this.durationMinutes].forEach((input) => {
      input.addEventListener('input', () => this.updateEndDate());
    });
    this.addConsumable.addEventListener('click', () => this.addRow());
    this.querySelectorAll('[data-close]').forEach((button) => button.addEventListener('click', () => this.close()));
    this.modal.addEventListener('click', (event) => {
      if (event.target === this.modal) this.close();
    });
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
      this.dispatchEvent(new CustomEvent('job-submit', { bubbles: true, detail: this.value }));
    });
    this.querySelectorAll('[data-autocomplete]').forEach((container) => this.setupAutocomplete(container));
  }

  setupAutocomplete(container) {
    const input = container.querySelector('input');
    const suggestions = container.querySelector('[data-suggestions]');
    const options = container.dataset.options.split('|');
    const updateUnit = () => {
      if (!input.matches('[data-consumable]')) return;
      this.updateConsumableUnit(input);
    };
    const render = () => {
      const query = input.value.trim().toLocaleLowerCase();
      suggestions.replaceChildren();
      options.filter((option) => option.toLocaleLowerCase().includes(query)).forEach((option) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'block w-full px-3.5 py-2.5 text-left text-sm text-slate-700 transition-colors sm:py-2 hover:bg-slate-50 hover:text-[#10203E]';
        button.textContent = option;
        button.addEventListener('click', () => {
          input.value = option;
          updateUnit();
          suggestions.classList.add('hidden');
        });
        suggestions.append(button);
      });
      suggestions.classList.toggle('hidden', !suggestions.children.length);
    };
    input.addEventListener('focus', render);
    input.addEventListener('input', () => {
      updateUnit();
      render();
    });
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' && suggestions.firstElementChild) {
        event.preventDefault();
        suggestions.firstElementChild.click();
      }
      if (event.key === 'Escape') suggestions.classList.add('hidden');
    });
  }

  updateConsumableUnit(input) {
    const row = input.closest('[data-consumable-row]');
    if (!row) return;
    const unit = input.value.toLocaleLowerCase().includes('résine') ? 'ml' : 'g';
    const quantity = row.querySelector('input[type="number"]');
    const unitLabel = row.querySelector('[data-consumable-unit]');
    unitLabel.textContent = unit;
    quantity.setAttribute('aria-label', `Quantité de ${input.value || 'ce consommable'} en ${unit}`);
  }

  addRow() {
    const row = document.createElement('div');
    row.className = 'flex flex-wrap items-center gap-2.5 sm:flex-nowrap rounded-xl border border-slate-200/90 bg-white p-2.5 shadow-sm';
    row.dataset.consumableRow = '';
    row.innerHTML = `<div class="relative min-w-0 flex-1 basis-full sm:basis-0" data-autocomplete data-options="PLA noir|PLA blanc|PETG transparent|Résine grise|Filament flexible"><input type="text" aria-label="Consommable" autocomplete="off" data-consumable class="w-full border-0 bg-transparent p-0 text-base text-slate-800 outline-none focus:ring-0 sm:text-sm"><div class="absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card" data-suggestions></div></div><label class="flex w-28 min-w-0 flex-1 items-center rounded-lg bg-slate-50/60 px-2.5 py-1.5 sm:flex-none"><input type="number" min="0" step="any" aria-label="Quantité du consommable en g" placeholder="0" class="w-full border-0 bg-transparent p-0 text-right text-base text-slate-900 outline-none focus:ring-0 sm:text-sm"><span class="ml-1 text-sm text-slate-500" data-consumable-unit>g</span></label><button type="button" aria-label="Supprimer ce consommable" data-remove class="flex h-10 w-10 items-center justify-center text-slate-400 hover:text-rose-600 sm:h-6 sm:w-6">×</button>`;
    row.querySelector('[data-remove]').addEventListener('click', () => row.remove());
    this.consumables.append(row);
    this.setupAutocomplete(row.querySelector('[data-autocomplete]'));
  }

  get value() {
    return Object.fromEntries(new FormData(this.querySelector('form')).entries());
  }

  toLocalDateTimeValue(date) {
    const pad = (value) => String(value).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
  }

  updateEndDate() {
    if (!this.startDate.value) {
      this.endDate.value = '';
      return;
    }

    const hours = Number(this.durationHours.value || 0);
    const minutes = Number(this.durationMinutes.value || 0);
    if (hours === 0 && minutes === 0) {
      this.endDate.value = '';
      return;
    }

    const start = new Date(this.startDate.value);
    if (Number.isNaN(start.getTime())) {
      this.endDate.value = '';
      return;
    }
    start.setMinutes(start.getMinutes() + (hours * 60) + minutes);
    this.endDate.value = this.toLocalDateTimeValue(start);
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

customElements.define('job-modal', JobModal);