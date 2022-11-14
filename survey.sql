-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2022 at 08:41 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `survey`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_survey_completed_num` (`id` INT) RETURNS DECIMAL(10,0)  BEGIN
DECLARE res decimal(0);
SELECT COUNT(survey_info.survey_id)
INTO res 
FROM survey_info WHERE survey_info.status = 'Completed' AND survey_info.survey_id=id;
return res;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question_type` enum('TEXT','RADIO','CHECKBOX') NOT NULL DEFAULT 'TEXT',
  `question_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`user_id`, `question_id`, `survey_id`, `question_type`, `question_answer`) VALUES
(3, 2, 1, 'TEXT', 'fine thank you');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_survey_id` int(11) NOT NULL,
  `question_title` varchar(200) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_survey_id`, `question_title`, `question_id`) VALUES
(1, 'how are you?', 2),
(1, 'choose', 3),
(1, 'choose multiple', 4),
(2, 'first', 5),
(2, 'second', 6),
(2, 'third', 7);

-- --------------------------------------------------------

--
-- Table structure for table `question_details`
--

CREATE TABLE `question_details` (
  `question_id` int(11) NOT NULL,
  `question_type` enum('TEXT','RADIO','CHECK') NOT NULL DEFAULT 'TEXT',
  `question_radio_num` int(11) NOT NULL DEFAULT 0,
  `question_radio_value` varchar(500) DEFAULT NULL,
  `answer_count` int(11) NOT NULL DEFAULT 0,
  `answer_values` varchar(500) DEFAULT NULL,
  `question_check_num` int(11) NOT NULL DEFAULT 0,
  `question_check_value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question_details`
--

INSERT INTO `question_details` (`question_id`, `question_type`, `question_radio_num`, `question_radio_value`, `answer_count`, `answer_values`, `question_check_num`, `question_check_value`) VALUES
(2, 'TEXT', 0, NULL, 0, NULL, 0, ''),
(3, 'RADIO', 2, 'ONE, TWO', 0, NULL, 0, ''),
(4, '', 0, NULL, 0, NULL, 3, 'ONE, THREE, TWO'),
(5, 'TEXT', 0, NULL, 0, NULL, 0, ''),
(6, 'RADIO', 2, 'ASF, SDF', 0, NULL, 0, ''),
(7, '', 0, NULL, 0, NULL, 3, 'ME, YOU, OTHER');

-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `survey_id` int(11) NOT NULL,
  `survey_title` varchar(200) NOT NULL,
  `survey_start_date` date NOT NULL DEFAULT current_timestamp(),
  `survey_expire_date` date NOT NULL DEFAULT current_timestamp(),
  `survey_status` enum('active','pended') NOT NULL DEFAULT 'pended'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey`
--

INSERT INTO `survey` (`survey_id`, `survey_title`, `survey_start_date`, `survey_expire_date`, `survey_status`) VALUES
(1, 'test', '2022-11-14', '2022-11-15', 'active'),
(2, 'test again', '2022-11-14', '2022-11-15', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `survey_info`
--

CREATE TABLE `survey_info` (
  `user_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `complete_date` date DEFAULT NULL,
  `sent_date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','Completed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_info`
--

INSERT INTO `survey_info` (`user_id`, `survey_id`, `complete_date`, `sent_date`, `status`) VALUES
(1, 2, '2022-11-15', '2022-11-14', 'Completed'),
(2, 1, NULL, '2022-11-14', 'pending'),
(3, 1, NULL, '2022-11-14', 'pending'),
(3, 2, NULL, '2022-11-14', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(40) NOT NULL,
  `user_type` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `user_created_at` date NOT NULL DEFAULT current_timestamp(),
  `user_status` enum('active','pended') NOT NULL DEFAULT 'pended'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_type`, `user_created_at`, `user_status`) VALUES
(1, 'ali ashour', 'aliashour592@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'ADMIN', '2022-11-14', 'active'),
(2, 'adam', 'adam@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'USER', '2022-11-14', 'pended'),
(3, 'moaz', 'moaz@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'USER', '2022-11-14', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`user_id`,`question_id`,`survey_id`),
  ADD KEY `quq` (`question_id`),
  ADD KEY `sus` (`survey_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `sdgxzfdgf` (`question_survey_id`);

--
-- Indexes for table `question_details`
--
ALTER TABLE `question_details`
  ADD KEY `qqqq` (`question_id`);

--
-- Indexes for table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`survey_id`);

--
-- Indexes for table `survey_info`
--
ALTER TABLE `survey_info`
  ADD PRIMARY KEY (`user_id`,`survey_id`),
  ADD KEY `FK_SUPPPLER_PRODUCT` (`survey_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `survey`
--
ALTER TABLE `survey`
  MODIFY `survey_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `quq` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sus` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uuu` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `sdgxzfdgf` FOREIGN KEY (`question_survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_details`
--
ALTER TABLE `question_details`
  ADD CONSTRAINT `qqqq` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_info`
--
ALTER TABLE `survey_info`
  ADD CONSTRAINT `FK_FAVOUIRT_POST_ID` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_SUPPPLER_PRODUCT` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
