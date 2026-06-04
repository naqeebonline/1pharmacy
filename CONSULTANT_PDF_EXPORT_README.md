# Consultant PDF Export Feature

## Overview
This feature allows you to export all consultants' information to a professional PDF report. The PDF includes comprehensive consultant details, statistics, and is formatted for easy reading and printing.

## Features

### 1. Comprehensive Data Export
- **Consultant Details**: Name, CNIC, PMDC Number, Joining Date
- **Professional Information**: Department, Speciality, Type
- **Financial Information**: General OPD Fee, Consultant OPD Fee, Hospital Share, Consultant Share
- **Percentage Details**: Sehat Card Share %, Lab Share %

### 2. Professional PDF Format
- **Layout**: Landscape orientation for better table display
- **Styling**: Professional hospital report styling with headers and footers
- **Pagination**: Automatic page breaks every 35 records for readability
- **Statistics**: Summary statistics including totals and averages

### 3. Multiple Access Methods
- **Header Button**: Direct PDF export button in the consultant registration page
- **DataTables Integration**: Custom PDF button in the data table toolbar
- **Direct URL**: Accessible via route `/export_consultants_pdf`

## Usage

### Method 1: Using Header Button
1. Navigate to the Consultant Registration page
2. Click the green "Export PDF" button in the header
3. PDF will automatically download with filename: `All_Consultants_Report_YYYY-MM-DD_HH-MM-SS.pdf`

### Method 2: Using DataTables Button
1. Navigate to the Consultant Registration page
2. Look for the "Custom PDF" button in the DataTables toolbar
3. Click the button to generate and download the PDF

### Method 3: Direct URL Access
- Access directly via: `yoursite.com/export_consultants_pdf`

## Technical Implementation

### Files Created/Modified

#### Controller Method
- **File**: `app/Http/Controllers/GeneralConfigration/ConsultantController.php`
- **Method**: `exportConsultantsPdf()`
- **Features**:
  - Error handling with try-catch
  - Statistics calculations
  - Professional PDF generation using DomPDF

#### PDF Template
- **File**: `resources/views/general_configuration/consultants_pdf.blade.php`
- **Features**:
  - Professional styling with CSS
  - Responsive table layout
  - Automatic pagination
  - Statistics summary section
  - Hospital branding

#### Route Addition
- **File**: `routes/web.php`
- **Route**: `GET /export_consultants_pdf`
- **Name**: `pos.export_consultants_pdf`

#### UI Enhancements
- **File**: `resources/views/general_configuration/consultant_registration.blade.php`
- **Changes**:
  - Added header export button
  - Enhanced DataTables with custom PDF button
  - Improved header layout with proper styling

## PDF Content Structure

### Header Section
- Hospital Management System branding
- Report title and generation timestamp
- Professional styling with borders

### Statistics Summary
- Total consultants count
- Average OPD fees (General and Consultant)
- Department-wise consultant breakdown

### Main Data Table
- All consultant information in tabular format
- Professional styling with alternating row colors
- Proper column headers and data alignment
- Currency formatting for financial fields
- Percentage formatting for share fields

### Footer Section
- Generation timestamp
- System branding
- Professional disclaimer

## Benefits

1. **Professional Reports**: Generate professional PDF reports for management
2. **Data Backup**: Export consultant data for backup purposes
3. **Documentation**: Create documentation for regulatory compliance
4. **Analysis**: Statistics help in understanding consultant distribution
5. **Printing**: Optimized layout for printing on A4 paper (landscape)

## Error Handling

The system includes comprehensive error handling:
- Try-catch blocks for PDF generation
- Fallback error messages
- Graceful degradation if data is missing

## Performance

- Optimized database queries with eager loading
- Efficient PDF generation using DomPDF
- Automatic pagination for large datasets
- Memory-efficient processing

## Customization

The PDF template can be easily customized by modifying:
- `consultants_pdf.blade.php` for layout changes
- CSS styles within the template for appearance
- Controller method for additional statistics or data

## Dependencies

- **DomPDF**: Already installed (`barryvdh/laravel-dompdf`)
- **Laravel Framework**: Core functionality
- **Bootstrap Icons**: For button icons