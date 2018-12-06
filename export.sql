CREATE TABLE comments
(
  id           INT AUTO_INCREMENT NOT NULL,
  photo_id     INT           NOT NULL,
  author_id    INT           NOT NULL,
  content      VARCHAR(1000) NOT NULL,
  published_on DATETIME      NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  INDEX        IDX_5F9E962A7E9E4C8C (photo_id),
  INDEX        IDX_5F9E962AF675F31B (author_id),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE messages
(
  id         INT AUTO_INCREMENT NOT NULL,
  name       VARCHAR(255)  NOT NULL,
  email      VARCHAR(255)  NOT NULL,
  content    VARCHAR(2550) NOT NULL,
  created_on DATETIME      NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE photos
(
  id          INT AUTO_INCREMENT NOT NULL,
  user_id     INT          NOT NULL,
  path        VARCHAR(255) NOT NULL,
  uploaded_on DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  INDEX       IDX_876E0D9A76ED395 (user_id),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
CREATE TABLE users
(
  id         INT AUTO_INCREMENT NOT NULL,
  username   VARCHAR(25)  NOT NULL,
  password   VARCHAR(255) NOT NULL,
  active     TINYINT(1) NOT NULL,
  roles      JSON         NOT NULL,
  created_on DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE comments
  ADD CONSTRAINT FK_5F9E962A7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id);
ALTER TABLE comments
  ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id);
ALTER TABLE photos
  ADD CONSTRAINT FK_876E0D9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
