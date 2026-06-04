# Package Appointments Module

## Overview
A dedicated module for managing appointments that include medicine packages or lab packages. This module displays today's appointments where the OPD type has `including_medicine=1` OR `including_labs=1`.

## Files Created

### 1. Controller
**File**: `app/Http/Controllers/Appointments/PackageAppointmentController.php`

**Methods**:
- `index()`: Display the package appointments listing page
- `get_package_appointments(Request $request)`: DataTables AJAX endpoint for fetching appointments

**Query Logic**:
```php
Appointment::with(['patient', 'consultant', 'opd_type'])
    ->whereHas('opd_type', function ($query) {
        $query->where(function ($q) {
            $q->where('including_medicine', 1)
              ->orWhere('including_labs', 1);
        });
    })
    ->whereDate('appointment_date', Carbon::today())
    ->where('is_active', 1)
    ->orderBy('appointment_date', 'desc')
```

### 2. View
**File**: `resources/views/appointments/package_appointments.blade.php`

**Features**:
- DataTable with server-side processing
- Displays appointment details with patient, consultant, and OPD type info
- Shows package type badges (Medicine/Labs)
- Action buttons:
  - **Medicine Button**: Links to package sale page (if including_medicine=1)
  - **Investigation Button**: Opens modal to add investigations (if including_labs=1)
  - **View Button**: View appointment details

### 3. Routes
**File**: `routes/web.php`

**Routes Added**:
```php
Route::get('package-appointments', [PackageAppointmentController::class, 'index'])
    ->name('pos.package_appointments');

Route::get('get-package-appointments', [PackageAppointmentController::class, 'get_package_appointments'])
    ->name('pos.get_package_appointments');
```

## Features

### 1. Filtering Criteria
- **Date**: Only shows appointments for current date (today)
- **OPD Type**: Only appointments where opd_type has:
  - `including_medicine = 1` OR
  - `including_labs = 1`
- **Status**: Only active appointments (`is_active = 1`)

### 2. DataTable Columns
1. **#** - Row number
2. **Appointment No** - Appointment number
3. **Patient Name** - Patient's full name
4. **MR No** - Medical record number
5. **Consultant** - Consulting doctor name
6. **OPD Type** - Type of OPD visit
7. **Package Type** - Badge display:
   - Green badge "Medicine" if including_medicine=1
   - Blue badge "Labs" if including_labs=1
8. **Appointment Date** - Date and time of appointment
9. **Fee** - Consultation fee (formatted)
10. **Actions** - Action buttons

### 3. Action Buttons

#### Medicine Button (Green)
- **Visible**: When `including_medicine = 1`
- **Action**: Redirects to package sale page with appointment_id parameter
- **URL**: `/package-sale?appointment_id={id}`
- **Icon**: Pills icon

#### Investigation Button (Blue)
- **Visible**: When `including_labs = 1`
- **Action**: Opens modal to add investigations
- **Functionality**: Reuses existing investigation modal from appointments module
- **Icon**: Flask icon

#### View Button (Primary)
- **Visible**: Always
- **Action**: View appointment details
- **Icon**: Eye icon

### 4. Investigation Modal Integration
The view includes the investigation modal that:
- Shows already added investigations
- Lists available investigations from OPD type
- Allows selection of multiple investigations
- Saves investigations to patient_investigations table
- Reloads DataTable after saving

## Usage

### Access Package Appointments Page
```
URL: http://your-domain/package-appointments
Route Name: pos.package_appointments
```

### DataTables AJAX Endpoint
```
URL: http://your-domain/get-package-appointments
Method: GET
Route Name: pos.get_package_appointments
```

## Database Tables Used
- `appointments` - Main appointments table
- `opd_type` - OPD type configuration
- `patients` - Patient information
- `users` (consultants) - Consultant information
- `patient_investigations` - Investigation assignments

## Dependencies
- **DataTables**: Server-side processing
- **Bootstrap 5**: UI components and modals
- **jQuery**: AJAX requests and DOM manipulation
- **Carbon**: Date handling
- **Yajra DataTables**: Laravel DataTables package

## Integration Points

### 1. Package Sale Integration
When user clicks "Medicine" button:
- Redirects to `/package-sale` with appointment_id
- Package sale page can pre-populate patient information
- Can apply package discount rules

### 2. Investigation Module Integration
When user clicks "Investigation" button:
- Opens investigation modal
- Uses existing routes:
  - `pos.get_opd_investigations` - Load investigations
  - `pos.save_patient_investigations` - Save selections
- Saves to `patient_investigations` table

## Benefits
1. **Centralized View**: All package appointments in one place
2. **Date Filtered**: Shows only today's appointments
3. **Quick Actions**: Direct access to medicine and investigation modules
4. **Real-time Updates**: DataTable reloads after actions
5. **Visual Indicators**: Badges show package types at a glance

## Example Data Flow

### Scenario 1: Medicine Package
1. Patient has appointment with OPD type "UTI Package"
2. OPD type has `including_medicine=1`
3. Appointment appears in package appointments list
4. Green "Medicine" badge displayed
5. User clicks "Medicine" button
6. Redirects to package sale page
7. User selects medicines and completes sale

### Scenario 2: Lab Package
1. Patient has appointment with OPD type "Diagnostic Package"
2. OPD type has `including_labs=1`
3. Appointment appears in package appointments list
4. Blue "Labs" badge displayed
5. User clicks "Investigation" button
6. Modal opens with available investigations
7. User selects investigations and saves
8. Investigations added to patient record

### Scenario 3: Combined Package
1. Patient has appointment with OPD type "Complete Health Package"
2. OPD type has `including_medicine=1` AND `including_labs=1`
3. Both badges displayed
4. Both action buttons available
5. User can perform both actions

## Testing Checklist
- [ ] Access package appointments page
- [ ] Verify only today's appointments displayed
- [ ] Check filtering by including_medicine/including_labs
- [ ] Test Medicine button navigation
- [ ] Test Investigation button modal
- [ ] Verify badge display logic
- [ ] Test DataTable sorting and pagination
- [ ] Test search functionality
- [ ] Verify action buttons visibility rules
- [ ] Test investigation save functionality

## Future Enhancements
- [ ] Date range filter
- [ ] Export to PDF/Excel
- [ ] Print appointment list
- [ ] Bulk actions
- [ ] Status tracking
- [ ] Package completion status
- [ ] Reports and analytics

---

**Created**: January 24, 2026  
**Status**: Complete - Ready for testing  
**Version**: 1.0
