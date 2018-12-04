BEGIN TRANSACTION

CREATE TABLE [__app__Config] (
	[Key] varchar(64) PRIMARY KEY ,
	[Value] nvarchar(MAX) NULL ,
)
GO

CREATE TABLE [__app__User] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Name] nvarchar(64) NOT NULL ,
	[Email] nvarchar(256) NULL ,
	[Passwd] varchar(40) NULL ,
	[Salt] varchar(16) NULL ,
	--[Sex] bit NULL ,
	[Data] nvarchar(MAX) NULL ,
	[Commit] nvarchar(MAX) NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL
)
GO
CREATE INDEX [__app__User_Email] ON [__app__User] ([Email] ASC)
GO
CREATE TRIGGER [User_Delete] ON [__app__User] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__User] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
	DELETE [__app__UserRole] WHERE [Uid] IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [User_Update] ON [__app__User] AFTER UPDATE AS
BEGIN
	UPDATE [__app__User] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__Token] (
	[Token] varchar(96) PRIMARY KEY ,
	[Uid] bigint NULL ,
	[AuthSource] int NULL , -- 0:Passwd 1:OpenId? 2:OAuth2?
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeletionTime] datetime2 NULL -- DEFAULT DATEADD(MINUTE, 60, getdate()) 
	CONSTRAINT [__app__Token_User] FOREIGN KEY ([Uid]) REFERENCES [__app__User] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE INDEX [__app__Token_Token] ON [__app__Token] ([Token] ASC)
GO
CREATE TRIGGER [Token_Delete] ON [__app__Token] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__Token] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Token IN (SELECT Token FROM Deleted)
END
GO

CREATE TABLE [__app__Tenant] (
	[Id] int PRIMARY KEY IDENTITY ,
	[Tname] nvarchar(64) NOT NULL UNIQUE,
	[Name] nvarchar(256) NULL ,
	[IsActive] bit NOT NULL DEFAULT 1 ,
	[Commit] nvarchar(MAX) NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
)
GO
CREATE UNIQUE INDEX [__app__Tenant_Tname] ON [__app__Tenant] ([Tname] ASC) WITH (IGNORE_DUP_KEY = ON)
GO
CREATE TRIGGER [Tenant_Delete] ON [__app__Tenant] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__Tenant] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [Tenant_Update] ON [__app__Tenant] AFTER UPDATE AS
BEGIN
	UPDATE [__app__Tenant] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__Article] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Title] nvarchar(200) NULL ,
	[Public] bit NOT NULL DEFAULT 0 ,
	[Content] nvarchar(MAX) NULL ,
	[ExtensionData] nvarchar(MAX) NULL ,
	[ContestId] bigint NULL ,
	[TenantId] int NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
	CONSTRAINT [__app__Article_Tenant] FOREIGN KEY ([TenantId]) REFERENCES [__app__Tenant] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE INDEX [__app__Article_Title] ON [__app__Article] ([Title] ASC) 
GO
CREATE TRIGGER [Article_Delete] ON [__app__Article] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__Article] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [Article_Update] ON [__app__Article] AFTER UPDATE AS
BEGIN
	UPDATE [__app__Article] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__ArticleCommit] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Aid] bigint NULL ,
	[Pid] bigint NULL ,
	[Commit] nvarchar(MAX) NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModificationTime] datetime2 NULL 
	CONSTRAINT [__app__ArticleCommit_Article] FOREIGN KEY ([Aid]) REFERENCES [__app__Article] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE TRIGGER [ArticleCommit_Delete] ON [__app__ArticleCommit] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__ArticleCommit] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [ArticleCommit_Update] ON [__app__ArticleCommit] AFTER UPDATE AS
BEGIN
	UPDATE [__app__ArticleCommit] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__BinaryObject] (
	[Id] uniqueidentifier PRIMARY KEY ,
	[Public] bit NULL ,
	[FileName] nvarchar(256) NULL ,
	[FilePath] nvarchar(256) NULL ,
	[ExtensionData] nvarchar(MAX) NULL ,
	[TenantId] int NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL 
	CONSTRAINT [__app__BinaryObject_Tenant] FOREIGN KEY ([TenantId]) REFERENCES [__app__Tenant] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE TRIGGER [BinaryObject_Delete] ON [__app__BinaryObject] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__BinaryObject] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__ArticleFile] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Aid] bigint NOT NULL ,
	[Fid] uniqueidentifier NOT NULL ,
	CONSTRAINT [__app__ArticleFiles_Article] FOREIGN KEY ([Aid]) REFERENCES [__app__Article] ([Id]) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT [__app__ArticleFiles_File] FOREIGN KEY ([Fid]) REFERENCES [__app__BinaryObject] ([Id]) ON DELETE CASCADE
)
GO

