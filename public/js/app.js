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
  const historyList = document.getElementById('abonoHistory');
  const form = document.getElementById('abonoForm');

  document.querySelectorAll('.payment-cell').forEach((button) => {
    button.addEventListener('click', async () => {
      const alumnoId = button.dataset.alumnoId;
      const mes = button.dataset.mes;
      const anio = button.dataset.anio;

      document.getElementById('formAlumnoId').value = alumnoId;
      document.getElementById('formMes').value = mes;
      document.getElementById('formAnio').value = anio;

      const response = await fetch(`/abonos/historial?alumno_id=${alumnoId}&mes=${mes}&anio=${anio}`);
      const json = await response.json();

      historyList.innerHTML = '';
      json.data.forEach((item) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between';
        li.innerHTML = `<span>${item.fecha_abono}</span><strong>$${Number(item.valor).toFixed(2)}</strong>`;
        historyList.appendChild(li);
      });

      if (json.data.length === 0) {
        historyList.innerHTML = '<li class="list-group-item">Sin abonos registrados.</li>';
      }
      modal.show();
    });
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const payload = new FormData(form);

    const response = await fetch('/abonos', {
      method: 'POST',
      body: payload
    });

    const json = await response.json();
    if (json.ok) {
      window.location.reload();
    } else {
      alert(json.message || 'Error al registrar abono');
    }
  });
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
        li.className = 'list-group-item d-flex justify-content-between';
        li.innerHTML = `<span>${item.fecha_abono}</span><strong>$${Number(item.valor).toFixed(2)}</strong>`;
        historyListPublic.appendChild(li);
      });

      if (json.data.length === 0) {
        historyListPublic.innerHTML = '<li class="list-group-item">Sin abonos registrados.</li>';
      }
      modalPublic.show();
    });
  });
})();
