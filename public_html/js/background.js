// background.js

document.addEventListener('DOMContentLoaded', () => {
  const particlesContainer = document.getElementById('particles-container');
  const particleCount = 80;

  // Crée les particules initiales
  for (let i = 0; i < particleCount; i++) {
    createParticle();
  }

  function createParticle() {
    const p = document.createElement('div');
    p.className = 'particle';
    const size = Math.random() * 3 + 1;
    p.style.width = `${size}px`;
    p.style.height = `${size}px`;
    resetParticle(p);
    particlesContainer.appendChild(p);
    animateParticle(p);
  }

  function resetParticle(p) {
    const posX = Math.random() * 100;
    const posY = Math.random() * 100;
    p.style.left = `${posX}%`;
    p.style.top  = `${posY}%`;
    p.style.opacity = '0';
    return { x: posX, y: posY };
  }

  function animateParticle(p) {
    const start = resetParticle(p);
    const duration = Math.random() * 10 + 10;
    const delay    = Math.random() * 5;

    setTimeout(() => {
      p.style.transition = `all ${duration}s linear`;
      p.style.opacity = Math.random() * 0.3 + 0.1;

      const moveX = start.x + (Math.random() * 20 - 10);
      const moveY = start.y - Math.random() * 30;

      p.style.left = `${moveX}%`;
      p.style.top  = `${moveY}%`;

      setTimeout(() => animateParticle(p), duration * 1000);
    }, delay * 1000);
  }

  // Traînée de particules au survol
  document.addEventListener('mousemove', e => {
    const rect = particlesContainer.getBoundingClientRect();
    const mouseX = ((e.clientX - rect.left) / rect.width) * 100;
    const mouseY = ((e.clientY - rect.top)  / rect.height) * 100;

    const mp = document.createElement('div');
    mp.className = 'particle';
    const sz = Math.random() * 4 + 2;
    mp.style.width  = `${sz}px`;
    mp.style.height = `${sz}px`;
    mp.style.left   = `${mouseX}%`;
    mp.style.top    = `${mouseY}%`;
    mp.style.opacity = '0.6';
    particlesContainer.appendChild(mp);

    // anim trail
    setTimeout(() => {
      mp.style.transition = 'all 2s ease-out';
      mp.style.left    = `${mouseX + (Math.random() * 10 - 5)}%`;
      mp.style.top     = `${mouseY + (Math.random() * 10 - 5)}%`;
      mp.style.opacity = '0';
      setTimeout(() => mp.remove(), 2000);
    }, 10);

    // bosse légère des sphères
    document.querySelectorAll('.gradient-sphere').forEach(s => {
      const dx = (e.clientX / window.innerWidth  - 0.5) * 5;
      const dy = (e.clientY / window.innerHeight - 0.5) * 5;
      s.style.transform = `translate(${dx}px,${dy}px)`;
    });
  });
});
