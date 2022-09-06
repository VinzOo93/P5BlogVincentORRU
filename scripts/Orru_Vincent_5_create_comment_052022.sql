CREATE TABLE comment (
	id_comment INT AUTO_INCREMENT PRIMARY KEY,
	author INT NOT NULL, 
    content TEXT NOT NULL,
    date_added DATETIME NOT NULL,
    is_visible BOOL DEFAULT FALSE,
    article INT NOT NULL,
    FOREIGN KEY(author) REFERENCES user(id_user)
    ON DELETE CASCADE,
	FOREIGN KEY(article) REFERENCES article(id_article)
    ON DELETE CASCADE
);

