document.addEventListener('DOMContentLoaded', () => {
    const startBtn = document.getElementById('start-btn');
    const gameContainer = document.getElementById('game-container');
    const startScreen = document.getElementById('start-screen');
    const resultContainer = document.getElementById('result-container');
    const timerDisplay = document.getElementById('timer');
    const posterImg = document.getElementById('movie-poster');
    const form = document.getElementById('guess-form');
    const input = document.getElementById('movie-input');
    const helpBtn = document.getElementById('help-btn');
    const resultsList = document.getElementById('results');
    const scoreDisplay = document.getElementById('score-display');
    const restartBtn = document.getElementById('restart-btn');
  
    let films = [];
    let currentFilm = null;
    let score = 0;
    let combo = 0;
    let timer = 60 * 1000; // 60 secondes
    let interval = null;
    let hintIndex = 0; // Déclarez hintIndex ici, avant toute utilisation
    let results = []; // Stocker les résultats des films joués
  
    // Charger les films depuis le CSV
    fetch('/devine-le-film-floute/bd_film/films_details.csv')
  .then(response => response.text())
  .then(data => {
    films = parseCSV(data);
    console.log('Films chargés :', films); // Vérifiez les données chargées
  })
  .catch(err => console.error('Erreur lors du chargement du CSV :', err));
  
    function parseCSV(data) {
      const rows = data.split('\n').slice(1); // Ignorer l'en-tête
      return rows.map((row, index) => {
        if (!row.trim()) {
          console.warn(`Ligne vide ignorée à l'index ${index + 1}`);
          return null;
        }
        // Diviser les colonnes en tenant compte des guillemets
        const columns = row.match(/(".*?"|[^",]+)(?=\s*,|\s*$)/g);
        if (!columns || columns.length < 9) {
          console.error(`Ligne mal formatée à l'index ${index + 1} :`, row);
          return null;
        }
        // Supprimer les guillemets autour des champs et les espaces inutiles
        const [original, french, poster, genres, director, directorPhoto, actor, actorPhoto, year] = columns.map(col => col.replace(/^"|"$/g, '').trim());
        return {
          original,
          french,
          poster,
          genres,
          director, // Contient le prénom et le nom
          directorPhoto,
          actor, // Contient le prénom et le nom
          actorPhoto,
          year
        };
      }).filter(film => film); // Filtrer les lignes nulles
    }
  
    startBtn.addEventListener('click', () => {
      startScreen.classList.add('hidden');
      gameContainer.classList.remove('hidden');
      startGame();
    });
  
    
    
  
  
    function nextFilm() {
  if (films.length === 0) {
    endGame();
    return;
  }

  // Réinitialiser les indices
  hintIndex = 0;
  const hintContainer = document.getElementById('hint-container');
  hintContainer.innerHTML = ''; // Vider le conteneur des indices
  closeHintPopup(); // Fermer la popup des indices

  // Sélectionner un nouveau film
  currentFilm = films.splice(Math.floor(Math.random() * films.length), 1)[0];
  console.log('Film sélectionné :', currentFilm); // Vérifiez si un film est sélectionné
  if (!currentFilm || !currentFilm.poster) {
    console.error('Aucun film valide sélectionné ou URL d\'affiche manquante.');
    return;
  }

  // Mettre à jour l'affiche
  posterImg.src = currentFilm.poster;
  posterImg.style.filter = 'blur(10px)';
  input.value = '';
  input.focus();
}
  
    form.addEventListener('submit', e => {
      e.preventDefault();
      const guess = input.value.trim();
      console.log('Réponse utilisateur :', guess);
  
      let pointsGained = 0;
      if (validateGuess(guess, currentFilm)) {
        pointsGained = 50 + combo * 10;
        score += pointsGained;
        combo++;
        showToast(`+${pointsGained} points !`);
        if (combo > 1) {
          showToast(`Combo x${combo} !`);
        }
      } else {
        pointsGained = -10; // Perte de points en cas d'erreur
        score += pointsGained;
        combo = 0;
        posterImg.classList.add('shake');
        setTimeout(() => posterImg.classList.remove('shake'), 500);
      }
    
      // Ajouter le résultat du film joué
      results.push({
        title: currentFilm.french || currentFilm.original,
        poster: currentFilm.poster,
        points: pointsGained
      });
    
      nextFilm();
    });
  
    helpBtn.addEventListener('click', () => {
  if (!currentFilm) {
    console.error('Aucun film sélectionné pour afficher les indices.');
    return;
  }

  const hints = [
    { label: 'Acteur principal', value: currentFilm.actor, image: currentFilm.actorPhoto },
    { label: 'Réalisateur', value: currentFilm.director, image: currentFilm.directorPhoto },
    { label: 'Année de sortie', value: currentFilm.year },
    { label: 'Genres', value: currentFilm.genres }
  ];

  const hintContainer = document.getElementById('hint-container');
  hintContainer.classList.remove('hidden'); // Afficher la popup

  if (hintIndex < hints.length) {
    const hint = hints[hintIndex];

    const hintElement = document.createElement('div');
    hintElement.className = 'hint';

    // Ajoutez une image si elle existe
    if (hint.image) {
      const img = document.createElement('img');
      img.src = hint.image;
      img.alt = hint.label;
      img.style.width = '50px';
      img.style.marginRight = '10px';
      hintElement.appendChild(img);
    }

    // Ajoutez le texte de l'indice
    const text = document.createElement('span');
    text.innerHTML = `<strong>${hint.label} :</strong> ${hint.value}`; // Affiche prénom et nom
    hintElement.appendChild(text);

    // Ajoutez l'indice au conteneur
    hintContainer.appendChild(hintElement);

    // Réduisez les points en fonction de l'indice affiché
    score -= hintIndex === 0 ? 20 : 5; // -20 points pour le premier indice, -5 pour les suivants
    hintIndex++;
  } else {
    hintContainer.innerHTML = '<p>Tous les indices ont été affichés.</p>';
  }
});

const closeHintBtn = document.getElementById('close-hint-btn');
closeHintBtn.addEventListener('click', () => {
  closeHintPopup();
});

function closeHintPopup() {
  const hintContainer = document.getElementById('hint-container');
  hintContainer.classList.add('hidden');
  hintContainer.innerHTML = '';
}
  
    function endGame() {
  console.log('Fin du jeu, affichage des résultats...');
  gameContainer.classList.add('hidden');
  resultContainer.classList.remove('hidden');

  if (scoreDisplay) {
    scoreDisplay.textContent = `Score : ${score}`;
  } else {
    console.error('Élément scoreDisplay introuvable dans le DOM.');
  }

  console.log('scoreDisplay :', scoreDisplay);

  // Afficher les résultats
  const resultsList = document.getElementById('results');
  resultsList.innerHTML = ''; // Réinitialiser les résultats
  results.forEach(result => {
    const resultItem = document.createElement('div');
    resultItem.className = 'result-item';

    // Ajouter l'affiche du film
    const poster = document.createElement('img');
    poster.src = result.poster;
    poster.alt = result.title;
    poster.style.width = '100px';
    poster.style.marginRight = '10px';

    // Ajouter le texte (titre et points)
    const text = document.createElement('span');
    text.textContent = `${result.title} - Points : ${result.points}`;

    resultItem.appendChild(poster);
    resultItem.appendChild(text);
    resultsList.appendChild(resultItem);
  });
}
  
    restartBtn.addEventListener('click', () => {
      location.reload(); // Rafraîchir la page
    });
    
    function resetGame() {
      console.log('Réinitialisation du jeu');
      score = 0;
      combo = 0;
      timer = 60 * 1000;
      hintIndex = 0;
    
      // Rechargez les films
      fetch('/devine-le-film-floute/bd_film/films_details.csv')
        .then(response => response.text())
        .then(data => {
          films = parseCSV(data);
          console.log('Films rechargés :', films);
          startScreen.classList.remove('hidden');
          gameContainer.classList.add('hidden');
          resultContainer.classList.add('hidden');
          timerDisplay.textContent = '60.00';
        })
        .catch(err => console.error('Erreur lors du rechargement du CSV :', err));
    }
    
    function startGame() {
      console.log('Démarrage du jeu');
      score = 0;
      combo = 0;
      timer = 60 * 1000;
      clearInterval(interval);
      interval = setInterval(updateTimer, 100);
      nextFilm();
    }
    
    function updateTimer() {
      timer -= 100;
      if (timer <= 0) {
        clearInterval(interval);
        endGame();
      }
      const seconds = Math.floor(timer / 1000);
      const milliseconds = Math.floor((timer % 1000) / 10);
      timerDisplay.textContent = `${seconds}.${milliseconds.toString().padStart(2, '0')}`;
    }



function showToast(message) {
  const toastContainer = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.textContent = message;

  toastContainer.appendChild(toast);

  // Supprime le toast après l'animation
  setTimeout(() => {
    toast.remove();
  }, 3000); // Durée de l'animation (3s)
}

function normalizeString(str) {
  return str
    .toLowerCase() // Convertir en minuscules
    .normalize('NFD') // Décomposer les caractères accentués
    .replace(/[\u0300-\u036f]/g, '') // Supprimer les accents
    .trim(); // Supprimer les espaces en début et fin
}

function generateTitleVariants(title) {
  const variants = [title];
  if (title.includes(':')) {
    const parts = title.split(':').map(part => part.trim());
    variants.push(parts[0]); // Ajouter la partie avant ":"
    variants.push(parts[1]); // Ajouter la partie après ":"
  }
  return variants.map(normalizeString); // Normaliser toutes les variantes
}

function levenshteinDistance(a, b) {
  const matrix = Array.from({ length: a.length + 1 }, () => Array(b.length + 1).fill(0));

  for (let i = 0; i <= a.length; i++) matrix[i][0] = i;
  for (let j = 0; j <= b.length; j++) matrix[0][j] = j;

  for (let i = 1; i <= a.length; i++) {
    for (let j = 1; j <= b.length; j++) {
      const cost = a[i - 1] === b[j - 1] ? 0 : 1;
      matrix[i][j] = Math.min(
        matrix[i - 1][j] + 1, // Suppression
        matrix[i][j - 1] + 1, // Insertion
        matrix[i - 1][j - 1] + cost // Substitution
      );
    }
  }

  return matrix[a.length][b.length];
}

function fuzzyMatch(input, target, threshold = 0.85) {
  const distance = levenshteinDistance(input, target);
  const similarity = 1 - distance / Math.max(input.length, target.length);
  return similarity >= threshold;
}

function validateGuess(guess, film) {
  const normalizedGuess = normalizeString(guess);
  const titleVariants = [
    ...generateTitleVariants(film.original),
    ...generateTitleVariants(film.french)
  ];

  // Vérifier si une variante correspond exactement
  if (titleVariants.some(variant => variant === normalizedGuess)) {
    return true;
  }

  // Vérifier si une variante correspond approximativement (matching flou)
  return titleVariants.some(variant => fuzzyMatch(normalizedGuess, variant));
}

  });

