(function(){
  const KEY = 'theme'; // 'light' | 'dark' | 'system'
  const root = document.documentElement;
  function apply(theme){
    root.classList.remove('theme-dark','theme-light');
    if(theme === 'dark'){ root.classList.add('theme-dark'); }
    else if(theme === 'light'){ root.classList.add('theme-light'); }
    // if 'system', rely on prefers-color-scheme (default styles)
  }
  function current(){
    return localStorage.getItem(KEY) || 'system';
  }
  function toggle(){
    const next = ({light:'dark', dark:'system', system:'light'})[current()];
    localStorage.setItem(KEY, next);
    apply(next);
    renderLabel(next);
  }
  function renderLabel(v){
    const el = document.getElementById('themeToggle');
    if(!el) return;
    const map = {light:'🌞 Light', dark:'🌙 Dark', system:'🖥️ System'};
    el.setAttribute('aria-label', 'Theme: ' + map[v]);
    el.textContent = map[v];
  }
  document.addEventListener('DOMContentLoaded', () => {
    apply(current());
    renderLabel(current());
    const btn = document.getElementById('themeToggle');
    if(btn){ btn.addEventListener('click', toggle); }
  });
})();
