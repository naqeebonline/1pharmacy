# GRN Returns Module Implementation

## Overview
This module handles the return of expired or short expiry items to suppliers. It allows users to select items from inventory, specify return quantities, and process returns which are saved in the database.

## Database Tables

### 1. `grn_returns` (Main Return Transaction)
Stores the header information for each return transaction.

**Columns:**
- `ReturnID` (PK) - Auto-increment unique identifier
- `SCID` - Supplier ID (who items are being returned to)
- `ReturnDate` - Date of the return
- `TotalAmount` - Total value of all items being returned
- `Status` - Return status (Pending, Approved, Rejected, Completed)
- `CreatedBy` - User ID who created the return
- `ApprovedBy` - User ID who approved the return (nullable)
- `CreatedAt` - Timestamp when return was created
- `ApprovedAt` - Timestamp when return was approved (nullable)
- `Remarks` - Additional notes about the return

### 2. `grn_return_details` (Individual Return Items)
Stores details of each item in a return transaction.

**Columns:**
- `ReturnDetailID` (PK) - Auto-increment unique identifier
- `ReturnID` (FK) - References grn_returns.ReturnID
- `GDID` - Original GRN Detail ID (links back to purchased item)
- `ProductID` - Product being returned
- `BatchNo` - Batch number of the item
- `ExpiryDate` - Expiry date of the item
- `ReturnQuantity` - Quantity being returned
- `UnitPrice` - Price per unit
- `TotalAmount` - Line total (ReturnQuantity × UnitPrice)
- `CreatedAt` - Timestamp when detail was created

## Files Created/Modified

### Controllers
- `app/Http/Controllers/Admin/ExpiryUpdateController.php`
  - Added `save_return_to_supplier()` method
  - Added `grn_returns_list()` method - Display listing page
  - Added `get_grn_returns_list()` method - DataTables endpoint
  - Added `approve_grn_return()` method - Approve returns
  - Added `reject_grn_return()` method - Reject returns (restores quantities)
  - Added `delete_grn_return()` method - Delete pending returns (restores quantities)
  - Added `view_grn_return()` method - View return details
  - Added `edit_grn_return()` method - Edit page loader
  - Added `update_grn_return()` method - Save edited return changes with smart quantity management

### Routes
- `routes/web.php`
  - Added POST route: `expiry/save-return-to-supplier`
  - Added GET route: `grn-returns-list`
  - Added GET route: `expiry/get-grn-returns-list`
  - Added POST route: `expiry/approve-grn-return`
  - Added POST route: `expiry/reject-grn-return`
  - Added POST route: `expiry/delete-grn-return`
  - Added GET route: `expiry/view-grn-return/{id}`
  - Added GET route: `expiry/edit-grn-return/{id}`
  - Added POST route: `expiry/update-grn-return`

### Views
- `resources/views/expiry_update/return_expired_items.blade.php`
  - Updated JavaScript to send data via AJAX
  - Added loading state for submit button
  - Implemented success/error handling
- `resources/views/expiry_update/grn_returns_list.blade.php`
  - Returns listing with DataTables
  - Filter by supplier, status, date range
  - Action buttons (View, Edit, Approve, Reject, Delete)
  - Approve/Reject modals
- `resources/views/expiry_update/view_grn_return.blade.php`
  - Detailed view of return with all items
  - Shows approval/rejection information
  - In-page approve/reject actions
- `resources/views/expiry_update/edit_grn_return.blade.php`
  - Full edit functionality for pending returns
  - Modify quantities with real-time validation
  - Remove items from return
  - Real-time total calculations
  - Smart quantity management

### Migrations
- `database/migrations/2025_12_18_create_grn_returns_tables.php`

### SQL Scripts
- `database/grn_returns_tables.sql` (for manual reference)

## Functionality

### Return Process Flow
1. User selects items from the returnable items grid
2. Selected items appear in the "Selected Items for Return" section
3. User can edit return quantities (up to available quantity)
4. System calculates line totals and grand total automatically
5. User clicks "Process Return to Supplier" button
6. System validates data and saves to database
7. On success:
   - Creates new record in `grn_returns` table
   - Creates detail records in `grn_return_details` table
   - Updates `RemainingQuantity` in `grn_details` table (decrements)
   - Reloads the grid
   - Clears selected items

