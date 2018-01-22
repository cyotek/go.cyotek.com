BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS 'Error' 
(
  'Id'          INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, 'Timestamp'   INTEGER NOT NULL
, 'IpAddress'   TEXT    NOT NULL
, 'Url'         TEXT    NOT NULL
, 'StatusCode'  INTEGER NOT NULL
);

DROP VIEW 'SystemVersion';

CREATE VIEW IF NOT EXISTS 'SystemVersion'
AS
   SELECT '1.1.0' AS [SchemaVersion];

INSERT INTO [Config] ([Name], [Value]) VALUES ('SystemVersion', '1.1.0');

INSERT INTO [Config] ([Name], [Value]) VALUES ('Title'        , 'go.cyotek.com');          -- title used for a friendly 200 page
INSERT INTO [Config] ([Name], [Value]) VALUES ('HomePage'     , 'https://www.cyotek.com'); -- url of site to display in friendly 200 page

COMMIT;
