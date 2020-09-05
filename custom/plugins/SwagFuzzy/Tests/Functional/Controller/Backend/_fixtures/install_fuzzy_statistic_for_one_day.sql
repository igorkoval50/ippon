INSERT INTO `s_plugin_swag_fuzzy_statistics` (`shopId`, `searchTerm`, `firstSearchDate`, `lastSearchDate`, `searchesCount`, `resultsCount`) VALUES
(1, 'fooBar', '1980-01-01 00:00:01','1980-01-03 23:44:12', 5, 0),
(2, 'fooBar', '1980-01-01 00:00:01','1980-01-03 23:44:12', 3, 0);

INSERT INTO `s_statistics_search` (`datum`, `searchterm`, `results`, `shop_id`) VALUES
('1980-01-02 15:24:12', 'fooBar', 0, 1),
('1980-01-02 15:25:12', 'fooBar', 0, 1),
('1980-01-03 19:26:12', 'fooBar', 0, 1),
('1980-01-03 20:27:12', 'fooBar', 0, 1),
('1980-01-03 23:28:12', 'fooBar', 0, 1),

('1980-01-02 15:30:12', 'fooBar', 0, 2),
('1980-01-03 18:31:12', 'fooBar', 0, 2),
('1980-01-03 23:32:12', 'fooBar', 0, 2);
