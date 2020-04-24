-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2014 at 11:17 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `millways`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL DEFAULT 'admin',
  `lname` varchar(50) NOT NULL DEFAULT 'admin',
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `millID` int(11) NOT NULL,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`staff_id`, `fname`, `lname`, `username`, `password`, `role`, `millID`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client`
--

CREATE TABLE IF NOT EXISTS `tbl_client` (
  `client_ID` varchar(50) NOT NULL,
  `millID` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `address1` varchar(100) NOT NULL,
  `address2` varchar(100) NOT NULL,
  `phoneNo` varchar(15) NOT NULL,
  `occupation` varchar(100) NOT NULL DEFAULT 'Farmer',
  `datemodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_ID`),
  KEY `millID` (`millID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_discount`
--

CREATE TABLE IF NOT EXISTS `tbl_discount` (
  `discountID` int(11) NOT NULL AUTO_INCREMENT,
  `itemID` int(11) NOT NULL,
  `rate` int(11) NOT NULL DEFAULT '0',
  `millID` int(11) NOT NULL,
  `st_weigth` int(11) NOT NULL DEFAULT '0',
  `end_weigth` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`discountID`),
  KEY `itemID` (`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item`
--

CREATE TABLE IF NOT EXISTS `tbl_item` (
  `itemID` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(50) NOT NULL,
  `charge` int(11) NOT NULL,
  PRIMARY KEY (`itemID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tbl_item`
--

INSERT INTO `tbl_item` (`itemID`, `itemName`, `charge`) VALUES
(1, 'Maize', 50),
(2, 'Millet', 80);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mill`
--

CREATE TABLE IF NOT EXISTS `tbl_mill` (
  `millID` int(11) NOT NULL AUTO_INCREMENT,
  `millName` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `modifiedby` int(11) NOT NULL,
  PRIMARY KEY (`millID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_mill`
--

INSERT INTO `tbl_mill` (`millID`, `millName`, `district`, `modifiedby`) VALUES
(1, 'Soroti Millers Ltd', 'Soroti', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales`
--

CREATE TABLE IF NOT EXISTS `tbl_sales` (
  `saleID` int(11) NOT NULL AUTO_INCREMENT,
  `clientNo` varchar(50) NOT NULL,
  `discount` int(11) NOT NULL,
  `salesDate` date NOT NULL,
  `millBranch` int(11) NOT NULL,
  `modifiedby` int(11) NOT NULL,
  PRIMARY KEY (`saleID`),
  KEY `clientNo` (`clientNo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sold_item`
--

CREATE TABLE IF NOT EXISTS `tbl_sold_item` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `saleID` int(11) NOT NULL,
  `itemNo` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `itemNo` (`itemNo`),
  KEY `saleID` (`saleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE IF NOT EXISTS `tbl_staff` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `address1` varchar(100) NOT NULL,
  `address2` varchar(100) NOT NULL,
  `nssfno` varchar(50) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `millID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `datemodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_id`),
  KEY `millID` (`millID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`staff_id`, `fname`, `lname`, `address1`, `address2`, `nssfno`, `tin`, `role`, `millID`, `username`, `password`, `datemodified`) VALUES
(1, 'Allan', 'Odeke', 'Kumi', 'Kumi', '00980009ee00w3', '99400rwjjfso99e403', 'Supervisor', 1, 'allanjes', 'allanjes', '2014-08-12 11:14:37');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_client`
--
ALTER TABLE `tbl_client`
  ADD CONSTRAINT `tbl_client_ibfk_1` FOREIGN KEY (`millID`) REFERENCES `tbl_mill` (`millID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_discount`
--
ALTER TABLE `tbl_discount`
  ADD CONSTRAINT `tbl_discount_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `tbl_item` (`itemID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sales`
--
ALTER TABLE `tbl_sales`
  ADD CONSTRAINT `tbl_sales_ibfk_1` FOREIGN KEY (`clientNo`) REFERENCES `tbl_client` (`client_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_sold_item`
--
ALTER TABLE `tbl_sold_item`
  ADD CONSTRAINT `tbl_sold_item_ibfk_1` FOREIGN KEY (`saleID`) REFERENCES `tbl_sales` (`saleID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_sold_item_ibfk_2` FOREIGN KEY (`itemNo`) REFERENCES `tbl_item` (`itemID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD CONSTRAINT `tbl_staff_ibfk_1` FOREIGN KEY (`millID`) REFERENCES `tbl_mill` (`millID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
