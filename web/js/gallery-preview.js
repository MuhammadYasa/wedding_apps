/**
 * Gallery Preview - Multiple File Upload Preview
 *
 * Handles preview for multiple file uploads in gallery creation
 */

/**
 * Initialize multiple file preview functionality
 */
function initGalleryPreview() {
  const fileInput = document.querySelector('input[type="file"][multiple]');

  if (!fileInput) {
    console.warn("File input not found");
    return;
  }

  fileInput.addEventListener("change", function (e) {
    handleFileChange(e.target.files);
  });
}

/**
 * Handle file input change event
 * @param {FileList} files - Selected files
 */
function handleFileChange(files) {
  const container = document.getElementById("preview-container");
  const fileCount = document.getElementById("file-count");

  if (!container || !fileCount) {
    console.error("Preview container or file count element not found");
    return;
  }

  // Clear previous previews
  container.innerHTML = "";

  if (files.length > 0) {
    displayFileCount(files.length, fileCount);

    // Show warning if exceeds max files
    if (files.length > 20) {
      showMaxFilesWarning(container);
    }

    // Preview each file (max 20)
    previewFiles(files, container);
  } else {
    showEmptyState(container, fileCount);
  }
}

/**
 * Display file count
 * @param {number} count - Number of files
 * @param {HTMLElement} fileCount - File count display element
 */
function displayFileCount(count, fileCount) {
  fileCount.innerHTML = `<i class="bi bi-check-circle text-success"></i> ${count} foto dipilih`;
}

/**
 * Show max files warning
 * @param {HTMLElement} container - Preview container
 */
function showMaxFilesWarning(container) {
  const warning = document.createElement("div");
  warning.className = "alert alert-warning small";
  warning.innerHTML =
    '<i class="bi bi-exclamation-triangle"></i> Maksimal 20 foto. Hanya 20 foto pertama yang akan diupload.';
  container.appendChild(warning);
}

/**
 * Preview multiple files
 * @param {FileList} files - Files to preview
 * @param {HTMLElement} container - Preview container
 */
function previewFiles(files, container) {
  const maxFiles = Math.min(files.length, 20);

  for (let i = 0; i < maxFiles; i++) {
    const file = files[i];

    if (file.type.match("image.*")) {
      previewSingleFile(file, container);
    }
  }
}

/**
 * Preview single file
 * @param {File} file - File to preview
 * @param {HTMLElement} container - Preview container
 */
function previewSingleFile(file, container) {
  const reader = new FileReader();

  reader.onload = function (e) {
    const previewDiv = createPreviewElement(e.target.result, file);
    container.appendChild(previewDiv);
  };

  reader.readAsDataURL(file);
}

/**
 * Create preview element for a file
 * @param {string} imageSrc - Image source (data URL)
 * @param {File} file - File object
 * @returns {HTMLElement} Preview element
 */
function createPreviewElement(imageSrc, file) {
  const previewDiv = document.createElement("div");
  previewDiv.className = "mb-2 border rounded p-2";

  const img = document.createElement("img");
  img.src = imageSrc;
  img.className = "img-fluid rounded mb-1";
  img.style.maxHeight = "150px";

  const fileInfo = document.createElement("div");
  fileInfo.className = "small text-muted";
  fileInfo.innerHTML = `<i class="bi bi-image"></i> ${file.name} (${(
    file.size / 1024
  ).toFixed(2)} KB)`;

  previewDiv.appendChild(img);
  previewDiv.appendChild(fileInfo);

  return previewDiv;
}

/**
 * Show empty state when no files selected
 * @param {HTMLElement} container - Preview container
 * @param {HTMLElement} fileCount - File count element
 */
function showEmptyState(container, fileCount) {
  container.innerHTML =
    '<p class="text-center text-muted small"><i class="bi bi-images"></i><br>Preview akan muncul setelah memilih gambar</p>';
  fileCount.innerHTML = "";
}

// Initialize on DOM ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initGalleryPreview);
} else {
  initGalleryPreview();
}
