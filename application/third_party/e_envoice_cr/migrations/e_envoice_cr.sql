INSERT INTO `ospos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_e_envoice_cr', 'module_e_envoice_cr_desc', 1000, 'e_envoice_cr');

INSERT INTO `ospos_permissions` (`permission_id`, `module_id`) VALUES
('e_envoice_cr', 'e_envoice_cr');

INSERT INTO `ospos_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('e_envoice_cr', 1, 'office');

INSERT INTO `ospos_app_config` (`key`, `value`) VALUES
('e_envoice_cr_env', ''),
('e_envoice_cr_username', ''),
('e_envoice_cr_password', ''),
('e_envoice_cr_id_type', ''),
('e_envoice_cr_id', ''),
('e_envoice_cr_commercial_name',''),
('e_envoice_cr_cert_password', ''),
('e_envoice_cr_resolution_number',''),
('e_envoice_cr_resolution_date','');

INSERT INTO `ospos_app_config` (`key`, `value`) VALUES
('e_envoice_cr_address_province', ''),
('e_envoice_cr_address_canton', ''),
('e_envoice_cr_address_distrit', ''),
('e_envoice_cr_address_neighborhood', ''),
('e_envoice_cr_address_other','');

INSERT INTO `ospos_app_config` (`key`, `value`) VALUES
('e_envoice_cr_consecutive_fe',''),
('e_envoice_cr_consecutive_te',''),
('e_envoice_cr_consecutive_nc',''),
('e_envoice_cr_consecutive_nd','');

