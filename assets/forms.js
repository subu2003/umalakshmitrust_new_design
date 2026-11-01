/* Shared helpers for the public forms (apply, contact, donate).
   - UET.compressImage(file, maxDim, quality): shrink a JPEG/PNG in the browser before upload
   - UET.refill(form, data): put values typed before a server-side error back into the fields
   - UET.fmt(bytes): human-readable size */
window.UET = (function () {
  var MB = 1048576;
  function fmt(b) { return b >= MB ? (b / MB).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; }

  function compressImage(file, maxDim, quality) {
    maxDim = maxDim || 1600; quality = quality || 0.8;
    return new Promise(function (resolve) {
      if (!file || !/^image\/(jpeg|png)$/i.test(file.type) || file.size < 250 * 1024) { resolve(file); return; }
      var done = false;
      function fin(f) { if (!done) { done = true; clearTimeout(timer); resolve(f); } }
      var timer = setTimeout(function () { fin(file); }, 15000); // never block the user on a slow phone
      function draw(img, w, h) {
        try {
          var scale = Math.min(1, maxDim / Math.max(w, h));
          var cw = Math.max(1, Math.round(w * scale)), ch = Math.max(1, Math.round(h * scale));
          var c = document.createElement('canvas'); c.width = cw; c.height = ch;
          var ctx = c.getContext('2d'); ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, cw, ch); ctx.drawImage(img, 0, 0, cw, ch);
          c.toBlob(function (b) {
            if (!b || b.size >= file.size) { fin(file); return; }
            var name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
            fin(new File([b], name, { type: 'image/jpeg', lastModified: Date.now() }));
          }, 'image/jpeg', quality);
        } catch (e) { fin(file); }
      }
      function viaImage() {
        var url = URL.createObjectURL(file), im = new Image();
        im.onload = function () { draw(im, im.naturalWidth, im.naturalHeight); URL.revokeObjectURL(url); };
        im.onerror = function () { URL.revokeObjectURL(url); fin(file); };
        im.src = url;
      }
      if (window.createImageBitmap) {
        createImageBitmap(file, { imageOrientation: 'from-image' })
          .catch(function () { return createImageBitmap(file); })
          .then(function (bm) { draw(bm, bm.width, bm.height); if (bm.close) bm.close(); })
          .catch(viaImage);
      } else { viaImage(); }
    });
  }

  function refill(form, data) {
    if (!form || !data) return;
    Object.keys(data).forEach(function (name) {
      var v = data[name], els = form.elements[name];
      if (!els || typeof v !== 'string') return;
      if (typeof RadioNodeList !== 'undefined' && els instanceof RadioNodeList) {
        if (els[0] && els[0].type === 'checkbox') { [].forEach.call(els, function (c) { c.checked = c.value === v; }); }
        else { els.value = v; }
        return;
      }
      if (els.type === 'file') return;
      if (els.type === 'checkbox') { els.checked = v !== ''; return; }
      if (els.type === 'radio') { els.checked = els.value === v; return; }
      els.value = v;
    });
  }

  return { fmt: fmt, compressImage: compressImage, refill: refill, MB: MB };
})();
