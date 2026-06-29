
/**
 * index.php — GrandHorizon CRS Main Dashboard
 * Session guard: redirects to login if not authenticated
 */
<?php

require_once 'auth.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrandHorizon | Enterprise Hotel Booking ERP</title>
    <!-- ✅ FIX: style.css link is correctly in <head>, logout link moved to <body> -->
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
 
    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>
 
    <!-- Layout Wrapper -->
    <div class="dashboard-wrapper">
 
        <!-- Fixed Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button id="mobile-menu-toggle" class="menu-toggle-btn" aria-label="Toggle Navigation Sidebar">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
                <div class="app-logo">
                    <span class="logo-icon">🏨</span>
                    <span class="logo-text">GrandHorizon<span class="logo-sub">ERP</span></span>
                </div>
            </div>
            <div class="header-right">
                <div class="system-status">
                    <span class="status-indicator online"></span>
                    <span class="status-label">PMS Connected</span>
                </div>
                <div class="user-profile-block">
                    <div class="user-avatar">
                        <?= strtoupper(substr($sessionUsername, 0, 2)) ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $sessionUsername ?></span>
                        <span class="user-role"><?= $sessionRole ?></span>
                    </div>
                </div>
                <!-- ✅ FIX: Logout link correctly placed in <body> header area -->
                <a href="logout.php" class="logout-btn" title="Sign out">
                    <span class="logout-icon">⏻</span>
                    <span class="logout-label">Logout</span>
                </a>
            </div>
        </header>
 
        <!-- Sidebar Navigation -->
        <aside id="sidebar-nav" class="sidebar-navigation">
            <nav class="sidebar-inner">
                <div class="nav-section-title">Core Operations</div>
                <ul class="nav-menu">
                    <li class="nav-item active">
                        <a href="#" class="nav-link">
                            <span class="nav-icon">📅</span>
                            <span class="nav-label">Booking Engine</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="alert('Demo: Room Inventory Matrix')">
                            <span class="nav-icon">🔑</span>
                            <span class="nav-label">Room Matrix</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="alert('Demo: Guest Folio Records')">
                            <span class="nav-icon">👥</span>
                            <span class="nav-label">Guest Folios</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="alert('Demo: Financial Ledger Accounts')">
                            <span class="nav-icon">📊</span>
                            <span class="nav-label">Accounts Ledger</span>
                        </a>
                    </li>
                </ul>
 
                <div class="nav-section-title">Global Settings</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="alert('Demo: System Configuration')">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-label">Configuration</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
 
        <!-- Mobile backdrop overlay -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
 
        <!-- Main Workspace -->
        <main class="main-content-area">
            <div class="workspace-container">
 
                <!-- Page Title Header -->
                <div class="workspace-header-card">
                    <div class="header-title-group">
                        <h1 class="page-title">Reservation Engineering</h1>
                        <p class="page-subtitle">Configure, provision, and deploy global partner reservation entries across corporate accounts.</p>
                    </div>
                </div>
 
                <!-- Master Booking Form -->
                <form id="master-booking-form" name="master_booking_form" autocomplete="off" novalidate
                      data-api-endpoint="api/bookings/submit"
                      data-method="POST">
 
                    <!-- Booking Type Card -->
                    <div class="dashboard-card classification-card">
                        <div class="card-header">
                            <h2 class="card-title">Booking Operational Type</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group max-w-md">
                                <label for="booking-type-select" class="form-label required-mark">Strategic Channel Classification</label>
                                <div class="select-wrapper">
                                    <select id="booking-type-select" name="booking_type" class="form-control" required aria-required="true" aria-describedby="booking-type-hint">
                                        <option value="" disabled selected>Select channel type...</option>
                                        <option value="Lease">Lease Operations</option>
                                        <option value="Marketing">Marketing Allocation</option>
                                        <option value="Management">Management Portfolios</option>
                                        <option value="Non Say">Non Say Placement</option>
                                    </select>
                                </div>
                                <span id="booking-type-hint" class="field-hint">Dynamic transaction parameters depend on this setting.</span>
                            </div>
                        </div>
                    </div>
 
                    <!-- Module Tabs -->
                    <div class="module-processing-hub">
                        <div class="module-tabs-strip-container">
                            <div class="module-tabs-strip" role="tablist" aria-label="Booking Component Subsystems">
                                <button type="button" class="tab-trigger active" data-target="panel-room-booking" role="tab" aria-selected="true" aria-controls="panel-room-booking">
                                    <span class="tab-icon">🛏️</span> Room Allocation
                                </button>
                                <button type="button" class="tab-trigger" data-target="panel-packages" role="tab" aria-selected="false" aria-controls="panel-packages">
                                    <span class="tab-icon">📦</span> Custom Packages
                                </button>
                                <button type="button" class="tab-trigger" data-target="panel-mice" role="tab" aria-selected="false" aria-controls="panel-mice">
                                    <span class="tab-icon">🏢</span> MICE Events
                                </button>
                                <button type="button" class="tab-trigger" data-target="panel-cabs" role="tab" aria-selected="false" aria-controls="panel-cabs">
                                    <span class="tab-icon">🚗</span> Cab Dispatch
                                </button>
                                <button type="button" class="tab-trigger" data-target="panel-tickets" role="tab" aria-selected="false" aria-controls="panel-tickets">
                                    <span class="tab-icon">✈️</span> Transit Tickets
                                </button>
                                <button type="button" class="tab-trigger" data-target="panel-payables" role="tab" aria-selected="false" aria-controls="panel-payables">
                                    <span class="tab-icon">💳</span> Financial Payables
                                </button>
                            </div>
                        </div>
 
                        <!-- PANEL: Room Booking -->
                        <div id="panel-room-booking" class="module-panel active" role="tabpanel" aria-labelledby="tab-room-booking">
                            <div class="dashboard-card">
                                <div class="card-header"><h3 class="card-title">Room Accommodation Parameters</h3></div>
                                <div class="card-body grid-container col-3-lg col-2-md col-1-sm">
 
                                    <!-- Hidden meta fields for PHP API -->
                                    <input type="hidden" name="created_by" value="<?= $_SESSION['user_id'] ?>">
                                    <input type="hidden" name="booking_status" value="draft">
 
                                    <div class="form-group">
                                        <label for="room-hotel" class="form-label required-mark">Hotel / Homestay Property</label>
                                        <select id="room-hotel" name="room_hotel_id" class="form-control" required aria-required="true" aria-describedby="room-hotel-err">
                                            <option value="">Select property...</option>
                                            <option value="1">Grand Horizon Resort &amp; Spa</option>
                                            <option value="2">Horizon Vista Suites</option>
                                            <option value="3">The Elite Heritage Manor</option>
                                        </select>
                                        <span class="field-error" id="room-hotel-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-category" class="form-label required-mark">Room Category</label>
                                        <select id="room-category" name="room_category_id" class="form-control" required aria-required="true" aria-describedby="room-cat-err">
                                            <option value="">Select category...</option>
                                            <option value="1">Deluxe Corporate Single</option>
                                            <option value="2">Executive Ocean Suite</option>
                                            <option value="3">Presidential Premium Panoramic</option>
                                        </select>
                                        <span class="field-error" id="room-cat-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-guest-name" class="form-label required-mark">Guest Full Name</label>
                                        <input type="text" id="room-guest-name" name="guest_name"
                                               class="form-control" placeholder="Full name"
                                               required aria-required="true"
                                               minlength="2" maxlength="100"
                                               autocomplete="name"
                                               aria-describedby="guest-name-err">
                                        <span class="field-error" id="guest-name-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-phone" class="form-label required-mark">Phone Number</label>
                                        <input type="tel" id="room-phone" name="phone"
                                               class="form-control" placeholder="+91 XXXXX XXXXX"
                                               required aria-required="true"
                                               pattern="[+0-9\s\-]{8,15}" maxlength="15"
                                               autocomplete="tel"
                                               aria-describedby="phone-err">
                                        <span class="field-error" id="phone-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-email" class="form-label required-mark">Email Address</label>
                                        <input type="email" id="room-email" name="email"
                                               class="form-control" placeholder="guest@email.com"
                                               required aria-required="true"
                                               maxlength="150" autocomplete="email"
                                               aria-describedby="email-err">
                                        <span class="field-error" id="email-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-address" class="form-label">Billing / Address</label>
                                        <input type="text" id="room-address" name="address"
                                               class="form-control" placeholder="Full address"
                                               maxlength="300" autocomplete="street-address">
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-checkin-date" class="form-label required-mark">Check-In Date</label>
                                        <input type="date" id="room-checkin-date" name="checkin_date"
                                               class="form-control"
                                               required aria-required="true"
                                               aria-describedby="checkin-err"
                                               onchange="validateDateRange()">
                                        <span class="field-error" id="checkin-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-checkin-time" class="form-label required-mark">Check-In Time</label>
                                        <input type="time" id="room-checkin-time" name="checkin_time"
                                               class="form-control" value="14:00" required>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-checkout-date" class="form-label required-mark">Check-Out Date</label>
                                        <input type="date" id="room-checkout-date" name="checkout_date"
                                               class="form-control"
                                               required aria-required="true"
                                               aria-describedby="checkout-err"
                                               onchange="validateDateRange()">
                                        <span class="field-error" id="checkout-err" role="alert"></span>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-checkout-time" class="form-label required-mark">Check-Out Time</label>
                                        <input type="time" id="room-checkout-time" name="checkout_time"
                                               class="form-control" value="11:00" required>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-adults" class="form-label required-mark">Adults</label>
                                        <input type="number" id="room-adults" name="adults"
                                               class="form-control" min="1" max="20" value="1"
                                               required aria-required="true">
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-children" class="form-label">Children</label>
                                        <input type="number" id="room-children" name="children"
                                               class="form-control" min="0" max="10" value="0"
                                               oninput="toggleChildrenAge(this.value)">
                                    </div>
 
                                    <div class="form-group" id="children-age-group" style="display:none;">
                                        <label for="room-children-age" class="form-label">Children Ages (comma-separated)</label>
                                        <input type="text" id="room-children-age" name="children_ages"
                                               class="form-control" placeholder="e.g. 4, 8"
                                               maxlength="50">
                                    </div>
 
                                    <div class="form-group flex-row-layout">
                                        <label class="checkbox-container">
                                            <input type="checkbox" id="room-extra-mattress" name="extra_mattress" value="1">
                                            <span class="custom-checkbox"></span>
                                            Extra Mattress Required
                                        </label>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-meal-plan" class="form-label required-mark">Meal Plan</label>
                                        <select id="room-meal-plan" name="meal_plan" class="form-control" required aria-required="true">
                                            <option value="EP">EP — European Plan (Room Only)</option>
                                            <option value="CP" selected>CP — Continental Plan (B&amp;B)</option>
                                            <option value="MAP">MAP — Modified American Plan (Half Board)</option>
                                            <option value="AP">AP — American Plan (Full Board)</option>
                                        </select>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-business-source" class="form-label">Business Source</label>
                                        <select id="room-business-source" name="business_source_id" class="form-control">
                                            <option value="">Select source...</option>
                                            <option value="1">Direct</option>
                                            <option value="2">OTA — MakeMyTrip</option>
                                            <option value="3">OTA — Booking.com</option>
                                            <option value="4">Corporate</option>
                                            <option value="5">Travel Agent</option>
                                            <option value="6">Walk-in</option>
                                        </select>
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-pic" class="form-label required-mark">Person In Charge</label>
                                        <input type="text" id="room-pic" name="person_in_charge"
                                               class="form-control" placeholder="Staff name"
                                               required aria-required="true" maxlength="100">
                                    </div>
 
                                    <div class="form-group">
                                        <label for="room-special-requests" class="form-label">Special Requests</label>
                                        <input type="text" id="room-special-requests" name="special_requests"
                                               class="form-control" placeholder="Early check-in, dietary needs..."
                                               maxlength="500">
                                    </div>
 
                                </div>
                            </div>
                        </div><!-- /panel-room-booking -->
 
                        <!-- PANEL: Packages -->
                        <div id="panel-packages" class="module-panel" role="tabpanel" aria-labelledby="tab-packages">
                            <div class="panel-section-header">
                                <h3 class="section-block-title">Composite Destination Package Mapping</h3>
                                <button type="button" id="btn-add-hotel-card" class="btn btn-secondary-outline btn-sm">
                                    <span class="btn-icon-embedded">➕</span> Add Property Node
                                </button>
                            </div>
 
                            <div id="package-hotels-container" class="stack-layout gap-md">
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
                            </div>
 
                            <div class="dashboard-card mt-md">
                                <div class="card-header"><h3 class="card-title">Additional Services</h3></div>
                                <div class="card-body grid-container col-2-md col-1-sm">
                                    <div class="sub-form-card">
                                        <h4 class="sub-card-title">Cab / Ground Transport</h4>
                                        <div class="form-group">
                                            <label for="package-cab-vendor" class="form-label">Cab Vendor</label>
                                            <input type="text" id="package-cab-vendor" name="package_cab_vendor" class="form-control" placeholder="Vendor name" maxlength="100">
                                        </div>
                                        <div class="form-group">
                                            <label for="package-cab-amount" class="form-label">Cab Amount (₹)</label>
                                            <input type="number" id="package-cab-amount" name="package_cab_amount" class="form-control" min="0" step="0.01" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                    <div class="sub-form-card">
                                        <h4 class="sub-card-title">Flight / Aviation</h4>
                                        <div class="form-group">
                                            <label for="package-flight-vendor" class="form-label">Flight Vendor</label>
                                            <input type="text" id="package-flight-vendor" name="package_flight_vendor" class="form-control" placeholder="Airline / agency" maxlength="100">
                                        </div>
                                        <div class="form-group">
                                            <label for="package-flight-amount" class="form-label">Flight Amount (₹)</label>
                                            <input type="number" id="package-flight-amount" name="package_flight_amount" class="form-control" min="0" step="0.01" placeholder="0.00" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer-inline">
                                    <div class="form-group full-width">
                                        <label for="package-other-services" class="form-label">Other Services</label>
                                        <textarea id="package-other-services" name="package_other_services" class="form-control" rows="3" placeholder="Visa, tour guides, bespoke dining..." maxlength="1000"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /panel-packages -->
 
                        <!-- PANEL: MICE -->
                        <div id="panel-mice" class="module-panel" role="tabpanel" aria-labelledby="tab-mice">
                            <div class="dashboard-card">
                                <div class="card-header"><h3 class="card-title">MICE Event Configuration</h3></div>
                                <div class="card-body stack-layout gap-md">
                                    <div class="grid-container col-3-lg col-1-sm">
                                        <div class="form-group">
                                            <label for="mice-guest-company" class="form-label required-mark">Organisation / Guest Name</label>
                                            <input type="text" id="mice-guest-company" name="mice_guest_name"
                                                   class="form-control" placeholder="Company / guest name"
                                                   required aria-required="true" maxlength="150">
                                        </div>
                                        <div class="form-group">
                                            <label for="mice-attendees" class="form-label required-mark">Expected Pax</label>
                                            <input type="number" id="mice-attendees" name="mice_pax"
                                                   class="form-control" min="1" placeholder="Number of guests"
                                                   required aria-required="true">
                                        </div>
                                        <div class="form-group">
                                            <label for="mice-contact" class="form-label required-mark">Contact Phone</label>
                                            <input type="tel" id="mice-contact" name="mice_phone"
                                                   class="form-control" placeholder="+91 XXXXX XXXXX"
                                                   required aria-required="true" pattern="[+0-9\s\-]{8,15}">
                                        </div>
                                        <div class="form-group">
                                            <label for="mice-event-date" class="form-label required-mark">Event Date</label>
                                            <input type="date" id="mice-event-date" name="mice_event_date"
                                                   class="form-control" required aria-required="true">
                                        </div>
                                        <div class="form-group">
                                            <label for="mice-budget" class="form-label">Estimated Budget (₹)</label>
                                            <input type="number" id="mice-budget" name="mice_budget"
                                                   class="form-control" min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="mice-banquet-details" class="form-label required-mark">Banquet / Event Details</label>
                                        <textarea id="mice-banquet-details" name="mice_banquet_details"
                                                  class="form-control" rows="6"
                                                  placeholder="AV setup, seating layout, F&amp;B requirements, timing..."
                                                  required aria-required="true" minlength="10" maxlength="2000"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /panel-mice -->
 
                        <!-- PANEL: Cabs -->
                        <div id="panel-cabs" class="module-panel" role="tabpanel" aria-labelledby="tab-cabs">
                            <div class="dashboard-card">
                                <div class="card-header"><h3 class="card-title">Ground Transport / Cab Dispatch</h3></div>
                                <div class="card-body grid-container col-3-lg col-2-md col-1-sm">
                                    <div class="form-group">
                                        <label for="cab-guest-name" class="form-label required-mark">Guest Name</label>
                                        <input type="text" id="cab-guest-name" name="cab_guest_name"
                                               class="form-control" placeholder="Passenger name"
                                               required aria-required="true" maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-phone" class="form-label required-mark">Phone</label>
                                        <input type="tel" id="cab-phone" name="cab_phone"
                                               class="form-control" placeholder="+91 XXXXX XXXXX"
                                               required aria-required="true" pattern="[+0-9\s\-]{8,15}">
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-vendor" class="form-label required-mark">Cab Vendor</label>
                                        <select id="cab-vendor" name="cab_vendor_id" class="form-control" required aria-required="true">
                                            <option value="">Select vendor...</option>
                                            <option value="1">FleetCorp Global Logistics</option>
                                            <option value="2">Apex Executive Transit</option>
                                            <option value="3">City Cabs Network</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-date" class="form-label required-mark">Travel Date</label>
                                        <input type="date" id="cab-date" name="cab_travel_date"
                                               class="form-control" required aria-required="true">
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-pickup-time" class="form-label">Pickup Time</label>
                                        <input type="time" id="cab-pickup-time" name="cab_pickup_time" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-vehicle" class="form-label">Vehicle Type</label>
                                        <select id="cab-vehicle" name="cab_vehicle_type" class="form-control">
                                            <option value="">Select vehicle...</option>
                                            <option value="sedan">Sedan</option>
                                            <option value="suv">SUV (Innova)</option>
                                            <option value="luxury">Luxury</option>
                                            <option value="minibus">Mini Bus (12 Seater)</option>
                                            <option value="coach">Coach (32+ Seater)</option>
                                        </select>
                                    </div>
                                    <div class="form-group full-width-md-up">
                                        <label for="cab-pickup" class="form-label required-mark">Pickup Location</label>
                                        <input type="text" id="cab-pickup" name="cab_pickup_location"
                                               class="form-control" placeholder="Address / landmark"
                                               required aria-required="true" maxlength="200">
                                    </div>
                                    <div class="form-group full-width-md-up">
                                        <label for="cab-drop" class="form-label required-mark">Drop Location</label>
                                        <input type="text" id="cab-drop" name="cab_drop_location"
                                               class="form-control" placeholder="Destination address"
                                               required aria-required="true" maxlength="200">
                                    </div>
                                    <div class="form-group">
                                        <label for="cab-amount" class="form-label">Amount (₹)</label>
                                        <input type="number" id="cab-amount" name="cab_amount"
                                               class="form-control" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /panel-cabs -->
 
                        <!-- PANEL: Tickets -->
                        <div id="panel-tickets" class="module-panel" role="tabpanel" aria-labelledby="tab-tickets">
                            <div class="dashboard-card">
                                <div class="card-header"><h3 class="card-title">Transit Ticket Booking</h3></div>
                                <div class="card-body grid-container col-3-lg col-2-md col-1-sm">
                                    <div class="form-group">
                                        <label for="ticket-guest" class="form-label required-mark">Guest Name</label>
                                        <input type="text" id="ticket-guest" name="ticket_guest_name"
                                               class="form-control" placeholder="Passenger name"
                                               required aria-required="true" maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-phone" class="form-label required-mark">Phone</label>
                                        <input type="tel" id="ticket-phone" name="ticket_phone"
                                               class="form-control" placeholder="+91 XXXXX XXXXX"
                                               required aria-required="true" pattern="[+0-9\s\-]{8,15}">
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-type" class="form-label required-mark">Ticket Type</label>
                                        <select id="ticket-type" name="ticket_type" class="form-control" required aria-required="true" onchange="onTicketTypeChange(this.value)">
                                            <option value="">Select type...</option>
                                            <option value="Flight">Flight</option>
                                            <option value="Train">Train</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="ticket-flight-field" style="display:none;">
                                        <label for="ticket-flight-no" class="form-label">Flight Number / Route</label>
                                        <input type="text" id="ticket-flight-no" name="ticket_flight_number"
                                               class="form-control" placeholder="e.g. AI-401 DEL-BOM" maxlength="50">
                                    </div>
                                    <div class="form-group" id="ticket-train-field" style="display:none;">
                                        <label for="ticket-train-no" class="form-label">Train Number / Name</label>
                                        <input type="text" id="ticket-train-no" name="ticket_train_number"
                                               class="form-control" placeholder="e.g. 12001 Shatabdi" maxlength="80">
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-vendor" class="form-label required-mark">Vendor / Agency</label>
                                        <input type="text" id="ticket-vendor" name="ticket_vendor_name"
                                               class="form-control" placeholder="Travel agency / airline"
                                               required aria-required="true" maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-date" class="form-label required-mark">Travel Date</label>
                                        <input type="date" id="ticket-date" name="ticket_travel_date"
                                               class="form-control" required aria-required="true">
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-amount" class="form-label required-mark">Amount (₹)</label>
                                        <div class="currency-input-container">
                                            <span class="currency-symbol">₹</span>
                                            <input type="number" id="ticket-amount" name="ticket_amount"
                                                   class="form-control" min="0" step="0.01" placeholder="0.00"
                                                   required aria-required="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-class" class="form-label">Class / Quota</label>
                                        <select id="ticket-class" name="ticket_class" class="form-control">
                                            <option value="">Select class...</option>
                                            <option value="economy">Economy</option>
                                            <option value="business">Business</option>
                                            <option value="first">First Class</option>
                                            <option value="sl">Sleeper (SL)</option>
                                            <option value="3a">3rd AC (3A)</option>
                                            <option value="2a">2nd AC (2A)</option>
                                            <option value="1a">1st AC (1A)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="ticket-pnr" class="form-label">PNR / Booking Ref</label>
                                        <input type="text" id="ticket-pnr" name="ticket_pnr"
                                               class="form-control" placeholder="PNR or confirmation number" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /panel-tickets -->
 
                        <!-- PANEL: Payables -->
                        <div id="panel-payables" class="module-panel" role="tabpanel" aria-labelledby="tab-payables">
                            <div class="dashboard-card">
                                <div class="card-header"><h3 class="card-title">Accounts Payable Settlement</h3></div>
                                <div class="card-body stack-layout gap-md">
                                    <div class="grid-container col-4-lg col-2-md col-1-sm align-end-lg">
                                        <div class="form-group">
                                            <label for="payable-service-type" class="form-label required-mark">Service Type</label>
                                            <select id="payable-service-type" name="payable_service_type" class="form-control">
                                                <option value="">Select type...</option>
                                                <option value="Hotel">Hotel</option>
                                                <option value="Cab">Cab</option>
                                                <option value="Flight">Flight</option>
                                                <option value="Train">Train</option>
                                                <option value="MICE">MICE / Event</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="payable-vendor-name" class="form-label required-mark">Vendor Name</label>
                                            <input type="text" id="payable-vendor-name" name="payable_vendor_name"
                                                   class="form-control" placeholder="Vendor name" maxlength="150">
                                        </div>
                                        <div class="form-group">
                                            <label for="payable-amount" class="form-label required-mark">Amount (₹)</label>
                                            <div class="currency-input-container">
                                                <span class="currency-symbol">₹</span>
                                                <input type="number" id="payable-amount" name="payable_amount"
                                                       class="form-control" min="0" step="0.01" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="payable-status" class="form-label required-mark">Payment Status</label>
                                            <select id="payable-status" name="payable_status" class="form-control">
                                                <option value="Pending">⚠️ Pending</option>
                                                <option value="Paid">✅ Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="payable-notes" class="form-label">Notes / Reference</label>
                                        <input type="text" id="payable-notes" name="payable_notes"
                                               class="form-control" placeholder="PO number, approval ref..." maxlength="300">
                                    </div>
                                    <div class="action-row-inline-right">
                                        <button type="button" id="btn-save-payable-item" class="btn btn-secondary-outline">
                                            <span class="btn-icon-embedded">💾</span> Add to Payables List
                                        </button>
                                    </div>
                                    <div class="responsive-table-outer">
                                        <table id="payables-matrix-table" class="erp-table">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Vendor</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Notes</th>
                                                    <th class="text-center">Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody id="payables-table-body">
                                                <tr id="payables-empty-row">
                                                    <td colspan="6" class="table-empty-notice">No payables added yet. Fill the fields above and click "Add to Payables List".</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /panel-payables -->
 
                    </div><!-- /module-processing-hub -->
 
                    <!-- Sticky Action Footer -->
                    <div class="sticky-action-bar-footer">
                        <div class="sticky-footer-inner">
                            <div class="footer-left-buttons">
                                <button type="button" id="btn-form-reset" class="btn btn-danger-outline">Reset Form</button>
                                <button type="button" id="btn-form-cancel" class="btn btn-neutral">Cancel</button>
                            </div>
                            <div class="footer-right-buttons">
                                <button type="button" id="btn-form-draft" class="btn btn-secondary">Save Draft</button>
                                <button type="submit" id="btn-form-submit" class="btn btn-primary">Submit Booking</button>
                            </div>
                        </div>
                    </div>
 
                </form><!-- /master-booking-form -->
 
            </div>
        </main>
    </div><!-- /dashboard-wrapper -->
 
    <script src="script.js"></script>
</body>
</html>