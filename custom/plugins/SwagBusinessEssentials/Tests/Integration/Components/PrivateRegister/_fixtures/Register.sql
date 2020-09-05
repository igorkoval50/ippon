UPDATE s_user SET validation = 'H' WHERE id = 1;

INSERT INTO s_core_plugins_b2b_private
    VALUES (1000, 'H', 1, 1, 1, '', '', 'H', '', '', '', '');

INSERT INTO s_core_plugins_b2b_cgsettings
    VALUES (1000, 'H', 1, 1, 'TMP', '', '', '');

INSERT INTO s_core_customergroups
    VALUES (1000, 'TMP', '', 1, 1, 1, 0.0, 0.0, 0.0);