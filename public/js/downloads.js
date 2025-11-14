(function () {
  const tasks = new Map();
  let container, header, body, timer;
  const POLL_INTERVAL_MS = 500; // update UI during streaming

  function ensureUI() {
    if (container) return;
    container = document.createElement('div');
    container.id = 'dl-popup';
    container.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:999999;width:320px;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;';
    container.innerHTML = `
      <div id="dl-head" style="background:#111827;color:#fff;border-radius:8px 8px 0 0;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
        <div style="font-weight:600;font-size:14px;">Tải xuống</div>
        <div id="dl-count" style="opacity:.85;font-size:12px;">0</div>
      </div>
      <div id="dl-body" style="background:#fff;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px;max-height:360px;overflow:auto;display:block;"></div>
    `;
    document.body.appendChild(container);
    header = container.querySelector('#dl-head');
    body = container.querySelector('#dl-body');
    header.addEventListener('click', () => {
      body.style.display = body.style.display === 'none' ? 'block' : 'none';
    });
  }

  function formatProgress(task) {
    const p = Math.max(0, Math.min(100, task.progress || 0));
    return `<div style="height:8px;background:#f3f4f6;border-radius:9999px;overflow:hidden;">
      <div style="height:8px;width:${p}%;background:${task.status==='failed'?'#dc2626':(task.status==='completed'?'#059669':'#2563eb')};"></div>
    </div>`;
  }

  function render() {
    ensureUI();
    const countEl = container.querySelector('#dl-count');
    countEl.textContent = tasks.size ? `${tasks.size} tác vụ` : 'Không có tác vụ';
    body.innerHTML = '';
    Array.from(tasks.values()).forEach(task => {
      const row = document.createElement('div');
      row.style.cssText = 'padding:12px;border-top:1px solid #f3f4f6;';
      const status = task.status || 'processing';
      const statusText = status==='completed'?'Đã hoàn tất':(status==='failed'?'Lỗi':(status==='waiting'?'Đang chờ':'Đang tải xuống'));
      row.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;gap:8px;">
          <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;" title="${task.name||'Tệp'}">${task.name||'Tệp'}</div>
          <div style="font-size:12px;color:#374151;">${statusText}</div>
        </div>
        ${formatProgress(task)}
        <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
          ${status==='completed'?`<a href="#" data-open="${task.id}" style="background:#111827;color:#fff;border-radius:6px;padding:6px 10px;font-size:12px;text-decoration:none;">Mở</a>`:''}
          <button data-dismiss="${task.id}" style="margin-left:auto;background:#e5e7eb;color:#111827;border:none;border-radius:6px;padding:6px 10px;font-size:12px;cursor:pointer;">Ẩn</button>
        </div>`;
      row.querySelector('button[data-dismiss]')?.addEventListener('click', () => {
        tasks.delete(task.id);
        render();
      });
      row.querySelector('a[data-open]')?.addEventListener('click', (e) => {
        e.preventDefault();
        if (task.downloadUrl) window.open(task.downloadUrl);
      });
      body.appendChild(row);
    });
  }

  function schedule() {
    if (timer) clearTimeout(timer);
    if (!tasks.size) return;
    timer = setTimeout(render, POLL_INTERVAL_MS);
  }

  function parseFilename(contentDisposition, fallback) {
    try {
      if (!contentDisposition) return fallback;
      const m = contentDisposition.match(/filename=(?:"([^"]+)"|([^;]+))/i);
      return decodeURIComponent((m && (m[1]||m[2])||fallback).trim());
    } catch (_) { return fallback; }
  }

  async function streamToFile(response, fileName, task) {
    const reader = response.body.getReader();
    const total = Number(response.headers.get('content-length')) || 0;
    const chunks = [];
    let received = 0;
    task.status = 'processing';
    render();

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      chunks.push(value);
      received += value.length;
      if (total) task.progress = Math.floor((received / total) * 100);
      render();
    }

    const blob = new Blob(chunks, { type: 'application/zip' });
    const url = URL.createObjectURL(blob);
    task.downloadUrl = url;
    // trigger save
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName || 'download.zip';
    document.body.appendChild(a);
    a.click();
    a.remove();
    task.status = 'completed';
    task.progress = 100;
    render();
  }

  window.startDownloadWithPopup = async function ({ endpoint, setId, setName, paymentMethod = 'coins' }) {
    ensureUI();
    const id = `${Date.now()}_${Math.random().toString(36).slice(2)}`;
    const task = { id, name: setName || 'Tệp', progress: 0, status: 'waiting' };
    tasks.set(id, task);
    render();

    try {
      // Đảm bảo paymentMethod luôn có giá trị (không phải undefined)
      const finalPaymentMethod = paymentMethod || 'coins';
      const requestBody = { 
        user_confirmed: true,
        payment_method: finalPaymentMethod
      };
      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Accept': 'application/zip,application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content || ''
        },
        body: JSON.stringify(requestBody)
      });

      const cd = res.headers.get('content-disposition') || '';
      const ct = res.headers.get('content-type') || '';
      const filename = parseFilename(cd, (setName||'download') + '.zip');

      if (res.ok && ct.includes('application/zip')) {
        await streamToFile(res, filename, task);
        return;
      }

      // If JSON (error)
      const json = await res.json().catch(() => ({}));
      task.status = 'failed';
      task.message = json.message || 'Không thể tải file';
      task.progress = 0;
      render();
    } catch (e) {
      task.status = 'failed';
      task.message = 'Mạng lỗi';
      render();
    }
  };
})();