CREATE TABLE [__app__CMSPage] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Name] nvarchar(64) NOT NULL ,
	[Data] nvarchar(256) NULL ,
	[Commit] nvarchar(MAX) NULL ,
	[TenantId] int NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
	CONSTRAINT [__app__CMSPage_Tenant] FOREIGN KEY ([TenantId]) REFERENCES [__app__Tenant] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE INDEX [__app__CMSPage_name] ON [__app__CMSPage] ([Name] ASC)
GO
CREATE TRIGGER [CMSPage_Delete] ON [__app__CMSPage] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__CMSPage] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [CMSPage_Update] ON [__app__CMSPage] AFTER UPDATE AS
BEGIN
	UPDATE [__app__CMSPage] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__CMSTemplate] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Name] nvarchar(64) NOT NULL ,
	[RootId] bigint NULL ,
	[ParentId] bigint NULL ,
	[Data] nvarchar(MAX) NULL ,
	[Template] nvarchar(MAX) NULL ,
	[Commit] nvarchar(MAX) NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
)
GO
CREATE INDEX [__app__CMSTemplate_name] ON [__app__CMSTemplate] ([Name] ASC)
GO
CREATE TRIGGER [CMSTemplate_Delete] ON [__app__CMSTemplate] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__CMSTemplate] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [CMSTemplate_Update] ON [__app__CMSTemplate] AFTER UPDATE AS
BEGIN
	UPDATE [__app__CMSTemplate] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__Role] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Name] nvarchar(64) NOT NULL ,
	[Permission] nvarchar(MAX) NULL ,
	[TenantId] int NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
	CONSTRAINT [__app__Role_Tenant] FOREIGN KEY ([TenantId]) REFERENCES [__app__Tenant] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE
)
GO
CREATE TRIGGER [Role_Delete] ON [__app__Role] INSTEAD OF DELETE AS
BEGIN
	UPDATE [__app__Role] SET [IsDeleted] = 1, [DeletionTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
	DELETE [__app__UserRole] WHERE [Rid] IN (SELECT Id FROM Deleted)
END
GO
CREATE TRIGGER [Role_Update] ON [__app__Role] AFTER UPDATE AS
BEGIN
	UPDATE [__app__Role] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

CREATE TABLE [__app__UserRole] (
	[Id] bigint PRIMARY KEY IDENTITY ,
	[Uid] bigint NOT NULL ,
	[Rid] bigint NOT NULL ,
	[TenantId] int NULL ,
	[CreatorUserId] bigint NULL ,
	[CreationTime] datetime2 NOT NULL DEFAULT getdate() ,
	[IsDeleted] bit NOT NULL DEFAULT 0 ,
	[DeleterUserId] bigint NULL ,
	[DeletionTime] datetime2 NULL ,
	[LastModifierUserId] bigint NULL ,
	[LastModificationTime] datetime2 NULL 
	CONSTRAINT [__app__UserRole_Tenant] FOREIGN KEY ([TenantId]) REFERENCES [__app__Tenant] ([Id]) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT [__app__UserRole_User] FOREIGN KEY ([Uid]) REFERENCES [__app__User] ([Id]) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT [__app__UserRole_Role] FOREIGN KEY ([Rid]) REFERENCES [__app__Role] ([Id]) ON DELETE CASCADE ON UPDATE NO ACTION
)
GO
CREATE TRIGGER [UserRole_Update] ON [__app__UserRole] AFTER UPDATE AS
BEGIN
	UPDATE [__app__UserRole] SET [LastModificationTime] = getdate() WHERE Id IN (SELECT Id FROM Deleted)
END
GO

INSERT [__app__Tenant] ([Tname], [name]) VALUES ('DEFAULT', 'Default') --select @@identity
INSERT [__app__User] ([name], [Email]) VALUES ('Admin', 'Admin')
INSERT [__app__User] ([name], [Email]) VALUES ('Misaka', 'Misaka@163.com')
INSERT [__app__Role] ([name], [Permission], [TenantId]) VALUES ('Administrator', '{}', 1)
INSERT [__app__UserRole] ([Uid], [Rid], [TenantId]) VALUES (1, 1, 1)
INSERT [__app__UserRole] ([Uid], [Rid], [TenantId]) VALUES (2, 1, 1)
INSERT [__app__Config] ([Key], [Value]) VALUES ('CMS_SERVER_RENDER', 'False')
INSERT [__app__Article] ([Title], [Content]) VALUES ('TEST1', 'Test Content')
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId]) VALUES ('TEST2', 1, 'Test Content ADMIN', 1)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [LastModifierUserId]) VALUES ('TEST3', 1, 'Test Content', 1, 1)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime]) VALUES ('TEST4', 1, 'Test Content', 2, '1970-12-31 23:59:59')
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId], [LastModificationTime]) VALUES ('TEST5', 1, 'Test Content', 2, '1980-12-31 23:59:59', 1, getdate())
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [IsDeleted]) VALUES ('TEST6', 1, 'Test Content', 2, 1)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST7', 1, 'Test Content', 2, '2018-12-02 23:59:59', 1)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST8', 1, 'Test Content', 2, '2018-12-03 23:59:59', 2)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST9', 1, 'Test Content', 2, '2018-12-06 23:59:59', 2)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST10', 1, 'Test Content', 2, '2018-12-07 23:59:59', 2)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST11', 1, 'Test Content', 2, '2018-12-08 23:59:59', 2)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId]) VALUES ('TEST12', 1, 'Test Content', 2, '2018-12-09 23:59:59', 2)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime], [LastModifierUserId], [LastModificationTime]) VALUES ('TEST13', 1, 'Test Content', 2, '2018-12-07 23:59:59', 2, '2018-12-01 23:59:59')
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [LastModifierUserId]) VALUES ('TEST14', 1, 'Test Content', 2, 1)
INSERT [__app__Article] ([Title], [Public], [Content], [CreatorUserId], [CreationTime]) VALUES ('TEST15', 1, 'Test Content', 2, '2018-12-31 23:59:59')
GO

