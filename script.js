// Simple client-side auth + travel planner logic
const $ = s=>document.querySelector(s)
const $$ = s=>document.querySelectorAll(s)

// Modal handling (separate modals)
const btnLogin = $('#btn-login'), btnSignup = $('#btn-signup')
const loginModal = $('#loginModal'), signupModal = $('#signupModal'), forgotModal = $('#forgotModal')
const closeLogin = $('#closeLogin'), closeSignup = $('#closeSignup'), closeForgot = $('#closeForgot')
const loginForm = $('#loginForm'), signupForm = $('#signupForm'), forgotForm = $('#forgotForm')

function openModal(mod){ if(mod) mod.classList.remove('hidden') }
function closeModal(mod){ if(mod) mod.classList.add('hidden') }
function closeAllModals(){ [loginModal, signupModal, forgotModal].forEach(m=>m && m.classList.add('hidden')) }

btnLogin.addEventListener('click', ()=>{ if(getCurrent()){ if(confirm('Logout?')) logout() } else openModal(loginModal) })
btnSignup.addEventListener('click', ()=>{ if(getCurrent()){ if(confirm('Logout?')) logout() } else openModal(signupModal) })

closeLogin && closeLogin.addEventListener('click', ()=>closeModal(loginModal))
closeSignup && closeSignup.addEventListener('click', ()=>closeModal(signupModal))
closeForgot && closeForgot.addEventListener('click', ()=>closeModal(forgotModal))

// open forgot from login
const openForgotFromLogin = $('#openForgotFromLogin')
if(openForgotFromLogin){ openForgotFromLogin.addEventListener('click', (e)=>{ e.preventDefault(); closeModal(loginModal); openModal(forgotModal) }) }

// Local storage based users
function getUsers(){return JSON.parse(localStorage.getItem('users')||'{}')}
function saveUsers(u){localStorage.setItem('users',JSON.stringify(u))}
function getCurrent(){return JSON.parse(localStorage.getItem('currentUser')||'null')}
function setCurrent(email){localStorage.setItem('currentUser', JSON.stringify(email))}
function logout(){localStorage.removeItem('currentUser'); updateAuthButtons()}

function updateAuthButtons(){
  const cur = getCurrent()
  if(cur){btnLogin.textContent = 'Logout'; btnSignup.textContent = cur}
  else{btnLogin.textContent='Login'; btnSignup.textContent='Sign up'}
}

// Signup
$('#signupSubmit').addEventListener('click', ()=>{
  const name = $('#signupName').value.trim()
  const email = $('#signupEmail').value.trim().toLowerCase()
  const pw = $('#signupPassword').value
  if(!email || !pw){alert('Please fill email and password');return}
  const users = getUsers()
  if(users[email]){alert('Account already exists. Please login')}
  else{
    users[email] = {name, email, pw}; saveUsers(users); setCurrent(email); updateAuthButtons(); closeModal(signupModal); alert('Account created and logged in!')
  }
})

// Login
$('#loginSubmit').addEventListener('click', ()=>{
  const email = $('#loginEmail').value.trim().toLowerCase()
  const pw = $('#loginPassword').value
  const users = getUsers()
  if(users[email] && users[email].pw===pw){ setCurrent(email); updateAuthButtons(); closeModal(loginModal); alert('Logged in!') }
  else alert('Invalid credentials')
})

// Logout via login button when already logged in
btnLogin.addEventListener('click', ()=>{ if(getCurrent()){ if(confirm('Logout?')) logout() }})

// Forgot password (simulated)
let lastResetCode = null, lastResetEmail = null
$('#forgotSend').addEventListener('click', ()=>{
  const email = $('#forgotEmail').value.trim().toLowerCase()
  const users = getUsers()
  if(!users[email]){alert('No account with that email')}
  else{lastResetCode = Math.floor(100000 + Math.random()*900000).toString(); lastResetEmail = email; $('#resetPanel').classList.remove('hidden'); alert('Reset code (simulated): '+lastResetCode)}
})
$('#resetSubmit').addEventListener('click', ()=>{
  const code = $('#resetCode').value.trim(), pw = $('#resetNew').value
  if(code===lastResetCode && lastResetEmail){ const users = getUsers(); users[lastResetEmail].pw = pw; saveUsers(users); alert('Password updated!'); lastResetCode=null; lastResetEmail=null; $('#resetPanel').classList.add('hidden'); closeModal(forgotModal) }
  else alert('Invalid code')
})

