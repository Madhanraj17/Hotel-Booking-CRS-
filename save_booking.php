<?php

session_start();

header("Content-Type: application/json");

require_once "../db.php";

$response = [];

/* -----------------------------------------
   Check Login
------------------------------------------ */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized Access"]);
    exit;
}

/* -----------------------------------------
   Allow POST Only
------------------------------------------ */
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
    exit;
}

/* -----------------------------------------
   Get Core Form Data
------------------------------------------ */
$booking_type   = trim($_POST["booking_type"] ?? "");
$active_tab     = trim($_POST["active_tab"]   ?? "panel-room-booking");
$created_by     = $_SESSION["user_id"];

if ($booking_type == "") {
    echo json_encode(["status" => "error", "message" => "Booking Type is required."]);
    exit;
}

/* -----------------------------------------
   Generate Booking Reference
------------------------------------------ */
$booking_ref = "CRS-" . date("YmdHis");

/* -----------------------------------------
   Begin Transaction
------------------------------------------ */
mysqli_begin_transaction($conn);

try {

    /* ── 1. Insert master booking ── */
    $sql  = "INSERT INTO bookings (booking_ref, booking_type, created_by) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $booking_ref, $booking_type, $created_by);
    mysqli_stmt_execute($stmt);
    $booking_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    /* ── 2. Room Booking ── */
    $room_hotel_id      = intval($_POST["room_hotel_id"]      ?? 0);
    $room_category_id   = intval($_POST["room_category_id"]   ?? 0);
    $guest_name         = trim($_POST["guest_name"]           ?? "");
    $phone              = trim($_POST["phone"]                ?? "");
    $email              = trim($_POST["email"]                ?? "");
    $address            = trim($_POST["address"]              ?? "");
    $checkin_date       = trim($_POST["checkin_date"]         ?? "");
    $checkin_time       = trim($_POST["checkin_time"]         ?? "");
    $checkout_date      = trim($_POST["checkout_date"]        ?? "");
    $checkout_time      = trim($_POST["checkout_time"]        ?? "");
    $adults             = intval($_POST["adults"]             ?? 1);
    $children           = intval($_POST["children"]          ?? 0);
    $child_age          = trim($_POST["children_ages"]        ?? "");
    $extra_mattress     = isset($_POST["extra_mattress"])      ? "Yes" : "No";
    $meal_plan          = trim($_POST["meal_plan"]            ?? "EP");
    $business_source_id = intval($_POST["business_source_id"] ?? 0) ?: null;
    $person_in_charge   = trim($_POST["person_in_charge"]    ?? "");

    if ($room_hotel_id > 0 && $room_category_id > 0 && $guest_name != "") {
        $sql = "INSERT INTO room_bookings
                (booking_id, hotel_id, room_category_id, guest_name, phone, email, address,
                 checkin_date, checkin_time, checkout_date, checkout_time,
                 adults, children, child_age, extra_mattress, meal_plan,
                 business_source_id, person_in_charge)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiissssssssiisssis",
            $booking_id, $room_hotel_id, $room_category_id,
            $guest_name, $phone, $email, $address,
            $checkin_date, $checkin_time, $checkout_date, $checkout_time,
            $adults, $children, $child_age, $extra_mattress, $meal_plan,
            $business_source_id, $person_in_charge
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* ── 3. MICE Booking ── */
    $mice_company  = trim($_POST["mice_guest_name"]     ?? "");
    $mice_pax      = intval($_POST["mice_pax"]          ?? 0);
    $mice_phone    = trim($_POST["mice_phone"]          ?? "");
    $mice_details  = trim($_POST["mice_banquet_details"] ?? "");

    if ($mice_company != "" && $mice_pax > 0) {
        $sql  = "INSERT INTO mice_bookings (booking_id, company_name, attendees, contact_number, banquet_details)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisss",
            $booking_id, $mice_company, $mice_pax, $mice_phone, $mice_details
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* ── 4. Cab Booking ── */
    $cab_guest    = trim($_POST["cab_guest_name"]     ?? "");
    $cab_vendor   = intval($_POST["cab_vendor_id"]    ?? 0) ?: null;
    $cab_date     = trim($_POST["cab_travel_date"]    ?? "");
    $cab_pickup   = trim($_POST["cab_pickup_location"] ?? "");
    $cab_drop     = trim($_POST["cab_drop_location"]  ?? "");

    if ($cab_guest != "" && $cab_pickup != "" && $cab_drop != "") {
        $sql  = "INSERT INTO cab_bookings (booking_id, guest_name, vendor_id, travel_date, pickup_location, drop_location)
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isisss",
            $booking_id, $cab_guest, $cab_vendor, $cab_date, $cab_pickup, $cab_drop
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* ── 5. Ticket Booking ── */
    $ticket_guest  = trim($_POST["ticket_guest_name"]  ?? "");
    $ticket_type   = trim($_POST["ticket_type"]        ?? "");
    $ticket_date   = trim($_POST["ticket_travel_date"] ?? "");
    $ticket_amount = floatval($_POST["ticket_amount"]  ?? 0);

    if ($ticket_guest != "" && $ticket_type != "") {
        $sql  = "INSERT INTO ticket_bookings (booking_id, guest_name, ticket_type, travel_date, amount)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssd",
            $booking_id, $ticket_guest, $ticket_type, $ticket_date, $ticket_amount
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* ── 6. Package Booking ── */
    $pkg_cab_amount    = floatval($_POST["package_cab_amount"]    ?? 0);
    $pkg_flight_amount = floatval($_POST["package_flight_amount"] ?? 0);
    $pkg_other         = trim($_POST["package_other_services"]    ?? "");
    $pkg_hotel_names   = $_POST["package_hotel_name"] ?? [];

    if (!empty($pkg_hotel_names) && $pkg_hotel_names[0] != "") {
        $sql  = "INSERT INTO package_bookings (booking_id, cab_amount, flight_amount, other_services)
                 VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "idds",
            $booking_id, $pkg_cab_amount, $pkg_flight_amount, $pkg_other
        );
        mysqli_stmt_execute($stmt);
        $package_booking_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Package hotels — use hotel_id=1, room_category_id=1 as default (no dropdown in form)
        $pkg_rooms  = $_POST["package_hotel_room"]   ?? [];
        $pkg_nights = $_POST["package_hotel_nights"] ?? [];

        foreach ($pkg_hotel_names as $i => $h_name) {
            if (trim($h_name) == "") continue;
            $nights = intval($pkg_nights[$i] ?? 1);
            $sql  = "INSERT INTO package_hotels (package_booking_id, hotel_id, room_category_id, nights)
                     VALUES (?, 1, 1, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $package_booking_id, $nights);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ── 7. Payables ── */
    $payables_json = trim($_POST["payables_json"] ?? "[]");
    $payables      = json_decode($payables_json, true);

    if (is_array($payables) && count($payables) > 0) {
        foreach ($payables as $p) {
            $p_type   = trim($p["type"]   ?? "");
            $p_vendor = trim($p["vendor"] ?? "");
            $p_amount = floatval($p["amount"] ?? 0);
            $p_status = trim($p["status"] ?? "Pending");
            $p_notes  = trim($p["notes"]  ?? "");

            // Map allowed enum values
            $allowed_types = ["Hotel","Cab","Flight"];
            if (!in_array($p_type, $allowed_types)) $p_type = "Hotel";

            $sql  = "INSERT INTO payables (booking_id, service_type, amount, payment_status, notes)
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isdss",
                $booking_id, $p_type, $p_amount, $p_status, $p_notes
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ── Commit ── */
    mysqli_commit($conn);

    $response = [
        "status"     => "success",
        "booking_id" => $booking_id,
        "booking_ref"=> $booking_ref,
        "message"    => "Booking Saved Successfully"
    ];

} catch (Exception $e) {
    mysqli_rollback($conn);
    $response = [
        "status"  => "error",
        "message" => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
?>
