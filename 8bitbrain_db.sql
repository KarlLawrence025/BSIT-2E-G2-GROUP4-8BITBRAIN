CREATE DATABASE IF NOT EXISTS 8bitbrain_db;
USE 8bitbrain_db;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id`           int(11)                   NOT NULL AUTO_INCREMENT,
  `fullname`     varchar(100)              NOT NULL,
  `email`        varchar(100)              NOT NULL,
  `username`     varchar(50)               NOT NULL,
  `age`          int(11)                   NOT NULL,
  `password`     varchar(255)              NOT NULL,
  `account_type` enum('user','admin')      NOT NULL DEFAULT 'user',
  `status`       enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at`   timestamp                 NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email`    (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `age`, `password`, `account_type`, `status`, `created_at`) VALUES
(1, 'Admin', 'admin@8bitbrain.com', 'admin', 25, 'admin123', 'admin', 'active', '2026-03-13 04:51:10');

-- --------------------------------------------------------
-- Table: quizzes
-- --------------------------------------------------------
CREATE TABLE `quizzes` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `title`      varchar(255) NOT NULL,
  `category`   varchar(100) NOT NULL,
  `difficulty` enum('easy','medium','hard')                                                   NOT NULL DEFAULT 'medium',
  `mode`       enum('single_player','timed_quiz','ranked_quiz','memory_match','endless_quiz') NOT NULL DEFAULT 'single_player',
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: questions
-- --------------------------------------------------------
CREATE TABLE `questions` (
  `id`            int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id`       int(11) NOT NULL,
  `question_text` text    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `questions_ibfk_1`
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: answers
-- --------------------------------------------------------
CREATE TABLE `answers` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `question_id` int(11)      NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `is_correct`  tinyint(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `answers_ibfk_1`
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: quiz_attempts
-- Stores every quiz attempt with computed points
-- --------------------------------------------------------
CREATE TABLE `quiz_attempts` (
  `id`            int(11)                                                                      NOT NULL AUTO_INCREMENT,
  `user_id`       int(11)                                                                      NOT NULL,
  `quiz_id`       int(11)                                                                      NOT NULL,
  `mode`          enum('single_player','timed_quiz','ranked_quiz','memory_match','endless_quiz') NOT NULL DEFAULT 'single_player',
  `difficulty`    enum('easy','medium','hard')                                                  NOT NULL DEFAULT 'medium',
  `correct`       int(11)                                                                      NOT NULL DEFAULT 0,
  `total`         int(11)                                                                      NOT NULL DEFAULT 0,
  `time_taken`    int(11)                                                                      NOT NULL DEFAULT 0,
  `points_earned` int(11)                                                                      NOT NULL DEFAULT 0,
  `created_at`    timestamp                                                                    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `quiz_id`  (`quiz_id`),
  CONSTRAINT `qa_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users`   (`id`) ON DELETE CASCADE,
  CONSTRAINT `qa_quiz_fk`
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: leaderboard
-- One row per user — stores accumulated totals
-- Updated automatically by save_quiz_result.php after each attempt
-- --------------------------------------------------------
CREATE TABLE `leaderboard` (
  `id`              int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`         int(11)      NOT NULL,
  `username`        varchar(50)  NOT NULL,
  `fullname`        varchar(100) NOT NULL,
  `total_points`    int(11)      NOT NULL DEFAULT 0,
  `total_correct`   int(11)      NOT NULL DEFAULT 0,
  `total_questions` int(11)      NOT NULL DEFAULT 0,
  `attempts`        int(11)      NOT NULL DEFAULT 0,
  `updated_at`      timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `leaderboard_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: feedback
-- --------------------------------------------------------
CREATE TABLE `feedback` (
  `id`            int(11)                                        NOT NULL AUTO_INCREMENT,
  `user_id`       int(11)                                        DEFAULT NULL,
  `quiz_id`       int(11)                                        DEFAULT NULL,
  `feedback_text` text                                           NOT NULL,
  `feedback_type` enum('general','suggestion','bug','complaint') NOT NULL DEFAULT 'general',
  `rating`        tinyint(1)                                     DEFAULT NULL,
  `status`        enum('pending','resolved')                     NOT NULL DEFAULT 'pending',
  `created_at`    timestamp                                      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `quiz_id`  (`quiz_id`),
  CONSTRAINT `fb_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users`   (`id`) ON DELETE SET NULL,
  CONSTRAINT `fb_quiz_fk`
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: quiz_references
-- --------------------------------------------------------
CREATE TABLE `quiz_references` (
  `id`             int(11)      NOT NULL AUTO_INCREMENT,
  `quiz_id`        int(11)      NOT NULL,
  `question_id`    int(11)      DEFAULT NULL,
  `reference_text` text         DEFAULT NULL,
  `reference_url`  varchar(500) DEFAULT NULL,
  `reference_type` varchar(50)  NOT NULL DEFAULT 'url',
  `created_at`     timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `quiz_id`     (`quiz_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `qr_quiz_fk`
    FOREIGN KEY (`quiz_id`)     REFERENCES `quizzes`   (`id`) ON DELETE CASCADE,
  CONSTRAINT `qr_question_fk`
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------
ALTER TABLE `users`          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `quizzes`        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `questions`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `answers`        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `quiz_attempts`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `leaderboard`    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `feedback`       MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `quiz_references`MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
