# Package Sale System Implementation

## Overview
A complete package sale system has been created as a duplicate of the retail sale system. This allows managing package-based sales separately from regular retail sales.

## Files Created/Modified

### 1. View File
- **File**: `resources/views/sale/package_sale.blade.php`
- **Description**: Blade template for package sale interface (copied from retail_sale.blade.php)
- **Features**: 
  - Product selection with batch numbers
  - Patient/appointment association
  - Discount management
  - Invoice generation

### 2. Controller
- **File**: `app/Http/Controllers/Admin/PackageSaleController.php`
- **Methods**:
  - `package_sale()`: Display package sale form
    - Sets session store_id = 2
    - Sets store_name = "Package Sale"
    - Loads appointments from last 2 days
    - Loads active products with available quantities
    - Generates invoice number
  
  - `save_package_sale()`: Process and save package sale
    - Validates product discounts based on allow_percentage
    - Creates Sale and TempSale records
    - Creates SaleDetails and TempSaleDetails records
    - Updates GRN (Goods Receipt Note) stock quantities
    - Returns JSON response with success/error status
  
  - `returnInvoiceNumber()`: Generate next invoice number

### 3. Routes
- **File**: `routes/web.php` (added at the end)
- **Routes**:
  ```php
  Route::get('package-sale', [PackageSaleController::class, 'package_sale'])
      ->name('pos.package_sale');
  
  Route::post('save_package_sale', [PackageSaleController::class, 'save_package_sale'])
      ->name('pos.save_package_sale');
  ```
- **Middleware**: auth (requires authentication)

## Database Tables Used
- `sales`: Main sale records
- `temp_sales`: Temporary sale records
- `sale_details`: Sale line items
- `temp_sale_details`: Temporary sale line items
- `grn_details`: Stock management (RemainingQuantity updated)
- `patients`: Customer information
- `appointments`: Appointment linkage
- `products`: Product details

## Key Features

### Session Management
```php
session(['store_id' => 2]);
session(['store_name' => "Package Sale"]);
session(['is_free' => 0]);
```

### Discount Logic
- Checks product's `allow_percentage` field
- If `allow_percentage = 0`: No discount allowed
- If `allow_percentage > 0`: Limits discount to allowed percentage
- If `allow_percentage = null`: Applies requested discount

### Stock Management
- Uses FIFO (First In First Out) method
- Updates `RemainingQuantity` in `grn_details` table
- Processes stock from oldest batches first

### Transaction Safety
- Uses database transactions (DB::beginTransaction())
- Rolls back on error
- Returns JSON response for AJAX handling

## Usage

### Access Package Sale Page
```
URL: http://your-domain/package-sale
Route Name: pos.package_sale
```

### Save Package Sale (AJAX)
```
URL: http://your-domain/save_package_sale
Method: POST
Route Name: pos.save_package_sale
```

### Request Format
```javascript
{
    patient_id: 123,
    appointment_id: 456,
    invoice_discount: 50,
    discount_percentage: 5,
    discount_amount: 100,
    ReceivedAmount: 1000,
    ReceivedAmountFromCustomer: 1000,
    bill_date: "2024-01-15",
    BillDiscription: "Package sale description",
    medicine_type: "prescription",
    ProductList: [
        {
            ProductID: 1,
            ProductName: "Product 1",
            Quantity: 10,
            UnitePrice: 50,
            batch_number: "BATCH001",
            discount_percentage: 5
        }
    ]
}
```

### Response Format
```javascript
// Success
{
    success: true,
    message: "Package sale saved successfully!",
    invoice_no: 12345,
    sale_id: 67890
}

// Error
{
    success: false,
    message: "Error saving package sale: [error details]"
}
```

## Technical Notes

### Dependencies
- Laravel Framework
- Eloquent ORM
- Database transactions
- Session management
- JSON encoding for bill_details

### Models Used
- Sale
- TempSale
- SaleDetails
- TempSaleDetails
- Product
- GrnDetails
- Patient
- Appointment
- Store

### Important Fields
- `SCID`: 1 for sehat card, 2 for retail/package sale
- `store_id`: Defaults to 2 for package sale
- `is_free`: 0 for package sale (not free)
- `bill_details`: JSON encoded product list
- `RemainingQuantity`: Updated in GRN for stock tracking

## Future Enhancements
- Package sale reports
- Package sale returns/refunds
- Package sale printing
- Package sale listing with DataTables
- Package sale view/edit functionality
- Package sale deletion (soft delete)

## Testing Checklist
- [ ] Access package sale page
- [ ] Load appointments
- [ ] Select products
- [ ] Apply discounts
- [ ] Select batch numbers
- [ ] Save package sale
- [ ] Verify invoice generation
- [ ] Check stock updates in GRN
- [ ] Test discount validation
- [ ] Test with and without patient
- [ ] Test with and without appointment
- [ ] Test error handling
- [ ] Test transaction rollback on error

## Related Systems
- Retail Sale System (parent system)
- Sehat Card Pharmacy Sale
- Retail Sale Point
- GRN (Goods Receipt Note) System
- Appointment System
- Patient Management

---

**Created**: January 2024  
**Status**: Complete - Ready for testing  
**Version**: 1.0
