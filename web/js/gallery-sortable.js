/**
 * Gallery Sortable - Drag & Drop Gallery Management
 *
 * Handles drag and drop sorting for gallery items with AJAX save
 */

/**
 * Initialize sortable gallery
 * @param {string} sortUrl - URL endpoint for saving sort order
 */
function initGallerySortable(sortUrl) {
  const el = document.getElementById("gallery-sortable");

  if (!el) {
    console.warn("Gallery sortable element not found");
    return;
  }

  const sortable = Sortable.create(el, {
    animation: 150,
    ghostClass: "sortable-ghost",
    onEnd: function (evt) {
      saveSortOrder(sortUrl);
    },
  });
}

/**
 * Save gallery sort order via AJAX
 * @param {string} sortUrl - URL endpoint for saving
 */
function saveSortOrder(sortUrl) {
  const order = [];

  // Collect all gallery item IDs in current order
  document.querySelectorAll(".gallery-item").forEach(function (item) {
    order.push(item.getAttribute("data-id"));
  });

  // Send AJAX request
  fetch(sortUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "X-CSRF-Token": yii.getCsrfToken(),
    },
    body: "order=" + JSON.stringify(order),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateOrderNumbers();
        showNotification("Urutan foto berhasil diperbarui", "success");
      } else {
        showNotification("Gagal menyimpan urutan", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Terjadi kesalahan saat menyimpan", "error");
    });
}

/**
 * Update order numbers in UI after sorting
 */
function updateOrderNumbers() {
  document.querySelectorAll(".gallery-item").forEach(function (item, index) {
    const orderElement = item.querySelector(".gallery-item-order");
    if (orderElement) {
      orderElement.innerHTML =
        '<i class="bi bi-arrows-move"></i> Urutan: ' + (index + 1);
    }
  });
}

/**
 * Show notification message
 * @param {string} message - Message to display
 * @param {string} type - Notification type (success, error, info)
 */
function showNotification(message, type = "info") {
  const alertClass =
    type === "success"
      ? "alert-success"
      : type === "error"
      ? "alert-danger"
      : "alert-info";

  const notification = document.createElement("div");
  notification.className = `alert ${alertClass} notification-toast`;
  notification.style.position = "fixed";
  notification.style.top = "20px";
  notification.style.right = "20px";
  notification.style.zIndex = "9999";
  notification.style.minWidth = "250px";
  notification.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
  notification.innerHTML = `
        <i class="bi bi-${
          type === "success"
            ? "check-circle"
            : type === "error"
            ? "exclamation-circle"
            : "info-circle"
        }"></i>
        ${message}
    `;

  document.body.appendChild(notification);

  // Animate in
  setTimeout(() => {
    notification.style.animation = "slideInRight 0.3s ease-out";
  }, 10);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOutRight 0.3s ease-in";
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}