### Return Management Flow
1. **Listing Page** (`/grn-returns-list`)
   - View all return requests in a DataTable
   - Filter by supplier, status, and date range
   - Status badges (Pending, Approved, Rejected, Completed)
   - Action buttons based on status

2. **View Details** (`/expiry/view-grn-return/{id}`)
   - Complete return information
   - List of all items in the return
   - Approval/rejection details if applicable
   - Quick approve/reject actions for pending returns

3. **Edit Return** (`/expiry/edit-grn-return/{id}`) - **FULLY IMPLEMENTED**
   - Only available for Pending returns
   - Modify return quantities (within available limits)
   - Remove items from return
   - Real-time total calculations
   - Smart quantity restoration:
     - Removed items: Restore full quantity to inventory
     - Quantity decreased: Return difference to inventory
     - Quantity increased: Deduct difference from inventory
   - Validates all changes before saving

4. **Approve Return**
   - Changes status from Pending to Approved
   - Records approver and approval timestamp
   - Optional approval remarks
   - Quantities remain updated (not restored)

5. **Reject Return**
   - Changes status from Pending to Rejected
   - **Restores quantities** back to `grn_details.RemainingQuantity`
   - Required rejection reason
   - Records approver and rejection timestamp

6. **Delete Return**
   - Only allowed for Pending returns
   - **Restores quantities** back to `grn_details.RemainingQuantity`
   - Permanently deletes the return record
   - Cascade deletes related detail records

### Data Validation
- Minimum 1 item must be selected
- Return quantity must be between 1 and available quantity
- All required fields are validated
- Uses database transactions for data integrity

### Features
- **Supplier Filtering**: Only shows suppliers with returnable items
- **Product Filtering**: Only shows products with expiry dates and remaining quantity
- **Status Filtering**: Filter by expired, expiring soon, or near expiry
- **Checkbox State Persistence**: Selected items remain checked after filtering/searching
- **Duplicate Prevention**: Prevents same item from being added multiple times
- **Real-time Calculations**: Line totals and grand total update as quantities change
- **Transaction Safety**: Uses DB transactions to ensure data consistency

## Usage

### Routes
```
# Create Return
GET  /return-expired-items - Display return items page
GET  /expiry/get-returnable-items - DataTables AJAX endpoint
POST /expiry/save-return-to-supplier - Save return transaction

# List & Management
GET  /grn-returns-list - Display returns list page
GET  /expiry/get-grn-returns-list - DataTables AJAX endpoint for list
POST /expiry/approve-grn-return - Approve a return request
POST /expiry/reject-grn-return - Reject a return request
POST /expiry/delete-grn-return - Delete a pending return request
GET  /expiry/view-grn-return/{id} - View return details
GET  /expiry/edit-grn-return/{id} - Edit pending return
POST /expiry/update-grn-return - Save edited return changes
```

### JavaScript API
```javascript
// Selected items structure
selectedItems = [{
    gdid: 123,
    product_id: 45,
    product_name: "Product Name",
    batch_no: "BATCH001",
    expiry_date: "2025-12-31",
    available_qty: 100,
    unit_price: 25.50,
    supplier_id: 10,
    supplier_name: "Supplier Name",
    return_qty: 50  // Editable by user
}]
```

### Sample AJAX Request
```javascript
$.ajax({
    url: "/expiry/save-return-to-supplier",
    method: "POST",
    data: {
        _token: "csrf_token",
        items: selectedItems
    },
    success: function(response) {
        // Handle success
    },
    error: function(xhr) {
        // Handle error
    }
});
```

## Database Updates
When a return is processed:
1. `grn_returns` - New record inserted with return summary
2. `grn_return_details` - New records for each item
3. `grn_details.RemainingQuantity` - Decremented by return quantity

## Future Enhancements
- Print return voucher/receipt
- Supplier debit note generation
- Return history report with advanced filters
- Email notification to supplier
- Barcode scanning for item selection
- Batch approval for multiple returns
- Return analytics dashboard
- Add new items to existing pending returns

## Notes
- Returns are initially saved with 'Pending' status
- Approval workflow can be implemented in future
- RemainingQuantity is updated immediately upon return creation
- Foreign key constraint ensures referential integrity
