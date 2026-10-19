// open and close the menu on small screens
var menuToggle = document.getElementById("menuToggle");
var navLinks = document.getElementById("navLinks");

if (menuToggle && navLinks) {
    menuToggle.addEventListener("click", function () {
        navLinks.classList.toggle("open");
    });
}

// calculate the total price on the booking page
var startInput = document.getElementById("start_date");
var endInput = document.getElementById("end_date");
var bookingForm = document.getElementById("bookingForm");

function updateTotal() {
    var pricePerDay = parseFloat(bookingForm.dataset.price);
    var daysText = document.getElementById("totalDays");
    var totalText = document.getElementById("totalPrice");

    if (startInput.value == "" || endInput.value == "") {
        daysText.textContent = "-";
        totalText.textContent = "-";
        return;
    }

    var start = new Date(startInput.value);
    var end = new Date(endInput.value);
    var days = Math.round((end - start) / (1000 * 60 * 60 * 24));

    if (days < 1) {
        daysText.textContent = "Return date must be after pick-up date";
        totalText.textContent = "-";
        return;
    }

    daysText.textContent = days + (days == 1 ? " day" : " days");
    totalText.textContent = (days * pricePerDay).toFixed(2) + " DT";
}

if (bookingForm && startInput && endInput) {
    startInput.addEventListener("change", function () {
        // the return date can't be before the pick-up date
        endInput.min = startInput.value;
        updateTotal();
    });
    endInput.addEventListener("change", updateTotal);
    updateTotal();
}

// ask before doing dangerous actions (delete, cancel...)
var confirmButtons = document.querySelectorAll("[data-confirm]");

for (var i = 0; i < confirmButtons.length; i++) {
    confirmButtons[i].addEventListener("click", function (event) {
        if (!confirm(this.dataset.confirm)) {
            event.preventDefault();
        }
    });
}
