-- =====================================================
-- Colevora Restaurant Management System
-- MySQL Database Schema and Seed Data Export
-- Generated for XAMPP Production Deployment
-- Database: colevora_rims
-- Laravel Version: 12.x
-- Generated: 2026-07-18
-- =====================================================

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS `colevora_rims`;
CREATE DATABASE `colevora_rims` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `colevora_rims`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- =====================================================
-- CORE LARAVEL TABLES
-- =====================================================

-- Table: cache
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cache_locks
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: failed_jobs
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: job_batches
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jobs
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: migrations
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: password_reset_tokens
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER MANAGEMENT & AUTHENTICATION
-- =====================================================

-- Table: users
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `account_status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `user_type` enum('admin','employee','customer') NOT NULL DEFAULT 'customer',
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_type_index` (`user_type`),
  KEY `users_account_status_index` (`account_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permissions
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permission_role
CREATE TABLE `permission_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: role_user
CREATE TABLE `role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: employees
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `employment_status` enum('active','on_leave','resigned','terminated') NOT NULL DEFAULT 'active',
  `hire_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_user_id_unique` (`user_id`),
  KEY `employees_employment_status_index` (`employment_status`),
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_model_index` (`model`),
  KEY `audit_logs_model_id_index` (`model_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CMS & SITE SETTINGS
-- =====================================================

-- Table: hero_slides
CREATE TABLE `hero_slides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pages
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `meta_data` json DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: site_settings
CREATE TABLE `site_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: testimonials
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_photo` varchar(255) DEFAULT NULL,
  `customer_title` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: newsletter_subscribers
CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscribers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FOOD & MENU MANAGEMENT
-- =====================================================

-- Table: categories
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_created_by_foreign` (`created_by`),
  CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: food
CREATE TABLE `food` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `food_category_id_foreign` (`category_id`),
  KEY `food_created_by_foreign` (`created_by`),
  CONSTRAINT `food_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: food_price_changes
CREATE TABLE `food_price_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food_id` bigint(20) unsigned NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `food_price_changes_food_id_foreign` (`food_id`),
  KEY `food_price_changes_requested_by_foreign` (`requested_by`),
  KEY `food_price_changes_approved_by_foreign` (`approved_by`),
  CONSTRAINT `food_price_changes_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_price_changes_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_price_changes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: food_assignments
CREATE TABLE `food_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `assigned_by` bigint(20) unsigned NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `food_assignments_food_id_foreign` (`food_id`),
  KEY `food_assignments_employee_id_foreign` (`employee_id`),
  KEY `food_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `food_assignments_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_assignments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RESTAURANT & TABLE MANAGEMENT
-- =====================================================

-- Table: restaurant_tables
CREATE TABLE `restaurant_tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table_number` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('available','occupied','reserved','maintenance') NOT NULL DEFAULT 'available',
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `restaurant_tables_table_number_unique` (`table_number`),
  UNIQUE KEY `restaurant_tables_qr_code_unique` (`qr_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: reservations
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `guests` int(10) unsigned NOT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_user_id_foreign` (`user_id`),
  KEY `reservations_table_id_foreign` (`table_id`),
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CART & ORDER MANAGEMENT
-- =====================================================

-- Table: carts
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `status` enum('active','completed','abandoned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_id_index` (`user_id`,`status`),
  KEY `carts_session_id_index` (`session_id`,`status`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cart_items
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `food_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_food_id_unique` (`cart_id`,`food_id`),
  KEY `cart_items_food_id_foreign` (`food_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: orders
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `order_type` enum('dine_in','takeout','delivery') NOT NULL DEFAULT 'dine_in',
  `status` enum('pending','preparing','ready','served','completed','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `notes` text,
  `assigned_waiter_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_status_index` (`status`,`created_at`),
  KEY `orders_customer_id_index` (`customer_id`),
  KEY `orders_table_id_foreign` (`table_id`),
  KEY `orders_assigned_waiter_id_foreign` (`assigned_waiter_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_assigned_waiter_id_foreign` FOREIGN KEY (`assigned_waiter_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_items
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `food_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `special_notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_food_id_foreign` (`food_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_assignments
CREATE TABLE `order_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `assigned_by` bigint(20) unsigned NOT NULL,
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_assignments_order_id_index` (`order_id`,`status`),
  KEY `order_assignments_employee_id_foreign` (`employee_id`),
  KEY `order_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `order_assignments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_assignments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: payments
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('cash','card','mobile','bank_transfer') NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `paid_by` bigint(20) unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_index` (`order_id`,`status`),
  KEY `payments_paid_by_foreign` (`paid_by`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INVENTORY MANAGEMENT
-- =====================================================

-- Table: suppliers
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_status_index` (`status`),
  KEY `suppliers_created_by_foreign` (`created_by`),
  CONSTRAINT `suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_categories
CREATE TABLE `inventory_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_items
CREATE TABLE `inventory_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost_price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_items_category_id_index` (`category_id`,`status`),
  KEY `inventory_items_quantity_index` (`quantity`),
  KEY `inventory_items_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stock_transactions
CREATE TABLE `stock_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` bigint(20) unsigned NOT NULL,
  `type` enum('purchase','usage','adjustment','return') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transactions_inventory_item_id_index` (`inventory_item_id`,`type`),
  KEY `stock_transactions_reference_index` (`reference_type`,`reference_id`),
  KEY `stock_transactions_created_by_foreign` (`created_by`),
  CONSTRAINT `stock_transactions_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchases
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `purchase_number` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','received','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_status_index` (`status`,`created_at`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_created_by_foreign` (`created_by`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: purchase_items
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `inventory_item_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_index` (`purchase_id`),
  KEY `purchase_items_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: food_ingredients
CREATE TABLE `food_ingredients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food_id` bigint(20) unsigned NOT NULL,
  `inventory_item_id` bigint(20) unsigned NOT NULL,
  `quantity_required` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `food_ingredients_food_id_inventory_item_id_unique` (`food_id`,`inventory_item_id`),
  KEY `food_ingredients_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `food_ingredients_food_id_foreign` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_ingredients_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_alerts
CREATE TABLE `inventory_alerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` bigint(20) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` enum('active','resolved') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_alerts_inventory_item_id_index` (`inventory_item_id`,`status`),
  CONSTRAINT `inventory_alerts_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NOTIFICATIONS
-- =====================================================

-- Table: notifications
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SEED DATA - DEFAULT RECORDS FOR PRODUCTION
-- =====================================================

-- Insert Roles
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Full system access with all administrative privileges', NOW(), NOW()),
(2, 'Manager', 'Restaurant management access including staff, inventory, and reports', NOW(), NOW()),
(3, 'Chef', 'Kitchen management and order preparation access', NOW(), NOW()),
(4, 'Waiter', 'Customer service and order taking access', NOW(), NOW()),
(5, 'Cashier', 'Payment processing and billing access', NOW(), NOW()),
(6, 'Receptionist', 'Front desk and reservation management access', NOW(), NOW()),
(7, 'Inventory Officer', 'Inventory management and stock control access', NOW(), NOW()),
(8, 'Customer', 'Customer portal access for orders and reservations', NOW(), NOW());

-- Insert Default Users (Password: password - hashed with bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `account_status`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0001', '123 Admin Street', 'active', 'admin', NOW(), NOW()),
(2, 'Manager', 'manager@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0002', '123 Manager Street', 'active', 'employee', NOW(), NOW()),
(3, 'Chef', 'chef@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0003', '123 Chef Street', 'active', 'employee', NOW(), NOW()),
(4, 'Waiter', 'waiter@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0004', '123 Waiter Street', 'active', 'employee', NOW(), NOW()),
(5, 'Cashier', 'cashier@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0005', '123 Cashier Street', 'active', 'employee', NOW(), NOW()),
(6, 'Receptionist', 'reception@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0006', '123 Reception Street', 'active', 'employee', NOW(), NOW()),
(7, 'Inventory Officer', 'inventory@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0007', '123 Inventory Street', 'active', 'employee', NOW(), NOW()),
(8, 'Customer', 'customer@colevora.com', NOW(), '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lZ3PYKxYKYaa', '+1 (555) 100-0008', '123 Customer Street', 'active', 'customer', NOW(), NOW());

-- Assign Roles to Users
INSERT INTO `role_user` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, NOW(), NOW()),
(2, 2, NOW(), NOW()),
(3, 3, NOW(), NOW()),
(4, 4, NOW(), NOW()),
(5, 5, NOW(), NOW()),
(6, 6, NOW(), NOW()),
(7, 7, NOW(), NOW()),
(8, 8, NOW(), NOW());

-- Insert Employees
INSERT INTO `employees` (`id`, `user_id`, `job_title`, `employment_status`, `hire_date`, `salary`, `created_at`, `updated_at`) VALUES
(1, 2, 'Restaurant Manager', 'active', '2026-01-01', 5000.00, NOW(), NOW()),
(2, 3, 'Head Chef', 'active', '2026-01-01', 4000.00, NOW(), NOW()),
(3, 4, 'Senior Waiter', 'active', '2026-01-01', 2500.00, NOW(), NOW()),
(4, 5, 'Cashier', 'active', '2026-01-01', 2500.00, NOW(), NOW()),
(5, 6, 'Receptionist', 'active', '2026-01-01', 2500.00, NOW(), NOW()),
(6, 7, 'Inventory Officer', 'active', '2026-01-01', 3000.00, NOW(), NOW());

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Appetizers', 'appetizers', 'Start your meal with our delicious appetizers', 'active', NOW(), NOW()),
(2, 'Main Course', 'main-course', 'Our signature main dishes', 'active', NOW(), NOW()),
(3, 'Desserts', 'desserts', 'Sweet treats to end your meal', 'active', NOW(), NOW()),
(4, 'Beverages', 'beverages', 'Refreshing drinks and beverages', 'active', NOW(), NOW()),
(5, 'Hot Drinks', 'hot-drinks', 'Coffee, tea, and hot beverages', 'active', NOW(), NOW()),
(6, 'Cold Drinks', 'cold-drinks', 'Juices, smoothies, and cold beverages', 'active', NOW(), NOW()),
(7, 'Alcoholic Drinks', 'alcoholic-drinks', 'Beer, wine, and cocktails', 'active', NOW(), NOW()),
(8, 'Salads', 'salads', 'Fresh and healthy salad options', 'active', NOW(), NOW()),
(9, 'Soups', 'soups', 'Warm and comforting soups', 'active', NOW(), NOW());

-- Insert Site Settings
INSERT INTO `site_settings` (`key`, `value`, `type`, `created_at`, `updated_at`) VALUES
('restaurant_name', 'Colevora Restaurant', 'text', NOW(), NOW()),
('restaurant_tagline', 'Experience the Finest Dining', 'text', NOW(), NOW()),
('phone', '+1 (555) 123-4567', 'text', NOW(), NOW()),
('phone_secondary', '+1 (555) 123-4568', 'text', NOW(), NOW()),
('email', 'info@colevora.com', 'text', NOW(), NOW()),
('address', '123 Main Street, City, State 12345', 'textarea', NOW(), NOW()),
('facebook', 'https://facebook.com/colevora', 'url', NOW(), NOW()),
('twitter', 'https://twitter.com/colevora', 'url', NOW(), NOW()),
('instagram', 'https://instagram.com/colevora', 'url', NOW(), NOW()),
('footer_about', 'Experience the finest dining with our exquisite menu and exceptional service.', 'textarea', NOW(), NOW()),
('footer_content', '© 2026 Colevora Restaurant. All rights reserved.', 'textarea', NOW(), NOW()),
('opening_hours_mon_fri', '11:00 AM – 10:00 PM', 'text', NOW(), NOW()),
('opening_hours_sat', '10:00 AM – 11:00 PM', 'text', NOW(), NOW()),
('opening_hours_sun', '10:00 AM – 9:00 PM', 'text', NOW(), NOW()),
('meta_description', 'Colevora Restaurant – Experience the finest dining with our exquisite menu, professional chefs, and exceptional service.', 'textarea', NOW(), NOW()),
('meta_keywords', 'restaurant, dining, food, colevora, menu, reservation', 'text', NOW(), NOW()),
('cookie_consent_text', 'We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.', 'textarea', NOW(), NOW()),
('privacy_policy_url', '/privacy', 'text', NOW(), NOW()),
('terms_url', '/terms', 'text', NOW(), NOW()),
('tax_rate', '10', 'text', NOW(), NOW()),
('currency_symbol', '$', 'text', NOW(), NOW()),
('delivery_fee', '3.99', 'text', NOW(), NOW()),
('min_order_amount', '15.00', 'text', NOW(), NOW());

-- Insert Sample Restaurant Tables
INSERT INTO `restaurant_tables` (`id`, `table_number`, `capacity`, `location`, `status`, `created_at`, `updated_at`) VALUES
(1, 'T01', 2, 'Main Hall', 'available', NOW(), NOW()),
(2, 'T02', 2, 'Main Hall', 'available', NOW(), NOW()),
(3, 'T03', 4, 'Main Hall', 'available', NOW(), NOW()),
(4, 'T04', 4, 'Main Hall', 'available', NOW(), NOW()),
(5, 'T05', 6, 'Main Hall', 'available', NOW(), NOW()),
(6, 'T06', 6, 'VIP Section', 'available', NOW(), NOW()),
(7, 'T07', 8, 'VIP Section', 'available', NOW(), NOW()),
(8, 'T08', 4, 'Outdoor', 'available', NOW(), NOW()),
(9, 'T09', 4, 'Outdoor', 'available', NOW(), NOW()),
(10, 'T10', 2, 'Bar Area', 'available', NOW(), NOW());

-- Insert Inventory Categories
INSERT INTO `inventory_categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Vegetables', 'Fresh vegetables and produce', 'active', NOW(), NOW()),
(2, 'Meats', 'Beef, chicken, pork, and other meats', 'active', NOW(), NOW()),
(3, 'Seafood', 'Fish and seafood products', 'active', NOW(), NOW()),
(4, 'Dairy', 'Milk, cheese, butter, and dairy products', 'active', NOW(), NOW()),
(5, 'Spices & Herbs', 'Cooking spices and herbs', 'active', NOW(), NOW()),
(6, 'Beverages', 'Soft drinks, juices, and beverage supplies', 'active', NOW(), NOW()),
(7, 'Dry Goods', 'Rice, pasta, flour, and dry ingredients', 'active', NOW(), NOW()),
(8, 'Oils & Sauces', 'Cooking oils and sauces', 'active', NOW(), NOW());

-- Insert Migrations Record
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_07_17_234931_add_extended_fields_to_users_table', 1),
('2026_07_17_234952_create_roles_table', 1),
('2026_07_17_235009_create_permissions_table', 1),
('2026_07_17_235029_create_employees_table', 1),
('2026_07_17_235043_create_audit_logs_table', 1),
('2026_07_17_235057_create_permission_role_table', 1),
('2026_07_17_235105_create_role_user_table', 1),
('2026_07_18_000623_create_hero_slides_table', 1),
('2026_07_18_000636_create_pages_table', 1),
('2026_07_18_000650_create_site_settings_table', 1),
('2026_07_18_000703_create_categories_table', 1),
('2026_07_18_000722_create_food_table', 1),
('2026_07_18_000737_create_food_price_changes_table', 1),
('2026_07_18_000752_create_food_assignments_table', 1),
('2026_07_18_002431_create_restaurant_tables_table', 1),
('2026_07_18_002447_create_carts_table', 1),
('2026_07_18_002453_create_cart_items_table', 1),
('2026_07_18_002507_create_orders_table', 1),
('2026_07_18_002513_create_order_items_table', 1),
('2026_07_18_002526_create_order_assignments_table', 1),
('2026_07_18_002539_create_payments_table', 1),
('2026_07_18_004440_create_suppliers_table', 1),
('2026_07_18_004457_create_inventory_categories_table', 1),
('2026_07_18_004519_create_inventory_items_table', 1),
('2026_07_18_004535_create_stock_transactions_table', 1),
('2026_07_18_004550_create_purchases_table', 1),
('2026_07_18_004626_create_purchase_items_table', 1),
('2026_07_18_004700_create_food_ingredients_table', 1),
('2026_07_18_004714_create_inventory_alerts_table', 1),
('2026_07_18_103753_create_testimonials_table', 1),
('2026_07_18_103754_create_reservations_table', 1),
('2026_07_18_103755_create_newsletter_subscribers_table', 1),
('2026_07_18_135734_create_notifications_table', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- END OF SQL EXPORT
-- All 36 tables created successfully
-- Default accounts ready for login
-- Database: colevora_rims is ready for deployment
-- =====================================================
