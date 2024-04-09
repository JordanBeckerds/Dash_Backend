-- Table: ClientLogin
CREATE TABLE ClientLogin (
    ClientId SMALLINT AUTO_INCREMENT PRIMARY KEY,
    Client VARCHAR(28) NOT NULL,
    ClientKey VARCHAR(32) NOT NULL
);

-- Table: AdminLogin
CREATE TABLE AdminLogin (
    AdminId SMALLINT AUTO_INCREMENT PRIMARY KEY,
    Admin VARCHAR(28) NOT NULL,
    AdminKey VARCHAR(32) NOT NULL
);

-- Table: Timestamp
CREATE TABLE Timestamp (
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    AdminId SMALLINT,
    ClientId SMALLINT,
    FOREIGN KEY (AdminId) REFERENCES AdminLogin(AdminId) ON UPDATE CASCADE,
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE
);

-- Table: AdminInfo
CREATE TABLE AdminInfo (
    AdminId SMALLINT,
    Name VARCHAR(40),
    Email VARCHAR(60),
    FOREIGN KEY (AdminId) REFERENCES AdminLogin(AdminId) ON UPDATE CASCADE
);

-- Table: ClientInfo
CREATE TABLE ClientInfo (
    ClientId SMALLINT,
    Name VARCHAR(40) NOT NULL,
    Company VARCHAR(40),
    Email VARCHAR(60) NOT NULL,
    Phone VARCHAR(15) NOT NULL PRIMARY KEY,
    ClientDesc TEXT,
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE
);

-- Table: ClientProject
CREATE TABLE ClientProject (
    ClientId SMALLINT,
    ProjectId INT AUTO_INCREMENT PRIMARY KEY,
    ProjectName VARCHAR(30),
    ProjectFiles BLOB,
    ProjectDesc TEXT,
    AnalyticsId INT,
    ProjectCompletion TINYINT UNSIGNED DEFAULT 0 CHECK (ProjectCompletion >= 0 AND ProjectCompletion <= 100);
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE
);

-- Table: ClientTicket
CREATE TABLE ClientTicket (
    ClientId SMALLINT,
    ProjectId INT,
    TicketId INT AUTO_INCREMENT PRIMARY KEY,
    Ticket TEXT,
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE,
    FOREIGN KEY (ProjectId) REFERENCES ClientProject(ProjectId) ON UPDATE CASCADE
);

CREATE TABLE ClientWebTest (
    WebTestId INT AUTO_INCREMENT PRIMARY KEY,
    ClientId SMALLINT,
    ProjectId INT,
    TestResults VARCHAR(1000),
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE,
    FOREIGN KEY (ProjectId) REFERENCES ClientProject(ProjectId) ON UPDATE CASCADE
);

-- Inserting values into AdminLogin
INSERT INTO AdminLogin (Admin, AdminKey) VALUES 
('Mateo', 'TooMuchFlowIn2024'),
('Jordan', 'NotEnoughFlowIn2024');
