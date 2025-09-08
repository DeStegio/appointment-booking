;(function(){
  const KEY = 'theme'; // 'light' | 'dark' | 'system'
  const root = document.documentElement;

  function apply(theme){
    root.classList.remove('theme-dark','theme-light');
    if(theme === 'dark') root.classList.add('theme-dark');
    else if(theme === 'light') root.classList.add('theme-light');
    // 'system' relies on media queries
  }
  function getTheme(){ return localStorage.getItem(KEY) || 'system'; }
  function setTheme(v){ localStorage.setItem(KEY, v); apply(v); renderLabel(v); }
  function toggle(){ setTheme(({system:'light', light:'dark', dark:'system'})[getTheme()]); }
  function renderLabel(v){
    const el = document.getElementById('themeToggle');
    if(!el) return;
    const label = v === 'dark' ? 'Dark' : v === 'light' ? 'Light' : 'System';
    el.setAttribute('aria-label', 'Theme: ' + label);
    el.textContent = 'Theme: ' + label;
  }
  document.addEventListener('DOMContentLoaded', () => {
    apply(getTheme());
    renderLabel(getTheme());
    const btn = document.getElementById('themeToggle');
    if(btn) btn.addEventListener('click', toggle);
  });
})();
