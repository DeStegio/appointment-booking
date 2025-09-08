@extends('layouts.app')
@section('content')
<div class="container">
  <h1 class="mb-2">{{ $provider->name }}</h1>
  <p class="text-muted mb-4">{{ $provider->email }}</p>

  @if($provider->services->isEmpty())
    <p>No active services yet.</p>
    <p><a href="{{ route('providers.index') }}">&larr; Back to providers</a></p>
  @else
  <div class="card p-3 mb-4">
    <h2 class="h5">Book an appointment</h2>
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Service</label>
        <select id="svc" class="form-select">
          @foreach($provider->services as $s)
            <option value="{{ $s->id }}"
                    data-slots-url="{{ url('/providers/'.$provider->getRouteKey().'/services/'.$s->slug.'/slots.json') }}">
              {{ $s->name }} ({{ $s->duration_minutes }}')
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input id="day" type="date" class="form-control" min="{{ now()->toDateString() }}">
      </div>
      <div class="col-md-3">
        <button id="loadSlots" class="btn btn-primary w-100">Find slots</button>
      </div>
    </div>

    <div id="slots" class="mt-3"></div>
  </div>
  @endif

  <h2 class="h5 mb-2">Active services</h2>
  <ul>
    @foreach($provider->services as $s)
      <li>{{ $s->name }} &mdash; {{ $s->duration_minutes }}' @if(!is_null($s->price)) &mdash; &euro;{{ number_format($s->price,2) }} @endif</li>
    @endforeach
  </ul>

  <p class="mt-4"><a href="{{ route('providers.index') }}">&larr; Back to providers</a></p>
</div>

@php
  $bookRoute = \Illuminate\Support\Facades\Route::has('appointments.store')
      ? route('appointments.store')
      : ( \Illuminate\Support\Facades\Route::has('appointments.book') ? route('appointments.book') : null );
@endphp

<script>
(function() {
  const svcSel = document.getElementById('svc');
  const dayInp = document.getElementById('day');
  const loadBtn = document.getElementById('loadSlots');
  const slotsDiv = document.getElementById('slots');
  const csrf = '{{ csrf_token() }}';
  const bookUrl = {!! json_encode($bookRoute) !!};

  function currentSlotsUrl() {
    const opt = svcSel.options[svcSel.selectedIndex];
    const base = opt.getAttribute('data-slots-url');
    const d = dayInp.value;
    if (!d) return null;
    const sep = base.includes('?') ? '&' : '?';
    return base + sep + 'date=' + encodeURIComponent(d);
  }

  function renderSlots(items) {
    if (!items || !items.length) {
      slotsDiv.innerHTML = '<p class="text-muted">No available slots for this day.</p>';
      return;
    }
    const serviceId = svcSel.value;
    slotsDiv.innerHTML = '<div class="d-flex flex-wrap gap-2"></div>';
    const wrap = slotsDiv.firstElementChild;

    items.forEach(t => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-primary';
      const rawLabel = typeof t === 'string' ? t : (t.label || t.value || t.start_at || t.iso || '');
      // Prefer showing HH:mm for clarity if we have a full timestamp
      if (typeof rawLabel === 'string' && /^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/.test(rawLabel)) {
        btn.textContent = rawLabel.replace('T',' ').substring(11,16);
      } else {
        btn.textContent = rawLabel || 'Slot';
      }
      btn.onclick = async () => {
        if (!bookUrl) { alert('Booking route not available.'); return; }
        // Normalize start_at to exact 'Y-m-d H:i:s'
        let start = (typeof t === 'string') ? t : (t.start_at || t.iso || (dayInp.value ? (dayInp.value + ' ' + (t.value || '00:00')) : ''));
        if (!start) { alert('Missing slot start time.'); return; }
        start = String(start).replace('T',' ').replace('Z','');
        if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/.test(start)) {
          start = start + ':00';
        }
        // build a minimal booking payload; server will do validation & conflict checks
        const payload = {
          _token: csrf,
          provider_id: {{ $provider->id }},
          service_id: serviceId,
          start_at: start // exact 'Y-m-d H:i:s'
        };
        try {
          const resp = await fetch(bookUrl, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf,'Accept':'application/json'},
            body: JSON.stringify(payload),
            credentials: 'same-origin'
          });
          if (resp.status === 401) { window.location.href = '{{ route('login') }}?intended={{ urlencode(request()->fullUrl()) }}'; return; }
          if (resp.redirected) { window.location.href = resp.url; return; }
          const data = await resp.json().catch(()=>({}));
          if (resp.ok) {
            alert('Booked!');
            window.location.href = data.redirect ?? '{{ url('/my/appointments') }}';
          } else {
            alert((data.message || 'Booking failed') + (data.errors ? '\n' + JSON.stringify(data.errors) : ''));
          }
        } catch(e){ alert('Network error'); }
      };
      wrap.appendChild(btn);
    });
  }

  loadBtn.addEventListener('click', async () => {
    const url = currentSlotsUrl();
    if (!url) { alert('Pick a date first'); return; }
    slotsDiv.innerHTML = '<p class="text-muted">Loading...</p>';
    try {
      const r = await fetch(url, {headers:{'Accept':'application/json'}});
      const data = await r.json();
      renderSlots(data.slots || data || []);
    } catch(e) {
      slotsDiv.innerHTML = '<p class="text-danger">Failed to load slots.</p>';
    }
  });
})();
</script>
@endsection
