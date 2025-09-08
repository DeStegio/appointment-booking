;(function(){
  function qs(sel,root=document){return root.querySelector(sel)}
  function qsa(sel,root=document){return Array.from(root.querySelectorAll(sel))}

  function initAlertDismiss(){
    qsa('[data-dismiss="alert"]').forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const alert = btn.closest('.alert');
        if(alert){ alert.remove(); }
      });
    });
  }

  function initConfirm(){
    // Forms with confirmation
    qsa('form[data-confirm]').forEach(form=>{
      form.addEventListener('submit', (e)=>{
        const msg = form.getAttribute('data-confirm') || 'Are you sure?';
        if(!window.confirm(msg)) e.preventDefault();
      });
    });
    // Links/buttons with confirmation
    qsa('[data-confirm]:not(form)').forEach(el=>{
      el.addEventListener('click', (e)=>{
        const msg = el.getAttribute('data-confirm') || 'Are you sure?';
        if(!window.confirm(msg)) e.preventDefault();
      });
    });
  }

  function initAutoDisable(){
    qsa('form').forEach(form=>{
      form.addEventListener('submit', ()=>{
        qsa('button[type="submit"]', form).forEach(btn=>{
          btn.classList.add('is-loading');
          btn.disabled = true;
        });
      });
    });
  }

  function initAutoSubmit(){
    qsa('select[data-auto-submit], input[data-auto-submit]').forEach(el=>{
      el.addEventListener('change', ()=>{
        const form = el.closest('form');
        if(form) form.submit();
      });
    });
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    initAlertDismiss();
    initConfirm();
    initAutoDisable();
    initAutoSubmit();
  });
})();

