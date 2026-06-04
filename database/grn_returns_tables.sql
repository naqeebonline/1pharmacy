-- GRN Returns Tables Creation Script
-- Created: 2025-12-18
-- Purpose: Store information about items returned to suppliers (expired/short expiry items)

-- Main table for return transactions
CREATE TABLE IF NOT EXISTS `grn_returns` (
  `ReturnID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `SCID` bigint(20) UNSIGNED NOT NULL COMMENT 'Supplier ID',
  `ReturnDate` date NOT NULL,
  `TotalAmount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `Status` enum('Pending','Approved','Rejected','Completed') NOT NULL DEFAULT 'Pending',
  `CreatedBy` bigint(20) UNSIGNED NULL,
  `ApprovedBy` bigint(20) UNSIGNED NULL,
  `CreatedAt` timestamp NULL,
  `ApprovedAt` timestamp NULL,
  `Remarks` text NULL,
  PRIMARY KEY (`ReturnID`),
  KEY `grn_returns_scid_index` (`SCID`),
  KEY `grn_returns_returndate_index` (`ReturnDate`),
  KEY `grn_returns_status_index` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Details table for individual items being returned
CREATE TABLE IF NOT EXISTS `grn_return_details` (
  `ReturnDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ReturnID` bigint(20) UNSIGNED NOT NULL,
  `GDID` bigint(20) UNSIGNED NOT NULL COMMENT 'GRN Detail ID',
  `ProductID` bigint(20) UNSIGNED NOT NULL,
  `BatchNo` varchar(100) NULL,
  `ExpiryDate` date NULL,
  `ReturnQuantity` decimal(15,2) NOT NULL,
  `UnitPrice` decimal(15,2) NOT NULL,
  `TotalAmount` decimal(15,2) NOT NULL,
  `CreatedAt` timestamp NULL,
  PRIMARY KEY (`ReturnDetailID`),
  KEY `grn_return_details_returnid_index` (`ReturnID`),
  KEY `grn_return_details_gdid_index` (`GDID`),
  KEY `grn_return_details_productid_index` (`ProductID`),
  CONSTRAINT `grn_return_details_returnid_foreign` FOREIGN KEY (`ReturnID`) REFERENCES `grn_returns` (`ReturnID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
