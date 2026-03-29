document.addEventListener('DOMContentLoaded', () => {
  const id = 'product_image';
  const fileInput = document.getElementById(id);
  const dropZone = document.getElementById('drop-zone-' + id);
  const previewContainer = document.getElementById('preview-container-' + id);
  const previewImg = document.getElementById('preview-img-' + id);
  const prompt = document.getElementById('prompt-' + id);

  // Hidden inputs for both X and Y
  const inputX = document.getElementById('pos-x-' + id);
  const inputY = document.getElementById('pos-y-' + id);

  if (!dropZone || !fileInput || !previewContainer || !previewImg) return;

  // ==============================
  // STATE
  // ==============================
  let isDragging = false;
  let startX = 0,
    startY = 0;
  let currentXPos = 50,
    currentYPos = 50;

  // ==============================
  // FILE SELECT (CLICK)
  // ==============================
  dropZone.addEventListener('click', (e) => {
    // If the image is already there, don't trigger the file input via click
    // This forces the user to drag the image instead of accidentally replacing it
    if (!previewContainer.classList.contains('hidden')) {
      // Only allow click-to-upload if they click OUTSIDE the image area
      // (usually the border/padding)
      if (e.target !== dropZone) return;
    }
    fileInput.click();
  });

  // ==============================
  // FILE CHANGE (UPLOAD)
  // ==============================
  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;

      previewContainer.classList.remove('hidden');
      previewContainer.style.display = 'block';
      prompt.classList.add('hidden');
      prompt.style.display = 'none';

      dropZone.classList.remove('border-dashed');
      dropZone.classList.add('border-solid', 'border-primary');

      // Reset positions to center
      currentXPos = 50;
      currentYPos = 50;
      previewImg.style.objectPosition = `${currentXPos}% ${currentYPos}%`;
      previewContainer.style.cursor = 'move';
    };

    reader.readAsDataURL(file);
  });

  // ==============================
  // DRAG TO REPOSITION (360°)
  // ==============================
  previewImg.addEventListener('mousedown', (e) => {
    e.stopPropagation(); // Stops the drop-zone click event
    e.preventDefault(); // Prevents ghost image drag

    isDragging = true;
    startX = e.clientX;
    startY = e.clientY;

    previewContainer.style.cursor = 'grabbing';
  });

  window.addEventListener('mousemove', (e) => {
    if (!isDragging) return;

    const deltaX = e.clientX - startX;
    const deltaY = e.clientY - startY;

    // Sensitivity: 0.1 for precise, 0.2 for fast
    let newX = currentXPos + deltaX * 0.15;
    let newY = currentYPos + deltaY * 0.15;

    // Constrain 0-100%
    newX = Math.max(0, Math.min(100, newX));
    newY = Math.max(0, Math.min(100, newY));

    previewImg.style.objectPosition = `${newX}% ${newY}%`;

    // Update hidden inputs if they exist
    if (inputX) inputX.value = newX;
    if (inputY) inputY.value = newY;
  });

  window.addEventListener('mouseup', () => {
    if (!isDragging) return;

    isDragging = false;
    previewContainer.style.cursor = 'move';

    // Store final positions for next drag
    const posArray = previewImg.style.objectPosition.split(' ');
    if (posArray.length > 1) {
      currentXPos = parseFloat(posArray);
      currentYPos = parseFloat(posArray);
    }
  });

  // ==============================
  // DRAG & DROP UPLOAD
  // ==============================
  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('bg-slate-50', 'border-primary/50');
  });

  dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('bg-slate-50', 'border-primary/50');
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('bg-slate-50', 'border-primary/50');

    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      fileInput.dispatchEvent(new Event('change'));
    }
  });
});
