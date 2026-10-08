CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_id` varchar(255) UNIQUE DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'available',
  `trait_tag` varchar(50) DEFAULT 'family',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pets` (`name`, `category`, `trait_tag`) VALUES
('Persian', 'Cat', 'calm'),
('Indian Billi', 'Cat', 'active'),
('Golden Retriever', 'Dog', 'family'),
('German Shepherd', 'Dog', 'active'),
('Macaw', 'Parrot', 'active'),
('Syrian Hamster', 'Hamster', 'calm'),
('Lionhead', 'Rabbit', 'family'),
('Red-Eared Slider', 'Turtle', 'calm');
