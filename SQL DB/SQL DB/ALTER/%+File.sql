ALTER TABLE ClientProject
ADD ProjectCompletion TINYINT UNSIGNED DEFAULT 0 CHECK (ProjectCompletion >= 0 AND ProjectCompletion <= 100);

ALTER TABLE ClientProject
ADD ProjectFiles BLOB;