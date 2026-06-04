# Database Schema

Generated: 2026-04-11 00:00:10
Database: hospital (mysql)

## Tables
- [appointments](##appointments)
- [apps](##apps)
- [companies](##companies)
- [company_levels](##company_levels)
- [company_types](##company_types)
- [consultant_department](##consultant_department)
- [consultant_procedure_pricing](##consultant_procedure_pricing)
- [consultant_procedures](##consultant_procedures)
- [consultant_sc_payments_details](##consultant_sc_payments_details)
- [consultant_shares_payment_invoice](##consultant_shares_payment_invoice)
- [consultant_speciality](##consultant_speciality)
- [consultant_type](##consultant_type)
- [consultants](##consultants)
- [daily_user_closings](##daily_user_closings)
- [departments](##departments)
- [districts](##districts)
- [expense_sub](##expense_sub)
- [expenses](##expenses)
- [finance_heads](##finance_heads)
- [finance_transactions](##finance_transactions)
- [finance_vouchers](##finance_vouchers)
- [general_expenses](##general_expenses)
- [grn](##grn)
- [grn_audit](##grn_audit)
- [grn_audit_details](##grn_audit_details)
- [grn_damage](##grn_damage)
- [grn_details](##grn_details)
- [grn_request](##grn_request)
- [grn_request_details](##grn_request_details)
- [grn_return_details](##grn_return_details)
- [grn_returns](##grn_returns)
- [hx_complaints](##hx_complaints)
- [in_patient_admissions](##in_patient_admissions)
- [in_patient_payments](##in_patient_payments)
- [investigation_category](##investigation_category)
- [investigation_sub_category](##investigation_sub_category)
- [investigation_sub_category_parameters](##investigation_sub_category_parameters)
- [item_form](##item_form)
- [item_generic_name](##item_generic_name)
- [item_make](##item_make)
- [jobs](##jobs)
- [machine_categories](##machine_categories)
- [machine_patients](##machine_patients)
- [machine_shifts](##machine_shifts)
- [main_category](##main_category)
- [market](##market)
- [menus](##menus)
- [migrations](##migrations)
- [opd_type](##opd_type)
- [opd_type_investigations](##opd_type_investigations)
- [opd_type_products](##opd_type_products)
- [parameter_heading](##parameter_heading)
- [password_resets](##password_resets)
- [patient_admissions](##patient_admissions)
- [patient_baby](##patient_baby)
- [patient_discharge_checklist](##patient_discharge_checklist)
- [patient_investigation_result](##patient_investigation_result)
- [patient_investigations](##patient_investigations)
- [patient_investigations_payments](##patient_investigations_payments)
- [patient_locations](##patient_locations)
- [patient_nurse_notes](##patient_nurse_notes)
- [patient_ot_notes](##patient_ot_notes)
- [patient_refunds](##patient_refunds)
- [patient_service_charges](##patient_service_charges)
- [patient_vitals](##patient_vitals)
- [patients](##patients)
- [payment_type](##payment_type)
- [payments_details](##payments_details)
- [permission_role](##permission_role)
- [permission_routes](##permission_routes)
- [permission_user](##permission_user)
- [permissions](##permissions)
- [personal_access_tokens](##personal_access_tokens)
- [pharmacy_return_items](##pharmacy_return_items)
- [pharmacy_transfer](##pharmacy_transfer)
- [pharmacy_transfer_details](##pharmacy_transfer_details)
- [procedure_type](##procedure_type)
- [product_consumption](##product_consumption)
- [product_kits](##product_kits)
- [products](##products)
- [receivables_details](##receivables_details)
- [relations](##relations)
- [role_user](##role_user)
- [roles](##roles)
- [sale](##sale)
- [sale_details](##sale_details)
- [sale_payments](##sale_payments)
- [saleman_details](##saleman_details)
- [sections](##sections)
- [service_type](##service_type)
- [sessions](##sessions)
- [store](##store)
- [sub_category](##sub_category)
- [sup_cus_details](##sup_cus_details)
- [tehsils](##tehsils)
- [temp_sale](##temp_sale)
- [temp_sale_details](##temp_sale_details)
- [temporary_files](##temporary_files)
- [users](##users)
- [ward_beds](##ward_beds)
- [ward_request](##ward_request)
- [ward_request_details](##ward_request_details)
- [wards](##wards)

---

### appointments

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| appointment_number | varchar(100) | YES |  |  |
| patient_id | int | NO |  |  |
| consultant_id | int | NO |  |  |
| opd_type_id | int | NO |  |  |
| appointment_date | datetime | YES |  |  |
| fee | decimal(10,2) | NO |  |  |
| hospital_share | decimal(10,2) | YES | 0.00 |  |
| consultant_share | decimal(10,2) | YES | 0.00 |  |
| created_at | datetime | NO |  |  |
| updated_at | datetime | NO |  |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### apps

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| title | varchar(255) | YES |  |  |
| route | varchar(255) | YES |  |  |
| app_form | varchar(30) | YES |  |  |
| description | mediumtext | YES |  |  |
| icon | varchar(255) | YES |  |  |
| sdp | tinyint | YES | 0 |  |
| app_type | varchar(45) | YES | CORE_APP |  |
| active | int | YES |  |  |
| extra_fields | longtext | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### companies

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| company_type_id | bigint unsigned | YES |  |  |
| company_level_id | bigint unsigned | YES |  |  |
| parent_id | int | YES |  |  |
| country_id | int | YES |  |  |
| province_id | int | YES |  |  |
| division_id | int | YES |  |  |
| district_id | int | YES |  |  |
| tehsil_id | int | YES |  |  |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| prefix | varchar(10) | YES |  |  |
| user_id | int | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| reference_id | bigint | YES |  |  |
| reference_model | varchar(255) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### company_levels

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### company_types

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_department

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | NO |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_procedure_pricing

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| consultant_procedure_id | int | NO |  |  |
| service_type_id | int | NO |  |  |
| amount | decimal(10,2) | NO | 0.00 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_procedures

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| consultant_id | int | YES |  |  |
| procedure_type_id | int | YES |  |  |
| amount | decimal(10,2) | NO |  |  |
| consultant_charges | decimal(10,2) | YES |  |  |
| consultant_share_percentage | decimal(10,2) | YES |  |  |
| consultant_share_amount | decimal(10,2) | YES |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_sc_payments_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| consultant_id | int | NO |  |  |
| dated | date | NO |  |  |
| payment_type_id | int | NO |  |  |
| amount | decimal(10,2) | NO |  |  |
| description | varchar(255) | YES |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| updated_by | int | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_shares_payment_invoice

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| consultant_id | int | NO |  |  |
| from_date | date | NO |  |  |
| to_date | date | NO |  |  |
| total_amount | decimal(10,2) | NO | 0.00 |  |
| admission_ids | text | YES |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| is_payment_complete | int | NO | 0 |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_speciality

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultant_type

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### consultants

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| finance_head_id | int | YES | 0 |  |
| name | varchar(255) | YES |  |  |
| pmdc_number | varchar(255) | YES |  |  |
| consultant_department_id | int | YES |  |  |
| consultant_speciality_id | int | YES |  |  |
| cnic | varchar(100) | YES |  |  |
| joining_date | date | YES |  |  |
| consultant_type_id | int | YES |  |  |
| share_percentage | decimal(10,2) | YES | 0.00 |  |
| in_patient_share | decimal(10,2) | YES |  |  |
| general_opd_fee | decimal(10,2) | NO | 0.00 |  |
| general_opd_hospital_share | decimal(10,2) | NO | 0.00 |  |
| general_opd_consultant_share | decimal(10,2) | NO | 0.00 |  |
| consultant_opd_fee | decimal(10,2) | NO | 0.00 |  |
| hospital_share | decimal(10,2) | NO | 0.00 |  |
| consultant_share | decimal(10,2) | NO | 0.00 |  |
| er_fee | decimal(10,2) | NO | 0.00 |  |
| er_hospital_share | decimal(10,2) | NO | 0.00 |  |
| er_consultant_share | decimal(10,2) | NO | 0.00 |  |
| lab_percentage | int | NO | 0 |  |
| description | text | YES |  |  |
| is_active | int | YES | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### daily_user_closings

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| user_id | bigint | NO |  |  |
| investigation_amount | decimal(10,2) | NO |  |  |
| sale_amount | decimal(10,2) | NO |  |  |
| appointment_amount | decimal(10,2) | NO |  |  |
| total_amount | decimal(10,2) | YES | 0.00 |  |
| closing_date | datetime | NO |  |  |
| remarks | varchar(255) | YES |  |  |
| created_at | datetime | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### departments

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| name | varchar(255) | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### districts

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| province_id | int | NO |  |  |
| division_id | int | YES |  |  |
| reagin | int | YES |  |  |
| title | varchar(255) | YES |  |  |
| short_title | varchar(3) | NO |  |  |
| latitude | varchar(100) | YES |  |  |
| longitude | varchar(100) | YES |  |  |
| description | text | YES |  |  |
| active | tinyint | NO | 1 |  |
| district_group_id | int | YES |  |  |
| deleted_at | datetime | YES |  |  |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| check | int | YES | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX province_id on (province_id)
- INDEX division_id on (division_id)

---

### expense_sub

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| ESID | int | NO |  | auto_increment |
| ESTitle | varchar(255) | YES |  |  |
| ExpenseID | int | YES |  |  |
| IsActive | int | YES | 1 |  |
| Editable | int | YES | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* ESID

*Indexes*

- UNIQUE PRIMARY on (ESID)

---

### expenses

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| ExpenseID | int | NO |  | auto_increment |
| ExpenseTitle | varchar(255) | YES |  |  |
| IsActive | int | YES | 1 |  |
| Editable | int | YES | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* ExpenseID

*Indexes*

- UNIQUE PRIMARY on (ExpenseID)

---

### finance_heads

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| parent_id | bigint unsigned | YES |  |  |
| level | tinyint | YES | 3 |  |
| head_code | varchar(255) | YES |  |  |
| name | varchar(100) | NO |  |  |
| type | enum('income','expense','asset','liability','capital') | YES | asset |  |
| description | text | YES |  |  |
| is_contra | tinyint(1) | YES | 0 |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE name on (name)

---

### finance_transactions

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| voucher_id | bigint | YES |  |  |
| transaction_date | date | NO |  |  |
| head_id | bigint unsigned | NO |  |  |
| debit | decimal(12,2) | NO | 0.00 |  |
| credit | decimal(12,2) | NO | 0.00 |  |
| reference_type | varchar(100) | YES |  |  |
| reference_id | bigint unsigned | YES |  |  |
| user_id | bigint unsigned | YES |  |  |
| remarks | text | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| created_by | int | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX head_id on (head_id)

---

### finance_vouchers

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| user_id | bigint | YES |  |  |
| voucher_number | varchar(50) | NO |  |  |
| voucher_type | enum('sale','pharmacy_purchase','receipt','payment','journal_voucher','adjustment','closing','investigation_shares') | NO |  |  |
| voucher_date | date | YES |  |  |
| total_amount | decimal(12,2) | YES | 0.00 |  |
| remarks | text | YES |  |  |
| created_by | bigint unsigned | YES |  |  |
| approved_by | bigint unsigned | YES |  |  |
| approved_at | timestamp | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE voucher_number on (voucher_number)
- INDEX created_by on (created_by)
- INDEX approved_by on (approved_by)

---

### general_expenses

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| GXID | int | NO |  | auto_increment |
| ESID | int | YES |  |  |
| Amount | decimal(10,2) | YES |  |  |
| Dated | date | YES |  |  |
| Description | mediumtext | YES |  |  |
| CreatedAt | date | YES |  |  |
| CreatedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| IsActive | int | YES | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* GXID

*Indexes*

- UNIQUE PRIMARY on (GXID)
- INDEX ESID on (ESID)
- INDEX IsActive on (IsActive)

---

### grn

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | YES |  |  |
| GRNID | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| grn_request_id | int | YES |  |  |
| SCID | int | YES |  |  |
| InvoiceNo | varchar(55) | YES |  |  |
| Freight | float | YES |  |  |
| Dated | date | YES |  |  |
| Description | varchar(500) | YES |  |  |
| Discount | float | YES |  |  |
| per_item_discount | decimal(10,2) | NO | 0.00 |  |
| TotalPurchase | decimal(10,2) | YES |  |  |
| paid_amount | decimal(10,2) | NO | 0.00 |  |
| total_gst | decimal(10,2) | YES |  |  |
| total_advance_tax | decimal(10,2) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| bill_json_form | mediumtext | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* GRNID

*Indexes*

- UNIQUE PRIMARY on (GRNID)
- INDEX id on (id)

---

### grn_audit

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| audit_no | varchar(100) | NO |  |  |
| dated | datetime | NO |  |  |
| aprove_date | datetime | YES |  |  |
| audit_by | int | YES |  |  |
| approve_by | int | YES |  |  |
| status | int | NO | 0 |  |
| store_id | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### grn_audit_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| audit_id | int | NO |  |  |
| product_id | int | NO |  |  |
| phy_avaliable_quantity | float | NO |  |  |
| avaliable_quantity | float | NO |  |  |
| diffrence | float | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### grn_damage

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| DamageID | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| GDID | int | YES |  |  |
| ProductID | int | YES |  |  |
| Quantity | float | YES |  |  |
| date | date | YES |  |  |
| Description | mediumtext | YES |  |  |
| GXID | int | YES | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* DamageID

*Indexes*

- UNIQUE PRIMARY on (DamageID)
- INDEX GDID on (GDID)
- INDEX ProductID on (ProductID)
- INDEX date on (date)
- INDEX GXID on (GXID)

---

### grn_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | YES |  |  |
| GDID | int | NO |  | auto_increment |
| store_id | int | NO |  |  |
| ProductID | int | YES |  |  |
| GRNID | int | YES |  |  |
| batch_no | varchar(255) | YES |  |  |
| Quantity | float | YES |  |  |
| Damage | float | YES |  |  |
| UnitPrice | decimal(10,2) | YES |  |  |
| discount | decimal(10,2) | NO | 0.00 |  |
| pack_price | decimal(10,2) | YES |  |  |
| pack_size | decimal(10,2) | YES |  |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| taxAmount | decimal(10,2) | NO | 0.00 |  |
| gst_tax_amount | decimal(10,2) | YES |  |  |
| advance_tax_amount | decimal(10,2) | YES |  |  |
| advance_tax | decimal(10,2) | YES |  |  |
| gst_tax | decimal(10,2) | YES |  |  |
| SoldQuantity | int | YES | 0 |  |
| TotalReturn | int | YES | 0 |  |
| RemainingQuantity | float | YES |  |  |
| ProductStatus | int | YES | 1 |  |
| expiry_date | date | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* GDID

*Indexes*

- UNIQUE PRIMARY on (GDID)
- INDEX ProductID on (ProductID)
- INDEX GRNID on (GRNID)
- INDEX ProductStatus on (ProductStatus)
- INDEX id on (id)

---

### grn_request

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| GRNID | int | NO |  | auto_increment |
| id | int | NO |  |  |
| store_id | int | NO | 1 |  |
| SCID | int | YES |  |  |
| InvoiceNo | varchar(55) | YES |  |  |
| Freight | float | YES |  |  |
| Dated | date | YES |  |  |
| Description | varchar(500) | YES |  |  |
| Discount | float | YES |  |  |
| per_item_discount | decimal(10,2) | YES | 0.00 |  |
| TotalPurchase | decimal(10,2) | YES |  |  |
| paid_amount | decimal(10,2) | NO | 0.00 |  |
| total_gst | decimal(10,2) | YES |  |  |
| total_advance_tax | decimal(10,2) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| bill_json_form | mediumtext | YES |  |  |
| bill_status | int | NO | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* GRNID

*Indexes*

- UNIQUE PRIMARY on (GRNID)
- UNIQUE GRNID_unique on (id)

---

### grn_request_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| GDID | int | NO |  | auto_increment |
| id | int | NO |  |  |
| store_id | int | NO | 1 |  |
| ProductID | int | YES |  |  |
| GRNID | int | YES |  |  |
| batch_no | varchar(255) | NO |  |  |
| Quantity | float | YES |  |  |
| Damage | float | YES |  |  |
| UnitPrice | decimal(10,2) | YES |  |  |
| discount | decimal(10,2) | NO | 0.00 |  |
| pack_price | decimal(10,2) | YES |  |  |
| pack_size | decimal(10,2) | YES |  |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| taxAmount | decimal(10,2) | NO | 0.00 |  |
| gst_tax_amount | decimal(10,2) | YES |  |  |
| advance_tax_amount | decimal(10,2) | YES |  |  |
| advance_tax | decimal(10,2) | YES |  |  |
| gst_tax | decimal(10,2) | YES |  |  |
| SoldQuantity | int | YES | 0 |  |
| TotalReturn | int | YES | 0 |  |
| RemainingQuantity | float | YES |  |  |
| ProductStatus | int | YES | 1 |  |
| expiry_date | date | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* GDID

*Indexes*

- UNIQUE PRIMARY on (GDID)
- UNIQUE GDID_unique on (id)
- INDEX ProductID on (ProductID)
- INDEX GRNID on (GRNID)
- INDEX ProductStatus on (ProductStatus)

---

### grn_return_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| ReturnDetailID | bigint unsigned | NO |  | auto_increment |
| ReturnID | bigint unsigned | NO |  |  |
| GDID | bigint unsigned | NO |  |  |
| ProductID | bigint unsigned | NO |  |  |
| BatchNo | varchar(100) | YES |  |  |
| ExpiryDate | date | YES |  |  |
| ReturnQuantity | decimal(15,2) | NO |  |  |
| UnitPrice | decimal(15,2) | NO |  |  |
| TotalAmount | decimal(15,2) | NO |  |  |
| CreatedAt | timestamp | YES |  |  |

*Primary Key:* ReturnDetailID

*Indexes*

- UNIQUE PRIMARY on (ReturnDetailID)
- INDEX grn_return_details_returnid_foreign on (ReturnID)
- INDEX grn_return_details_gdid_index on (GDID)
- INDEX grn_return_details_productid_index on (ProductID)

---

### grn_returns

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| ReturnID | bigint unsigned | NO |  | auto_increment |
| SCID | bigint unsigned | NO |  |  |
| ReturnDate | date | NO |  |  |
| TotalAmount | decimal(15,2) | NO | 0.00 |  |
| Status | enum('Pending','Approved','Rejected','Completed') | NO | Pending |  |
| CreatedBy | bigint unsigned | YES |  |  |
| ApprovedBy | bigint unsigned | YES |  |  |
| CreatedAt | timestamp | YES |  |  |
| ApprovedAt | timestamp | YES |  |  |
| Remarks | text | YES |  |  |

*Primary Key:* ReturnID

*Indexes*

- UNIQUE PRIMARY on (ReturnID)
- INDEX grn_returns_scid_index on (SCID)
- INDEX grn_returns_returndate_index on (ReturnDate)
- INDEX grn_returns_status_index on (Status)

---

### hx_complaints

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| appointment_id | bigint unsigned | NO |  |  |
| patient_id | bigint unsigned | NO |  |  |
| complaint | text | YES |  |  |
| bp | varchar(50) | YES |  |  |
| temp | varchar(20) | YES |  |  |
| pulse | varchar(20) | YES |  |  |
| rr | varchar(20) | YES |  |  |
| investigation | text | YES |  |  |
| recorded_by | bigint unsigned | YES |  |  |
| is_active | tinyint(1) | YES | 1 |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### in_patient_admissions

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | bigint | NO |  |  |
| ward_id | int | NO |  |  |
| bed_id | int | NO |  |  |
| consultant_id | int | YES |  |  |
| included_medicine | int | NO | 1 |  |
| g4no | varchar(100) | YES |  |  |
| consultant_procedure_id | int | YES |  |  |
| guardian_name | varchar(255) | YES |  |  |
| emergency_contact_no | varchar(255) | YES |  |  |
| relation_id | int | YES |  |  |
| admission_date | datetime | YES |  |  |
| discharge_date | datetime | YES |  |  |
| discharge_summary | text | YES |  |  |
| canelation_reason | text | YES |  |  |
| is_active | int | NO | 1 |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| updated_at | datetime | YES |  |  |
| admission_status | enum('Admit','Canceled','Discharged','Reffered') | NO | Admit |  |
| consultant_share | decimal(10,2) | NO |  |  |
| consultant_share_amount | double(10,2) | NO | 0.00 |  |
| procedure_rate | decimal(10,2) | NO |  |  |
| consultant_charges | decimal(10,2) | YES |  |  |
| total_amount_received_from_patient | decimal(10,2) | NO | 0.00 |  |
| sec_procedure_rate | decimal(10,2) | NO | 0.00 |  |
| investigation_cost | double(10,2) | NO | 0.00 |  |
| service_charges_cost | double(10,2) | NO | 0.00 |  |
| medicine_cost | double(10,2) | NO | 0.00 |  |
| totalCost | double(10,2) | NO | 0.00 |  |
| balance | double(10,2) | NO | 0.00 |  |
| consultant_shares_payment_invoice_id | bigint | NO | 0 |  |
| amount_received_from_sehat_card | double(10,2) | YES |  |  |
| patient_type | varchar(100) | NO | sehat_card |  |
| advance_payment | decimal(10,2) | NO | 0.00 |  |
| security_amount | decimal(10,2) | NO | 0.00 |  |
| discharge_by | int | YES |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX ward_id on (ward_id)
- INDEX patient_id on (patient_id)
- INDEX bed_id on (bed_id)
- INDEX admission_type_id on (consultant_procedure_id)

---

### in_patient_payments

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| patient_id | bigint | NO |  |  |
| admission_id | bigint | NO |  |  |
| amount | decimal(10,2) | NO |  |  |
| payment_type | varchar(100) | NO |  |  |
| remarks | varchar(255) | YES |  |  |
| created_by | int | NO |  |  |
| created_at | datetime | NO |  |  |
| is_active | int | NO | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### investigation_category

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(100) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### investigation_sub_category

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| finance_head_id | int | NO | 0 |  |
| investigation_category_id | int | NO |  |  |
| name | varchar(255) | YES |  |  |
| price | decimal(10,2) | YES |  |  |
| sale_price | decimal(10,2) | NO | 0.00 |  |
| is_parameter | int | NO | 1 |  |
| is_ict | int | YES | 0 |  |
| result_text | text | YES |  |  |
| is_dialeses_test | int | NO | 0 |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX finance_head_id on (finance_head_id)

---

### investigation_sub_category_parameters

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| parameter_heading_id | int | NO | 1 |  |
| index_number | int | NO | 1 |  |
| name | varchar(200) | NO |  |  |
| investigation_sub_category_id | int | YES |  |  |
| male_min | decimal(10,2) | YES |  |  |
| male_max | decimal(10,2) | YES |  |  |
| female_min | decimal(10,2) | YES |  |  |
| female_max | decimal(10,2) | YES |  |  |
| child_min | decimal(10,2) | YES |  |  |
| child_max | decimal(10,2) | YES |  |  |
| unit | varchar(100) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### item_form

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### item_generic_name

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### item_make

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### jobs

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| queue | varchar(125) | NO |  |  |
| payload | longtext | NO |  |  |
| attempts | tinyint unsigned | NO |  |  |
| reserved_at | int unsigned | YES |  |  |
| available_at | int unsigned | NO |  |  |
| created_at | int unsigned | NO |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX jobs_queue_index on (queue)

---

### machine_categories

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| machine | varchar(255) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### machine_patients

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| patient_id | int | NO |  |  |
| date | date | YES |  |  |
| day | varchar(255) | YES |  |  |
| machine_shift_id | int | YES |  |  |
| machine_category_id | int | YES |  |  |
| status | int | YES | 1 |  |
| is_active | int | YES | 1 |  |
| created_at | datetime | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### machine_shifts

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| time_range | varchar(255) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### main_category

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### market

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | NO |  |  |
| IsActive | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### menus

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| app_id | int | YES |  |  |
| parent_id | int | YES |  |  |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| icon | varchar(255) | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| order | int | YES |  |  |
| is_collapsible | varchar(125) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### migrations

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| migration | varchar(255) | NO |  |  |
| batch | int | NO |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### opd_type

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(100) | NO |  |  |
| fees | decimal(10,2) | NO |  |  |
| including_medicine | int | NO | 0 |  |
| including_labs | int | NO | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### opd_type_investigations

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| opd_type_id | int unsigned | NO |  |  |
| investigation_sub_category_id | bigint unsigned | NO |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### opd_type_products

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| opd_type_id | int unsigned | NO |  |  |
| product_id | bigint unsigned | NO |  |  |
| quantity | int | NO | 1 |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### parameter_heading

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| investigation_sub_category_id | int | YES | 0 |  |
| index_number | int | NO |  |  |
| name | varchar(255) | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### password_resets

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| email | varchar(255) | NO |  |  |
| token | varchar(255) | NO |  |  |
| created_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Indexes*

- INDEX password_resets_email_index on (email)

---

### patient_admissions

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | bigint | NO |  |  |
| ward_id | int | NO |  |  |
| bed_id | int | NO |  |  |
| consultant_id | int | YES |  |  |
| sub_consultant_id | int | YES |  |  |
| included_medicine | int | NO | 1 |  |
| sc_ref_no | varchar(100) | YES |  |  |
| g4no | varchar(100) | YES |  |  |
| procedure_type_id | int | YES |  |  |
| sec_procedure_type_id | int | YES |  |  |
| guardian_name | varchar(255) | YES |  |  |
| emergency_contact_no | varchar(255) | YES |  |  |
| relation_id | int | YES |  |  |
| admission_date | datetime | YES |  |  |
| discharge_date | datetime | YES |  |  |
| discharge_summary | text | YES |  |  |
| canelation_reason | text | YES |  |  |
| is_active | int | NO | 1 |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| updated_at | datetime | YES |  |  |
| admission_status | enum('Admit','Canceled','Discharged','Reffered') | NO | Admit |  |
| consultant_share | decimal(10,2) | NO |  |  |
| consultant_share_amount | double(10,2) | NO | 0.00 |  |
| procedure_rate | decimal(10,2) | NO |  |  |
| total_amount_received_from_patient | decimal(10,2) | NO | 0.00 |  |
| sec_procedure_rate | decimal(10,2) | NO | 0.00 |  |
| investigation_cost | double(10,2) | NO | 0.00 |  |
| service_charges_cost | double(10,2) | NO | 0.00 |  |
| medicine_cost | double(10,2) | NO | 0.00 |  |
| totalCost | double(10,2) | NO | 0.00 |  |
| balance | double(10,2) | NO | 0.00 |  |
| consultant_shares_payment_invoice_id | bigint | NO | 0 |  |
| amount_received_from_sehat_card | double(10,2) | YES |  |  |
| patient_type | varchar(100) | NO | sehat_card |  |
| advance_payment | decimal(10,2) | NO | 0.00 |  |
| security_amount | decimal(10,2) | NO | 0.00 |  |
| discharge_by | int | YES |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX ward_id on (ward_id)
- INDEX patient_id on (patient_id)
- INDEX bed_id on (bed_id)
- INDEX admission_type_id on (procedure_type_id)

---

### patient_baby

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| admission_id | int | NO |  |  |
| patient_id | int | NO |  |  |
| baby_gender | varchar(100) | NO |  |  |
| baby_name | varchar(255) | NO |  |  |
| mother_name | varchar(100) | YES |  |  |
| father_name | varchar(100) | YES |  |  |
| dob | datetime | YES |  |  |
| father_mother_cnic | varchar(100) | YES |  |  |
| baby_status | enum('Alive','died_after_delivery','died_before_delivery','') | YES |  |  |
| created_at | datetime | NO |  |  |
| updated_at | datetime | NO |  |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_discharge_checklist

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| admission_id | int | YES |  |  |
| patient_id | int | YES |  |  |
| cnic | int | YES |  |  |
| shp_form | int | YES |  |  |
| ultrasound | int | YES |  |  |
| doctor_prescription | int | YES |  |  |
| labs | int | YES |  |  |
| admission_form | int | YES |  |  |
| nursing_notes | int | YES |  |  |
| medication_chart | int | YES |  |  |
| ot_notes | int | YES |  |  |
| consern_notes | int | YES |  |  |
| discharge_slip | int | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_investigation_result

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| patient_investigation_id | int | YES |  |  |
| parameter_id | int | YES |  |  |
| result_value | varchar(100) | YES |  |  |
| result_text_value | varchar(100) | YES |  |  |
| result_entry_date | datetime | NO |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_by | int | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX patient_investigation_id on (patient_investigation_id)
- INDEX parameter_id on (parameter_id)

---

### patient_investigations

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| invoice_no | varchar(100) | YES |  |  |
| patient_id | int | YES |  |  |
| appointment_id | bigint | YES |  |  |
| admission_id | int | YES |  |  |
| investigation_sub_category_id | int | YES |  |  |
| consultant_id | int | NO | 0 |  |
| consultant_share_percentage | int | NO | 0 |  |
| consultant_share_amount | int | NO | 0 |  |
| inv_amount | decimal(10,2) | YES |  |  |
| sale_price | decimal(10,2) | NO | 0.00 |  |
| frequency | int | NO | 1 |  |
| discount_percentage | decimal(10,2) | NO | 0.00 |  |
| discount_amount | decimal(10,2) | NO | 0.00 |  |
| inv_date | datetime | YES |  |  |
| inv_out_date | datetime | YES |  |  |
| inv_comment | text | YES |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_by | int | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| patient_type | varchar(100) | NO | sehat_card |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| status | int | NO | 0 |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX admission_id on (admission_id)
- INDEX patient_id on (patient_id)
- INDEX investigation_id on (investigation_sub_category_id)
- INDEX consultant_id on (consultant_id)
- INDEX invoice_no on (invoice_no)

---

### patient_investigations_payments

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| patient_id | int | NO |  |  |
| admission_id | int | YES |  |  |
| invoice_no | bigint | YES |  |  |
| amount | decimal(10,2) | NO |  |  |
| remarks | varchar(255) | NO | investigation_payment |  |
| created_by | int | NO |  |  |
| created_at | datetime | NO |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| updated_by | bigint | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_locations

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_nurse_notes

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| nurse_note | text | YES |  |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| created_at | datetime | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| is_active | int | YES | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_ot_notes

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| ot_notes | text | YES |  |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| created_at | datetime | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| is_active | int | YES | 1 |  |
| updated_at | datetime | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_refunds

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | int | NO |  |  |
| admission_id | int | NO |  |  |
| inv_invoice_no | varchar(255) | YES |  |  |
| consultant_id | int | YES |  |  |
| consultant_fee_amount | varchar(100) | YES |  |  |
| investigation_ids | longtext | YES |  |  |
| investigation_amount | varchar(100) | YES |  |  |
| created_at | datetime | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patient_service_charges

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| service_type_id | int | YES |  |  |
| actual_price | decimal(10,2) | NO | 0.00 |  |
| service_rate | decimal(10,2) | YES |  |  |
| service_date | datetime | YES |  |  |
| created_by | bigint | YES |  |  |
| created_at | datetime | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_by | bigint | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| patient_type | varchar(100) | NO | in_patient |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX patient_id on (patient_id)
- INDEX admission_id on (admission_id)
- INDEX service_type_id on (service_type_id)

---

### patient_vitals

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| patient_id | bigint | NO |  |  |
| admission_id | bigint | NO |  |  |
| date | date | YES |  |  |
| time | varchar(255) | YES |  |  |
| rbs | varchar(255) | NO |  |  |
| r_r | varchar(255) | NO |  |  |
| hr | varchar(255) | NO |  |  |
| bp | varchar(255) | NO |  |  |
| temp | varchar(255) | NO |  |  |
| spo2 | varchar(255) | NO |  |  |
| remarks | varchar(255) | NO |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### patients

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| mr_no | varchar(255) | YES |  |  |
| regdate | datetime | YES |  |  |
| name | varchar(255) | YES |  |  |
| cnic | varchar(20) | YES |  |  |
| father_husband_name | varchar(255) | YES |  |  |
| gender | varchar(255) | YES |  |  |
| age | int | YES |  |  |
| months | int | NO | 0 |  |
| days | int | NO | 0 |  |
| dob | date | YES |  |  |
| district_id | int | YES |  |  |
| location_id | int | YES |  |  |
| contact_no | varchar(20) | YES |  |  |
| is_active | int | NO | 1 |  |
| created_by | int | YES |  |  |
| updated_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| updated_at | datetime | YES |  |  |
| patient_type | varchar(100) | NO | sehat_card |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX district_id on (district_id)

---

### payment_type

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| payment_type_id | int | NO |  | auto_increment |
| payment_type | varchar(255) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* payment_type_id

*Indexes*

- UNIQUE PRIMARY on (payment_type_id)

---

### payments_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| PDID | int | NO |  | auto_increment |
| SCID | int | NO |  |  |
| Dated | date | NO |  |  |
| payment_type_id | int | NO |  |  |
| BankID | int | YES |  |  |
| Amount | decimal(10,2) | NO |  |  |
| FromAccount | varchar(200) | YES |  |  |
| ToAccount | varchar(200) | YES |  |  |
| ChecqueNo | varchar(100) | YES |  |  |
| ChecqueDate | date | YES |  |  |
| TransferCode | varchar(255) | YES |  |  |
| Description | varchar(255) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| InvoiceNo | varchar(255) | YES |  |  |
| TransectionNo | int | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* PDID

*Indexes*

- UNIQUE PRIMARY on (PDID)
- INDEX SCID on (SCID)
- INDEX Dated on (Dated)
- INDEX payment_type_id on (payment_type_id)

---

### permission_role

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| permission_id | int unsigned | NO |  |  |
| role_id | int unsigned | NO |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Foreign Keys*

- permission_id → permissions.id
- role_id → roles.id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX permission_role_permission_id_index on (permission_id)
- INDEX permission_role_role_id_index on (role_id)

*Inferred Eloquent Relationships*

- belongsTo:
  - Permission (fk: permission_id → id)
  - Role (fk: role_id → id)

---

### permission_routes

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| is_default | enum('yes','no') | YES | no |  |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| route | varchar(255) | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| app_id | int | YES |  |  |
| menu_id | int | YES |  |  |
| permission_id | int | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |
| menu_order | int | NO | 0 |  |

---

### permission_user

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| permission_id | int unsigned | NO |  |  |
| user_id | bigint unsigned | NO |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Foreign Keys*

- permission_id → permissions.id
- user_id → users.id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX permission_user_permission_id_index on (permission_id)
- INDEX permission_user_user_id_index on (user_id)

*Inferred Eloquent Relationships*

- belongsTo:
  - Permission (fk: permission_id → id)
  - User (fk: user_id → id)

---

### permissions

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| name | varchar(255) | NO |  |  |
| slug | varchar(255) | NO |  |  |
| description | mediumtext | YES |  |  |
| model | varchar(255) | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| app_id | int | YES |  |  |
| menu_id | int | YES |  |  |
| show_in_menu | enum('yes','no') | YES | no |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE permissions_slug_unique on (slug)

*Inferred Eloquent Relationships*

- hasMany:
  - PermissionRole (fk: permission_id → id)
  - PermissionUser (fk: permission_id → id)

---

### personal_access_tokens

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| tokenable_type | varchar(125) | NO |  |  |
| tokenable_id | bigint unsigned | NO |  |  |
| name | varchar(125) | NO |  |  |
| token | varchar(64) | NO |  |  |
| abilities | text | YES |  |  |
| last_used_at | timestamp | YES |  |  |
| expires_at | timestamp | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE personal_access_tokens_token_unique on (token)
- INDEX personal_access_tokens_tokenable_type_tokenable_id_index on (tokenable_type, tokenable_id)

---

### pharmacy_return_items

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| sale_id | int | NO |  |  |
| sale_detail_id | int | NO |  |  |
| product_id | int | NO |  |  |
| quantity | int | NO |  |  |
| amount | decimal(10,2) | YES |  |  |
| created_by | int | YES |  |  |
| created_at | datetime | YES |  |  |
| is_posted | int | YES | 0 |  |
| posted_on | datetime | YES |  |  |
| updated_by | bigint | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### pharmacy_transfer

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| SaleID | bigint | YES | 0 |  |
| store_id | int | NO | 1 |  |
| SCID | varchar(255) | YES | - |  |
| appointment_id | bigint | YES |  |  |
| wr_id | int | NO | 0 |  |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| InvoiceNo | varchar(255) | YES |  |  |
| Date | datetime | YES |  |  |
| Description | mediumtext | YES |  |  |
| TNo | int | YES |  |  |
| TotalSale | decimal(10,2) | YES |  |  |
| received_amount | decimal(10,2) | NO | 0.00 |  |
| ReceivedAmountFromCustomer | decimal(10,2) | NO | 0.00 |  |
| Discount | int | YES |  |  |
| discount_percentage | int | NO | 0 |  |
| invoice_discount | decimal(10,2) | YES |  |  |
| SalemanID | int | YES | 0 |  |
| SalemanCommesion | int | YES | 0 |  |
| CustomerMobile | varchar(255) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| SalemanCommesionStatus | int | YES | 0 |  |
| sale_descriptions | varchar(255) | YES |  |  |
| bill_details | mediumtext | YES |  |  |
| is_sehat_card | int | NO | 1 |  |
| medicine_type | varchar(100) | YES |  |  |
| is_sync | int | NO | 0 |  |
| is_return_made | int | NO | 0 |  |
| transfer_type | varchar(100) | NO | - |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX admission_id on (admission_id)
- INDEX patient_id on (patient_id)

---

### pharmacy_transfer_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| temp_sale_id | bigint | YES | 0 |  |
| store_id | int | NO | 1 |  |
| ProductID | int | YES |  |  |
| SaleID | int | YES |  |  |
| Quantity | int | YES |  |  |
| UnitePrice | decimal(10,2) | YES |  |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| taxAmount | decimal(10,2) | YES |  |  |
| PurchasePrice | decimal(10,2) | YES |  |  |
| ReturnQuantity | int | YES | 0 |  |
| GDID | int | YES | 0 |  |
| patient_id | bigint | YES |  |  |
| admission_id | bigint | YES |  |  |
| return_by | int | YES |  |  |
| dose_type | varchar(255) | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX ProductID on (ProductID)
- INDEX SaleID on (SaleID)
- INDEX GDID on (GDID)
- INDEX temp_sale_id on (temp_sale_id)

---

### procedure_type

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| type | varchar(100) | YES |  |  |
| sc_rate | decimal(10,2) | YES |  |  |
| tax_deduction | decimal(10,2) | YES |  |  |
| net_rate | decimal(10,2) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### product_consumption

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| audit_id | int | NO |  |  |
| product_id | int | NO |  |  |
| quantity | decimal(10,2) | NO |  |  |
| created_at | datetime | NO |  |  |
| created_by | int | YES |  |  |
| updated_at | datetime | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### product_kits

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| product_id | int | YES |  |  |
| product_main_id | int | YES |  |  |
| qty | int | YES |  |  |
| created_at | datetime | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| created_by | int | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### products

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| ProductID | int | NO |  | auto_increment |
| id | int | NO |  |  |
| store_id | int | NO | 1 |  |
| allow_percentage | decimal(5,2) | NO | 0.00 |  |
| main_category_id | int | YES |  |  |
| sub_category_id | int | YES |  |  |
| item_form_id | int | YES |  |  |
| item_make_id | int | YES |  |  |
| generic_name_id | int | YES |  |  |
| ProductName | varchar(255) | YES |  |  |
| BrandID | int | YES | 0 |  |
| UnitID | int | YES | 0 |  |
| specification_name | varchar(200) | YES |  |  |
| BarCode | varchar(255) | YES | 123 |  |
| pack_price | decimal(10,2) | YES |  |  |
| pack_size | decimal(10,2) | YES |  |  |
| PurchasePrice | decimal(10,2) | YES | 0.00 |  |
| SalePrice | decimal(10,2) | YES | 0.00 |  |
| unit_sale_price | decimal(10,2) | NO | 0.00 |  |
| low_limit | int | YES | 0 |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| IsActive | int | YES | 1 |  |
| zakat_purchase_price | int | NO | 0 |  |
| zakat_qty | int | NO | 0 |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| total_amount_of_avaliable_stock | decimal(10,2) | NO | 0.00 |  |
| total_amount_of_purchase_stock | decimal(10,2) | NO | 0.00 |  |
| avaliable_quantity | decimal(10,2) | NO | 0.00 |  |
| phy_avaliable_quantity | int | NO | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* ProductID

*Indexes*

- UNIQUE PRIMARY on (ProductID)
- UNIQUE products_id_unique on (id)
- INDEX store_id on (store_id)
- INDEX main_category_id on (main_category_id)
- INDEX sub_category_id on (sub_category_id)
- INDEX item_form_id on (item_form_id)
- INDEX item_make_id on (item_make_id)
- INDEX generic_name_id on (generic_name_id)

---

### receivables_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| RDID | int | NO |  | auto_increment |
| SCID | int | YES |  |  |
| InvoiceNo | varchar(255) | YES |  |  |
| Payment_type_ID | int | YES |  |  |
| BankID | int | YES |  |  |
| Amount | decimal(10,2) | YES |  |  |
| FromAccount | varchar(255) | YES |  |  |
| ToAccount | varchar(255) | YES |  |  |
| ChecqueNo | int | YES |  |  |
| ChecqueDate | date | YES |  |  |
| TransferCode | varchar(255) | YES |  |  |
| Description | varchar(255) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| TransectionNo | int | YES |  |  |
| Date | date | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* RDID

*Indexes*

- UNIQUE PRIMARY on (RDID)
- INDEX SCID on (SCID)
- INDEX Payment_type_ID on (Payment_type_ID)
- INDEX BankID on (BankID)
- INDEX Date on (Date)
- INDEX SCID_2 on (SCID)
- INDEX Payment_type_ID_2 on (Payment_type_ID)
- INDEX BankID_2 on (BankID)
- INDEX Date_2 on (Date)

---

### relations

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### role_user

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| role_id | int unsigned | NO |  |  |
| user_id | bigint unsigned | NO |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Foreign Keys*

- role_id → roles.id
- user_id → users.id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX role_user_role_id_index on (role_id)
- INDEX role_user_user_id_index on (user_id)

*Inferred Eloquent Relationships*

- belongsTo:
  - Role (fk: role_id → id)
  - User (fk: user_id → id)

---

### roles

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int unsigned | NO |  | auto_increment |
| name | varchar(255) | NO |  |  |
| slug | varchar(255) | NO |  |  |
| description | varchar(255) | YES |  |  |
| level | int | NO | 1 |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| deleted_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE roles_slug_unique on (slug)

*Inferred Eloquent Relationships*

- hasMany:
  - PermissionRole (fk: role_id → id)
  - RoleUser (fk: role_id → id)

---

### sale

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | YES |  |  |
| SaleID | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| SCID | int | YES |  |  |
| appointment_id | bigint | YES |  |  |
| wr_id | int | NO | 0 |  |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| InvoiceNo | varchar(255) | YES |  |  |
| Date | datetime | YES |  |  |
| Description | mediumtext | YES |  |  |
| TNo | int | YES |  |  |
| TotalSale | decimal(10,2) | YES |  |  |
| received_amount | decimal(10,2) | NO | 0.00 |  |
| ReceivedAmountFromCustomer | decimal(10,2) | NO | 0.00 |  |
| Discount | int | YES |  |  |
| discount_percentage | int | NO | 0 |  |
| invoice_discount | decimal(10,2) | YES |  |  |
| SalemanID | int | YES | 0 |  |
| SalemanCommesion | int | YES | 0 |  |
| CustomerMobile | varchar(255) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| SalemanCommesionStatus | int | YES | 0 |  |
| sale_descriptions | varchar(255) | YES |  |  |
| bill_details | mediumtext | YES |  |  |
| is_sehat_card | int | NO | 1 |  |
| medicine_type | varchar(100) | YES |  |  |
| is_return_made | int | NO | 0 |  |
| is_sync | int | NO | 0 |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| sale_type | varchar(100) | YES |  |  |

*Primary Key:* SaleID

*Indexes*

- UNIQUE PRIMARY on (SaleID)
- INDEX SCID on (SCID)
- INDEX SalemanID on (SalemanID)
- INDEX Date on (Date)
- INDEX id on (id)

---

### sale_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | YES |  |  |
| SDID | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| ProductID | int | YES |  |  |
| SaleID | int | YES |  |  |
| Quantity | int | YES |  |  |
| UnitePrice | decimal(10,2) | YES |  |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| taxAmount | decimal(10,2) | YES |  |  |
| PurchasePrice | decimal(10,2) | YES |  |  |
| ReturnQuantity | int | YES | 0 |  |
| GDID | int | YES | 0 |  |
| discount_percentage | decimal(10,2) | NO | 0.00 |  |
| discount_percentage_amount | decimal(10,2) | NO | 0.00 |  |
| patient_id | bigint | YES |  |  |
| admission_id | bigint | YES |  |  |
| return_by | int | YES |  |  |
| dose_type | varchar(255) | YES |  |  |
| is_sync | int | NO | 0 |  |

*Primary Key:* SDID

*Indexes*

- UNIQUE PRIMARY on (SDID)
- INDEX ProductID on (ProductID)
- INDEX SaleID on (SaleID)
- INDEX GDID on (GDID)
- INDEX id on (id)

---

### sale_payments

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| patient_id | int | NO |  |  |
| admission_id | int | YES |  |  |
| sale_id | bigint | YES |  |  |
| amount | decimal(10,2) | NO |  |  |
| remarks | varchar(255) | YES |  |  |
| created_by | int | NO |  |  |
| created_at | datetime | NO |  |  |
| is_posted | int | NO | 0 |  |
| posted_on | datetime | YES |  |  |
| updated_by | bigint | YES |  |  |
| updated_at | datetime | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### saleman_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| SMDID | int | NO |  | auto_increment |
| SMID | int | YES |  |  |
| ProductID | int | YES |  |  |
| DamageID | int | YES |  |  |
| Quantity | int | YES |  |  |
| SMReturn | int | YES |  |  |
| Remaining | int | YES |  |  |
| Sold | int | YES |  |  |
| Status | int | YES | 1 |  |
| PDate | date | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* SMDID

*Indexes*

- UNIQUE PRIMARY on (SMDID)

---

### sections

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| parent_id | int | YES |  |  |
| company_id | int | YES |  |  |
| title | varchar(255) | YES |  |  |
| description | mediumtext | YES |  |  |
| user_id | int | YES |  |  |
| created_at | timestamp | YES | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### service_type

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| finance_head_id | int | NO | 0 |  |
| name | varchar(255) | NO |  |  |
| price | decimal(10,2) | NO |  |  |
| type | enum('Fixed','Per Day','','') | NO |  |  |
| show_in_discharge_form | int | NO | 0 |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### sessions

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | varchar(255) | NO |  |  |
| user_id | bigint unsigned | YES |  |  |
| ip_address | varchar(45) | YES |  |  |
| user_agent | text | YES |  |  |
| payload | text | NO |  |  |
| last_activity | int | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### store

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| store_name | varchar(255) | NO |  |  |
| use_purchase_price_as_sale_price | int | NO | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### sub_category

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| main_category_id | int | NO |  |  |
| name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### sup_cus_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| SCID | int | NO |  | auto_increment |
| finance_head_id | int | NO | 0 |  |
| market_id | int | NO | 0 |  |
| Name | varchar(255) | YES |  |  |
| ContactNo | varchar(255) | YES |  |  |
| Address | mediumtext | YES |  |  |
| BusinessAddress | mediumtext | YES |  |  |
| Email | varchar(255) | YES |  |  |
| Type | int | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| OpeningBalance | int | YES | 0 |  |
| SalemanCommesion | int | YES | 0 |  |
| IsActive | int | YES | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* SCID

*Indexes*

- UNIQUE PRIMARY on (SCID)
- INDEX market_id on (market_id)
- INDEX IsActive on (IsActive)

---

### tehsils

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| district_id | int | YES |  |  |
| title | varchar(100) | YES |  |  |
| short_title | varchar(6) | NO |  |  |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime | YES |  |  |
| deleted_at | datetime | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX district_id on (district_id)

---

### temp_sale

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| SaleID | bigint | YES | 0 |  |
| store_id | int | NO | 1 |  |
| SCID | int | YES |  |  |
| appointment_id | bigint | YES |  |  |
| wr_id | int | NO | 0 |  |
| patient_id | int | YES |  |  |
| admission_id | int | YES |  |  |
| InvoiceNo | varchar(255) | YES |  |  |
| Date | datetime | YES |  |  |
| Description | mediumtext | YES |  |  |
| TNo | int | YES |  |  |
| TotalSale | decimal(10,2) | YES |  |  |
| received_amount | decimal(10,2) | NO | 0.00 |  |
| ReceivedAmountFromCustomer | decimal(10,2) | NO | 0.00 |  |
| Discount | int | YES |  |  |
| discount_percentage | int | NO | 0 |  |
| invoice_discount | decimal(10,2) | YES |  |  |
| SalemanID | int | YES | 0 |  |
| SalemanCommesion | int | YES | 0 |  |
| CustomerMobile | varchar(255) | YES |  |  |
| CreatedBy | int | YES |  |  |
| CreatedAt | date | YES |  |  |
| ModifiedBy | int | YES |  |  |
| ModifiedAt | date | YES |  |  |
| SalemanCommesionStatus | int | YES | 0 |  |
| sale_descriptions | varchar(255) | YES |  |  |
| bill_details | mediumtext | YES |  |  |
| is_sehat_card | int | NO | 1 |  |
| medicine_type | varchar(100) | YES |  |  |
| is_sync | int | NO | 0 |  |
| is_return_made | int | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE SaleID on (SaleID)
- INDEX SCID on (SCID)
- INDEX admission_id on (admission_id)
- INDEX patient_id on (patient_id)

---

### temp_sale_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| temp_sale_id | bigint | YES | 0 |  |
| store_id | int | NO | 1 |  |
| ProductID | int | YES |  |  |
| SaleID | int | YES |  |  |
| Quantity | int | YES |  |  |
| UnitePrice | decimal(10,2) | YES |  |  |
| taxPercentage | decimal(10,2) | YES |  |  |
| taxAmount | decimal(10,2) | YES |  |  |
| PurchasePrice | decimal(10,2) | YES |  |  |
| ReturnQuantity | int | YES | 0 |  |
| GDID | int | YES | 0 |  |
| patient_id | bigint | YES |  |  |
| admission_id | bigint | YES |  |  |
| return_by | int | YES |  |  |
| dose_type | varchar(255) | YES |  |  |
| is_sync | int | NO | 0 |  |
| discount_percentage | decimal(10,2) | NO | 0.00 |  |
| discount_percentage_amount | decimal(10,2) | NO | 0.00 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- INDEX ProductID on (ProductID)
- INDEX SaleID on (SaleID)
- INDEX GDID on (GDID)
- INDEX temp_sale_id on (temp_sale_id)

---

### temporary_files

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| token | varchar(125) | NO |  |  |
| collection | varchar(125) | NO |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### users

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint unsigned | NO |  | auto_increment |
| department_id | int | NO |  |  |
| name | varchar(255) | NO |  |  |
| email | varchar(255) | NO |  |  |
| cnic | varchar(255) | YES |  |  |
| email_verified_at | timestamp | YES |  |  |
| password | varchar(255) | NO |  |  |
| remember_token | varchar(100) | YES |  |  |
| contact_number | varchar(100) | YES |  |  |
| district_id | int | YES |  |  |
| tehsil_id | int | YES |  |  |
| created_at | timestamp | YES |  |  |
| updated_at | timestamp | YES |  |  |
| username | varchar(255) | YES |  |  |
| company_id | int | YES |  |  |
| section_id | int | YES |  |  |
| parent_id | int | YES |  |  |
| status | int | NO | 1 |  |
| description | mediumtext | YES |  |  |
| is_hod | varchar(100) | YES |  |  |
| deleted_at | datetime | YES |  |  |
| device_token | mediumtext | YES |  |  |
| android_token | varchar(255) | YES |  |  |
| ios_token | varchar(255) | YES |  |  |
| is_specific | int | NO | 0 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)
- UNIQUE users_email_unique on (email)

*Inferred Eloquent Relationships*

- hasMany:
  - PermissionUser (fk: user_id → id)
  - RoleUser (fk: user_id → id)

---

### ward_beds

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| ward_id | int | NO |  |  |
| name | varchar(255) | YES |  |  |
| is_active | int | YES | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### ward_request

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | bigint | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| invoice_no | varchar(100) | NO |  |  |
| patient_id | int | NO |  |  |
| admission_id | int | NO |  |  |
| requested_by | int | NO |  |  |
| requested_at | datetime | NO |  |  |
| issued_by | int | YES |  |  |
| issued_at | datetime | YES |  |  |
| status | int | NO | 0 |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### ward_request_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| store_id | int | NO | 1 |  |
| wr_id | int | NO |  |  |
| product_id | int | NO |  |  |
| quantity | int | NO |  |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

### wards

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| short_name | varchar(255) | YES |  |  |
| is_active | int | NO | 1 |  |
| is_sync | tinyint(1) | NO | 0 |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

> *Copilot Hint:* Use this schema to generate Laravel Eloquent models with relationships.
> - belongsTo for each FK.
> - hasOne if child FK is unique, else hasMany.
> - belongsToMany for pairs connected via pivot tables.
