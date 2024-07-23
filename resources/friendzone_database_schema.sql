CREATE DATABASE IF NOT EXISTS `friendzone`;

USE `friendzone`;

CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) UNIQUE,
    `profile_image_url` VARCHAR(500),
    `email` VARCHAR(255),
    `password` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `posts` (
    `post_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `title` TEXT,
    `content` TEXT,
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

CREATE TABLE IF NOT EXISTS `comments` (
    `comment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT,
    `user_id` INT,
    `content` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`post_id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);
