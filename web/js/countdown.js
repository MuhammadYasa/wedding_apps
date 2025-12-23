/**
 * Countdown Timer for Wedding Invitation
 */

let countdownInterval;

/**
 * Initialize countdown timer
 * @param {number} eventTimestamp - Event date in milliseconds
 */
function initCountdown(eventTimestamp) {
  // Clear any existing interval
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }

  // Update countdown immediately
  updateCountdown(eventTimestamp);

  // Update every second
  countdownInterval = setInterval(function () {
    updateCountdown(eventTimestamp);
  }, 1000);
}

/**
 * Update countdown display
 * @param {number} eventTimestamp - Event date in milliseconds
 */
function updateCountdown(eventTimestamp) {
  const now = new Date().getTime();
  const distance = eventTimestamp - now;

  // Get elements
  const daysElement = document.getElementById("days");
  const hoursElement = document.getElementById("hours");
  const minutesElement = document.getElementById("minutes");
  const secondsElement = document.getElementById("seconds");

  // Check if elements exist
  if (!daysElement || !hoursElement || !minutesElement || !secondsElement) {
    console.error("Countdown elements not found");
    return;
  }

  // If event has passed
  if (distance < 0) {
    clearInterval(countdownInterval);
    daysElement.textContent = "0";
    hoursElement.textContent = "0";
    minutesElement.textContent = "0";
    secondsElement.textContent = "0";

    // Optional: Show "Event has started" message
    const countdownTimer = document.getElementById("countdown-timer");
    if (countdownTimer) {
      countdownTimer.innerHTML =
        '<div class="text-center"><h4>Acara Telah Dimulai! 🎉</h4></div>';
    }
    return;
  }

  // Calculate time units
  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor(
    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Update display with animation
  animateNumber(daysElement, days);
  animateNumber(hoursElement, hours);
  animateNumber(minutesElement, minutes);
  animateNumber(secondsElement, seconds);
}

/**
 * Animate number change
 * @param {HTMLElement} element - Element to update
 * @param {number} newValue - New value to display
 */
function animateNumber(element, newValue) {
  const currentValue = parseInt(element.textContent) || 0;

  if (currentValue !== newValue) {
    // Add animation class
    element.classList.add("countdown-update");

    // Update value
    element.textContent = newValue.toString().padStart(2, "0");

    // Remove animation class after animation completes
    setTimeout(function () {
      element.classList.remove("countdown-update");
    }, 300);
  }
}

/**
 * Copy text to clipboard
 * @param {string} text - Text to copy
 */
function copyToClipboard(text) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard
      .writeText(text)
      .then(function () {
        showNotification("Link berhasil disalin!", "success");
      })
      .catch(function (err) {
        console.error("Failed to copy:", err);
        fallbackCopyToClipboard(text);
      });
  } else {
    fallbackCopyToClipboard(text);
  }
}

/**
 * Fallback copy method for older browsers
 * @param {string} text - Text to copy
 */
function fallbackCopyToClipboard(text) {
  const textArea = document.createElement("textarea");
  textArea.value = text;
  textArea.style.position = "fixed";
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.opacity = "0";
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    document.execCommand("copy");
    showNotification("Link berhasil disalin!", "success");
  } catch (err) {
    console.error("Failed to copy:", err);
    showNotification("Gagal menyalin link", "error");
  }

  document.body.removeChild(textArea);
}

/**
 * Show notification message
 * @param {string} message - Message to show
 * @param {string} type - Type of notification (success, error, info)
 */
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `alert alert-${
    type === "success" ? "success" : type === "error" ? "danger" : "info"
  } notification-toast`;
  notification.style.position = "fixed";
  notification.style.top = "20px";
  notification.style.right = "20px";
  notification.style.zIndex = "9999";
  notification.style.minWidth = "200px";
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

  // Add to body
  document.body.appendChild(notification);

  // Animate in
  setTimeout(function () {
    notification.style.animation = "slideInRight 0.3s ease-out";
  }, 10);

  // Remove after 3 seconds
  setTimeout(function () {
    notification.style.animation = "slideOutRight 0.3s ease-in";
    setTimeout(function () {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}

// Add CSS animations for notifications
if (!document.getElementById("notification-styles")) {
  const style = document.createElement("style");
  style.id = "notification-styles";
  style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .countdown-update {
            animation: pulse 0.3s ease-in-out;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
    `;
  document.head.appendChild(style);
}

// Clean up on page unload
window.addEventListener("beforeunload", function () {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});
