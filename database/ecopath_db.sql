-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2026 at 01:39 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecopath_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `BusID` varchar(50) NOT NULL,
  `DriverID` varchar(50) DEFAULT NULL,
  `OperatorName` varchar(255) DEFAULT NULL,
  `EmissionRate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bus_seats`
--

CREATE TABLE `bus_seats` (
  `SeatID` varchar(50) NOT NULL,
  `BusID` varchar(50) DEFAULT NULL,
  `SeatNumber` varchar(10) DEFAULT NULL,
  `IsBooked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carbon_log`
--

CREATE TABLE `carbon_log` (
  `LogID` varchar(50) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `PackageID` varchar(50) DEFAULT NULL,
  `CO2Emitted` decimal(10,2) DEFAULT NULL,
  `TreesPlanted` int DEFAULT NULL,
  `LogDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `CityID` varchar(50) NOT NULL,
  `RegionID` varchar(50) DEFAULT NULL,
  `CityName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `DonationID` varchar(50) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `PartnerID` varchar(50) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `DonationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `DriverID` varchar(50) NOT NULL,
  `DriverName` varchar(255) NOT NULL,
  `LicenseCode` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `environmental_partner`
--

CREATE TABLE `environmental_partner` (
  `PartnerID` varchar(50) NOT NULL,
  `OrganizationName` varchar(255) NOT NULL,
  `VerificationStatus` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

CREATE TABLE `hotel` (
  `HotelID` varchar(50) NOT NULL,
  `CityID` varchar(50) DEFAULT NULL,
  `HotelName` varchar(255) NOT NULL,
  `EcoRating` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landmarks`
--

CREATE TABLE `landmarks` (
  `LandmarkID` varchar(50) NOT NULL,
  `CityID` varchar(50) DEFAULT NULL,
  `LandmarkName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `PackageID` varchar(50) NOT NULL,
  `BusID` varchar(50) DEFAULT NULL,
  `PackageName` varchar(255) NOT NULL,
  `BaseTreeCount` int DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_hotel`
--

CREATE TABLE `package_hotel` (
  `PackageID` varchar(50) NOT NULL,
  `HotelID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_landmarks`
--

CREATE TABLE `package_landmarks` (
  `PackageID` varchar(50) NOT NULL,
  `LandmarkID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` varchar(50) NOT NULL,
  `TransactionID` varchar(50) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `PermissionID` varchar(50) NOT NULL,
  `PermissionName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `point_exchange`
--

CREATE TABLE `point_exchange` (
  `ExchangeID` varchar(50) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `PointsUsed` int DEFAULT NULL,
  `DiscountApplied` decimal(10,2) DEFAULT NULL,
  `ExchangeDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `RegionID` varchar(50) NOT NULL,
  `RegionName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward`
--

CREATE TABLE `reward` (
  `RewardID` varchar(50) NOT NULL,
  `TransactionID` varchar(50) DEFAULT NULL,
  `RewardDetails` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `RoleID` varchar(50) NOT NULL,
  `RoleName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `RoleID` varchar(50) NOT NULL,
  `PermissionID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_tier`
--

CREATE TABLE `subscription_tier` (
  `TierID` varchar(50) NOT NULL,
  `TierName` varchar(255) NOT NULL,
  `TreeMultiplier` decimal(5,2) DEFAULT NULL,
  `BasePrice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `TransactionID` varchar(50) NOT NULL,
  `UserID` varchar(50) DEFAULT NULL,
  `PackageID` varchar(50) DEFAULT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `TransactionDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` varchar(50) NOT NULL,
  `RoleID` varchar(50) DEFAULT NULL,
  `TierID` varchar(50) DEFAULT NULL,
  `Username` varchar(255) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `EcoPoints` int DEFAULT '0',
  `RegistrationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`BusID`),
  ADD KEY `DriverID` (`DriverID`);

--
-- Indexes for table `bus_seats`
--
ALTER TABLE `bus_seats`
  ADD PRIMARY KEY (`SeatID`),
  ADD KEY `BusID` (`BusID`);

--
-- Indexes for table `carbon_log`
--
ALTER TABLE `carbon_log`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `PackageID` (`PackageID`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`CityID`),
  ADD KEY `RegionID` (`RegionID`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`DonationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `PartnerID` (`PartnerID`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`DriverID`);

--
-- Indexes for table `environmental_partner`
--
ALTER TABLE `environmental_partner`
  ADD PRIMARY KEY (`PartnerID`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`HotelID`),
  ADD KEY `CityID` (`CityID`);

--
-- Indexes for table `landmarks`
--
ALTER TABLE `landmarks`
  ADD PRIMARY KEY (`LandmarkID`),
  ADD KEY `CityID` (`CityID`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`PackageID`),
  ADD KEY `BusID` (`BusID`);

--
-- Indexes for table `package_hotel`
--
ALTER TABLE `package_hotel`
  ADD PRIMARY KEY (`PackageID`,`HotelID`),
  ADD KEY `HotelID` (`HotelID`);

--
-- Indexes for table `package_landmarks`
--
ALTER TABLE `package_landmarks`
  ADD PRIMARY KEY (`PackageID`,`LandmarkID`),
  ADD KEY `LandmarkID` (`LandmarkID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `TransactionID` (`TransactionID`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`PermissionID`);

--
-- Indexes for table `point_exchange`
--
ALTER TABLE `point_exchange`
  ADD PRIMARY KEY (`ExchangeID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`RegionID`);

--
-- Indexes for table `reward`
--
ALTER TABLE `reward`
  ADD PRIMARY KEY (`RewardID`),
  ADD KEY `TransactionID` (`TransactionID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`RoleID`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`RoleID`,`PermissionID`),
  ADD KEY `PermissionID` (`PermissionID`);

--
-- Indexes for table `subscription_tier`
--
ALTER TABLE `subscription_tier`
  ADD PRIMARY KEY (`TierID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `PackageID` (`PackageID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `RoleID` (`RoleID`),
  ADD KEY `TierID` (`TierID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus`
--
ALTER TABLE `bus`
  ADD CONSTRAINT `bus_ibfk_1` FOREIGN KEY (`DriverID`) REFERENCES `driver` (`DriverID`);

--
-- Constraints for table `bus_seats`
--
ALTER TABLE `bus_seats`
  ADD CONSTRAINT `bus_seats_ibfk_1` FOREIGN KEY (`BusID`) REFERENCES `bus` (`BusID`);

--
-- Constraints for table `carbon_log`
--
ALTER TABLE `carbon_log`
  ADD CONSTRAINT `carbon_log_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `carbon_log_ibfk_2` FOREIGN KEY (`PackageID`) REFERENCES `package` (`PackageID`);

--
-- Constraints for table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `city_ibfk_1` FOREIGN KEY (`RegionID`) REFERENCES `region` (`RegionID`);

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `donation_ibfk_2` FOREIGN KEY (`PartnerID`) REFERENCES `environmental_partner` (`PartnerID`);

--
-- Constraints for table `hotel`
--
ALTER TABLE `hotel`
  ADD CONSTRAINT `hotel_ibfk_1` FOREIGN KEY (`CityID`) REFERENCES `city` (`CityID`);

--
-- Constraints for table `landmarks`
--
ALTER TABLE `landmarks`
  ADD CONSTRAINT `landmarks_ibfk_1` FOREIGN KEY (`CityID`) REFERENCES `city` (`CityID`);

--
-- Constraints for table `package`
--
ALTER TABLE `package`
  ADD CONSTRAINT `package_ibfk_1` FOREIGN KEY (`BusID`) REFERENCES `bus` (`BusID`);

--
-- Constraints for table `package_hotel`
--
ALTER TABLE `package_hotel`
  ADD CONSTRAINT `package_hotel_ibfk_1` FOREIGN KEY (`PackageID`) REFERENCES `package` (`PackageID`),
  ADD CONSTRAINT `package_hotel_ibfk_2` FOREIGN KEY (`HotelID`) REFERENCES `hotel` (`HotelID`);

--
-- Constraints for table `package_landmarks`
--
ALTER TABLE `package_landmarks`
  ADD CONSTRAINT `package_landmarks_ibfk_1` FOREIGN KEY (`PackageID`) REFERENCES `package` (`PackageID`),
  ADD CONSTRAINT `package_landmarks_ibfk_2` FOREIGN KEY (`LandmarkID`) REFERENCES `landmarks` (`LandmarkID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`TransactionID`) REFERENCES `transaction` (`TransactionID`);

--
-- Constraints for table `point_exchange`
--
ALTER TABLE `point_exchange`
  ADD CONSTRAINT `point_exchange_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `reward`
--
ALTER TABLE `reward`
  ADD CONSTRAINT `reward_ibfk_1` FOREIGN KEY (`TransactionID`) REFERENCES `transaction` (`TransactionID`);

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `role` (`RoleID`),
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`PermissionID`) REFERENCES `permission` (`PermissionID`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`PackageID`) REFERENCES `package` (`PackageID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `role` (`RoleID`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`TierID`) REFERENCES `subscription_tier` (`TierID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
