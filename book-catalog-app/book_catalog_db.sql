-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2023 at 04:15 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book_catalog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_book`
--

CREATE TABLE `tbl_book` (
  `tbl_book_id` int(11) NOT NULL,
  `book_image` text NOT NULL,
  `book_title` text NOT NULL,
  `book_category` text NOT NULL,
  `book_author` text NOT NULL,
  `book_abstract` longtext NOT NULL,
  `book_text` longtext NOT NULL,
  `read_status` enum('not_started','reading','finished') DEFAULT 'not_started',
  `last_read_position` float DEFAULT 0,
  `rating` int(11) DEFAULT NULL,
  `time_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_book`
--

INSERT INTO `tbl_book` (`tbl_book_id`, `book_image`, `book_title`, `book_category`, `book_author`, `book_abstract`, `time_added`) VALUES
(7, 'FANTASY.jpg', 'Shadow and Bone', 'Fantasy', 'Leigh Bardugo', 'The novel follows Alina Starkov, a teenage orphan who grows up in the Russia-inspired land of Ravka when, one day, she unexpectedly harnesses a power she never knew she had, becoming a target of intrigue and violence. It is the first book in the Shadow and Bone trilogy, followed by Siege and Storm and Ruin and Rising.', '2023-08-30 20:05:42'),
(8, 'HORROR.jpg', 'IT', 'Horror', 'Stephen King', 'It is a 1986 horror novel by American author Stephen King. It was his 22nd book and his 17th novel written under his own name. The story follows the experiences of seven children as they are terrorized by an evil entity that exploits the fears of its victims to disguise itself while hunting its prey.', '2023-08-30 20:06:53'),
(9, 'php.jpg', 'PHP: Learn PHP in One Day and Learn It Well. PHP for Beginners with Hands-on Project.', 'Educational', 'Jamie Chan', 'PHP performs system functions, i.e. from files on a system it can create, open, read, write, and close them. PHP can handle forms, i.e. gather data from files, save data to a file, through email you can send data, return data to the user. You add, delete, modify elements within your database through PHP.', '2023-08-31 02:08:35'),
(10, 'HTML.jpg', 'A Smarter Way to Learn HTML and CSS: Learn It Faster. Remember It Longer', 'Educational', 'Mark Myers', 'HTML is a markup language used to create static web pages and web applications. CSS is a style sheet language responsible for the presentation of documents written in a markup language.', '2023-08-30 20:10:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_book`
--
ALTER TABLE `tbl_book`
  ADD PRIMARY KEY (`tbl_book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_book`
--
ALTER TABLE `tbl_book`
  MODIFY `tbl_book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
