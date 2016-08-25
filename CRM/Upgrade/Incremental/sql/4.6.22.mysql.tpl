ALTER TABLE civicrm_event
  ADD COLUMN `domain_id` int(10) unsigned DEFAULT NULL COMMENT 'Which Domain is this entry for';

INSERT IGNORE INTO civicrm_setting
  (domain_id, contact_id, is_domain, group_name, name, value)
VALUES
  ({$domainID}, NULL, 0, 'Multi Site Preferences', 'multisite_event_per_domain', '{serialize}0{/serialize}');
