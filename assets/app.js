const $=s=>document.querySelector(s),T=$('meta[name=csrf]').content;
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const api=(a,d)=>fetch('api.php?a='+a,{method:'POST',headers:{'X-CSRF':T,'Content-Type':'application/json'},body:JSON.stringify(d||{})}).then(r=>r.json());
const W=c=>c===0?{t:'Cerah',i:'☀️',k:'sun',q:'lagu pop ceria'}:c<=3?{t:'Berawan',i:'⛅',k:'cloud',q:'indie santai'}:
 c<=48?{t:'Berkabut',i:'🌫️',k:'fog',q:'ambient chill'}:(c<=67||(c>=80&&c<=82))?{t:'Hujan',i:'🌧️',k:'rain',q:'lofi hujan'}:
 c<=77?{t:'Salju',i:'❄️',k:'snow',q:'piano tenang'}:{t:'Badai petir',i:'⛈️',k:'storm',q:'rock energik'};
let favs=[],tracks=[],tab=1,mood='',recommendationQuery='lofi santai',searchTerm='',musicLoading=false,musicError='',musicRequest=0;
const au=$('#au');

async function cuaca(kota,simpan){
  $('#cuaca').innerHTML='<p class="mute">Memuat cuaca…</p>';
  try{
    const g=await(await fetch('https://geocoding-api.open-meteo.com/v1/search?count=1&language=id&name='+encodeURIComponent(kota))).json();
    if(!g.results)throw Error('Kota tidak ditemukan. Coba ejaan lain.');
    const {latitude:la,longitude:lo,name,country}=g.results[0];
    const w=await(await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${la}&longitude=${lo}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&forecast_days=3`)).json();
    const c=w.current,k=W(c.weather_code);
    document.body.className=k.k;
    $('#cuaca').innerHTML=`<div class="weather-current">
      <div class="weather-place"><span class="weather-label">CUACA SAAT INI</span><span class="condition">${k.t}</span></div>
      <div class="big"><span class="ico">${k.i}</span><p class="temp">${Math.round(c.temperature_2m)}°</p></div>
      <h2>${esc(name)}, ${esc(country)}</h2>
      <div class="stats">
        <div class="weather-stat"><span aria-hidden="true">⌁</span><p><small>Kelembapan</small><b>${c.relative_humidity_2m}%</b></p></div>
        <div class="weather-stat"><span aria-hidden="true">↗</span><p><small>Angin</small><b>${c.wind_speed_10m} km/j</b></p></div>
      </div>
     </div>
     <div class="forecast-heading"><span>Beberapa hari ke depan</span><span>3 HARI</span></div>
     <div class="fc">${w.daily.time.map((d,i)=>{const x=W(w.daily.weather_code[i]);
      return `<div class="forecast-day"><b>${i?new Date(d).toLocaleDateString('id-ID',{weekday:'short'}):'Hari ini'}</b><i>${x.i}</i><span>${Math.round(w.daily.temperature_2m_min[i])}° <small>/</small> ${Math.round(w.daily.temperature_2m_max[i])}°</span></div>`}).join('')}</div>`;
    mood=`Pilihan untuk cuaca ${k.t.toLowerCase()}`;
    recommendationQuery=k.q;
    if(simpan){await api('hist_add',{kota:name});await Promise.all([riwayat(),muatStats()]);}
    if(!searchTerm)musik(recommendationQuery);
  }catch(e){$('#cuaca').innerHTML=`<p class="err">${esc(e.message||'Gagal memuat data.')}</p>`;}
}
async function musik(q,pencarian=false){
  const request=++musicRequest;
  searchTerm=pencarian?q:'';
  musicLoading=true;musicError='';tracks=[];render();
  try{
    const response=await fetch('https://itunes.apple.com/search?media=music&entity=song&limit=12&country=ID&term='+encodeURIComponent(q));
    if(!response.ok)throw Error('Layanan musik sedang tidak bisa dijangkau. Coba lagi sebentar.');
    const r=await response.json();
    if(request!==musicRequest)return;
    tracks=r.results.map(s=>({track_id:s.trackId,title:s.trackName,artist:s.artistName,art:s.artworkUrl100,preview:s.previewUrl}));
  }catch(e){
    if(request===musicRequest)musicError=e.message||'Pencarian musik gagal. Periksa koneksi internet lalu coba lagi.';
  }finally{
    if(request===musicRequest){musicLoading=false;render();}
  }
}
function pilihLagu(li){
  document.querySelectorAll('li.on').forEach(x=>x.classList.remove('on'));li.classList.add('on');
  const data=tab===1?tracks:favs,s=data[li.dataset.i];
  if(!s.preview){$('#music-status').textContent='Pratinjau lagu ini tidak tersedia.';return;}
  au.src=s.preview;au.play();$('#np').textContent=`${s.title} · ${s.artist}`;$('#player').style.display='flex';
}
function render(){
  const data=tab===1?tracks:favs;
  $('#mood').textContent=tab===2?'Lagu yang kamu simpan':searchTerm?`Hasil pencarian untuk “${searchTerm}”`:mood;
  $('#music-status').textContent=tab===2?'':musicLoading?'Sedang mencari lagu…':musicError?musicError:
    tab===1&&searchTerm&&!data.length?'Tidak ada lagu yang cocok. Coba kata kunci lain.':'';
  $('#list').innerHTML=data.length?data.map((s,i)=>{const f=favs.some(x=>x.track_id==s.track_id);
    return `<li data-i="${i}" tabindex="0" aria-label="Putar ${esc(s.title)} oleh ${esc(s.artist)}"><button class="play-track" aria-label="Putar ${esc(s.title)}">▶</button>
    <img src="${esc(s.art)}" alt=""><div class="track-info"><b>${esc(s.title)}</b><small>${esc(s.artist)}</small></div>
    <button class="heart" data-h="${i}" aria-label="${f?'Hapus dari':'Tambahkan ke'} favorit" title="${f?'Hapus dari':'Tambahkan ke'} favorit">${f?'♥':'♡'}</button></li>`}).join(''):
    tab===2?'<li class="empty-state">Belum ada lagu favorit. Simpan lagu yang kamu suka untuk menemukannya lagi.</li>':'';
}
$('#list').onclick=async e=>{
  const h=e.target.closest('[data-h]'),li=e.target.closest('li[data-i]');if(!li)return;
  const data=tab===1?tracks:favs,s=data[li.dataset.i];
  if(h){const f=favs.some(x=>x.track_id==s.track_id);
    await api(f?'fav_del':'fav_add',s);await muatFav();await muatStats();render();return;}
  pilihLagu(li);
};
$('#list').onkeydown=e=>{
  if(e.target.matches('li[data-i]')&&(e.key==='Enter'||e.key===' ')){e.preventDefault();pilihLagu(e.target);}
};
$('#t1').onclick=()=>{tab=1;$('#t1').className='on';$('#t2').className='';musik(recommendationQuery);};
$('#t2').onclick=async()=>{tab=2;searchTerm='';$('#t2').className='on';$('#t1').className='';await muatFav();render();};
$('#music-search').onsubmit=e=>{
  e.preventDefault();const query=$('#music-query').value.trim();
  if(!query)return;
  tab=1;$('#t1').className='on';$('#t2').className='';
  musik(query,true);
};
async function muatFav(){favs=await api('fav_list');}
async function muatStats(){
  const counts=await api('stats');
  $('#favorite-count').textContent=counts.favorites;
  $('#city-count').textContent=counts.cities;
}
async function riwayat(){
  const h=await api('hist_list');
  $('#chips').innerHTML=h.map(k=>`<button>${esc(k)}</button>`).join('');
  $('#chips').querySelectorAll('button').forEach(b=>b.onclick=()=>cuaca(b.textContent,false));
  return h;
}
$('#f').onsubmit=e=>{e.preventDefault();const k=$('#q').value.trim();if(k)cuaca(k,true);};
(async()=>{await muatFav();await muatStats();const h=await riwayat();cuaca(h[0]||'Jakarta',false);})();
