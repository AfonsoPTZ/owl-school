console.log("cursor.js carregado");

(() => {
  const isTouch = "ontouchstart" in window || navigator.maxTouchPoints > 0;
  if (isTouch) return;

  const prefersReduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  let ring = document.getElementById("cursor-ring");
  if (!ring) {
    ring = document.createElement("div");
    ring.id = "cursor-ring";
    document.body.appendChild(ring);
  }

 
  const mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
  const pos   = { x: mouse.x, y: mouse.y };
  const prev  = { x: mouse.x, y: mouse.y };


  const speed = prefersReduce ? 1 : 0.18;       
  const rotThreshold = 20;                      
  const maxVel = 150;                           
  const stretch = 0.5;                          
  let scaleNow = 0;
  let angleNow = 0;

  function show() { if (ring.style.opacity !== "1") ring.style.opacity = "1"; }
  function hide() { ring.style.opacity = "0"; }

  function onMove(e) {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
    show();
  }

  function onDown() { ring.classList.add("ring-click"); }
  function onUp()   { ring.classList.remove("ring-click"); }

  function onLeave() { hide(); }
  function onEnter() { show(); }

  window.addEventListener("mousemove", onMove, { passive: true });
  window.addEventListener("mousedown", onDown);
  window.addEventListener("mouseup", onUp);
  window.addEventListener("mouseleave", onLeave);
  window.addEventListener("mouseenter", onEnter);

  function tick() {

    pos.x += (mouse.x - pos.x) * speed;
    pos.y += (mouse.y - pos.y) * speed;

    const dx = mouse.x - prev.x;
    const dy = mouse.y - prev.y;
    prev.x = mouse.x;
    prev.y = mouse.y;

    const vel = Math.min(Math.hypot(dx, dy) * 4, maxVel);
    const targetScale = prefersReduce ? 0 : (vel / maxVel) * stretch;
    scaleNow += (targetScale - scaleNow) * speed;

 
    if (vel > rotThreshold) angleNow = Math.atan2(dy, dx) * 180 / Math.PI;


    const translate = `translate3d(${pos.x}px, ${pos.y}px, 0)`;
    const rotate    = `rotate(${angleNow}deg)`;
    const scale     = `scale(${1 + scaleNow}, ${1 - scaleNow})`;

    ring.style.transform = `${translate} ${rotate} ${scale}`;

    requestAnimationFrame(tick);
  }

  requestAnimationFrame(tick);
})();
