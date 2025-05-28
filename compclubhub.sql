/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET FOREIGN_KEY_CHECKS=0; 

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `compclubhub` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `compclubhub`;

/*------------------------------TABLE STRUCTURE------------------------------*/
/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT, /*unique identifier for user*/
  `firstName` varchar(255) NOT NULL ,
  `lastName` varchar(255) NOT NULL,
  `totalScore` decimal NOT NULL DEFAULT 0, /*totalScore across all clubs joined*/
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `numActiveMemberships` int NOT NULL DEFAULT 0, /*number of memberships to clubs that are in use*/
  `deleted` BOOLEAN NOT NULL DEFAULT false, /*if the user's account has been deleted or not*/
  PRIMARY KEY (`userID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `users` */

LOCK TABLES `users` WRITE;

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `totalScore`, `email`, `username`, `password`, `numActiveMemberships`) VALUES
/* created to solve insert null values for competitor2  - no need to log into this account*/
(0, 'Bye', 'Bye', 0, 'byebyebye@compclubhub.com', 'Bye', 'bye', 0),

(1, 'Mars', 'Rei', 0, 'marsRei@compclubhub.com', 'marsRei', '$2y$10$c9MtfZrp.wMQooQ3MA3mfuUgo0evlFtw7xy1nkhRFf0z5mOh9eeJS', 2),
(2, 'Safica', 'Mougamadou', 0, 'saficaMougamadou@compclubhub.com', 'safMou', '$2y$10$L9hIj/oyBzSzWQZWT6z/TOdlOY.e872vq3MCVdO7Fme186dqyW3ze', 2),
(3, 'Jasmine', 'Kaur', 0, 'jasmineKaur@compclubhub.com', 'jasmine04k', '$2y$10$/Yryu3CArBniS2rUNfBMtu38ajRLOCKocmlWrzo8pKht0HVIJsso2', 1);

UNLOCK TABLES;


/*Table structure for table `memberships` */

DROP TABLE IF EXISTS `memberships`;

CREATE TABLE `memberships` (
`userID` int NOT NULL,
`clubID` int NOT NULL,
/* userID and clubID combined is the primary key to 
uniquely identify memberships*/
`score` decimal NOT NULL DEFAULT 0, /*the user's score for this club*/
`activityStatus` boolean NOT NULL DEFAULT True, /*whether the membership is valid or not*/
`clubRank` int NOT NULL, /*the user's ranking in the club*/
PRIMARY KEY (`userID`, `clubID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/* trigger for automatically assigning rankings to new club members*/
DELIMITER $$

CREATE TRIGGER `assignMembershipRanks`
BEFORE INSERT 
ON `memberships`
FOR EACH ROW
BEGIN
    DECLARE maxRank INT;

    /* assign club ranking */
    SELECT IFNULL(MAX(clubRank), 0) INTO maxRank
    FROM memberships
    WHERE clubID = NEW.clubID;

    SET NEW.clubRank = maxRank + 1;
END$$

DELIMITER ;

/*Data for the table `memberships` */

LOCK TABLES `memberships` WRITE;

insert  into `memberships`(`userID`, `clubID`, `score`, `clubRank`) values 
(1, 1, 2, 1), 
(2, 2, 0, 1), 
(3, 3, 0, 1),
(1, 4, 0, 1),
(2, 1, 1, 3),
(3, 1, 1, 2),
(2, 4, 0, 2);

UNLOCK TABLES;


/*Table structure for table `clubs` */

DROP TABLE IF EXISTS `clubs`;

CREATE TABLE `clubs` (
`clubID` int NOT NULL AUTO_INCREMENT, /*uniquely identifies each club*/
`clubName` varchar(255) NOT NULL,
`clubEmail` varchar(255) NOT NULL,
`category` varchar(255) NOT NULL, /*groups clubs within the same topic area*/
`description` varchar(255) NOT NULL,
`activityStatus` Boolean NOT NULL DEFAULT True, /*specifies if club is active or not*/
`address` varchar(255) NOT NULL,
PRIMARY KEY (`clubID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `clubs` */

LOCK TABLES `clubs` WRITE;

insert  into `clubs`(`clubName`, `clubEmail`, `category`, `description`, `address`) values 
('academic weaponry', 'academics@compclubhub.com', 'academic', 'for those who want to become an academic weapon, not an acadmic victim', 'academic centre of success @bcu'), 
('badminton', 'badminton@compclubhub.com', 'badminton', 'for only the best badminton players', 'badminton @bcu'), 
('chess and chit chat', 'chess@compclubhub.com', 'chess', 'become a chess master and be the very best that noone ever was', 'chess @bcu'),
('academic victimary', 'victims@compclubhub.com', 'academic', 'for those who failed to be an academic weapon', 'academic centre of failure @bcu');

UNLOCK TABLES;


/*Table structure for table `admins` */

DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
`adminID` int NOT NULL AUTO_INCREMENT, /*uniquely identifies admins*/
`club` int NOT NULL, /*references clubID in clubs*/
`userID` int NOT NULL, /*references userID in users*/
`username` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
PRIMARY KEY (`adminID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `admins` */

LOCK TABLES `admins` WRITE;

INSERT INTO `admins` (`club`, `userID`, `username`, `password`) VALUES
(1, 1, 'marsAcademicWeapon', '$2y$10$EWMcqyicDT1coJXeYtDdKe/W2lq5rQu9q0veBtfwEVxRjS9r9.LWu'),
(4, 1, 'marsFailure', '$2y$10$nBllRHJOVG4g6bSQDmysWuNKO3yY2bBfv3Wn5/Hi1dygRp2Z/NNYm'),
(2, 2, 'saficaBadmintonSmasher', '$2y$10$2DDIa11H33mxZ/X.LhE0d.yz2GktlKH6pebvd4WAwSpv7sGcQAO.e'),
(3, 3, 'jasmineGM', '$2y$10$GhCMKaIRbAwGEyqtl4j.PuXU7xtoADtbskvpn95vjJPFEvQ0c0ru6');

UNLOCK TABLES;


/*Table structure for table `competitions` */

DROP TABLE IF EXISTS `competitions`;

CREATE TABLE `competitions` (
`competitionID` int NOT NULL AUTO_INCREMENT, /*uniquely identifies each competition*/
`club` int NOT NULL, /*references clubID in clubs*/
`name` varchar(255) NOT NULL,
`numParticipants` int NOT NULL DEFAULT 0,
`1stPlace` int, /*references userID in users*/
`2ndPlace` int, /*references userID in users*/
`3rdPlace` int, /*references userID in users*/
`numRounds` int NOT NULL,
`start` date NOT NULL, /*start date of competition*/
`end` date NOT NULL, /*end date of competition*/
`finished` BOOLEAN NOT NULL DEFAULT False, /*whether the competition is finished or not*/
PRIMARY KEY (`competitionID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `competitions` */

LOCK TABLES `competitions` WRITE;

INSERT INTO `competitions` (`competitionID`, `club`, `name`, `numParticipants`, `1stPlace`, 
`2ndPlace`, `3rdPlace`, `numRounds`, `start`, `end`, `finished`) 
VALUES (1, 1, 'Who Can Get This Coding Task Working The Quickest?', 3, 1, 3, 2, 2, '2025-01-26', '2025-01-27', true);

UNLOCK TABLES; 


/*Table structure for table `matches` */

DROP TABLE IF EXISTS `matches`;

CREATE TABLE `matches` (
`competition` int NOT NULL, /*references competitionID in competition*/
`competitor1` int NOT NULL, /*references userID in users*/
`competitor2` int NOT NULL, /*references userID in users*/
/*competition and the two competitors uniquely identify each match*/
`schedule` DATETIME NOT NULL, /* the date and time of a match*/
`roundNum` int NOT NULL, /* the round number of a match*/
`result` int NOT NULL DEFAULT 4, /* result at match creation is 0-0, references resultID in results*/
PRIMARY KEY (`competition`, `competitor1`, `competitor2`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `matches` */

LOCK TABLES `matches` WRITE;

INSERT INTO `matches` (`competition`, `competitor1`, `competitor2`, `schedule`, 
`roundNum`, `result`) VALUES 
(1, 1, 2, '2025-01-27 16:00:00', 2, 1),
(1, 2, 0, '2025-01-26 20:00:00', 1, 1),
(1, 3, 0, '2025-01-27 16:00:00', 2, 1),
(1, 3, 1, '2025-01-26 20:00:00', 1, 2);

UNLOCK TABLES; 


/*Table structure for table `results` */

DROP TABLE IF EXISTS `results`;

CREATE TABLE `results` (
`resultID` int NOT NULL AUTO_INCREMENT, /*uniquely identifies each result*/
`result` varchar(7) NOT NULL,
PRIMARY KEY (`resultID`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `results` */

LOCK TABLES `results` WRITE;

insert  into `results`(`result`) values 
('1-0'), /* a win for the first competitor */
('0-1'), /* a win for the second competitor */
('0.5-0.5'), /* a draw for both competitors */
('0-0'); /* disregarded result / default when creating match */

UNLOCK TABLES; 


/*Table structure for table `competitors` */

DROP TABLE IF EXISTS `competitors`;

CREATE TABLE `competitors` (
`userID` int NOT NULL, /*references userID in users*/
`competition` int NOT NULL, /*references competitionID in competition*/
/*userID and competition uniquely identify each competitor*/
`score` decimal NOT NULL DEFAULT 0,
`finalRank` int NULL, /*starts of null but is set at the end of the competition*/
PRIMARY KEY (`userID`, `competition`)
) ENGINE = InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `competitors` */

LOCK TABLES `competitors` WRITE;

INSERT INTO `competitors` (`userID`, `competition`, `score`, `finalRank`) VALUES 
(1, 1, 2, 1),
(2, 1, 1, 3),
(3, 1, 1, 2);

UNLOCK TABLES; 


/*------------------------------CONSTRAINTS------------------------------*/
/* Adding table constraints for table `memberships`*/

LOCK TABLES `memberships` WRITE;

ALTER TABLE `memberships`
ADD CONSTRAINT `memberships_fk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `memberships`
ADD CONSTRAINT `memberships_fk_2` FOREIGN KEY (`clubID`) REFERENCES `clubs` (`clubID`) ON DELETE CASCADE;

UNLOCK TABLES;


/* Adding table constraints for table `admins`*/

LOCK TABLES `admins` WRITE;

ALTER TABLE `admins`
ADD CONSTRAINT `admins_fk_1` FOREIGN KEY (`club`) REFERENCES `clubs` (`clubID`) ON DELETE CASCADE;

ALTER TABLE `admins`
ADD CONSTRAINT `admins_fk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

UNLOCK TABLES;


/* Adding table constraints for table `competitions`*/

LOCK TABLES `competitions` WRITE;

ALTER TABLE `competitions`
ADD CONSTRAINT `competitions_fk_1` FOREIGN KEY (`club`) REFERENCES `clubs` (`clubID`) ON DELETE CASCADE;

ALTER TABLE `competitions`
ADD CONSTRAINT `competitions_fk_2` FOREIGN KEY (`1stPlace`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `competitions`
ADD CONSTRAINT `competitions_fk_3` FOREIGN KEY (`2ndPlace`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `competitions`
ADD CONSTRAINT `competitions_fk_4` FOREIGN KEY (`3rdPlace`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

UNLOCK TABLES;


/* Adding table constraints for table `matches`*/

LOCK TABLES `matches` WRITE;

ALTER TABLE `matches`
ADD CONSTRAINT `matches_fk_1` FOREIGN KEY (`competition`) REFERENCES `competitions` (`competitionID`) ON DELETE CASCADE;

ALTER TABLE `matches`
ADD CONSTRAINT `matches_fk_2` FOREIGN KEY (`competitor1`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `matches`
ADD CONSTRAINT `matches_fk_3` FOREIGN KEY (`competitor2`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `matches`
ADD CONSTRAINT `matches_fk_4` FOREIGN KEY (`result`) REFERENCES `results` (`resultID`) ON DELETE CASCADE;

UNLOCK TABLES;


/* Adding table constraints for table `competitors`*/

LOCK TABLES `competitors` WRITE;

ALTER TABLE `competitors`
ADD CONSTRAINT `competitors_fk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

ALTER TABLE `competitors`
ADD CONSTRAINT `competitors_fk_2` FOREIGN KEY (`competition`) REFERENCES `competitions` (`competitionID`) ON DELETE CASCADE;

UNLOCK TABLES;