updateAuthButtons()

// Planner logic
const prices = {economy:50, standard:90, luxury:220} // per person per day
const addOnPrices = {guide:50, meals:30, transfer:20} // flat per traveler

$('#calcBtn').addEventListener('click', calculateBudget)
$('#bookBtn').addEventListener('click', ()=>{
  if(!getCurrent()){ openModal(loginModal); alert('Please login to complete booking') }
  else { if(calculateBudget()){ alert('Booking confirmed! Details saved to Local Storage (simulated)') } }
})

// --- Dashboard sample data & rendering ---
const samplePOIs = [
  {id:1, name:'Paris, France', short:'Eiffel & museums', price:120, lat:48.8566, lon:2.3522},
  {id:2, name:'Kyoto, Japan', short:'Temples & cuisine', price:95, lat:35.0116, lon:135.7681},
  {id:3, name:'Bali, Indonesia', short:'Beaches & rice fields', price:80, lat:-8.4095, lon:115.1889},
  {id:4, name:'New York, USA', short:'City that never sleeps', price:140, lat:40.7128, lon:-74.0060}
]

let map = null
let markers = {}

function initMap(){
  if(typeof L === 'undefined') return
  map = L.map('map', {scrollWheelZoom:false, zoomControl:true}).setView([20,0],2)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap contributors'}).addTo(map)

  samplePOIs.forEach(p=>{
    const m = L.marker([p.lat, p.lon]).addTo(map).bindPopup(`<strong>${p.name}</strong><div class="muted">${p.short}</div>`)
    markers[p.id] = m
    m.on('click', ()=>{
      $('#destination').value = p.name
      document.querySelector('#currentAdventure .muted').textContent = p.name
      document.querySelector('#startDate').focus()
    })
  })

  setTimeout(()=>{ try{ map.invalidateSize() }catch(e){} },300)
  window.addEventListener('resize', ()=>{ try{ map.invalidateSize() }catch(e){} })
}

function renderPOIs(){
  const el = document.querySelector('.poi-grid')
  if(!el) return
  el.innerHTML = ''
  samplePOIs.forEach(p=>{
    const d = document.createElement('div')
    d.className = 'poi'
    d.innerHTML = `<div class="thumb"></div><div class="poi-body"><strong>${p.name}</strong><div class="muted">${p.short}</div><div class="poi-actions"><button class="btn-outline selectPoi" data-id="${p.id}">Select</button> <span class="price">$${p.price}/day</span></div></div>`
    el.appendChild(d)
  })
  document.querySelectorAll('.selectPoi').forEach(b=>b.addEventListener('click', (e)=>{
    const id = Number(e.currentTarget.dataset.id)
    const poi = samplePOIs.find(x=>x.id===id)
    if(poi){
      $('#destination').value = poi.name
      $('#package').value = 'standard'
      document.querySelector('#currentAdventure .muted').textContent = poi.name
      // pan and open popup if map exists
      if(map && markers[id]){ map.setView([poi.lat, poi.lon], 10, {animate:true}); markers[id].openPopup() }
      // move focus to planner
      document.querySelector('#startDate').focus()
    }
  }))
}

// Todo list
let todos = [ {id:1,text:'Book flights'}, {id:2,text:'Check visa requirements'} ]
function renderTodos(){
  const el = $('#todoList')
  if(!el) return
  el.innerHTML = ''
  todos.forEach(t=>{
    const li = document.createElement('li')
    li.innerHTML = `<span>${t.text}</span><button class="remove" data-id="${t.id}">✕</button>`
    el.appendChild(li)
  })
  document.querySelectorAll('#todoList .remove').forEach(b=>b.addEventListener('click', (e)=>{
    const id = Number(e.currentTarget.dataset.id)
    todos = todos.filter(t=>t.id!==id)
    renderTodos()
  }))
}

$('#addTodo').addEventListener('click', ()=>{
  const v = $('#newTodo').value.trim()
  if(!v) return
  todos.push({id:Date.now(), text:v})
  $('#newTodo').value = ''
  renderTodos()
})

// map placeholder interaction
$('#mapPlaceholder').addEventListener('click', ()=>{ alert('Map is a placeholder — select a POI instead or enable map integration later') })

