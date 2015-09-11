{* file to handle db changes in 4.4.19 during upgrade *}
-- Cividesk: additional periodicity for scheduled jobs
ALTER TABLE civicrm_job
  MODIFY COLUMN `run_frequency` enum('Always','Hourly','Daily','Weekly','Monthly','Quarterly') COLLATE utf8_unicode_ci DEFAULT 'Daily' COMMENT 'Scheduled job run frequency.';
