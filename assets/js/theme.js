(function () {
  const root = document.documentElement;
  const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  }[char]));
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.primary-nav');
  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  document.querySelectorAll('.search-toggle').forEach((button) => {
    button.addEventListener('click', () => {
      const form = button.closest('.header-search');
      const input = form && form.querySelector('input[type="search"]');
      const open = form.classList.toggle('is-open');
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && input) input.focus();
    });
  });

  const heroEvents = Array.isArray(window.tsEvents) ? window.tsEvents : [];
  const heroFront = document.querySelector('[data-hero-front-card]');
  const heroBack = document.querySelector('[data-hero-back-card]');
  if (heroEvents.length && heroFront && heroBack) {
    let heroIndex = 0;
    const dots = Array.from(document.querySelectorAll('[data-hero-dot]'));
    const formatMeta = (event) => [event.date, event.city].filter(Boolean).join(' · ');
    const topicHtml = (event) => (Array.isArray(event.topics) ? event.topics : [])
      .map((topic) => `<b>${escapeHtml(topic)}</b>`)
      .join('');
    const setText = (selector, value, scope = document) => {
      const node = scope.querySelector(selector);
      if (node) node.textContent = value || '';
    };
    const renderHeroEvent = (frontEvent, backEvent) => {
      setText('[data-hero-type]', frontEvent.type, heroFront);
      setText('[data-hero-title]', frontEvent.title, heroFront);
      setText('[data-hero-meta]', formatMeta(frontEvent), heroFront);
      setText('[data-hero-footer-topic]', (frontEvent.topics && frontEvent.topics[0]) || frontEvent.type, heroFront);
      const topics = heroFront.querySelector('[data-hero-topics]');
      if (topics) topics.innerHTML = topicHtml(frontEvent);
      const register = heroFront.querySelector('[data-hero-register]');
      if (register) register.href = frontEvent.registration_url || '#';
      setText('[data-hero-back-type]', backEvent.type, heroBack);
      setText('[data-hero-back-title]', backEvent.title, heroBack);
      setText('[data-hero-back-meta]', formatMeta(backEvent), heroBack);
      setText('[data-hero-back-topic]', (backEvent.topics && backEvent.topics[0]) || backEvent.type, heroBack);
      dots.forEach((dot) => dot.classList.toggle('is-active', Number(dot.dataset.heroDot) === heroIndex));
    };
    const showHeroEvent = (nextIndex) => {
      heroIndex = (nextIndex + heroEvents.length) % heroEvents.length;
      heroFront.classList.add('is-fading');
      heroBack.classList.add('is-fading');
      window.setTimeout(() => {
        renderHeroEvent(heroEvents[heroIndex], heroEvents[(heroIndex + 1) % heroEvents.length]);
        heroFront.classList.remove('is-fading');
        heroBack.classList.remove('is-fading');
      }, 300);
    };
    dots.forEach((dot) => {
      dot.addEventListener('click', () => showHeroEvent(Number(dot.dataset.heroDot) || 0));
    });
    window.setInterval(() => showHeroEvent(heroIndex + 1), 5000);
  }

  const localFilter = document.querySelector('[data-local-filter]');
  if (localFilter) {
    const cards = Array.from(document.querySelectorAll('[data-card-grid] .event-card'));
    const city = localFilter.querySelector('[data-city]');
    const topic = localFilter.querySelector('[data-topic]');
    let type = 'All';
    const apply = () => {
      cards.forEach((card) => {
        const typeOk = type === 'All' || card.dataset.type === type;
        const cityOk = !city.value || card.dataset.city === city.value;
        const topicOk = !topic.value || (card.dataset.topics || '').split(',').includes(topic.value);
        card.hidden = !(typeOk && cityOk && topicOk);
      });
    };
    localFilter.querySelectorAll('[data-type]').forEach((button) => {
      button.addEventListener('click', () => {
        localFilter.querySelectorAll('[data-type]').forEach((el) => el.classList.remove('is-active'));
        button.classList.add('is-active');
        type = button.dataset.type;
        apply();
      });
    });
    [city, topic].forEach((select) => select && select.addEventListener('change', apply));
  }

  const ajaxForm = document.querySelector('.ajax-filter-form');
  const results = document.querySelector('[data-ajax-results]');
  const resultCount = document.querySelector('[data-result-count]');
  const resultProgress = document.querySelector('[data-result-progress]');
  const sortSelect = document.querySelector('[data-sort-select]');
  const filterDrawer = document.querySelector('[data-filter-drawer]');
  const filterBackdrop = document.querySelector('[data-filter-backdrop]');
  const activeFilters = document.querySelector('[data-active-filters]');
  const openFilters = () => {
    if (!filterDrawer || !filterBackdrop) return;
    filterDrawer.classList.add('is-open');
    filterBackdrop.hidden = false;
  };
  const closeFilters = () => {
    if (!filterDrawer || !filterBackdrop) return;
    filterDrawer.classList.remove('is-open');
    filterBackdrop.hidden = true;
  };
  document.querySelector('[data-filter-open]')?.addEventListener('click', openFilters);
  document.querySelector('[data-filter-close]')?.addEventListener('click', closeFilters);
  filterBackdrop?.addEventListener('click', closeFilters);
  if (ajaxForm && results && window.techstackData) {
    const typeChecks = Array.from(ajaxForm.querySelectorAll('input[name="type"]'));
    const labels = {
      s: 'Search',
      type: 'Type',
      'city[]': 'City',
      'topics[]': 'Topic',
      date_from: 'From',
      date_to: 'To',
      price_free: 'Free',
      online_only: 'Online'
    };
    const selectedControls = () => Array.from(ajaxForm.elements).filter((el) => {
      if (!el.name || el.disabled) return false;
      if (el.name === 'include_past' || el.name === 'action' || el.name === 'nonce') return false;
      if (el.hasAttribute('data-permanent')) return false;
      if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return false;
      if (el.tagName === 'SELECT' && el.multiple) return Array.from(el.selectedOptions).length;
      return Boolean(el.value);
    });
    const renderActiveFilters = () => {
      if (!activeFilters) return;
      const tags = [];
      selectedControls().forEach((el) => {
        if (el.tagName === 'SELECT' && el.multiple) {
          Array.from(el.selectedOptions).forEach((option) => {
            tags.push({ name: el.name, value: option.value, text: `${labels[el.name] || el.name}: ${option.textContent}` });
          });
        } else {
          const value = el.type === 'checkbox' ? el.value : el.value;
          const text = el.type === 'checkbox' && (el.name === 'price_free' || el.name === 'online_only')
            ? (labels[el.name] || value)
            : `${labels[el.name] || el.name}: ${value}`;
          tags.push({ name: el.name, value, text });
        }
      });
      activeFilters.innerHTML = tags.map((tag) => `<span class="active-filter-tag">${escapeHtml(tag.text)}<button type="button" aria-label="Remove ${escapeHtml(tag.text)}" data-filter-remove="${escapeHtml(tag.name)}" data-filter-value="${escapeHtml(tag.value)}">&times;</button></span>`).join('');
    };
    activeFilters?.addEventListener('click', (event) => {
      const button = event.target.closest('[data-filter-remove]');
      if (!button) return;
      const name = button.dataset.filterRemove;
      const value = button.dataset.filterValue;
      Array.from(ajaxForm.elements).forEach((el) => {
        if (el.name !== name) return;
        if (el.tagName === 'SELECT' && el.multiple) {
          Array.from(el.options).forEach((option) => {
            if (option.value === value) option.selected = false;
          });
        } else if (el.type === 'checkbox' || el.type === 'radio') {
          if (el.value === value) el.checked = false;
        } else if (el.type === 'hidden') {
          if (el.value === value) el.remove();
        } else {
          el.value = '';
        }
      });
      // Deactivate the corresponding pill if one exists.
      ajaxForm.querySelectorAll('.filter-pill').forEach((pill) => {
        if (pill.dataset.filterName === name && pill.dataset.filterValue === value) {
          pill.classList.remove('is-active');
        }
      });
      renderActiveFilters();
      submit();
    });
    const submit = () => {
      const data = new FormData(ajaxForm);
      data.append('action', 'ks_filter_events');
      data.append('nonce', techstackData.nonce);
      if (sortSelect) data.append('sort', sortSelect.value);
      fetch(techstackData.ajaxUrl, { method: 'POST', body: data })
        .then((res) => res.json())
        .then((json) => {
          if (!json.success) return;
          results.innerHTML = json.data.html || '<p class="empty-state">No matching events found.</p>';
          if (resultCount) {
            resultCount.dataset.showing = json.data.showing;
            resultCount.dataset.found = json.data.found;
            resultCount.innerHTML = `Showing <strong>${json.data.showing}</strong> of ${json.data.found} events`;
          }
          if (resultProgress) {
            const percent = json.data.found ? Math.min(100, Math.round((json.data.showing / json.data.found) * 100)) : 0;
            resultProgress.style.width = `${percent}%`;
          }
          renderActiveFilters();
        });
    };
    ajaxForm.addEventListener('submit', (event) => {
      event.preventDefault();
      submit();
      closeFilters();
    });
    typeChecks.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
          typeChecks.forEach((item) => {
            if (item !== checkbox) item.checked = false;
          });
        }
      });
    });
    ajaxForm.querySelectorAll('input, select').forEach((el) => el.addEventListener('change', () => {
      renderActiveFilters();
      submit();
    }));
    if (sortSelect) sortSelect.addEventListener('change', submit);

    // Pill filter buttons (Cities and Topics).
    ajaxForm.querySelectorAll('.pill-filter-group[data-pill-filter]').forEach((group) => {
      const fieldName = group.dataset.pillFilter;
      const inputContainer = ajaxForm.querySelector(`[data-pill-inputs="${fieldName}"]`);
      const syncInputs = () => {
        if (!inputContainer) return;
        inputContainer.innerHTML = '';
        group.querySelectorAll('.filter-pill.is-active').forEach((pill) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = fieldName;
          input.value = pill.dataset.filterValue;
          inputContainer.appendChild(input);
        });
      };
      group.querySelectorAll('.filter-pill').forEach((pill) => {
        pill.addEventListener('click', () => {
          pill.classList.toggle('is-active');
          syncInputs();
          renderActiveFilters();
          submit();
        });
      });
    });

    // Toggle pills (Free only / Online only) — boolean on/off with hidden inputs.
    ajaxForm.querySelectorAll('[data-toggle-pills] .filter-pill[data-toggle-input]').forEach((pill) => {
      pill.addEventListener('click', () => {
        const key = pill.dataset.toggleInput;
        const isActive = pill.classList.toggle('is-active');
        const existing = ajaxForm.querySelector(`input[data-toggle-hidden="${key}"]`);
        if (isActive) {
          if (!existing) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = '1';
            input.setAttribute('data-toggle-hidden', key);
            ajaxForm.appendChild(input);
          }
        } else {
          if (existing) existing.remove();
        }
        renderActiveFilters();
        submit();
      });
    });

    renderActiveFilters();

    // Fire an initial AJAX request when a pre-filter is active (CFP / hackathon pages)
    // so the result count and card context are always in sync with server state.
    if (window.tsPreFilter && Object.keys(tsPreFilter).length > 0) {
      submit();
    }
  }

  document.querySelectorAll('[data-copy-link]').forEach((button) => {
    button.addEventListener('click', () => {
      navigator.clipboard.writeText(button.dataset.copyLink || window.location.href);
      button.textContent = 'Copied';
    });
  });

  document.querySelectorAll('[data-ics]').forEach((button) => {
    button.addEventListener('click', () => {
      const item = JSON.parse(button.dataset.ics);
      const ics = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//TechStack//Events//EN',
        'BEGIN:VEVENT',
        `UID:${item.uid}`,
        `DTSTAMP:${item.stamp}`,
        `DTSTART;VALUE=DATE:${item.start}`,
        `DTEND;VALUE=DATE:${item.end}`,
        `SUMMARY:${item.title}`,
        `LOCATION:${item.location}`,
        `URL:${item.url}`,
        'END:VEVENT',
        'END:VCALENDAR'
      ].join('\r\n');
      const blob = new Blob([ics], { type: 'text/calendar' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = `${item.slug}.ics`;
      link.click();
      URL.revokeObjectURL(link.href);
    });
  });

  const submitForm = document.querySelector('[data-submit-form]');
  if (submitForm) {
    const requiredFields = Array.from(submitForm.querySelectorAll('[required]'));
    const messageFor = (field) => {
      if (field.validity.typeMismatch) return 'Enter a valid value.';
      return 'This field is required.';
    };
    const setFieldState = (field, shouldShow) => {
      const holder = field.closest('label') || field.parentElement;
      if (!holder) return true;
      let error = holder.querySelector('.field-error');
      const isValid = field.checkValidity();
      holder.classList.toggle('is-invalid', shouldShow && !isValid);
      holder.classList.toggle('is-valid', Boolean(field.value) && isValid);
      if (shouldShow && !isValid) {
        if (!error) {
          error = document.createElement('small');
          error.className = 'field-error';
          holder.appendChild(error);
        }
        error.textContent = messageFor(field);
      } else if (error) {
        error.remove();
      }
      return isValid;
    };
    requiredFields.forEach((field) => {
      field.addEventListener('input', () => setFieldState(field, true));
      field.addEventListener('blur', () => setFieldState(field, true));
    });
    submitForm.addEventListener('submit', (event) => {
      const states = requiredFields.map((field) => setFieldState(field, true));
      const isValid = states.every(Boolean);
      if (!isValid) {
        event.preventDefault();
        requiredFields.find((field) => !field.checkValidity())?.focus();
      }
    });
  }

  const calendar = document.querySelector('[data-calendar]');
  if (calendar && window.techstackData) {
    const grid = calendar.querySelector('[data-calendar-grid]');
    const list = calendar.querySelector('[data-calendar-list]');
    const title = calendar.querySelector('[data-cal-title]');
    const popover = calendar.querySelector('[data-calendar-popover]');
    const upcoming = calendar.querySelector('[data-calendar-upcoming]');
    const stats = calendar.querySelector('[data-calendar-stats]');
    let cursor = new Date();
    let events = [];
    const localIso = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const typeClass = (type) => `type-${String(type || '').toLowerCase().replace(/\//g, '-').replace(/\s+/g, '-')}`;
    const typeColors = {
      Conference: '#6C47FF',
      Hackathon: '#FF4D6D',
      Meetup: '#00C896',
      Workshop: '#00D4FF',
      Summit: '#FF9500',
      Webinar: '#7B8DB0'
    };
    const relativeDay = (iso) => {
      const today = new Date(localIso(new Date()));
      const date = new Date(iso);
      return Math.round((date - today) / 86400000);
    };
    const priceClass = (price) => String(price || '').toLowerCase().includes('free') ? 'is-free' : 'is-paid';
    const showPopover = (day, items) => {
      if (!items.length) {
        hidePopover();
        return;
      }
      popover.hidden = false;
      popover.innerHTML = `<strong>${day.dataset.day}</strong>${items.map((item) => `
        <div class="calendar-popover__event">
          <span class="event-badge ${typeClass(item.type)}">${escapeHtml(item.type || 'Conference')}</span>
          <a href="${escapeHtml(item.url)}">${escapeHtml(item.title)}</a>
          <small>${escapeHtml(item.city || 'Online')}</small>
          <a class="calendar-popover__register" href="${escapeHtml(item.register_url || item.url)}">Register</a>
        </div>`).join('')}`;
      const shellRect = calendar.getBoundingClientRect();
      const dayRect = day.getBoundingClientRect();
      const popoverWidth = popover.offsetWidth || 300;
      const popoverHeight = popover.offsetHeight || 220;
      const left = Math.max(0, Math.min(dayRect.left - shellRect.left, calendar.clientWidth - popoverWidth));
      const belowTop = dayRect.bottom - shellRect.top + 8;
      const aboveTop = dayRect.top - shellRect.top - popoverHeight - 8;
      const hasBelowRoom = window.innerHeight - dayRect.bottom > popoverHeight + 24;
      popover.style.left = `${left}px`;
      popover.style.top = `${!hasBelowRoom && aboveTop > 0 ? aboveTop : belowTop}px`;
    };
    const hidePopover = () => {
      popover.hidden = true;
    };
    const load = () => {
      const data = new FormData();
      data.append('action', 'ks_get_events_by_month');
      data.append('nonce', techstackData.nonce);
      data.append('year', cursor.getFullYear());
      data.append('month', cursor.getMonth() + 1);
      fetch(techstackData.ajaxUrl, { method: 'POST', body: data })
        .then((res) => res.json())
        .then((json) => {
          events = json.success ? json.data : [];
          render();
        });
    };
    const render = () => {
      title.textContent = cursor.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
      const first = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
      const last = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0);
      const offset = (first.getDay() + 6) % 7;
      const todayIso = localIso(new Date());
      grid.innerHTML = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map((d) => `<b>${d}</b>`).join('');
      for (let i = 0; i < offset; i += 1) grid.insertAdjacentHTML('beforeend', '<div class="calendar-day is-empty"></div>');
      for (let day = 1; day <= last.getDate(); day += 1) {
        const iso = `${cursor.getFullYear()}-${String(cursor.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = events.filter((event) => event.date === iso);
        const visibleDots = dayEvents.slice(0, 3).map((event) => `<i class="${typeClass(event.type)}"></i>`).join('');
        const extraCount = dayEvents.length > 3 ? `<mark>+${dayEvents.length - 3}</mark>` : '';
        const dominantType = dayEvents[0]?.type || '';
        const tint = dominantType ? ` style="--day-tint:${typeColors[dominantType] || '#6C47FF'};"` : '';
        const classes = ['calendar-day', dominantType ? typeClass(dominantType) : '', dayEvents.length ? 'has-events' : '', iso === todayIso ? 'is-today' : ''].filter(Boolean).join(' ');
        grid.insertAdjacentHTML('beforeend', `<button class="${classes}" type="button" data-day="${iso}"${tint}><span>${day}</span><em>${visibleDots}${extraCount}</em></button>`);
      }
      const monthLabel = cursor.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
      list.innerHTML = events.length ? `
        <h2 class="calendar-list__month">${escapeHtml(monthLabel)}</h2>
        ${events.map((event) => {
          const date = new Date(event.date);
          return `<a class="calendar-list__item ${typeClass(event.type)}" href="${escapeHtml(event.url)}">
            <time><strong>${date.getDate()}</strong><span>${date.toLocaleDateString(undefined, { weekday: 'short' })}</span></time>
            <span class="event-badge ${typeClass(event.type)}">${escapeHtml(event.type || 'Conference')}</span>
            <strong>${escapeHtml(event.title)}</strong>
            <span>${escapeHtml(event.city || 'Online')}</span>
            <mark class="price-badge ${priceClass(event.price)}">${escapeHtml(event.price || 'Free')}</mark>
          </a>`;
        }).join('')}` : '<p class="empty-state">No events this month.</p>';
      if (upcoming) {
        upcoming.innerHTML = events.length ? events.slice(0, 8).map((event) => {
          const date = new Date(event.date);
          return `<a class="calendar-upcoming__item ${typeClass(event.type)}" href="${escapeHtml(event.url)}">
            <time>${date.getDate()}</time>
            <span><strong>${escapeHtml(event.title)}</strong><small>${escapeHtml(event.city || 'Online')}</small></span>
            <em class="event-badge ${typeClass(event.type)}">${escapeHtml(event.type || 'Conference')}</em>
          </a>`;
        }).join('') : '<p class="empty-state">No events this month.</p>';
      }
      if (stats) {
        const conferences = events.filter((event) => event.type === 'Conference').length;
        const hackathons = events.filter((event) => event.type === 'Hackathon').length;
        const freeEvents = events.filter((event) => String(event.price || '').toLowerCase().includes('free')).length;
        stats.innerHTML = [
          ['Total events this month', events.length],
          ['Conferences', conferences],
          ['Hackathons', hackathons],
          ['Free events', freeEvents]
        ].map(([label, value]) => `<span><strong>${value}</strong>${label}</span>`).join('');
      }
    };
    grid.addEventListener('click', (event) => {
      const day = event.target.closest('[data-day]');
      if (!day) return;
      const items = events.filter((item) => item.date === day.dataset.day);
      showPopover(day, items);
    });
    grid.addEventListener('mouseover', (event) => {
      const day = event.target.closest('[data-day]');
      if (!day) return;
      showPopover(day, events.filter((item) => item.date === day.dataset.day));
    });
    grid.addEventListener('focusin', (event) => {
      const day = event.target.closest('[data-day]');
      if (!day) return;
      showPopover(day, events.filter((item) => item.date === day.dataset.day));
    });
    calendar.querySelector('[data-cal-prev]').addEventListener('click', () => { cursor = new Date(cursor.getFullYear(), cursor.getMonth() - 1, 1); load(); });
    calendar.querySelector('[data-cal-next]').addEventListener('click', () => { cursor = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 1); load(); });
    calendar.querySelector('[data-cal-today]')?.addEventListener('click', () => { cursor = new Date(); load(); });
    calendar.querySelectorAll('[data-view]').forEach((button) => {
      button.addEventListener('click', () => {
        calendar.querySelectorAll('[data-view]').forEach((el) => el.classList.remove('is-active'));
        button.classList.add('is-active');
        const isList = button.dataset.view === 'list';
        grid.hidden = isList;
        list.hidden = !isList;
      });
    });
    document.addEventListener('click', (event) => {
      if (popover.hidden) return;
      if (event.target.closest('[data-calendar-popover]') || event.target.closest('[data-day]')) return;
      hidePopover();
    });
    load();
  }
}());
