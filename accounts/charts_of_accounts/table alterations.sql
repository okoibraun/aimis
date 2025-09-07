Notes:

ALTER TABLE customers ADD invoice_id INT NULL AFTER id;
ALTER TABLE sales_customers ADD invoice_id INT NULL AFTER id;
ALTER TABLE invoices DROP user_id;
ALTER TABLE bills ADD bill_date DATETIME NULL AFTER amount, ADD description TEXT NULL, ADD reference VARCHAR(100) NULL AFTER description;
ALTER TABLE bills ADD paid_amount DECIMAL(15,2) NULL AFTER amount;
ALTER TABLE payments ADD bill_id INT NULL AFTER invoice_id;

ALTER TABLE crm_leads ADD ai_score DECIMAL(5,2) DEFAULT NULL;
ALTER TABLE cmr_leads ADD industry VARCHAR(150) DEFAULT NULL;

ALTER TABLE crm_activities ADD lead_id INT DEFAULT NULL AFTER company_id;