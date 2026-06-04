# HX Complaints Setup & Usage Guide

## Step 1: Create Database Table

**Run this SQL command in your MySQL/phpMyAdmin:**

```sql
CREATE TABLE `hx_complaints` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `complaint` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Blood Pressure (e.g., 120/80)',
  `temp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Temperature (e.g., 98.6°F or 37°C)',
  `pulse` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pulse Rate (e.g., 72 bpm)',
  `rr` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Respiratory Rate (e.g., 16/min)',
  `investigation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User ID who recorded this',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hx_complaints_appointment_id_foreign` (`appointment_id`),
  KEY `hx_complaints_patient_id_foreign` (`patient_id`),
  KEY `hx_complaints_recorded_by_foreign` (`recorded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Or run the SQL file:**
```bash
# In MySQL command line:
source d:/wamp64/www/hospital/database/hx_complaints_table.sql;

# Or in phpMyAdmin:
# Import > Choose file > hx_complaints_table.sql > Go
```

## Step 2: Verify Table Created

```sql
SHOW TABLES LIKE 'hx_complaints';
DESCRIBE hx_complaints;
```

## Step 3: Create Test Appointment (if needed)

```sql
-- Check if you have appointments for today
SELECT * FROM appointments WHERE DATE(appointment_date) = CURDATE();

-- If no appointments exist, insert a test one:
INSERT INTO appointments (
    patient_id, 
    consultant_id, 
    appointment_date, 
    appointment_time, 
    is_active, 
    created_at
) VALUES (
    1,  -- Replace with valid patient_id from your patients table
    1,  -- Replace with valid consultant_id from your consultants table
    CURDATE(), 
    '10:00 AM', 
    1, 
    NOW()
);
```

## Step 4: Access the Module

1. **Login to your system**
2. **Navigate to:** `http://localhost/hospital/nursing-hx-complaints`
   - Or: `http://your-domain/nursing-hx-complaints`

## Step 5: How to Use

### You Should See:

#### Section 1: Today's Appointments Table (Top)
- A table showing all appointments for today
- Columns: Sr#, MR No, Patient Name, Phone, Age, Consultant, Time, Status, Action
- Each row has an **Action button**:
  - **Green "Record" button** - for appointments without HX recorded
  - **Blue "View/Edit" button** - for appointments with HX already recorded

#### Section 2: HX Recording Form (Hidden until you click a button)
- Patient Information (auto-filled)
- Vital Signs section with fields:
  - BP (Blood Pressure)
  - Temp (Temperature)
  - Pulse
  - R/R (Respiratory Rate)
- Complaint text area
- Investigation text area
- Save button

### To Record HX:

1. **Find the appointment** in the table
2. **Click the green "Record" button** in the Action column
3. **The form will appear below** with patient info already filled
4. **Enter vital signs:**
   - BP: `120/80`
   - Temp: `98.6°F` or `37°C`
   - Pulse: `72 bpm`
   - R/R: `16/min`
5. **Type complaint** details
6. **Type investigation** notes
7. **Click "Save HX Complaint"**
8. Form closes, table refreshes, status changes to "Recorded"

### To View/Edit Existing HX:

1. **Click the blue "View/Edit" button**
2. Form appears with existing data pre-filled
3. Modify any fields
4. Click "Save HX Complaint"

## Troubleshooting

### Problem 1: Page shows "No appointments found for today"

**Solution:**
```sql
-- Check appointments
SELECT id, patient_id, consultant_id, appointment_date, appointment_time 
FROM appointments 
WHERE DATE(appointment_date) = CURDATE() 
AND is_active = 1;

-- If empty, add test appointment (see Step 3)
```

### Problem 2: Page doesn't load or shows errors

**Check browser console (F12 > Console tab) for errors:**

1. **If you see "jQuery is not loaded":**
   - Your layout file needs jQuery
   - Add to layout before closing `</body>`:
   ```html
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   ```

2. **If you see "DataTables is not loaded":**
   - Add after jQuery:
   ```html
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
   <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
   ```

3. **If you see 500 error:**
   - Check Laravel logs: `storage/logs/laravel.log`
   - Table might not exist - run SQL (Step 1)

### Problem 3: Buttons don't work when clicked

**Open browser console and check:**
```javascript
// Test in console (F12):
console.log('jQuery:', typeof $);  // Should show "function"
console.log('DataTables:', typeof $.fn.DataTable);  // Should show "function"
```

**If buttons don't respond:**
1. Check network tab (F12 > Network)
2. Click Record button
3. Look for AJAX call to `hx/get-appointment-details`
4. Check response

### Problem 4: Form doesn't save

**Check:**
1. Browser console for errors
2. Network tab for 422 validation errors
3. Laravel logs for server errors

## Quick Test

Run this in your browser console after page loads:

```javascript
// Test 1: Check dependencies
console.log('jQuery loaded:', typeof $ !== 'undefined');
console.log('DataTables loaded:', typeof $.fn.DataTable !== 'undefined');

// Test 2: Check DataTable initialized
console.log('Table exists:', $('#appointments_table').length > 0);
console.log('DataTable initialized:', $.fn.DataTable.isDataTable('#appointments_table'));

// Test 3: Manual form show (should make form visible)
$('#hx_form').slideDown();
```

## Files Created

1. **Database:** `database/hx_complaints_table.sql`
2. **Model:** `app/Models/HxComplaint.php`
3. **Controller:** `app/Http/Controllers/Admin/HxComplaintController.php`
4. **View:** `resources/views/hx_complaints/nursing_hx_complaints.blade.php`
5. **Routes:** Added to `routes/web.php` (bottom of file)

## Support

If still not working:
1. Run SQL script
2. Check browser console (F12)
3. Check `storage/logs/laravel.log`
4. Verify appointments exist for today
5. Make sure you're logged in
6. Clear browser cache (Ctrl+Shift+Delete)

---

**Module Status:** ✅ Ready to use after SQL table creation
**Last Updated:** December 7, 2025
