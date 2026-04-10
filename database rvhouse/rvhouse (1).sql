-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2026 at 02:33 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rvhouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `agent_id` varchar(10) NOT NULL,
  `agent_password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `agent_gender` varchar(10) DEFAULT NULL,
  `agent_birthOfDate` date DEFAULT NULL,
  `agent_address` varchar(100) DEFAULT NULL,
  `agent_contactNo` varchar(11) DEFAULT NULL,
  `agent_email` varchar(50) DEFAULT NULL,
  `role` int DEFAULT NULL,
  `agent_bank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`agent_id`, `agent_password`, `agent_name`, `agent_gender`, `agent_birthOfDate`, `agent_address`, `agent_contactNo`, `agent_email`, `role`, `agent_bank`) VALUES
('alina03', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Noor Alina', 'female', '2003-03-31', 'Kelantan', '0134428805', '2022484332@student.uitm.edu.my', 1, 'Bank Simpanan Nasional 1140041000064996'),
('azlini02', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Azlini', 'female', '2003-02-22', 'Kuala Lumpur', '0134428802', 'ftnnaz02@gmail.com', 1, 'Bank Islam 13017028848162'),
('shah03', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Shahira', 'female', '2003-02-22', 'Kelantan', '0134428802', 'shah03@gmail.com.my', 1, 'RHB 16301500268367'),
('wan01', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Wan', 'female', '2003-03-23', 'Kuala Lumpur', '0134428805', 'wsirysha02@gmail.com.my', 1, 'CIMB 7614691537');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` varchar(10) NOT NULL,
  `feedback_rating` double DEFAULT NULL,
  `feedback_comment` varchar(100) DEFAULT NULL,
  `house_id` varchar(10) DEFAULT NULL,
  `rental_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `feedback_rating`, `feedback_comment`, `house_id`, `rental_id`) VALUES
('F001', 4.5, 'Very good', 'H005', 'R010'),
('F002', 3, 'Good', 'H006', 'R012'),
('F003', 5, 'Very impressive and comfortable', 'H016', 'R034'),
('F004', 3.5, 'very good', 'H017', 'R036'),
('F005', 4, 'good', 'H001', 'R001'),
('F006', 4.5, 'very comfortable', 'H013', 'R025'),
('F007', 4.5, 'besttttt', 'H018', 'R039'),
('F008', 2.5, 'not bad but not good', 'H008', 'R015'),
('F009', 4, 'goood view', 'H004', 'R007'),
('F010', 4.5, 'i love ittt!!', 'H005', 'R009'),
('F011', 5, 'nice view', 'H015', 'R031'),
('F012', 4.5, 'very good place', 'H012', 'R023'),
('F013', 5, 'love itt', 'H024', 'R051'),
('F014', 5, 'goooddd', 'H022', 'R045'),
('F015', 4.5, 'comey dooohhh', 'H010', 'R020'),
('F016', 5, 'super duper good', 'H024', 'R050');

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `guest_id` varchar(10) NOT NULL,
  `guest_password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `guest_name` varchar(50) DEFAULT NULL,
  `guest_gender` varchar(10) DEFAULT NULL,
  `guest_birthOfDate` date DEFAULT NULL,
  `guest_address` varchar(100) DEFAULT NULL,
  `guest_contactNo` varchar(11) DEFAULT NULL,
  `guest_email` varchar(50) DEFAULT NULL,
  `role` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`guest_id`, `guest_password`, `guest_name`, `guest_gender`, `guest_birthOfDate`, `guest_address`, `guest_contactNo`, `guest_email`, `role`) VALUES
('Az', 'e10adc3949ba59abbe56e057f20f883e', 'Fatin', 'Female', '2002-08-14', 'No.123 Jalan Bahagia, Manir', '0123456789', 'fatin123@gmail.com', 0),
('ftnnaz02', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Fatin Noorazlini', 'Female', '2002-03-02', 'Kuala Terengganu', '0134428801', 'ftnnaz02@gmail.com', 0),
('nralnfrh', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Alina Fariha', 'female', '2003-02-22', 'Kelantan', '0134428805', '2022484332@student.uitm.edu.my', 0),
('shah', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Shahira Izwani', 'female', '2003-04-04', 'Kelantan', '0134428806', 'shah03@gmail.com.my', 0),
('wsirysha', '0102812fbd5f73aa18aa0bae2cd8f79f', 'Wan Seri', 'female', '2001-10-08', 'Kuala Lumpur', '0134428802', 'wsirysha02@student.uitm.edu.my', 0);

-- --------------------------------------------------------

--
-- Table structure for table `house`
--

CREATE TABLE `house` (
  `house_id` varchar(10) NOT NULL,
  `house_name` varchar(50) DEFAULT NULL,
  `house_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `house_state` varchar(100) DEFAULT NULL,
  `house_type` varchar(20) DEFAULT NULL,
  `house_rate` int DEFAULT NULL,
  `house_availability` varchar(100) DEFAULT NULL,
  `house_details` varchar(250) DEFAULT NULL,
  `house_image` varchar(255) DEFAULT NULL,
  `agent_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `house`
--

INSERT INTO `house` (`house_id`, `house_name`, `house_address`, `house_state`, `house_type`, `house_rate`, `house_availability`, `house_details`, `house_image`, `agent_id`) VALUES
('H001', 'Templer Park Rainforest Retreat', 'Jalan 3/2, Templer Height, Templer Park, 48000 Rawang, Selangor', 'Selangor', 'Terrace', 1200, 'Available', 'Mountain view,Free parking, Window AC unit, Kitchen, Private balcony, Shared Pool, Private garden, Fridge, Electric heaters, Cable TV', 'upload/HS01 (1).png,upload/HS01 (2).png,upload/HS01 (3).png,upload/HS01 (4).png', 'alina03'),
('H002', 'Pulaithree Retreat', 'Jalan Padang Golf, Tinggian KKB, 44000 Kuala Kubu Bharu, Selangor', 'Selangor', 'Apartment', 980, 'Available', 'Ocean view, Covered parking, Air conditioning, Fully equipped kitchen, Large balcony, Swimming pool access, Gym facilities, High-speed internet, Smart TV, Security system', 'upload/HS02 (1).png,upload/HS02 (2).png,upload/HS02 (3).png,upload/HS02 (4).png', 'alina03'),
('H003', 'The Neighbour KKB', 'Jalan Mat Kilau, Kampung Tun Abdul Razak, 44000 Kuala Kubu Baru, Selangor', 'Selangor', 'Condominium', 250, 'Available', 'Garden view, Covered parking, Split-type air conditioning, Modern kitchen, Balcony with cityscape, Gym facilities, Jacuzzi, Satellite TV, Laundry room, Concierge service', 'upload/HS03 (1).png,upload/HS03 (2).png,upload/HS03 (3).png,upload/HS03 (4).png', 'alina03'),
('H004', 'Hilton Petaling Jaya', 'Jalan Barat, Pjs 52, 46200 Petaling Jaya, Selangor', 'Selangor', 'Bungalow', 400, 'Available', 'Garden view, Underground parking, Central air conditioning, Gourmet kitchen, Private terrace, Infinity pool, Sauna, Home theater, BBQ area, Gym facilities', 'upload/HS04 (1).png,upload/HS04 (2).png,upload/HS04 (3).png,upload/HS04 (4).png', 'alina03'),
('H005', 'Courtyard No 6', 'Jalan Setia Dagang AH U13/AH, Seksyen U13, Selangor', 'Selangor', 'Condominium', 550, 'Available', 'Lake view, Garage parking, Ductless air conditioning, Open-plan kitchen, Spacious patio, Indoor pool, Tennis court, Game room, Library, Pet-friendly', 'upload/HS05 (1).png,upload/HS05 (2).png,upload/HS05 (3).png,upload/HS05 (4).png', 'alina03'),
('H006', 'Suite Dream', 'Jalan Sultan Omar, 18-3 Icon Residence, 20300 Kuala Terengganu', 'Terengganu', 'Condominium', 350, 'Available', 'River view, Street parking, Window AC unit, Compact kitchenette, Small balcony, Shared garden, Fitness center, Rooftop deck, Co-working space, Netflix', 'upload/HS06 (1).png,upload/HS06 (2).png,upload/HS06 (3).png,upload/HS06 (4).png', 'alina03'),
('H007', 'Tanjung Jara', 'Lot 6022, Kampung Temiang, 23000 Dungun, Terengganu', 'Terengganu', 'Bungalow', 820, 'Available', 'Mountain view, Covered parking, Split-system air conditioning, Breakfast bar, Large deck, Private pool, Hot tub, Home office, Movie theater, Fire pit', 'upload/HS07 (1).png,upload/HS07 (2).png,upload/HS07 (3).png,upload/HS07 (4).png', 'azlini02'),
('H008', 'Aurora Homes', 'PT24536 Jalan Titian Bulat, Kg Binjai Bongkok, 21400 Marang, Terengganu', 'Terengganu', 'Terrace', 530, 'Available', 'City skyline view, Driveway parking, Ceiling fans, Basic kitchenette, Juliet balcony, Communal courtyard, Yoga studio, BBQ terrace, Bike storage, Free Wi-Fi', 'upload/HS08 (1).png,upload/HS08 (2).png,upload/HS08 (3).png,upload/HS08 (4).png', 'azlini02'),
('H009', 'Redang De\' Rimba', 'Lot 171 Kampung Baru Pulau Redang, 21090 Redang Island, Terengganu', 'Terengganu', 'Chalet', 440, 'Available', 'Oceanfront view, Covered carport, Wall-mounted AC unit, Full kitchen, Sun deck, Shared beach access, Kayak rentals, Surfboard storage, Beach volleyball court, Outdoor shower', 'upload/HS09 (1).png,upload/HS09 (2).png,upload/HS09 (3).png,upload/HS09 (4).png', 'azlini02'),
('H010', 'De Jara', 'Jalan Seberang Pintasan, 23000 Dungun, Terengganu', 'Terengganu', 'Terrace', 255, 'Available', 'Panoramic city view, Private driveway, Window air conditioning, European-style kitchen, Juliet balcony, Shared rooftop garden, Community gym, Party room, Billiards table, Smart home technology', 'upload/HS10 (1).png,upload/HS10 (2).png,upload/HS10 (3).png,upload/HS10 (4).png', 'azlini02'),
('H011', 'Aurora Court Service Apartment', 'Jalan Perak off Burma Road, 10400 Kampong Makam, Pulau Pinang', 'Pulau Pinang', 'Apartment', 214, 'Available', 'Skyline view, Underground garage, Central HVAC system, Designer kitchen, Private patio, Lap pool, Sauna, Media room, Wine cellar, Butler service', 'upload/HS11 (1).png,upload/HS11 (2).png,upload/HS11 (3).png,upload/HS11 (4).png', 'azlini02'),
('H012', 'Metropol Serviced Apartment', 'Jalan Perda Utara 1, 14000 Bukit Mertajam, Pulau Pinang', 'Pulau Pinang', 'Chalet', 276, 'Unavailable', 'Forest view, On-street parking, Portable air conditioner, Compact kitchenette, Cozy terrace, Shared woodland, Meditation room, Art studio, Pet-friendly amenities, Local artist gallery', 'upload/HS12 (1).png,upload/HS12 (2).png,upload/HS12 (3).png,upload/HS12 (4).png', 'azlini02'),
('H013', 'Maritime Suites', 'Karpal Singh Drive, 11600 Jelutong, Penang', 'Pulau Pinang', 'Bungalow', 450, 'Available', 'Rope Walk Guest House, 78, Jalan Pintal Tali, 10100 George Town, Chalet RM 101', 'upload/HS13 (1).png,upload/HS13 (2).png,upload/HS13 (3).png,upload/HS13 (4).png', 'wan01'),
('H014', 'Royale Chulan Penang', 'No. 1 & 2, Pengkalan Weld, 10300 George Town', 'Pulau Pinang', 'Condominium', 567, 'Available', 'Table 4: House Rates in Pulau Pinang', 'upload/HS14 (1).png,upload/HS14 (2).png,upload/HS14 (3).png,upload/HS14 (4).png', 'wan01'),
('H015', 'Avillion Port Dickson', '3rd Mile Jalan Pantai, 71000 Port Dickson', 'Negeri Sembilan', 'Terrace', 700, 'Available', 'Lake view, Garage parking, Ductless air conditioning, Open-plan kitchen, Spacious patio, Indoor pool, Tennis court, Game room, Library, Pet-friendly', 'upload/HS15 (1).png,upload/HS15 (2).png,upload/HS15 (3).png,upload/HS15 (4).png', 'wan01'),
('H016', 'The Dusun Seremban', '437 Kampung Kolam Air, Mukim Pantai, 71770 Seremban', 'Negeri Sembilan', 'Bungalow', 235, 'Available', 'Lake view, Garage parking, Ductless air conditioning, Open-plan kitchen, Spacious patio, Indoor pool, Tennis court, Game room, Library, Pet-friendly', 'upload/HS16 (1).png,upload/HS16 (2).png,upload/HS16 (3).png,upload/HS16 (4).png', 'wan01'),
('H017', 'Klana Resort Seremban', 'PT 4388 Jalan Penghulu Cantik, Taman Tasik Seremban, 70100 Seremban', 'Negeri Sembilan', 'Condominium', 650, 'Available', 'River view, Street parking, Window AC unit, Compact kitchenette, Small balcony, Shared garden, Fitness center, Rooftop deck, Co-working space, Netflix', 'upload/HS17 (1).png,upload/HS17 (2).png,upload/HS17 (3).png,upload/HS17 (4).png', 'wan01'),
('H018', 'Thistle Port Dickson Resort', 'KM 16, Jalan Pantai, Teluk Kemang Si-Rusa, 71050 Port Dickson', 'Negeri Sembilan', 'Chalet', 890, 'Available', 'Mountain view,Free parking, Window AC unit, Kitchen, Private balcony, Shared Pool, Private garden, Fridge, Electric heaters, Cable TV', 'upload/HS18 (1).png,upload/HS18 (2).png,upload/HS18 (3).png,upload/HS18 (4).png', 'wan01'),
('H019', 'Wan\'s Apartment Bayu Beach Resort', 'Bayu Beach Resort, Batu 4½, Jalan Pantai, Mukim Si Rusa Bayu Beach Resort, 71050 Kampong Si Rusa', 'Negeri Sembilan', 'Apartment', 650, 'Available', 'River view, Street parking, Window AC unit, Compact kitchenette, Small balcony, Shared garden, Fitness center, Rooftop deck, Co-working space, Netflix', 'upload/HS19 (1).png,upload/HS19 (2).png,upload/HS19 (3).png,upload/HS19 (4).png', 'shah03'),
('H020', 'The Scarletz By RSR', '10 Jalan Yap Kwan Seng, 50450 Kuala Lumpur', 'Kuala Lumpur', 'Terrace', 500, 'Available', 'Mountain view,Free parking, Window AC unit, Kitchen, Private balcony, Shared Pool, Private garden, Fridge, Electric heaters, Cable TV', 'upload/HS20 (1).png,upload/HS20 (2).png,upload/HS20 (3).png,upload/HS20 (4).png', 'shah03'),
('H021', 'Ceylonz Suite KLCC', 'Ceylonz Suites @ Bukit Ceylon | EXSIM, Persiaran Raja Chulan, Bukit Kewangan, Bukit Bintang, 50200 Kuala Lumpur', 'Kuala Lumpur', 'Bungalow', 320, 'Available', 'Lake view, Garage parking, Ductless air conditioning, Open-plan kitchen, Spacious patio, Indoor pool, Tennis court, Game room, Library, Pet-friendly', 'upload/HS21 (1).png,upload/HS21 (2).png,upload/HS21 (3).png,upload/HS21 (4).png', 'shah03'),
('H022', 'Swiss Garden Residence Kuala Lumpur', 'No 2 Jalan Galloway, Bukit Bintang, 55100 Kuala Lumpur', 'Kuala Lumpur', 'Condominium', 699, 'Available', 'River view, Street parking, Window AC unit, Compact kitchenette, Small balcony, Shared garden, Fitness center, Rooftop deck, Co-working space, Netflix', 'upload/HS22 (1).png,upload/HS22 (2).png,upload/HS22 (3).png,upload/HS22 (4).png', 'shah03'),
('H023', 'The Platinum Kuala Lumpur By Crown Suites', 'Jalan Sultan Ismail, The Platinum, D 13A-9, 50250 Kuala Lumpur', 'Kuala Lumpur', 'Chalet', 540, 'Available', 'Mountain view,Free parking, Window AC unit, Kitchen, Private balcony, Shared Pool, Private garden, Fridge, Electric heaters, Cable TV', 'upload/HS23 (1).png,upload/HS23 (2).png,upload/HS23 (3).png,upload/HS23 (4).png', 'shah03'),
('H024', 'Marigold Apartment', '1 Jalan Imbi, Bukit Bintang, 55100 Kuala Lumpur', 'Kuala Lumpur', 'Apartment', 660, 'Available', 'Mountain view,Free parking, Window AC unit, Kitchen, Private balcony, Shared Pool, Private garden, Fridge, Electric heaters, Cable TV', 'upload/HS24 (1).png,upload/HS24 (2).png,upload/HS24 (3).png,upload/HS24 (4).png', 'shah03');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` varchar(10) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_amount` double DEFAULT NULL,
  `payment_receipt` varchar(255) DEFAULT NULL,
  `rental_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_date`, `payment_amount`, `payment_receipt`, `rental_id`) VALUES
('P001', '2024-05-01', 2520, 'receipts/Receipt1.png', 'R001'),
('P002', '2024-05-02', 2520, 'receipts/Receipt2.png', 'R002'),
('P003', '2024-05-03', 2058, 'receipts/Receipt3.png', 'R003'),
('P004', '2024-05-04', 2058, 'receipts/Receipt4.png', 'R004'),
('P005', '2024-05-05', 525, 'receipts/Receipt5.png', 'R005'),
('P006', '2024-05-05', 525, 'receipts/Receipt6.png', 'R006'),
('P007', '2024-05-06', 840, 'receipts/Receipt7.png', 'R007'),
('P008', '2024-05-08', 840, 'receipts/Receipt8.png', 'R008'),
('P009', '2024-05-09', 1155, 'receipts/Receipt9.png', 'R009'),
('P010', '2024-05-10', 1155, 'receipts/Receipt10.png', 'R010'),
('P011', '2024-05-10', 735, 'receipts/Receipt11.png', 'R011'),
('P012', '2024-06-01', 735, 'receipts/Receipt12.png', 'R012'),
('P013', '2024-05-11', 1722, 'receipts/Receipt13.png', 'R013'),
('P014', '2024-05-14', 1722, 'receipts/Receipt14.png', 'R014'),
('P015', '2024-06-17', 1113, 'receipts/Receipt15.png', 'R015'),
('P016', '2024-06-20', 1113, 'receipts/Receipt16.png', 'R016'),
('P017', '2024-06-23', 924, 'receipts/Receipt17.png', 'R017'),
('P018', '2024-06-26', 924, 'receipts/Receipt18.png', 'R018'),
('P019', '2024-06-29', 535.5, 'receipts/Receipt19.png', 'R019'),
('P020', '2024-07-01', 535.5, 'receipts/Receipt20.png', 'R020'),
('P021', '2024-07-04', 449.4, 'receipts/Receipt21.png', 'R021'),
('P022', '2024-07-07', 449.4, 'receipts/Receipt22.png', 'R022'),
('P023', '2024-07-01', 579.6, 'receipts/Receipt23.png', 'R023'),
('P024', '2024-07-02', 579.6, 'receipts/Receipt24.png', 'R024'),
('P025', '2024-05-26', 1395, 'receipts/Receipt25.png', 'R025'),
('P026', '2024-05-27', 945, 'receipts/Receipt26.png', 'R026'),
('P027', '2024-05-27', 1190.7, 'receipts/Receipt27.png', 'R027'),
('P028', '2024-05-28', 1757.7, 'receipts/Receipt28.png', 'R028'),
('P029', '2024-05-29', 1190.7, 'receipts/Receipt29.png', 'R029'),
('P030', '2024-06-01', 2170, 'receipts/Receipt30.png', 'R030'),
('P031', '2024-06-03', 3570, 'receipts/Receipt31.png', 'R031'),
('P032', '2024-06-15', 2170, 'receipts/Receipt32.png', 'R032'),
('P033', '2024-06-16', 1470, 'receipts/Receipt33.png', 'R033'),
('P034', '2024-06-04', 493.5, 'receipts/Receipt34.png', 'R034'),
('P035', '2024-06-05', 258.5, 'receipts/Receipt35.png', 'R035'),
('P036', '2024-06-05', 3315, 'receipts/Receipt36.png', 'R036'),
('P037', '2024-06-10', 1365, 'receipts/Receipt37.png', 'R037'),
('P038', '2024-06-08', 1869, 'receipts/Receipt38.png', 'R038'),
('P039', '2024-06-10', 1869, 'receipts/Receipt39.png', 'R039'),
('P040', '2024-06-13', 1365, 'receipts/Receipt40.png', 'R040'),
('P041', '2024-06-14', 1365, 'receipts/Receipt41.png', 'R041'),
('P042', '2024-06-16', 1050, 'receipts/Receipt42.png', 'R042'),
('P043', '2024-06-20', 1550, 'receipts/Receipt43.png', 'R043'),
('P044', '2024-06-21', 672, 'receipts/Receipt44.png', 'R044'),
('P045', '2024-06-22', 768.9, 'receipts/Receipt45.png', 'R045'),
('P046', '2024-06-25', 1674, 'receipts/Receipt46.png', 'R046'),
('P047', '2024-06-26', 1134, 'receipts/Receipt47.png', 'R047'),
('P048', '2024-06-27', 2046, 'receipts/Receipt48.png', 'R048'),
('P049', '2024-06-28', 1386, 'receipts/Receipt49.png', 'R049'),
('P050', '2024-07-04', 726, 'receipts/Receipt50.png', 'R050'),
('P051', '2024-07-05', 726, 'receipts/Receipt51.png', 'R051'),
('P052', '2024-07-11', 1640, 'receipts/QR202407050042594.pdf', 'R052'),
('P053', '2024-07-11', 1643, 'receipts/receipt(1).jpg', 'R053'),
('P054', '2024-07-11', 855.6, 'receipts/receipt(3).jpg', 'R054'),
('P055', '2024-07-11', 579.6, 'receipts/receipt(4).jpg', 'R055'),
('P056', '2026-04-10', 4018, 'receipts/Transaction_Receipt_15102023202219.pdf', 'R056');

-- --------------------------------------------------------

--
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `rental_id` varchar(10) NOT NULL,
  `checkin_date` date DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `rental_bookingdate` date DEFAULT NULL,
  `rental_status` varchar(100) DEFAULT NULL,
  `deposit` double DEFAULT NULL,
  `house_rate` double DEFAULT NULL,
  `full_payment` double DEFAULT NULL,
  `guest_id` varchar(10) DEFAULT NULL,
  `agent_id` varchar(10) DEFAULT NULL,
  `house_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental`
--

INSERT INTO `rental` (`rental_id`, `checkin_date`, `checkout_date`, `rental_bookingdate`, `rental_status`, `deposit`, `house_rate`, `full_payment`, `guest_id`, `agent_id`, `house_id`) VALUES
('R001', '2024-05-02', '2024-05-04', '2024-05-01', 'Accepted', 120, 1200, 2520, 'wsirysha', 'alina03', 'H001'),
('R002', '2024-05-05', '2024-05-07', '2024-05-02', 'Pending', 120, 1200, 2520, 'ftnnaz02', 'alina03', 'H001'),
('R003', '2024-05-08', '2024-05-10', '2024-05-03', 'Pending', 98, 980, 2058, 'shah', 'alina03', 'H002'),
('R004', '2024-05-11', '2024-05-13', '2024-05-04', 'Accepted', 98, 980, 2058, 'nralnfrh', 'alina03', 'H002'),
('R005', '2024-05-14', '2024-05-16', '2024-05-05', 'Rejected', 25, 250, 525, 'wsirysha', 'alina03', 'H003'),
('R006', '2024-05-17', '2024-05-19', '2024-05-05', 'Pending', 25, 250, 525, 'ftnnaz02', 'alina03', 'H003'),
('R007', '2024-05-20', '2024-05-22', '2024-05-06', 'Accepted', 40, 400, 840, 'shah', 'alina03', 'H004'),
('R008', '2024-05-22', '2024-05-24', '2024-05-08', 'Rejected', 40, 400, 840, 'nralnfrh', 'alina03', 'H004'),
('R009', '2024-05-25', '2024-05-27', '2024-05-09', 'Accepted', 55, 550, 1155, 'shah', 'alina03', 'H005'),
('R010', '2024-05-28', '2024-05-30', '2024-05-10', 'Accepted', 55, 550, 1155, 'ftnnaz02', 'alina03', 'H005'),
('R011', '2024-06-01', '2024-06-03', '2024-05-10', 'Pending', 35, 350, 735, 'wsirysha', 'alina03', 'H006'),
('R012', '2024-06-04', '2024-06-06', '2024-06-01', 'Accepted', 35, 350, 735, 'ftnnaz02', 'alina03', 'H006'),
('R013', '2024-06-07', '2024-06-09', '2024-05-11', 'Rejected', 82, 820, 1722, 'shah', 'azlini02', 'H007'),
('R014', '2024-06-10', '2024-06-12', '2024-05-14', 'Accepted', 82, 820, 1722, 'nralnfrh', 'azlini02', 'H007'),
('R015', '2024-06-21', '2024-06-23', '2024-06-17', 'Accepted', 53, 530, 1113, 'wsirysha', 'azlini02', 'H008'),
('R016', '2024-06-24', '2024-06-26', '2024-06-20', 'Rejected', 53, 530, 1113, 'ftnnaz02', 'azlini02', 'H008'),
('R017', '2024-06-24', '2024-06-26', '2024-06-23', 'Rejected', 44, 440, 924, 'shah', 'azlini02', 'H009'),
('R018', '2024-06-27', '2024-06-29', '2024-06-26', 'Accepted', 44, 440, 924, 'nralnfrh', 'azlini02', 'H009'),
('R019', '2024-07-01', '2024-07-03', '2024-06-29', 'Rejected', 25.5, 255, 535.5, 'shah', 'azlini02', 'H010'),
('R020', '2024-07-04', '2024-07-06', '2024-07-01', 'Accepted', 25.5, 255, 535.5, 'ftnnaz02', 'azlini02', 'H010'),
('R021', '2024-07-05', '2024-07-07', '2024-07-04', 'Accepted', 21.4, 214, 449.4, 'wsirysha', 'azlini02', 'H011'),
('R022', '2024-07-08', '2024-07-10', '2024-07-07', 'Rejected', 21.4, 214, 449.4, 'ftnnaz02', 'azlini02', 'H011'),
('R023', '2024-07-06', '2024-07-08', '2024-07-01', 'Accepted', 27.6, 276, 579.6, 'shah', 'azlini02', 'H012'),
('R024', '2024-07-08', '2024-07-10', '2024-07-02', 'Accepted', 27.6, 276, 579.6, 'nralnfrh', 'azlini02', 'H012'),
('R025', '2024-06-01', '2024-06-04', '2024-05-26', 'Accepted', 45, 450, 1395, 'wsirysha', 'wan01', 'H013'),
('R026', '2024-06-05', '2024-06-07', '2024-05-27', 'Pending', 45, 450, 945, 'ftnnaz02', 'wan01', 'H013'),
('R027', '2024-06-01', '2024-06-03', '2024-05-27', 'Pending', 56.7, 567, 1190.7, 'shah', 'wan01', 'H014'),
('R028', '2024-06-04', '2024-06-07', '2024-05-28', 'Accepted', 56.7, 567, 1757.7, 'nralnfrh', 'wan01', 'H014'),
('R029', '2024-06-08', '2024-06-10', '2024-05-29', 'Rejected', 56.7, 567, 1190.7, 'wsirysha', 'wan01', 'H014'),
('R030', '2024-06-05', '2024-06-08', '2024-06-01', 'Pending', 70, 700, 2170, 'ftnnaz02', 'wan01', 'H015'),
('R031', '2024-06-10', '2024-06-15', '2024-06-03', 'Accepted', 70, 700, 3570, 'shah', 'wan01', 'H015'),
('R032', '2024-06-20', '2024-06-23', '2024-06-15', 'Rejected', 70, 700, 2170, 'nralnfrh', 'wan01', 'H015'),
('R033', '2024-06-24', '2024-06-26', '2024-06-16', 'Pending', 70, 700, 1470, 'shah', 'wan01', 'H015'),
('R034', '2024-06-07', '2024-06-09', '2024-06-04', 'Accepted', 23.5, 235, 493.5, 'ftnnaz02', 'wan01', 'H016'),
('R035', '2024-06-10', '2024-06-11', '2024-06-05', 'Pending', 23.5, 235, 258.5, 'wsirysha', 'wan01', 'H016'),
('R036', '2024-06-10', '2024-06-15', '2024-06-05', 'Accepted', 65, 650, 3315, 'ftnnaz02', 'wan01', 'H017'),
('R037', '2024-06-17', '2024-06-19', '2024-06-10', 'Rejected', 65, 650, 1365, 'shah', 'wan01', 'H017'),
('R038', '2024-06-10', '2024-06-12', '2024-06-08', 'Pending', 89, 890, 1869, 'nralnfrh', 'wan01', 'H018'),
('R039', '2024-06-13', '2024-06-15', '2024-06-10', 'Accepted', 89, 890, 1869, 'wsirysha', 'wan01', 'H018'),
('R040', '2024-06-15', '2024-06-17', '2024-06-13', 'Rejected', 65, 650, 1365, 'ftnnaz02', 'shah03', 'H019'),
('R041', '2024-06-19', '2024-06-21', '2024-06-14', 'Pending', 65, 650, 1365, 'shah', 'shah03', 'H019'),
('R042', '2024-06-18', '2024-06-20', '2024-06-16', 'Accepted', 50, 500, 1050, 'nralnfrh', 'shah03', 'H020'),
('R043', '2024-06-22', '2024-06-25', '2024-06-20', 'Rejected', 50, 500, 1550, 'shah', 'shah03', 'H020'),
('R044', '2024-06-26', '2024-06-28', '2024-06-21', 'Pending', 32, 320, 672, 'ftnnaz02', 'shah03', 'H021'),
('R045', '2024-06-23', '2024-06-24', '2024-06-22', 'Accepted', 69.9, 699, 768.9, 'wsirysha', 'shah03', 'H022'),
('R046', '2024-07-01', '2024-07-04', '2024-06-25', 'Rejected', 54, 540, 1674, 'ftnnaz02', 'shah03', 'H023'),
('R047', '2024-07-05', '2024-07-07', '2024-06-26', 'Pending', 54, 540, 1134, 'shah', 'shah03', 'H023'),
('R048', '2024-07-01', '2024-07-04', '2024-06-27', 'Accepted', 66, 660, 2046, 'wsirysha', 'shah03', 'H024'),
('R049', '2024-07-05', '2024-07-07', '2024-06-28', 'Accepted', 66, 660, 1386, 'nralnfrh', 'shah03', 'H024'),
('R050', '2024-07-08', '2024-07-09', '2024-07-04', 'Accepted', 66, 660, 726, 'ftnnaz02', 'shah03', 'H024'),
('R051', '2024-07-10', '2024-07-11', '2024-07-05', 'Accepted', 66, 660, 726, 'shah', 'shah03', 'H024'),
('R052', '2024-07-23', '2024-07-27', '2024-07-11', 'Pending', 40, 400, 1640, 'shah', 'alina03', 'H004'),
('R053', '2024-07-31', '2024-08-03', '2024-07-11', 'Accepted', 53, 530, 1643, 'wsirysha', 'azlini02', 'H008'),
('R054', '2024-08-06', '2024-08-09', '2024-07-11', 'Pending', 27.6, 276, 855.6, 'shah', 'azlini02', 'H012'),
('R055', '2024-07-11', '2024-07-13', '2024-07-11', 'Accepted', 27.6, 276, 579.6, 'ftnnaz02', 'azlini02', 'H012'),
('R056', '2026-04-10', '2026-04-14', '2026-04-10', 'Pending', 98, 980, 4018, 'Az', 'alina03', 'H002');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`agent_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `fk_house` (`house_id`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `house`
--
ALTER TABLE `house`
  ADD PRIMARY KEY (`house_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `house_id` (`house_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`rental_id`),
  ADD CONSTRAINT `fk_house` FOREIGN KEY (`house_id`) REFERENCES `house` (`house_id`);

--
-- Constraints for table `house`
--
ALTER TABLE `house`
  ADD CONSTRAINT `house_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`agent_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`rental_id`);

--
-- Constraints for table `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `rental_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `guest` (`guest_id`),
  ADD CONSTRAINT `rental_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`agent_id`),
  ADD CONSTRAINT `rental_ibfk_3` FOREIGN KEY (`house_id`) REFERENCES `house` (`house_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
