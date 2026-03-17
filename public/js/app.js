(() => {
  const chart = document.getElementById('chartIngresos');
  if (chart) {
    const data = JSON.parse(chart.dataset.series);
    new Chart(chart, {
      type: 'bar',
      data: {
        labels: ['Sep','Oct','Nov','Dic','Ene','Feb','Mar','Abr','May','Jun'],
        datasets: [{ label: 'Ingresos', data, backgroundColor: '#0d6efd' }]
      }
    });
  }

  const modalElement = document.getElementById('paymentModal');
  if (!modalElement) return;

  const modal = new bootstrap.Modal(modalElement);
  const editModalElement = document.getElementById('editAbonoModal');
  const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
  
  const historyList = document.getElementById('abonoHistory');
  const form = document.getElementById('abonoForm');
  const editForm = document.getElementById('editAbonoForm');

  document.querySelectorAll('.payment-cell').forEach((button) => {
    button.addEventListener('click', async () => {
      const alumnoId = button.dataset.alumnoId;
      const mes = button.dataset.mes;
      const anio = button.dataset.anio;

      if (document.getElementById('formAlumnoId')) {
        document.getElementById('formAlumnoId').value = alumnoId;
        document.getElementById('formMes').value = mes;
        document.getElementById('formAnio').value = anio;
      }

      const response = await fetch(`/abonos/historial?alumno_id=${alumnoId}&mes=${mes}&anio=${anio}`);
      const json = await response.json();

      historyList.innerHTML = '';
      json.data.forEach((item) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
        
        let actions = '';
        if (form) { // User is authenticated
          actions = `
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-primary py-1 px-2 edit-btn" 
                      data-id="${item.id}" data-valor="${item.valor}" data-fecha="${item.fecha_abono}"
                      title="Editar">
                <i class="bi bi-pencil"></i> <small>Editar</small>
              </button>
              <button class="btn btn-outline-danger py-1 px-2 delete-btn" data-id="${item.id}"
                      title="Borrar">
                <i class="bi bi-trash"></i> <small>Borrar</small>
              </button>
            </div>
          `;
        }

        li.innerHTML = `
          <div>
            <span class="text-muted small">${item.fecha_abono}</span><br>
            <strong>$${Number(item.valor).toFixed(2)}</strong>
          </div>
          ${actions}
        `;
        historyList.appendChild(li);
      });

      if (json.data.length === 0) {
        historyList.innerHTML = '<li class="list-group-item text-center text-muted">Sin abonos registrados.</li>';
      }

      // Add listeners to new buttons
      historyList.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          if (editModal) {
            document.getElementById('editAbonoId').value = btn.dataset.id;
            document.getElementById('editAbonoValor').value = btn.dataset.valor;
            document.getElementById('editAbonoFecha').value = btn.dataset.fecha;
            modal.hide();
            editModal.show();
          }
        });
      });

      historyList.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
          if (!confirm('¿Seguro que deseas eliminar este abono?')) return;
          const csrf = document.querySelector('[name="csrf"]')?.value;
          if (!csrf) { alert('Sesión expirada'); return; }
          
          const formData = new FormData();
          formData.append('id', btn.dataset.id);
          formData.append('csrf', csrf);
          
          const res = await fetch('/abonos/eliminar', { method: 'POST', body: formData });
          const data = await res.json();
          if (data.ok) location.reload(); else alert(data.message);
        });
      });

      modal.show();
    });
  });

  if (form) {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const payload = new FormData(form);
      const response = await fetch('/abonos', { method: 'POST', body: payload });
      const json = await response.json();
      if (json.ok) window.location.reload(); else alert(json.message || 'Error al registrar abono');
    });
  }

  if (editForm) {
    editForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const payload = new FormData(editForm);
      const response = await fetch('/abonos/editar', { method: 'POST', body: payload });
      const json = await response.json();
      if (json.ok) window.location.reload(); else alert(json.message || 'Error al actualizar abono');
    });
  }
})();

(() => {
  const modalElementPublic = document.getElementById('paymentModalPublic');
  if (!modalElementPublic) return;

  const modalPublic = new bootstrap.Modal(modalElementPublic);
  const historyListPublic = document.getElementById('abonoHistoryPublic');

  document.querySelectorAll('.payment-cell-public').forEach((button) => {
    button.addEventListener('click', async () => {
      const alumnoId = button.dataset.alumnoId;
      const mes = button.dataset.mes;
      const anio = button.dataset.anio;

      const response = await fetch(`/abonos/historial?alumno_id=${alumnoId}&mes=${mes}&anio=${anio}`);
      const json = await response.json();

      historyListPublic.innerHTML = '';
      json.data.forEach((item) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `<span>${item.fecha_abono}</span><strong>$${Number(item.valor).toFixed(2)}</strong>`;
        historyListPublic.appendChild(li);
      });

      if (json.data.length === 0) {
        historyListPublic.innerHTML = '<li class="list-group-item text-center">Sin abonos registrados.</li>';
      }
      modalPublic.show();
    });
  });
})();
