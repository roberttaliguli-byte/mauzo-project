-- Fix Dickson User Authentication Issue
-- This script updates the Dickson user and company to allow login

-- 1. Update Dickson's company (ID 11) to be approved
UPDATE `companies` 
SET 
    `is_verified` = 1,
    `is_user_approved` = 1,
    `updated_at` = NOW()
WHERE `id` = 11;

-- 2. Update Dickson user to have company_id and be approved
UPDATE `users` 
SET 
    `company_id` = 11,
    `is_approved` = 1,
    `updated_at` = NOW()
WHERE `username` = 'Dickson';

-- Verify the changes
SELECT 'Users Table' as 'Table', id, username, email, company_id, is_approved FROM users WHERE username = 'Dickson'
UNION ALL
SELECT 'Companies Table', id, company_name, email, is_verified, is_user_approved FROM companies WHERE id = 11;
