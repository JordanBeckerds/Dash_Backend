CREATE TABLE ClientWebTest (
    WebTestId INT AUTO_INCREMENT PRIMARY KEY,
    ClientId SMALLINT,
    ProjectId INT,
    TestResults VARCHAR(1000),
    FOREIGN KEY (ClientId) REFERENCES ClientLogin(ClientId) ON UPDATE CASCADE,
    FOREIGN KEY (ProjectId) REFERENCES ClientProject(ProjectId) ON UPDATE CASCADE
);
