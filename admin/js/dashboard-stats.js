document.addEventListener('DOMContentLoaded', () => {
  let rawStats = {};

  // 1) Récupère la stats complète, puis initialise UI
  fetch('stats.json')
    .then(r => r.json())
    .then(s => {
      rawStats = s;
      initDatePickers();
      renderAll();
    })
    .catch(err => console.error(err));
// Récupère le nombre de suggestions
fetch('/admin/count_suggestions.php')
  .then(r => r.json())
  .then(d => {
    document.getElementById('suggestions-count').textContent =
      `Suggestions utilisateurs : ${d.count}`;
  })
  .catch(() => {
    document.getElementById('suggestions-count').textContent =
      'Suggestions : Indisponible';
  });

  // 2) Initialise les pickers à la plage par défaut (7 derniers jours)
  function initDatePickers() {
    const start = document.getElementById('start-date');
    const end   = document.getElementById('end-date');
    const today = new Date();
    const prev7 = new Date();
    prev7.setDate(today.getDate() - 6);

    const fmt = d => d.toISOString().slice(0,10);
    start.value = fmt(prev7);
    end.value   = fmt(today);

    start.max = end.max = fmt(today);
    start.min = end.min = Object.keys(rawStats.devine_affiche)
      .concat(Object.keys(rawStats.devine_emoji))
      .sort()[0] || fmt(prev7);

    // re-render quand l’un change
    [start, end].forEach(el =>
      el.addEventListener('change', () => {
        if (start.value <= end.value) renderAll();
      })
    );
  }

  // 3) Construction d’un array de dates entre start et end
  function getDateRange(start, end) {
    const a = [], cur = new Date(start);
    const last = new Date(end);
    while (cur <= last) {
      a.push(cur.toISOString().slice(0,10));
      cur.setDate(cur.getDate()+1);
    }
    return a;
  }

  // 4) Calcule et met à jour totaux et graphiques
  function renderAll() {
    const s = document.getElementById('start-date').value;
    const e = document.getElementById('end-date').value;
    const dates = getDateRange(s, e);

    // Totaux filtrés
    const sumInRange = (obj) =>
      dates.reduce((tot, d) => tot + (obj[d]||0), 0);

    document.getElementById('stats-affiche').textContent =
      `Lancements : ${sumInRange(rawStats.devine_affiche)}`;
    document.getElementById('stats-emoji').textContent =
      `Lancements : ${sumInRange(rawStats.devine_emoji)}`;

    // Mise à jour des graphiques
    updateChart(chartA,   rawStats.devine_affiche, dates);
    updateChart(chartE,   rawStats.devine_emoji,   dates);
  }

  // 5) Setup initial des charts (idem que précédemment)
  const ctxA = document.getElementById('chart-affiche').getContext('2d');
  const ctxE = document.getElementById('chart-emoji').getContext('2d');
  const chartA = makeChart(ctxA, [], 'Devine le film',    'rgba(90,200,250,0.7)');
  const chartE = makeChart(ctxE, [], 'Devine aux émoticônes', 'rgba(250,100,150,0.7)');

  // factory Chart.js vide
  function makeChart(ctx, data, label, color) {
    return new Chart(ctx, {
      type: 'bar',
      data: { labels: [], datasets: [{
        label, data, backgroundColor: color, borderColor: color, borderWidth:1
      }]},
      options: {
        scales: {
          x: { title:{display:true,text:'Date'} },
          y: {
            beginAtZero:true,
            title:{display:true,text:'Lancements'},
            ticks:{ stepSize:1, callback: v=>Number.isInteger(v)?v:'' }
          }
        },
        plugins:{ legend:{position:'top'}, tooltip:{mode:'index'} },
        responsive:true, maintainAspectRatio:false
      }
    });
  }

  // fonction pour mettre à jour un Chart existant
  function updateChart(chart, dataObj, dates) {
    chart.data.labels = dates;
    chart.data.datasets[0].data =
      dates.map(d => dataObj[d]||0);
    chart.update();
  }
});
