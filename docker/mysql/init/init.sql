-- Initialize Mewayz v2 Database
CREATE DATABASE IF NOT EXISTS mewayz_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user for security
CREATE USER IF NOT EXISTS 'mewayz_user'@'%' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz_production.* TO 'mewayz_user'@'%';

-- Performance optimizations
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL innodb_log_file_size = 256M;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL innodb_flush_method = O_DIRECT;

-- Security settings
SET GLOBAL local_infile = 0;
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

FLUSH PRIVILEGES;