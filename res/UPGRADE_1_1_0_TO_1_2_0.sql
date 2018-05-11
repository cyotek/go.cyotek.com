UPDATE [Config]
   SET [Value] = '1.2.0'
 WHERE [Name]  = 'SystemVersion';
 
INSERT INTO [Config] ([Name], [Value]) VALUES ('AnonymizeAddresses', 'true');                   -- should IP addresses be anonymised?