CREATE VIEW [__app__ArticleView] AS 
	SELECT A.[Id], [Title], [Content], [ExtensionData], [ContestId], [TenantId], A.[CreatorUserId], C.[Name] AS [CreatorUserName], A.[CreationTime], A.[LastModifierUserId], M.[Name] AS [LastModifierUserName], A.[LastModificationTime]
	FROM [__app__Article] AS A
	LEFT JOIN [__app__User] AS C
		ON A.[CreatorUserId] = C.Id
	LEFT JOIN [__app__User] AS M
		ON A.[LastModifierUserId] = C.Id
	WHERE A.[IsDeleted] = 0 AND [Public] = 1 
GO
CREATE VIEW [__app__ArticleCommitView] AS 
	SELECT * FROM [__app__ArticleCommit] WHERE [IsDeleted] = 0
GO
CREATE VIEW [__app__CMSPageView] AS 
	SELECT * FROM [__app__CMSPage] WHERE [IsDeleted] = 0
GO
CREATE VIEW [__app__CMSTemplateView] AS 
	SELECT * FROM [__app__CMSTemplate] WHERE [IsDeleted] = 0
GO


CREATE PROCEDURE [__app__Soft_Delete_Trigge] @able AS bit AS
BEGIN
	IF @able = 0
	BEGIN
		ALTER TABLE [__app__User] DISABLE TRIGGER [User_Delete]
		-- Disable Token_Delete means disable hard-logout and hard-logout time record
		--ALTER TABLE [__app__Token] DISABLE TRIGGER [Token_Delete]
		ALTER TABLE [__app__Tenant] DISABLE TRIGGER [Tenant_Delete]
		ALTER TABLE [__app__Article] DISABLE TRIGGER [Article_Delete]
		ALTER TABLE [__app__ArticleCommit] DISABLE TRIGGER [ArticleCommit_Delete]
		ALTER TABLE [__app__BinaryObject] DISABLE TRIGGER [BinaryObject_Delete]
		ALTER TABLE [__app__CMSPage] DISABLE TRIGGER [CMSPage_Delete]
		ALTER TABLE [__app__CMSTemplate] DISABLE TRIGGER [CMSTemplate_Delete]
		ALTER TABLE [__app__Role] DISABLE TRIGGER [Role_Delete]
	END ELSE BEGIN
		ALTER TABLE [__app__User] ENABLE TRIGGER [User_Delete]
		--ALTER TABLE [__app__Token] ENABLE TRIGGER [Token_Delete]
		ALTER TABLE [__app__Tenant] ENABLE TRIGGER [Tenant_Delete]
		ALTER TABLE [__app__Article] ENABLE TRIGGER [Article_Delete]
		ALTER TABLE [__app__ArticleCommit] ENABLE TRIGGER [ArticleCommit_Delete]
		ALTER TABLE [__app__BinaryObject] ENABLE TRIGGER [BinaryObject_Delete]
		ALTER TABLE [__app__CMSPage] ENABLE TRIGGER [CMSPage_Delete]
		ALTER TABLE [__app__CMSTemplate] ENABLE TRIGGER [CMSTemplate_Delete]
		ALTER TABLE [__app__Role] ENABLE TRIGGER [Role_Delete]
	END
END
GO
CREATE PROCEDURE [__app__Clean_Deleted] AS
BEGIN
	DELETE [__app__User] WHERE [IsDeleted] = 1
	DELETE [__app__Tenant] WHERE [IsDeleted] = 1
	DELETE [__app__Article] WHERE [IsDeleted] = 1
	DELETE [__app__ArticleCommit] WHERE [IsDeleted] = 1
	DELETE [__app__BinaryObject] WHERE [IsDeleted] = 1
	DELETE [__app__CMSPage] WHERE [IsDeleted] = 1
	DELETE [__app__CMSTemplate] WHERE [IsDeleted] = 1
	DELETE [__app__Role] WHERE [IsDeleted] = 1
END
GO

IF @@error>0 BEGIN
 ROLLBACK TRANSACTION
END ELSE BEGIN
 COMMIT TRANSACTION
END
GO