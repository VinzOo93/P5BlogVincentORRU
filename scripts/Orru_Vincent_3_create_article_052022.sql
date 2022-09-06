CREATE TABLE article (
	id_article INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
	tag  VARCHAR(255),
    image VARCHAR(255) NOT NULL,
	content TEXT NOT NULL,
    date_published DATETIME NOT NULL,
    author INT NOT NULL, 
    FOREIGN KEY(author) REFERENCES user(id_user)
    ON DELETE CASCADE,
	CONSTRAINT UC_Article UNIQUE (title),
	CONSTRAINT UC_Article_2 UNIQUE (slug)
);



