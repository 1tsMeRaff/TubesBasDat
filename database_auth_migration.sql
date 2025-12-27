-- Migration: Add Password and Role fields to Pelanggan table
-- Run this SQL before using the authentication system

ALTER TABLE Pelanggan 
ADD COLUMN Password VARCHAR(255) NULL AFTER Email,
ADD COLUMN Role ENUM('customer', 'admin') DEFAULT 'customer' AFTER Password;

-- Update existing customers to have NULL password (they need to register)
-- Or set a default password for testing (NOT RECOMMENDED FOR PRODUCTION)

