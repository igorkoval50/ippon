UPDATE `s_user`
SET `lastlogin` = NOW(),
    `sessionID` = 'sessionId2',
    `password` = 'testPassword123'
WHERE `s_user`.`id` = 1;
