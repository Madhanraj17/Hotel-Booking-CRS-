/**
 * =============================================================
 * script.js — GrandHorizon CRS Booking Engine
 * Backend-ready: form data structured for PHP/MySQL Fetch API
 * =============================================================
 */
 
document.addEventListener("DOMContentLoaded", () => {
 
    /* ─────────────────────────────────────────────────────────
       APPLICATION STATE
    ───────────────────────────────────────────────────────── */
    const erpState = {
        activeTabPanelId: "panel-room-booking",
        payablesLedgerItems: []
    };
 
    /* ─────────────────────────────────────────────────────────
       DOM REFERENCE CACHE  (null-safe — avoids crash on missing elements)
    ───────────────────────────────────────────────────────── */
    const get = (id) => document.getElementById(id);
 
    const DOM = {
        masterForm:              get("master-booking-form"),
        bookingTypeSelect:       get("booking-type-select"),
        tabTriggers:             document.querySelectorAll(".tab-trigger"),
        modulePanels:            document.querySelectorAll(".module-panel"),
        packageHotelsContainer:  get("package-hotels-container"),
        btnAddHotelCard:         get("btn-add-hotel-card"),
        payableServiceType:      get("payable-service-type"),
        payableVendorName:       get("payable-vendor-name"),
        payableAmount:           get("payable-amount"),
        payableStatus:           get("payable-status"),
        payableNotes:            get("payable-notes"),
        btnSavePayableItem:      get("btn-save-payable-item"),
        payablesTableBody:       get("payables-table-body"),
        payablesEmptyRow:        get("payables-empty-row"),
        btnReset:                get("btn-form-reset"),
        btnCancel:               get("btn-form-cancel"),
        btnDraft:                get("btn-form-draft"),
        mobileMenuToggle:        get("mobile-menu-toggle"),
        sidebarNav:              get("sidebar-nav"),
        sidebarOverlay:          get("sidebar-overlay"),
        toastContainer:          get("toast-container"),
        checkinDate:             get("room-checkin-date"),
        checkoutDate:            get("room-checkout-date"),
        childrenInput:           get("room-children"),
        childrenAgeGroup:        get("children-age-group"),
        ticketType:              get("ticket-type"),
        ticketFlightField:       get("ticket-flight-field"),
        ticketTrainField:        get("ticket-train-field"),
    };
 
    let hotelNodeCounter = 1;
 
    /* ─────────────────────────────────────────────────────────
       TAB / PANEL SWITCHING
    ───────────────────────────────────────────────────────── */
    function switchActiveTabPanel(targetPanelId) {
        DOM.tabTriggers.forEach(trigger => {
            const isTarget = trigger.getAttribute("data-target") === targetPanelId;
            trigger.classList.toggle("active", isTarget);
            trigger.setAttribute("aria-selected", isTarget ? "true" : "false");
        });
 
        DOM.modulePanels.forEach(panel => {
            panel.classList.toggle("active", panel.id === targetPanelId);
        });
 
        erpState.activeTabPanelId = targetPanelId;
    }
 
    DOM.tabTriggers.forEach(trigger => {
        trigger.addEventListener("click", () => {
            switchActiveTabPanel(trigger.getAttribute("data-target"));
        });
    });
 
    /* ─────────────────────────────────────────────────────────
       CHILDREN AGE TOGGLE
    ───────────────────────────────────────────────────────── */
    if (DOM.childrenInput) {
        DOM.childrenInput.addEventListener("input", () => {
            const count = parseInt(DOM.childrenInput.value) || 0;
            if (DOM.childrenAgeGroup) {
                DOM.childrenAgeGroup.style.display = count > 0 ? "block" : "none";
                DOM.childrenAgeGroup.setAttribute("aria-hidden", count > 0 ? "false" : "true");
            }
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       TICKET TYPE TOGGLE (Flight ↔ Train)
    ───────────────────────────────────────────────────────── */
    if (DOM.ticketType) {
        DOM.ticketType.addEventListener("change", () => {
            const val = DOM.ticketType.value;
            if (DOM.ticketFlightField) DOM.ticketFlightField.style.display = val === "Flight" ? "block" : "none";
            if (DOM.ticketTrainField)  DOM.ticketTrainField.style.display  = val === "Train"  ? "block" : "none";
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       DATE RANGE VALIDATION
    ───────────────────────────────────────────────────────── */
    function validateDateRange() {
        if (!DOM.checkinDate || !DOM.checkoutDate) return true;
        const ci = DOM.checkinDate.value;
        const co = DOM.checkoutDate.value;
        if (ci && co && co <= ci) {
            showFieldError(DOM.checkoutDate, "Check-out must be after check-in.");
            return false;
        }
        clearFieldError(DOM.checkoutDate);
        return true;
    }
 
    if (DOM.checkinDate)  DOM.checkinDate.addEventListener("change", validateDateRange);
    if (DOM.checkoutDate) DOM.checkoutDate.addEventListener("change", validateDateRange);
 
    /* ─────────────────────────────────────────────────────────
       FIELD-LEVEL VALIDATION HELPERS
    ───────────────────────────────────────────────────────── */
    function showFieldError(input, message) {
        input.classList.add("input-error");
        const errEl = input.parentElement.querySelector(".field-error");
        if (errEl) errEl.textContent = message;
    }
 
    function clearFieldError(input) {
        input.classList.remove("input-error");
        const errEl = input.parentElement.querySelector(".field-error");
        if (errEl) errEl.textContent = "";
    }
 
    function attachClearOnInput(input) {
        input.addEventListener("input", function handler() {
            if (this.value.trim()) {
                clearFieldError(this);
                this.removeEventListener("input", handler);
            }
        });
    }
 
    /**
     * Validates all required fields in a given panel.
     * Returns true if valid, false + highlights errors if not.
     */
    function validatePanel(panelId) {
        const panel = get(panelId);
        if (!panel) return true;
 
        let valid = true;
        panel.querySelectorAll("[required]").forEach(input => {
            const empty = input.type === "checkbox" ? !input.checked : !input.value.trim();
            if (empty) {
                showFieldError(input, "This field is required.");
                attachClearOnInput(input);
                valid = false;
            } else {
                clearFieldError(input);
 
                // Email format check
                if (input.type === "email" && input.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value)) {
                        showFieldError(input, "Enter a valid email address.");
                        valid = false;
                    }
                }
 
                // Phone pattern check
                if (input.type === "tel" && input.value) {
                    const phoneRegex = /^[+0-9\s\-]{8,15}$/;
                    if (!phoneRegex.test(input.value)) {
                        showFieldError(input, "Enter a valid phone number.");
                        valid = false;
                    }
                }
            }
        });
 
        return valid;
    }
 
    /* ─────────────────────────────────────────────────────────
       PACKAGES — DYNAMIC HOTEL CARD INJECTION
    ───────────────────────────────────────────────────────── */
    if (DOM.btnAddHotelCard) {
        DOM.btnAddHotelCard.addEventListener("click", () => {
            hotelNodeCounter++;
 
            const card = document.createElement("div");
            card.className = "dashboard-card component-node-card";
            card.innerHTML = `
                <div class="card-header node-accent-cyan" style="display:flex;justify-content:space-between;align-items:center;">
                    <h4 class="card-title-sub">Hotel #${hotelNodeCounter}</h4>
                    <button type="button" class="btn btn-danger-outline btn-sm btn-remove-node">Remove</button>
                </div>
                <div class="card-body grid-container col-3-lg col-1-sm">
                    <div class="form-group">
                        <label class="form-label required-mark">Hotel Name</label>
                        <input type="text" name="package_hotel_name[]" class="form-control" placeholder="Property name" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label class="form-label required-mark">Room Type</label>
                        <input type="text" name="package_hotel_room[]" class="form-control" placeholder="Room/Suite class" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label required-mark">Nights</label>
                        <input type="number" name="package_hotel_nights[]" class="form-control" min="1" value="1" required>
                    </div>
                </div>
            `;
 
            card.querySelector(".btn-remove-node").addEventListener("click", () => {
                card.remove();
                showToast("Hotel removed from package.", "info");
            });
 
            DOM.packageHotelsContainer.appendChild(card);
            showToast(`Hotel #${hotelNodeCounter} added to package.`, "success");
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       PAYABLES — LOCAL LEDGER MANAGEMENT
    ───────────────────────────────────────────────────────── */
    function renderPayablesTable() {
        // Remove all rows except the empty placeholder
        DOM.payablesTableBody.querySelectorAll("tr:not(#payables-empty-row)").forEach(r => r.remove());
 
        if (erpState.payablesLedgerItems.length === 0) {
            if (DOM.payablesEmptyRow) DOM.payablesEmptyRow.style.display = "table-row";
            return;
        }
        if (DOM.payablesEmptyRow) DOM.payablesEmptyRow.style.display = "none";
 
        erpState.payablesLedgerItems.forEach((item, index) => {
            const tr = document.createElement("tr");
            const badgeClass = item.status === "Paid" ? "badge-paid" : "badge-pending";
            tr.innerHTML = `
                <td><strong>${escapeHtml(item.type)}</strong></td>
                <td><span class="font-mono">${escapeHtml(item.vendor)}</span></td>
                <td><strong>₹${parseFloat(item.amount).toFixed(2)}</strong></td>
                <td><span class="badge ${badgeClass}">${escapeHtml(item.status)}</span></td>
                <td><small class="text-muted">${escapeHtml(item.notes || "—")}</small></td>
                <td class="text-center">
                    <button type="button" class="btn-action-drop" data-index="${index}" title="Remove row">🗑️</button>
                </td>
            `;
            tr.querySelector(".btn-action-drop").addEventListener("click", (e) => {
                const i = parseInt(e.currentTarget.getAttribute("data-index"));
                erpState.payablesLedgerItems.splice(i, 1);
                renderPayablesTable();
                showToast("Payable entry removed.", "info");
            });
            DOM.payablesTableBody.appendChild(tr);
        });
    }
 
    if (DOM.btnSavePayableItem) {
        DOM.btnSavePayableItem.addEventListener("click", () => {
            const type   = DOM.payableServiceType?.value?.trim();
            const vendor = DOM.payableVendorName?.value?.trim();
            const amount = DOM.payableAmount?.value?.trim();
            const status = DOM.payableStatus?.value  || "Pending";
            const notes  = DOM.payableNotes?.value?.trim() || "";
 
            // Validation
            let hasError = false;
            if (!type)   { DOM.payableServiceType.classList.add("input-error"); hasError = true; }
            else           DOM.payableServiceType.classList.remove("input-error");
            if (!vendor) { DOM.payableVendorName.classList.add("input-error");  hasError = true; }
            else           DOM.payableVendorName.classList.remove("input-error");
            if (!amount) { DOM.payableAmount.classList.add("input-error");      hasError = true; }
            else           DOM.payableAmount.classList.remove("input-error");
 
            if (hasError) {
                showToast("Please fill all required payable fields.", "error");
                return;
            }
 
            erpState.payablesLedgerItems.push({ type, vendor, amount, status, notes });
 
            // Clear fields after save
            DOM.payableServiceType.value = "";
            DOM.payableVendorName.value  = "";
            DOM.payableAmount.value      = "";
            DOM.payableNotes.value       = "";
 
            renderPayablesTable();
            showToast("Payable entry added successfully.", "success");
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       FORM SUBMISSION — BUSINESS WORKFLOW ROUTING
    ───────────────────────────────────────────────────────── */
    if (DOM.masterForm) {
        DOM.masterForm.addEventListener("submit", (e) => {
            e.preventDefault();
 
            // 1. Booking type check
            const bookingType = DOM.bookingTypeSelect?.value;
            if (!bookingType) {
                DOM.bookingTypeSelect.classList.add("input-error");
                showToast("Please select a Booking Type first.", "error");
                window.scrollTo({ top: 0, behavior: "smooth" });
                return;
            }
            DOM.bookingTypeSelect.classList.remove("input-error");
 
            // 2. Validate currently active panel
            if (!validatePanel(erpState.activeTabPanelId)) {
                showToast("Please fill all required fields in the current tab.", "error");
                return;
            }
 
            // 3. Date range check (room booking panel)
            if (erpState.activeTabPanelId === "panel-room-booking" && !validateDateRange()) {
                showToast("Check-out date must be after check-in date.", "error");
                return;
            }
 
            // 4. Route by booking type
            routeByBookingType(bookingType);
        });
    }
 
    /**
     * Business rule routing based on booking_type value.
     * Lease / Management → PMS success message
     * Marketing / Non Say → Switch to Payables tab
     */
    function routeByBookingType(bookingType) {
 
        // Disable submit button
        const submitBtn = document.getElementById("btn-form-submit");
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = "Saving..."; }
 
        // Build form data
        const formData = new FormData(DOM.masterForm);
        formData.append("payables_json", JSON.stringify(erpState.payablesLedgerItems));
        formData.append("active_tab", erpState.activeTabPanelId);
 
        fetch("api/save_booking.php", {
            method: "POST",
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === "success") {
                showToast("✅ Booking saved! Reference: " + data.booking_ref, "success");
                setTimeout(() => resetWorkspace(), 1500);
            } else {
                showToast("❌ " + (data.message || "Server error. Please try again."), "error");
            }
        })
        .catch(() => showToast("❌ Network error. Check your connection.", "error"))
        .finally(() => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = "Submit Booking"; }
        });
 
        if (bookingType === "Marketing" || bookingType === "Non Say") {
            showToast(`Channel [${bookingType}] requires payable entry. Switching to Payables tab.`, "info");
            switchActiveTabPanel("panel-payables");
 
            if (DOM.payableServiceType) DOM.payableServiceType.value = "Hotel";
            if (DOM.payableVendorName)  DOM.payableVendorName.value  = "";
            if (DOM.payableAmount)      DOM.payableAmount.value       = "";
            if (DOM.payableNotes)       DOM.payableNotes.value        = `Auto-routed from ${bookingType} booking.`;
            if (DOM.payableVendorName)  DOM.payableVendorName.focus();
        }
    }
 
    /* ─────────────────────────────────────────────────────────
       RESET WORKSPACE
    ───────────────────────────────────────────────────────── */
    function resetWorkspace() {
        if (DOM.masterForm) DOM.masterForm.reset();
 
        // Restore hotel cards to single default card
        if (DOM.packageHotelsContainer) {
            DOM.packageHotelsContainer.innerHTML = `
                <div class="dashboard-card component-node-card">
                    <div class="card-header node-accent-cyan">
                        <h4 class="card-title-sub">Hotel #1</h4>
                    </div>
                    <div class="card-body grid-container col-3-lg col-1-sm">
                        <div class="form-group">
                            <label class="form-label required-mark">Hotel Name</label>
                            <input type="text" name="package_hotel_name[]" class="form-control" placeholder="Property name" required maxlength="150">
                        </div>
                        <div class="form-group">
                            <label class="form-label required-mark">Room Type</label>
                            <input type="text" name="package_hotel_room[]" class="form-control" placeholder="Room/Suite class" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label class="form-label required-mark">Nights</label>
                            <input type="number" name="package_hotel_nights[]" class="form-control" min="1" value="1" required>
                        </div>
                    </div>
                </div>
            `;
        }
        hotelNodeCounter = 1;
 
        // Clear payables ledger
        erpState.payablesLedgerItems = [];
        renderPayablesTable();
 
        // Clear all error states
        document.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));
        document.querySelectorAll(".field-error").forEach(el => el.textContent = "");
 
        // Hide children age field
        if (DOM.childrenAgeGroup) DOM.childrenAgeGroup.style.display = "none";
 
        // Hide ticket sub-fields
        if (DOM.ticketFlightField) DOM.ticketFlightField.style.display = "none";
        if (DOM.ticketTrainField)  DOM.ticketTrainField.style.display  = "none";
 
        switchActiveTabPanel("panel-room-booking");
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
 
    /* ─────────────────────────────────────────────────────────
       ACTION BUTTONS
    ───────────────────────────────────────────────────────── */
    if (DOM.btnReset) {
        DOM.btnReset.addEventListener("click", () => {
            if (confirm("Reset all form data? This cannot be undone.")) {
                resetWorkspace();
                showToast("Form reset to clean state.", "info");
            }
        });
    }
 
    if (DOM.btnCancel) {
        DOM.btnCancel.addEventListener("click", () => {
            if (confirm("Cancel this booking session? All data will be cleared.")) {
                resetWorkspace();
                showToast("Session cancelled.", "info");
            }
        });
    }
 
    if (DOM.btnDraft) {
        DOM.btnDraft.addEventListener("click", () => {
            // ------------------------------------------------------------------
            // BACKEND INTEGRATION POINT — Save Draft:
            // const formData = new FormData(DOM.masterForm);
            // formData.set('booking_status', 'draft');
            // formData.append('payables_json', JSON.stringify(erpState.payablesLedgerItems));
            // fetch('api/bookings/save-draft', { method: 'POST', body: formData })
            //   .then(r => r.json())
            //   .then(data => showToast(data.message, data.success ? 'success' : 'error'));
            // ------------------------------------------------------------------
            showToast("Draft saved successfully.", "success");
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       MOBILE SIDEBAR TOGGLE
    ───────────────────────────────────────────────────────── */
    function toggleSidebar() {
        if (!DOM.sidebarNav || !DOM.sidebarOverlay) return;
        DOM.sidebarNav.classList.toggle("mobile-open");
        DOM.sidebarOverlay.classList.toggle("mobile-open");
        const isOpen = DOM.sidebarNav.classList.contains("mobile-open");
        if (DOM.mobileMenuToggle) DOM.mobileMenuToggle.setAttribute("aria-expanded", isOpen);
    }
 
    if (DOM.mobileMenuToggle) DOM.mobileMenuToggle.addEventListener("click", toggleSidebar);
    if (DOM.sidebarOverlay)   DOM.sidebarOverlay.addEventListener("click", toggleSidebar);
 
    if (DOM.sidebarNav) {
        DOM.sidebarNav.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", () => {
                if (DOM.sidebarNav.classList.contains("mobile-open")) toggleSidebar();
            });
        });
    }
 
    /* ─────────────────────────────────────────────────────────
       TOAST NOTIFICATION SYSTEM
    ───────────────────────────────────────────────────────── */
    function showToast(message, type = "success") {
        if (!DOM.toastContainer) return;
 
        const icons = { success: "✅", error: "❌", info: "ℹ️", warning: "⚠️" };
        const toast = document.createElement("div");
        toast.className = `toast-alert toast-${type}`;
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || "ℹ️"}</div>
            <div class="toast-body">
                <span class="toast-msg">${escapeHtml(message)}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Close">×</button>
        `;
 
        DOM.toastContainer.appendChild(toast);
 
        // Auto-remove after 4.5s
        setTimeout(() => {
            if (!toast.parentElement) return;
            toast.style.animation = "toastOut 0.25s ease forwards";
            toast.addEventListener("animationend", () => toast.remove(), { once: true });
        }, 4500);
    }
 
    /* ─────────────────────────────────────────────────────────
       XSS SANITIZER
    ───────────────────────────────────────────────────────── */
    function escapeHtml(str) {
        if (!str) return "";
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
 
    /* ─────────────────────────────────────────────────────────
       INIT
    ───────────────────────────────────────────────────────── */
    showToast("GrandHorizon CRS loaded. System ready.", "success");
 
}); // end DOMContentLoaded