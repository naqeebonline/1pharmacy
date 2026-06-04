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
