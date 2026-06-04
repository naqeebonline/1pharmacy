# Package System Implementation

## Overview
This document describes the complete Package Management System that allows creating packages with multiple products and investigations.

## Database Structure

### Tables Created

#### 1. `packages` Table
- `id` - Primary Key
- `name` - Package Name
- `description` - Package Description (nullable)
- `price` - Package Price
- `is_active` - Active Status (1 = active, 0 = inactive)
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

#### 2. `package_products` Table (Pivot)
- `id` - Primary Key
- `package_id` - Foreign Key to packages table
- `product_id` - Foreign Key to products table (ProductID)
- `quantity` - Product quantity in package
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

#### 3. `package_investigations` Table (Pivot)
- `id` - Primary Key
- `package_id` - Foreign Key to packages table
- `investigation_sub_category_id` - Foreign Key to investigation_sub_category table
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

## Files Created/Modified

### 1. Models
**File:** `app/Models/Package.php`
- Defines relationships with Product and InvestigationSubCategory models
- Uses many-to-many relationships with pivot tables

### 2. Controller
**File:** `app/Http/Controllers/Admin/PackageController.php`

**Methods:**
- `index()` - Display package list page
- `list_packages()` - Return DataTables JSON for package listing
- `create()` - Show create package form
- `store()` - Store new package
- `edit($id)` - Show edit package form
- `update($id)` - Update existing package
- `destroy($id)` - Soft delete package (sets is_active to 0)
- `get_package($id)` - Get package details as JSON

### 3. Routes
**File:** `routes/web.php`

Added routes:
```php
Route::get("packages", [PackageController::class, "index"])->name("pos.packages");
Route::get("list_packages", [PackageController::class, "list_packages"])->name("pos.list_packages");
Route::get("create_package", [PackageController::class, "create"])->name("pos.create_package");
Route::post("store_package", [PackageController::class, "store"])->name("pos.store_package");
Route::get("edit_package/{id}", [PackageController::class, "edit"])->name("pos.edit_package");
Route::post("update_package/{id}", [PackageController::class, "update"])->name("pos.update_package");
Route::post("delete_package/{id}", [PackageController::class, "destroy"])->name("pos.delete_package");
Route::get("get_package/{id}", [PackageController::class, "get_package"])->name("pos.get_package");
```

### 4. Views

#### Package List View
**File:** `resources/views/packages/list.blade.php`
- Displays all packages in a DataTable
- Columns: ID, Name, Description, Price, Products Count, Investigations Count, Actions
- Features: Search, Sort, Export (CSV, Excel, PDF, Print)
- Actions: Edit, Delete

#### Package Form View
**File:** `resources/views/packages/form.blade.php`
- Create and Edit package form
- Sections:
  - Package Details (Name, Price, Description)
  - Products Section (Select products with quantities)
  - Investigations Section (Select investigations)
- Features:
  - Select2 for product and investigation selection
  - Dynamic add/remove products and investigations
  - Quantity input for each product
  - Validation on form submission

### 5. Migrations
**Files:**
- `database/migrations/2026_01_05_184652_create_packages_table.php`
- `database/migrations/2026_01_05_184731_create_package_products_table.php`
- `database/migrations/2026_01_05_184748_create_package_investigations_table.php`

## Installation Steps

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the three required tables in your database.

### 2. Access Package Management
Navigate to: `/packages` (or use route name `pos.packages`)

## Usage Guide

### Creating a Package

1. **Navigate to Package List**
   - Go to `/packages` or click "Packages" in your navigation menu

2. **Click "Add New Package"**
   - Opens the package creation form

3. **Fill Package Details**
   - Enter Package Name (required)
   - Enter Package Price (required)
   - Enter Description (optional)

4. **Add Products**
   - Select a product from the dropdown
   - Click "Add Product" button
   - Set quantity for each product
   - Repeat to add multiple products

5. **Add Investigations**
   - Select an investigation from the dropdown
   - Click "Add Investigation" button
   - Repeat to add multiple investigations

6. **Save Package**
   - Click "Create Package" button
   - You'll be redirected to the package list

### Editing a Package

1. **From Package List**
   - Click the edit icon (pencil) next to the package you want to edit

2. **Update Details**
   - Modify package name, price, or description
   - Add or remove products/investigations
   - Adjust product quantities

3. **Save Changes**
   - Click "Update Package" button

### Deleting a Package

1. **From Package List**
   - Click the delete icon (trash) next to the package
   - Confirm the deletion
   - Package will be soft-deleted (is_active set to 0)

## API Endpoints

### Get Package Details
**Endpoint:** `GET /get_package/{id}`

**Response:**
```json
{
    "status": true,
    "data": {
        "id": 1,
        "name": "Health Checkup Package",
        "description": "Complete health checkup",
        "price": 5000.00,
        "is_active": 1,
        "products": [...],
        "investigations": [...]
    }
}
```

## Features

### Core Features
✅ Create packages with multiple products and investigations
✅ Edit existing packages
✅ Delete packages (soft delete)
✅ View all packages in a searchable, sortable table
✅ Export package list (CSV, Excel, PDF, Print)
✅ Set product quantities in packages
✅ Assign multiple products to a package
✅ Assign multiple investigations to a package

### Technical Features
✅ DataTables integration for fast searching and filtering
✅ Select2 for better dropdown experience
✅ AJAX-based operations for smooth UX
✅ Foreign key constraints for data integrity
✅ Many-to-many relationships with pivot tables
✅ Eloquent ORM for database operations
✅ Responsive design
✅ Form validation

## Database Relationships

```
packages
├── products (many-to-many via package_products)
│   └── pivot: quantity
└── investigations (many-to-many via package_investigations)

products (ProductID as primary key)
└── packages (many-to-many via package_products)

investigation_sub_category (id as primary key)
└── packages (many-to-many via package_investigations)
```

## Security Considerations

- All routes are protected with authentication middleware
- CSRF token validation on all POST requests
- Input validation on form submissions
- SQL injection protection via Eloquent ORM
- Soft delete instead of hard delete for data preservation

## Future Enhancements (Optional)

- Package activation/deactivation toggle
- Package usage tracking
- Package discount management
- Package expiry dates
- Package categories
- Package images
- Bulk operations (activate, deactivate, delete multiple packages)
- Package duplication feature
- Package usage reports
- Package price history

## Troubleshooting

### Issue: "Table doesn't exist" error
**Solution:** Run migrations: `php artisan migrate`

### Issue: "Foreign key constraint fails"
**Solution:** Ensure products and investigation_sub_category tables exist with correct primary keys

### Issue: Select2 not working
**Solution:** Make sure jQuery and Select2 assets are loaded in your layout

### Issue: DataTables not loading data
**Solution:** Check browser console for JavaScript errors and verify route names are correct

## Support

For any issues or questions, please refer to:
- Laravel Documentation: https://laravel.com/docs
- DataTables Documentation: https://datatables.net/
- Select2 Documentation: https://select2.org/

## File Structure Summary

```
app/
├── Http/Controllers/Admin/
│   └── PackageController.php
└── Models/
    └── Package.php

database/migrations/
├── 2026_01_05_184652_create_packages_table.php
├── 2026_01_05_184731_create_package_products_table.php
└── 2026_01_05_184748_create_package_investigations_table.php

resources/views/packages/
├── list.blade.php
└── form.blade.php

routes/
└── web.php (modified)
```

## Conclusion

The Package Management System is now fully implemented with complete CRUD functionality. You can create packages, assign products with quantities, assign investigations, edit packages, and delete packages. The system uses proper database relationships and provides a user-friendly interface for managing packages.
