BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS 'Config' 
(
  'Id'          INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, 'Name'        TEXT    NOT NULL
, 'Value'       TEXT    NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS 'UK_Config_Name' ON 'Config' 
(
  'Name'
);

CREATE TABLE IF NOT EXISTS 'Url' 
(
  'Id'          INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, 'Enabled'     INTEGER NOT NULL
, 'Title'       TEXT    NOT NULL
, 'Slug'        TEXT    NOT NULL
, 'Url'         TEXT    NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS 'UK_Url_Slug' ON 'Url' 
(
  'Slug'
);

CREATE TABLE IF NOT EXISTS 'Activity' 
(
  'Id'          INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, 'UrlId'       INTEGER NOT NULL
, 'Timestamp'   INTEGER NOT NULL
, 'IpAddress'   TEXT    NOT NULL
, 'Url'         TEXT    NOT NULL
, FOREIGN KEY(UrlId) REFERENCES Url(Id)
);

CREATE INDEX IF NOT EXISTS 'IX_Activity_UrlId' ON 'Activity' 
(
  'UrlId'
);

CREATE TABLE IF NOT EXISTS 'Error' 
(
  'Id'          INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT
, 'Timestamp'   INTEGER NOT NULL
, 'IpAddress'   TEXT    NOT NULL
, 'Url'         TEXT    NOT NULL
, 'StatusCode'  INTEGER NOT NULL
);

CREATE VIEW IF NOT EXISTS 'SystemVersion'
AS
   SELECT '1.1.0' AS [SchemaVersion];

CREATE VIEW IF NOT EXISTS 'ActivitySummary'
AS
   SELECT [R].[Id]         AS [Id]
        , [R].[Title]      AS [Title]
        , [R].[Slug]       AS [Slug]
        , [H].[Url]        AS [Url]
        , [H].[Timestamp]  AS [Timestamp]
        , [H].[IpAddress]  AS [IpAddress]
     FROM [Url] [R]
     JOIN [Activity] [H]
       ON [H].[UrlId] = [R].[Id];

CREATE VIEW IF NOT EXISTS 'UrlSummary'
AS
   SELECT MAX([R].[Id])         AS [Id]
        , MAX([R].[Enabled])    AS [Enabled]
        , MAX([R].[Title])      AS [Title]
        , MAX([R].[Slug])       AS [Slug]
        , MAX([R].[Url])        AS [Url]
        , MAX([H].[Timestamp])  AS [LastHit]
        , COUNT([H].[Id])       AS [HitCount]
     FROM [Url] [R]
LEFT JOIN [Activity] [H]
       ON [H].[UrlId] = [R].[Id]
 GROUP BY [R].[Id];
       
INSERT INTO [Config] ([Name], [Value]) VALUES ('ApiKey'            , '');                       -- make sure you add a real value here (such as GUID) to use pages "protected" with the key. Blank keys are rejected.
INSERT INTO [Config] ([Name], [Value]) VALUES ('Title'             , 'go.cyotek.com');          -- title used for a friendly 200 page
INSERT INTO [Config] ([Name], [Value]) VALUES ('HomePage'          , 'https://www.cyotek.com'); -- url of site to display in friendly 200 page
INSERT INTO [Config] ([Name], [Value]) VALUES ('AnonymizeAddresses', 'true');                   -- should IP addresses be anonymised?

INSERT INTO [Config] ([Name], [Value]) VALUES ('SystemVersion', '1.2.0');
 
COMMIT;