// initial render
renderPOIs(); renderTodos();

function daysBetween(a,b){ const d1=new Date(a), d2=new Date(b); const diff=(d2-d1)/(1000*60*60*24); return Math.max(0, Math.ceil(diff)) }

function calculateBudget(){
  const dest = $('#destination').value.trim()
  const start = $('#startDate').value
  const end = $('#endDate').value
  const travelers = Number($('#travelers').value)
  const pack = $('#package').value
  if(!dest||!start||!end||!travelers){alert('Please fill all fields'); return false}
  const days = daysBetween(start,end) || 1
  const basePerPerson = prices[pack]
  let subtotal = basePerPerson * days * travelers
  const selectedAddons = Array.from($$('.addon:checked')).map(i=>i.value)
  let addonsTotal = 0
  selectedAddons.forEach(a=>{ addonsTotal += addOnPrices[a]*travelers })
  const tax = Math.round((subtotal+addonsTotal)*0.08)
  const total = subtotal + addonsTotal + tax
  $('#summary').innerHTML = `${travelers} traveler(s) to <strong>${dest}</strong> for <strong>${days} day(s)</strong>. Package: <strong>${pack}</strong>. Add-ons: ${selectedAddons.length?selectedAddons.join(', '):'None'}. Tax: $${tax}`
  $('#total').textContent = '$'+total
  $('#result').classList.remove('hidden')
  return true
}

// small helper: prefill demo destination
$('#destination').addEventListener('focus', ()=>{if(!$('#destination').value) $('#destination').value='Bali'})

// Accessibility: close modal on escape
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeAllModals() })

// Defensive: ensure no auth modal is visible on page load
document.addEventListener('DOMContentLoaded', ()=>{
  [loginModal, signupModal, forgotModal].forEach(m=>{ if(m){ m.classList.add('hidden'); m.style.display = ''; } })
  closeAllModals()
  console.log('Modal states (login, signup, forgot):', [loginModal?.classList.contains('hidden'), signupModal?.classList.contains('hidden'), forgotModal?.classList.contains('hidden')])

  // Optional background video: set body data-bg-video="URL" to enable. Lazy-load on desktop only.
  const videoUrl = document.body.dataset.bgVideo || ''
  const bgVideo = document.getElementById('bgVideo')
  if(videoUrl && bgVideo && window.innerWidth > 700){
    bgVideo.src = videoUrl
    bgVideo.classList.remove('hidden')
    bgVideo.load()
    bgVideo.play().catch(()=>{})
    const grad = document.querySelector('.bg-gradient')
    if(grad) grad.style.opacity = '0.35'
  }

  // Optional background image: set body data-bg-image="PATH" (e.g., assets/bg-trip.jpg). Prefer image on desktop.
  const imgUrl = document.body.dataset.bgImage || ''
  const bgImageEl = document.querySelector('.bg-image')

  function loadImage(url){
    return new Promise((resolve)=>{
      const img = new Image()
      img.onload = ()=>resolve(true)
      img.onerror = ()=>resolve(false)
      img.src = url
    })
  }

  ;(async ()=>{
    if(imgUrl && bgImageEl && window.innerWidth > 480){
      const ok = await loadImage(imgUrl)
      if(ok){
        bgImageEl.style.backgroundImage = `url('${imgUrl}')`
        bgImageEl.classList.remove('hidden')
        const grad = document.querySelector('.bg-gradient')
        if(grad) grad.style.opacity = '0.45'
        console.log('Background image applied:', imgUrl)
      } else {
        console.warn('Local background image not found:', imgUrl)
        // fallback to a free stock travel image
        const fallback = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80'
        const ok2 = await loadImage(fallback)
        if(ok2){ bgImageEl.style.backgroundImage = `url('${fallback}')`; bgImageEl.classList.remove('hidden'); const grad = document.querySelector('.bg-gradient'); if(grad) grad.style.opacity = '0.45' }
        else { console.warn('Fallback background also failed to load') }
      }
    } else if(bgImageEl){
      bgImageEl.classList.add('hidden')
    }

    // Defensive adjustments for small screens
    if(window.innerWidth <= 700){ const grad = document.querySelector('.bg-gradient'); if(grad) grad.style.filter = 'blur(12px)'; const pl = document.querySelector('.plane'); if(pl) pl.style.display = 'none' }
  })()
})

// End of script.js