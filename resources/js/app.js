import './bootstrap';
import '../css/accessform.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const initCreatorSurveyFilter = () => {
	const filterInput = document.querySelector('[data-survey-filter]');
	const rows = Array.from(document.querySelectorAll('[data-survey-row]'));
	const status = document.querySelector('[data-survey-filter-status]');
	const emptyRow = document.querySelector('[data-survey-empty]');

	if (!filterInput || rows.length === 0) {
		return;
	}

	const updateFilter = () => {
		const query = filterInput.value.trim().toLowerCase();
		let visible = 0;

		rows.forEach((row) => {
			const text = row.getAttribute('data-survey-text') || '';
			const match = text.includes(query);
			row.classList.toggle('d-none', !match);
			if (match) {
				visible++;
			}
		});

		if (status) {
			status.textContent = `${visible} survey${visible === 1 ? '' : 's'} shown`;
		}

		if (emptyRow) {
			emptyRow.classList.toggle('d-none', visible !== 0);
		}
	};

	filterInput.addEventListener('input', updateFilter);
	updateFilter();
};

const initRespondentDashboard = () => {
	const themeTarget = document.querySelector('[data-respondent-theme]');
	const prefsForm = document.querySelector('[data-a11y-preferences]');
	const prefsStatus = document.querySelector('[data-prefs-status]');
	const contrastToggle = document.querySelector('[data-pref-contrast]');
	const dyslexiaToggle = document.querySelector('[data-pref-dyslexia]');
	const textSizeSelect = document.querySelector('[data-pref-text-size]');
	const motionToggle = document.querySelector('[data-pref-motion]');

	const prefsKey = 'accessform.respondent.prefs';

	const defaultPrefs = {
		highContrast: false,
		dyslexia: false,
		textSize: 'md',
		reducedMotion: false,
	};

	const loadPrefs = () => {
		try {
			const stored = localStorage.getItem(prefsKey);
			if (!stored) {
				return { ...defaultPrefs };
			}
			return { ...defaultPrefs, ...JSON.parse(stored) };
		} catch (error) {
			return { ...defaultPrefs };
		}
	};

	const savePrefs = (prefs) => {
		try {
			localStorage.setItem(prefsKey, JSON.stringify(prefs));
		} catch (error) {
			return;
		}
	};

	const applyPrefs = (prefs) => {
		if (!themeTarget) {
			return;
		}

		themeTarget.classList.toggle('theme-contrast', prefs.highContrast);
		themeTarget.classList.toggle('theme-dyslexia', prefs.dyslexia);
		themeTarget.classList.toggle('reduced-motion', prefs.reducedMotion);
		themeTarget.classList.remove('text-size-sm', 'text-size-md', 'text-size-lg');
		themeTarget.classList.add(`text-size-${prefs.textSize}`);
	};

	const announcePrefs = (message) => {
		if (prefsStatus) {
			prefsStatus.textContent = message;
		}
	};

	const updatePrefs = () => {
		const prefs = {
			highContrast: Boolean(contrastToggle?.checked),
			dyslexia: Boolean(dyslexiaToggle?.checked),
			textSize: textSizeSelect?.value || 'md',
			reducedMotion: Boolean(motionToggle?.checked),
		};

		savePrefs(prefs);
		applyPrefs(prefs);
		announcePrefs('Accessibility preferences updated.');
	};

	if (themeTarget) {
		const prefs = loadPrefs();

		applyPrefs(prefs);

		if (prefsForm) {
			if (contrastToggle) {
				contrastToggle.checked = prefs.highContrast;
			}
			if (dyslexiaToggle) {
				dyslexiaToggle.checked = prefs.dyslexia;
			}
			if (textSizeSelect) {
				textSizeSelect.value = prefs.textSize;
			}
			if (motionToggle) {
				motionToggle.checked = prefs.reducedMotion;
			}

			prefsForm.addEventListener('change', updatePrefs);
		}
	}

	const surveyContainer = document.querySelector('[data-survey-container]');
	const cards = Array.from(document.querySelectorAll('[data-survey-card]'));
	const searchInput = document.querySelector('[data-respondent-filter]');
	const filterContrast = document.querySelector('[data-filter-contrast]');
	const filterDyslexia = document.querySelector('[data-filter-dyslexia]');
	const sortSelect = document.querySelector('[data-survey-sort]');
	const filterStatus = document.querySelector('[data-survey-status]');
	const emptyState = document.querySelector('[data-survey-empty]');

	if (surveyContainer && cards.length > 0) {
		const updateList = () => {
			const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
			const requireContrast = Boolean(filterContrast?.checked);
			const requireDyslexia = Boolean(filterDyslexia?.checked);
			const sortValue = sortSelect?.value || 'newest';

			const sortedCards = [...cards].sort((a, b) => {
				const first = parseInt(a.dataset.updated || '0', 10);
				const second = parseInt(b.dataset.updated || '0', 10);
				return sortValue === 'oldest' ? first - second : second - first;
			});

			sortedCards.forEach((card) => surveyContainer.appendChild(card));

			let visible = 0;
			sortedCards.forEach((card) => {
				const text = (card.dataset.title || '').toLowerCase();
				const matchSearch = text.includes(query);
				const matchContrast = !requireContrast || card.dataset.contrast === '1';
				const matchDyslexia = !requireDyslexia || card.dataset.dyslexia === '1';
				const match = matchSearch && matchContrast && matchDyslexia;

				card.classList.toggle('d-none', !match);
				if (match) {
					visible++;
				}
			});

			if (filterStatus) {
				filterStatus.textContent = `${visible} survey${visible === 1 ? '' : 's'} shown`;
			}

			if (emptyState) {
				emptyState.classList.toggle('d-none', visible !== 0);
			}
		};

		searchInput?.addEventListener('input', updateList);
		filterContrast?.addEventListener('change', updateList);
		filterDyslexia?.addEventListener('change', updateList);
		sortSelect?.addEventListener('change', updateList);
		updateList();
	}

	const slugForm = document.querySelector('[data-slug-form]');
	const slugInput = document.querySelector('[data-slug-input]');
	const slugStatus = document.querySelector('[data-slug-status]');

	if (slugForm && slugInput) {
		slugForm.addEventListener('submit', (event) => {
			event.preventDefault();

			const rawValue = slugInput.value.trim();
			const sanitized = rawValue
				.replace(/^https?:\/\/[^/]+\/f\//i, '')
				.replace(/^\/f\//, '')
				.replace(/\s+/g, '-')
				.trim();

			if (!sanitized) {
				if (slugStatus) {
					slugStatus.textContent = 'Enter a survey link or slug first.';
				}
				return;
			}

			window.location.href = `/f/${encodeURIComponent(sanitized)}`;
		});
	}
};

const initAdminDashboard = () => {
	const filter = document.querySelector('[data-admin-activity-filter]');
	const items = Array.from(document.querySelectorAll('[data-admin-activity-item]'));
	const status = document.querySelector('[data-admin-activity-status]');
	const emptyState = document.querySelector('[data-admin-activity-empty]');

	if (!filter || items.length === 0) {
		return;
	}

	const updateFilter = () => {
		const value = filter.value || 'all';
		let visible = 0;

		items.forEach((item) => {
			const type = item.getAttribute('data-admin-activity-type') || 'other';
			const match = value === 'all' || value === type;
			item.classList.toggle('d-none', !match);
			if (match) {
				visible++;
			}
		});

		if (status) {
			status.textContent = `${visible} activit${visible === 1 ? 'y' : 'ies'} shown`;
		}

		if (emptyState) {
			emptyState.classList.toggle('d-none', visible !== 0);
		}
	};

	filter.addEventListener('change', updateFilter);
	updateFilter();
};

document.addEventListener('DOMContentLoaded', () => {
	initCreatorSurveyFilter();
	initRespondentDashboard();
	initAdminDashboard();
});
