
CREATE LOGIN [cms] WITH PASSWORD = N'cms' 
, CHECK_EXPIRATION=OFF 
, CHECK_POLICY=OFF 
, DEFAULT_DATABASE=[CMS_Database] 
GO

USE [CMS_Database]
GO

CREATE USER [cms] FOR LOGIN [cms]
GO