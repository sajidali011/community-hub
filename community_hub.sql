-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 06:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `community_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT 'uploads/community_logos/default-logo.png',
  `privacy` enum('public','private') NOT NULL,
  `creator_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `communities`
--

INSERT INTO `communities` (`id`, `name`, `description`, `category`, `logo`, `privacy`, `creator_id`, `created_at`) VALUES
(4, 'cafe hub', 'Cafe Hub\" ek modern aur attractive cafe ka naam ho sakta hai, jo customers ko ek relaxed aur vibrant environment provide kare. Agar aap Cafe Hub ka concept develop karna chahte hain, toh yahaan ek overview hai:', 'Other', 'uploads/community_logos/cafehub-rangirkhari-silchar-restaurants-c0nk1j7ll0.avif', 'public', 13, '2024-11-25 06:58:00'),
(5, 'xyz', 'xyz', 'Music', 'uploads/community_logos/.trashed-1729437073-Snapchat-670329417.jpg', 'public', 14, '2024-11-25 10:09:50'),
(6, 'shoes design for men', 'Men\'s shoes deals currently feature exciting discounts:\r\n\r\n1. **Adidas Men\'s Lite Racer Sneakers**: Available at $49 (originally $70) on Amazon.\r\n2. **Skechers Men\'s Afterburn Sneakers**: Priced at $48 (originally $80).\r\n3. **Brooks Men\'s Ghost Max Shoes**: Offered for $99.95 (down from $150) at Holabird Sports.\r\n4. **Clarks Men\'s Bradley Mid Boots**: On sale for $45 (regularly $100) on eBay.\r\n\r\nFor more options, you can check retailers like Amazon, DSW, and eBay for active Black Friday deals.', 'Other', 'uploads/community_logos/images.jpeg', 'public', 14, '2024-11-26 05:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `community_events`
--

CREATE TABLE `community_events` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_leaderboard`
--

CREATE TABLE `community_leaderboard` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_members`
--

CREATE TABLE `community_members` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('admin','moderator','member') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_members`
--

INSERT INTO `community_members` (`id`, `community_id`, `user_id`, `role`, `joined_at`) VALUES
(4, 4, 13, 'admin', '2024-11-25 06:58:00'),
(5, 5, 14, 'admin', '2024-11-25 10:09:50'),
(6, 6, 14, 'admin', '2024-11-26 05:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

CREATE TABLE `community_posts` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `votes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'draft',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

CREATE TABLE `post_reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction` enum('like','love','haha','wow','sad','angry') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `imgupload` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `imgupload`, `firstname`, `username`, `email`, `password`, `bio`) VALUES
(13, 'uploads/1000117884.jpg', 'sam', 'samxoxx', 'adi4a2x@gmail.com', '11122005', 'DNI'),
(14, 'uploads/IMG20240508124322.jpg', 'sajid ali', 'sajid_ali0005', 'sajidali2003@gmail.com', '1234', 'Hey i\'m Sajid Ali am a skilled Full Stack Developer with expertise in html, css, bootstap.js, php,laravel and databases like MySQL and MongoDB. I excel in creating responsive, user-friendly web applications and enjoy solving complex problems. With a focus on both front end and back-end development.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `community_events`
--
ALTER TABLE `community_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `community_leaderboard`
--
ALTER TABLE `community_leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community_members`
--
ALTER TABLE `community_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `community_events`
--
ALTER TABLE `community_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_leaderboard`
--
ALTER TABLE `community_leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_members`
--
ALTER TABLE `community_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_reactions`
--
ALTER TABLE `post_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `communities`
--
ALTER TABLE `communities`
  ADD CONSTRAINT `communities_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `register` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_events`
--
ALTER TABLE `community_events`
  ADD CONSTRAINT `community_events_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`),
  ADD CONSTRAINT `community_events_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `register` (`id`);

--
-- Constraints for table `community_leaderboard`
--
ALTER TABLE `community_leaderboard`
  ADD CONSTRAINT `community_leaderboard_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_leaderboard_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_members`
--
ALTER TABLE `community_members`
  ADD CONSTRAINT `community_members_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`),
  ADD CONSTRAINT `community_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`);

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`),
  ADD CONSTRAINT `polls_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `register` (`id`);

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`);

--
-- Constraints for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD CONSTRAINT `post_reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts_old` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts_old` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
