-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: rocket_launcher_db
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `airdefense_templates`
--

DROP TABLE IF EXISTS `airdefense_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airdefense_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `num_rockets` int NOT NULL,
  `reaction_time` decimal(5,2) NOT NULL,
  `interception_range` int NOT NULL,
  `detection_range` int NOT NULL,
  `accuracy` decimal(5,2) NOT NULL,
  `reload_time` decimal(5,2) NOT NULL,
  `max_simultaneous_targets` int NOT NULL,
  `description` text,
  `interception_speed` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `airdefense_templates`
--

LOCK TABLES `airdefense_templates` WRITE;
/*!40000 ALTER TABLE `airdefense_templates` DISABLE KEYS */;
INSERT INTO `airdefense_templates` VALUES (1,'S-400 Triumph','S-400','Russia',32,9.00,400000,600000,0.90,5.00,80,'One of the most advanced long-range air defense systems in the world, capable of targeting aircraft, UAVs, cruise and ballistic missiles. Highly flexible and strategic. Одна из самых современных систем ПВО дальнего радиуса действия в мире, способная поражать самолеты, беспилотники, крылатые и баллистические ракеты. Высокая гибкость и стратегическая значимость.',2500),(2,'Patriot PAC-3','MIM-104 Patriot','USA',16,15.00,100000,150000,0.85,3.00,9,'The most well-known American air defense system, focused on missile interception and capable of engaging multiple threats simultaneously. Used by many NATO members. Наиболее известная американская система ПВО, ориентированная на перехват ракет и способная противодействовать множеству угроз одновременно. Используется многими странами НАТО.',1700),(3,'THAAD','THAAD','USA',8,10.00,200000,300000,0.90,6.00,8,'Designed for intercepting short, medium, and intermediate-range ballistic missiles in their terminal phase. Operates at altitudes beyond the atmosphere. Разработана для перехвата баллистических ракет малого, среднего и промежуточного радиуса действия на конечной фазе полета. Работает на высотах за пределами атмосферы.',3000),(4,'Aster 30 SAMP/T','Aster 30','France/Italy',8,15.00,120000,150000,0.85,2.00,10,'A medium-to-long range system with highly agile missiles, designed to protect against a wide range of aerial threats. Operated by European and NATO countries. Система среднего и дальнего радиуса действия с высокоманевренными ракетами, предназначена для защиты от широкого спектра воздушных угроз. Эксплуатируется в странах Европы и НАТО.',1400),(5,'HQ-9','HQ-9A','China',16,10.00,250000,300000,0.85,5.00,72,'China’s most capable long-range system, comparable to the S-300. It provides layered defense against aircraft, cruise missiles, and ballistic missiles. Самая мощная система ПВО Китая дальнего действия, сравнимая с российским С-300. Обеспечивает многослойную защиту от самолетов, крылатых и баллистических ракет.',1900),(6,'David’s Sling','Stunner','Israel',12,5.00,200000,250000,0.88,4.00,12,'Developed by Israel to intercept medium-to-long range rockets and cruise missiles. Works in conjunction with Iron Dome and Arrow systems. Разработана Израилем для перехвата ракет среднего и дальнего радиуса действия, а также крылатых ракет. Работает в сочетании с системами Железный Купол и Стрела.',1200),(7,'Iron Dome','Tamir','Israel',20,2.00,70000,90000,0.95,1.00,6,'Highly effective short-range system designed to intercept rockets, artillery, and mortar shells. Deployed in urban areas and known for its success rate. Высокоэффективная система ближнего действия, предназначенная для перехвата ракет, артиллерийских снарядов и минометных обстрелов. Используется в городских районах, известна высокой степенью успешности.',750),(8,'S-300PMU-2','S-300PMU-2','Russia',16,10.00,200000,300000,0.85,5.00,36,'A predecessor to the S-400, still widely used around the world. Effective for medium-to-long range interception of aircraft, cruise, and ballistic missiles. Предшественник С-400, все еще широко используется по всему миру. Эффективен для перехвата целей среднего и дальнего радиуса действия, включая самолеты, крылатые и баллистические ракеты.',1900),(9,'NASAMS','NASAMS 2','Norway/USA',12,5.00,100000,120000,0.80,2.00,72,'Widely deployed in NATO countries, NASAMS is effective for short-to-medium range defense against UAVs, aircraft, and cruise missiles. Широко используется в странах НАТО, NASAMS эффективна для защиты на коротких и средних дистанциях от беспилотников, самолетов и крылатых ракет.',1000),(10,'Barak 8','Barak 8','Israel/India',8,8.00,150000,200000,0.85,3.00,12,'A joint Israeli-Indian system designed to target aircraft, helicopters, anti-ship missiles, and UAVs. Offers medium-range naval and land-based defense. Совместная израильско-индийская система, предназначенная для уничтожения самолетов, вертолетов, противокорабельных ракет и беспилотников. Обеспечивает среднедистанционную защиту на море и суше.',1900),(11,'S-500 Prometey','S-500','Russia',10,8.00,600000,750000,0.95,4.00,100,'A next-generation Russian air defense system, capable of intercepting hypersonic missiles, satellites, and ICBMs. With extended detection and interception ranges, it is a key system for future air defense. Новейшая российская система ПВО, способная перехватывать гиперзвуковые ракеты, спутники и МБР. Обладает расширенными возможностями обнаружения и перехвата, является ключевой системой для будущей противовоздушной обороны.',4000),(12,'Pantsir-S1','Pantsir-S1','Russia',12,4.00,20000,40000,0.90,5.00,4,'The Pantsir-S1 is a mobile, short-to-medium range air defense system combining anti-aircraft guns and missiles, designed to intercept aircraft, helicopters, drones, and precision-guided munitions. Its highly mobile nature and rapid reaction make it ideal for protecting strategic locations. (Панцирь-С1 - мобильная система ПВО малой и средней дальности, сочетающая зенитные орудия и ракеты, предназначенная для перехвата самолетов, вертолетов, дронов и высокоточных боеприпасов. Высокая мобильность и быстрая реакция делают его идеальным для защиты стратегических объектов).',1300),(13,'S-350 Vityaz','S-350','Russia',12,5.00,150000,600000,0.92,6.00,16,'The S-350 Vityaz is a medium-range air defense system, developed to replace the older S-300. It can engage multiple aerial targets such as aircraft, drones, and cruise missiles simultaneously. It is an agile system with advanced radar and fire control. (С-350 Витязь - система ПВО средней дальности, разработанная для замены старого С-300. Может одновременно атаковать несколько воздушных целей, таких как самолеты, дроны и крылатые ракеты. Это гибкая система с современным радаром и системой управления огнем).',1800),(14,'Tor-M2','Tor-M2','Russia',16,3.00,20000,40000,0.85,7.00,8,'The Tor-M2 is a highly automated short-range air defense system designed for the interception of drones, helicopters, and low-flying aircraft. It is capable of firing while on the move and is noted for its short reaction time. (Тор-М2 - высокоавтоматизированная система ПВО ближнего радиуса действия, предназначенная для перехвата дронов, вертолетов и низколетящих самолетов. Способен стрелять на ходу и отличается коротким временем реакции).',1000),(15,'Buk-M3','Buk-M3','Russia',6,7.00,75000,180000,0.93,8.00,36,'The Buk-M3 is a medium-range air defense system capable of intercepting both aerial targets and short-range ballistic missiles. With advanced radar and missile accuracy, it can simultaneously engage multiple targets. (Бук-М3 - система ПВО средней дальности, способная перехватывать как воздушные цели, так и баллистические ракеты малой дальности. Благодаря усовершенствованной радиолокации и точности ракет, она может одновременно атаковать несколько целей).',1100),(16,'A-235 Nudol','A-235','Russia',12,5.00,1500000,3000000,0.85,60.00,12,'A-235 is an advanced Russian ABM system designed to intercept ICBMs and satellites in low-earth orbit. It has a detection range of 4,000 km and can intercept ballistic threats from 1,500 to 3,500 km. Intended as a replacement for the A-135, it is one of the most sophisticated ABM systems in the world. А-235 является современной российской системой противоракетной обороны, предназначенной для перехвата МБР и спутников на низкой орбите. Обладает дальностью обнаружения до 4000 км.',4500),(17,'HQ-19','HQ-19','China',16,4.00,500000,3000000,0.90,45.00,10,'HQ-19 is China\'s advanced ballistic missile defense system designed to intercept medium- and intermediate-range ballistic missiles. It is comparable to the U.S. THAAD system, with a detection range of up to 4,000 km. HQ-19 является продвинутой системой ПРО Китая, способной перехватывать ракеты средней дальности.',2500),(18,'S-550','S-550','Russia',8,5.00,750000,1500000,0.95,40.00,15,'S-550 is an upgrade of the S-500, designed to intercept strategic missiles, satellites, and hypersonic threats. It is part of Russia\'s layered missile defense with superior capabilities over the S-500. S-550 является усовершенствованием С-500 и предназначен для перехвата стратегических ракет и гиперзвуковых угроз.',4500),(20,'HQ-26','HQ-26','China',10,5.00,300000,3000000,0.90,50.00,12,'HQ-26 is a naval ballistic missile defense system used by China, capable of intercepting medium- and intermediate-range ballistic missiles. Comparable to the U.S. SM-3, it is part of China\'s naval defense strategy. HQ-26 - морская система ПРО Китая, способная перехватывать ракеты средней дальности.',3000),(22,'S-300V4','S-300V4','Russia',8,3.00,400000,500000,0.85,20.00,10,'S-300V4 is the most advanced version of the S-300 series, capable of targeting aircraft, cruise missiles, and ballistic missiles. It features enhanced anti-ballistic capabilities with detection up to 1,000 km. С-300В4 - передовая версия С-300, способная перехватывать баллистические ракеты и самолеты.',2600);
/*!40000 ALTER TABLE `airdefense_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `airdefenses`
--

DROP TABLE IF EXISTS `airdefenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airdefenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `num_rockets` int NOT NULL,
  `reaction_time` decimal(4,2) NOT NULL,
  `interception_range` int NOT NULL,
  `detection_range` int NOT NULL,
  `accuracy` decimal(4,2) NOT NULL,
  `lat` decimal(9,6) NOT NULL,
  `lng` decimal(9,6) NOT NULL,
  `templateID` int DEFAULT NULL,
  `interception_speed` float DEFAULT '0',
  `description` varchar(4500) DEFAULT NULL,
  `total` float DEFAULT '0',
  `success` float DEFAULT '0',
  `failure` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `airdefenses`
--

LOCK TABLES `airdefenses` WRITE;
/*!40000 ALTER TABLE `airdefenses` DISABLE KEYS */;
INSERT INTO `airdefenses` VALUES (6,'S-300PMU-2','S-300PMU-2','Russia',16,10.00,200000,300000,0.85,48.536006,34.130863,8,1900,'A predecessor to the S-400, still widely used around the world. Effective for medium-to-long range interception of aircraft, cruise, and ballistic missiles. Предшественник С-400, все еще широко используется по всему миру. Эффективен для перехвата целей среднего и дальнего радиуса действия, включая самолеты, крылатые и баллистические ракеты.',94,73,21),(7,'S-400 Triumph','S-400','Russia',32,9.00,400000,600000,0.90,52.877971,31.047363,1,2500,'One of the most advanced long-range air defense systems in the world, capable of targeting aircraft, UAVs, cruise and ballistic missiles. Highly flexible and strategic. Одна из самых современных систем ПВО дальнего радиуса действия в мире, способная поражать самолеты, беспилотники, крылатые и баллистические ракеты. Высокая гибкость и стратегическая значимость.',70,50,20),(8,'Patriot PAC-3','MIM-104 Patriot','USA',16,15.00,100000,150000,0.85,48.523881,21.818848,2,1700,'The most well-known American air defense system, focused on missile interception and capable of engaging multiple threats simultaneously. Used by many NATO members. Наиболее известная американская система ПВО, ориентированная на перехват ракет и способная противодействовать множеству угроз одновременно. Используется многими странами НАТО.',134,107,27);
/*!40000 ALTER TABLE `airdefenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `launcher_templates`
--

DROP TABLE IF EXISTS `launcher_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `launcher_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `rocket_name` varchar(255) NOT NULL,
  `mass` float DEFAULT NULL,
  `area` float DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `country` char(100) DEFAULT NULL,
  `range` float DEFAULT NULL,
  `explosive_yield` float DEFAULT NULL,
  `overpressure` float DEFAULT NULL,
  `blast_radius` float DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `launcher_templates`
--

LOCK TABLES `launcher_templates` WRITE;
/*!40000 ALTER TABLE `launcher_templates` DISABLE KEYS */;
INSERT INTO `launcher_templates` VALUES (1,'M270 MLRS','M270','M31 GMLRS',307,0.05,860,'NATO',70000,0.09,3,3.4,'The M270 MLRS is a highly versatile multiple-launch rocket system (MLRS), designed to deliver high volumes of firepower with precision. Equipped with M31 GMLRS rockets, it is effective in long-range precision strikes against fortified targets and large-area bombardments. M270 MLRS – это универсальная система залпового огня (MLRS), способная наносить массированные удары с высокой точностью.'),(2,'HIMARS','M142','M31 GMLRS',307,0.05,860,'NATO',70000,0.09,3,3.4,'The HIMARS is a lighter, more mobile variant of the M270 MLRS, designed for rapid deployment and precision targeting. It uses the same M31 GMLRS rockets, offering highly accurate and devastating firepower in smaller, faster packages. HIMARS – это более мобильная версия M270, предназначенная для быстрого развертывания и точных ударов.'),(3,'ATACMS','M39','ATACMS',1670,0.2,1500,'NATO',300000,0.45,3,5.8,'The ATACMS is a long-range tactical missile system capable of striking targets up to 300 km away. Its M39 ATACMS missile carries a large payload, making it highly effective in neutralizing enemy command centers and air defenses. ATACMS – это тактическая ракетная система большой дальности, способная поражать цели на расстоянии до 300 км.'),(4,'BM-30 Smerch','9A52','9M55K',800,0.1,850,'Russia',90000,0.25,3,4.6,'BM-30 Smerch is a powerful multiple-launch rocket system, designed for long-range bombardment with high-explosive fragmentation warheads like the 9M55K. It is devastating in open areas and against fortifications. BM-30 Смерч – это мощная система залпового огня, предназначенная для дальнобойного обстрела.'),(5,'BM-21 Grad','BM-21','9M22U',66.6,0.02,690,'Russia',40000,0.015,3,2.1,'BM-21 Grad is one of the most widely used MLRS systems, known for its high mobility and ability to cover large areas with 9M22U rockets. It is highly effective in saturation bombardment but lacks precision. BM-21 Град – одна из самых распространенных систем залпового огня.'),(6,'Iskander-M','9K720','9M723',3800,0.35,2100,'Russia',500000,0.7,3,7.2,'Iskander-M is a tactical ballistic missile system with exceptional range and precision. Its 9M723 missile can deliver high-explosive warheads deep into enemy territory, making it highly effective against critical infrastructure. Искандер-М – это тактическая баллистическая ракетная система с исключительной дальностью.'),(7,'M270A1 MLRS','M270A1','M30A1 GMLRS',307,0.05,850,'NATO',70000,0.09,3,3.5,'M270A1 is an upgraded version of the M270 with enhanced targeting and firing capabilities. Using the M30A1 GMLRS rockets, it is highly effective for precision strikes on enemy formations and infrastructure. M270A1 – улучшенная версия M270 с расширенными возможностями.'),(8,'Excalibur 155mm','M982','M982 Excalibur',48,0.01,827,'NATO',40000,0.05,3,2.8,'The M982 Excalibur is a precision-guided artillery shell, designed to hit targets at long range with minimal collateral damage. It is highly effective in neutralizing enemy positions with pinpoint accuracy. M982 Excalibur – это управляемый артиллерийский снаряд.'),(9,'Spike NLOS','Spike-NLOS','Spike NLOS Missile',71,0.015,280,'NATO',25000,0.03,3,2.3,'Spike NLOS is an advanced anti-tank missile with extended range and precision guidance. Its ability to be launched from great distances makes it highly effective in engaging armored targets and fortified positions. Spike NLOS – это современная противотанковая ракета.'),(10,'TOS-1A','9K720','MO.1.01.04M',173,0.03,300,'Russia',6000,0.1,5,5.5,'TOS-1A fires thermobaric rockets like the MO.1.01.04M, designed to create intense overpressure and high-temperature explosions. It is devastating in urban and confined areas. TOS-1A стреляет термобарическими ракетами.'),(11,'BM-27 Uragan','9P140','9M27K',280,0.08,680,'Russia',35000,0.2,3,4,'BM-27 Uragan is a Soviet-era multiple launch rocket system that delivers a powerful punch with 9M27K fragmentation rockets. Its primary use is area bombardment, effective for large-scale damage. BM-27 Ураган – советская система залпового огня.'),(12,'S-400 Triumph','S-400','40N6E',1893,0.35,4800,'Russia',400000,0.08,3,3.6,'S-400 Triumph is one of Russia’s most advanced air-defense systems. Equipped with the 40N6E missile, it can target aircraft, drones, and even ballistic missiles, with a maximum range of 400 km. С-400 Триумф – одна из самых передовых систем ПВО России.'),(13,'PCL191','PCL191','AR3 Guided Rocket',800,0.08,850,'China',370000,0.15,3,4.5,'PCL191 is a Chinese multiple-launch rocket system capable of launching guided rockets over vast distances. It is used for long-range strikes, targeting enemy infrastructure and military bases. PCL191 – китайская система залпового огня.'),(14,'DF-15','DF-15','DF-15B',6200,0.45,2500,'China',600000,0.5,3,7.8,'DF-15 is a Chinese short-range ballistic missile system, known for its precision and effectiveness in targeting enemy bases and critical infrastructure at distances of up to 600 km. DF-15 – это китайская баллистическая ракета малой дальности.'),(15,'DF-21D','DF-21','DF-21D',14700,0.65,2800,'China',1450000,1,3,10.5,'DF-21D is a Chinese anti-ship ballistic missile designed to hit high-value naval targets such as aircraft carriers. It boasts a long range and significant destructive capability. DF-21D – это китайская баллистическая противокорабельная ракета.'),(16,'WS-2','WS-2','WS-2 Rocket',1200,0.12,1100,'China',200000,0.25,3,5.1,'WS-2 is a Chinese long-range multiple rocket launcher that can launch guided rockets to strike targets up to 200 km away. It is particularly effective in saturation bombardment. WS-2 – китайская система залпового огня большой дальности.'),(17,'HQ-9','HQ-9A','HQ-9 Missile',1300,0.25,4200,'China',200000,0.08,3,3.2,'HQ-9A is a Chinese air-defense system modeled after the Russian S-300. It can target a variety of aerial threats, including aircraft and drones, with a maximum range of 200 km. HQ-9A – китайская система ПВО, основанная на С-300.'),(18,'CJ-10','CJ-10A','CJ-10 Cruise Missile',2200,0.3,800,'China',2000000,0.5,3,6.2,'CJ-10 is a Chinese cruise missile with a range of up to 2,000 km. It is primarily used for precision strikes on enemy infrastructure and military installations. CJ-10 – китайская крылатая ракета с дальностью до 2000 км.'),(19,'RS-24 Yars','RS-24','RS-24 ICBM',49000,1.5,7000,'Russia',11000000,800000,5,30000,'RS-24 Yars is a Russian intercontinental ballistic missile capable of carrying multiple nuclear warheads. It is designed to evade missile defense systems and deliver devastating blows over long distances. RS-24 Ярс – это российская межконтинентальная баллистическая ракета.'),(20,'Trident II D5','UGM-133A','Trident II D5',58500,2.5,7360,'NATO',12000000,475000,5,25000,'Trident II D5 is a submarine-launched ballistic missile used by NATO forces. It has an extremely long range and is capable of delivering multiple nuclear warheads. Трайдент II D5 – это баллистическая ракета, запускаемая с подводных лодок НАТО.'),(21,'Dongfeng-41 (DF-41)','DF-41','DF-41 ICBM',80000,1.8,8000,'China',13000000,1000000,5,35000,'DF-41 is a Chinese intercontinental ballistic missile with a range of over 13,000 km. It is designed to carry multiple nuclear warheads and evade missile defense systems. DF-41 – китайская межконтинентальная баллистическая ракета.'),(22,'R-36M2 Voevoda (SS-18 Satan)','R-36M2','SS-18 ICBM',211000,3.5,7900,'Russia',16000000,20000000,5,50000,'R-36M2 Voevoda (SS-18 Satan) is one of the heaviest intercontinental ballistic missiles ever built, capable of carrying a large nuclear payload over vast distances. Its destructive power is unmatched. Р-36М2 Воевода – одна из самых тяжелых межконтинентальных баллистических ракет.'),(23,'LGM-30 Minuteman III','Minuteman III','LGM-30 ICBM',35000,1.3,7250,'NATO',13000000,350000,5,22000,'LGM-30 Minuteman III is a U.S. intercontinental ballistic missile capable of delivering multiple nuclear warheads. It has been a key component of U.S. nuclear deterrence for decades. LGM-30 Minuteman III – это межконтинентальная баллистическая ракета США.'),(24,'Pukguksong-2','KN-15','Pukguksong-2 MRBM',33000,1.2,3000,'North Korea',1000000,150000,5,18000,'Pukguksong-2 is a North Korean medium-range ballistic missile designed for nuclear strikes. It is highly mobile and can be launched from both land and sea. Пуккуксон-2 – это северокорейская баллистическая ракета средней дальности.'),(29,'FAB-500','FAB-500 M-54','FAB-500',500,0.45,250,'Russia',40000,0.5,5,116.43,'FAB-500 is a Russian general-purpose high-explosive bomb. It is highly effective against fortifications, vehicles, and large concentrations of troops, with a blast radius of over 100 meters. ФАБ-500 – это российская фугасная бомба, эффективная против укреплений.'),(30,'FAB-1500','FAB-1500 M-46','FAB-1500',1500,0.6,200,'Russia',40000,1.5,5,170.42,'FAB-1500 is a large Russian bomb designed to target heavily fortified positions and infrastructure. Its massive blast radius makes it highly effective in destroying bunkers and large buildings. ФАБ-1500 – крупная российская бомба для уничтожения укреплений.'),(31,'FAB-3000','FAB-3000 M-54','FAB-3000',3000,0.8,180,'Russia',40000,3,5,214.89,'FAB-3000 is an extremely heavy bomb used by Russian forces for large-scale destruction of enemy infrastructure. Its blast radius of over 200 meters ensures significant damage. ФАБ-3000 – это очень тяжелая бомба для разрушения инфраструктуры.'),(32,'FAB-9000','FAB-9000 M-54','FAB-9000',9000,1.2,150,'Russia',40000,9,5,294.57,'FAB-9000 is one of the heaviest non-nuclear bombs in the Russian arsenal. Its enormous explosive yield makes it capable of obliterating large structures and military installations. ФАБ-9000 – одна из самых тяжелых неядерных бомб России.'),(33,'BM-30 Smerch','9K58','9M55K',800,0.1,1150,'Russia',90000,0.8,4,100,'The Smerch 9A52 is a modernized version of the original 9K58 Smerch system, offering greater mobility, automated reload features, and increased targeting precision. The 9A52 also introduces more advanced guided rockets, making it more suitable for precision strikes on enemy positions at long ranges. Its enhanced automation and integration into modern command systems allow for faster deployment and greater battlefield efficiency. Смерч 9A52 – модернизированная версия системы 9К58, обеспечивающая лучшую мобильность и точность ударов.'),(34,'9A52-4 Tornado','Tornado-G','9M534',800,0.12,950,'Russia',70000,0.6,4,85,'9A52-4 Tornado is an advanced multiple-launch rocket system that can fire precision-guided rockets over long distances. It is an upgrade over the BM-30 Smerch and offers greater accuracy. 9A52-4 Торнадо – усовершенствованная система залпового огня.'),(35,'Kh-101','Kh-101','Kh-101',2300,0.2,240,'Russia',4500000,1,5,200,'Kh-101 is a long-range cruise missile designed for precision strikes. It is used to target critical infrastructure and military installations, delivering powerful explosive payloads. Х-101 – крылатая ракета большой дальности для точечных ударов по инфраструктуре.'),(36,'Tornado-S','Tornado-S','9M542',800,0.12,850,'Russia',120000,0.7,4,90,'Tornado-S is a modernized version of the BM-30 Smerch, offering increased range and precision. It can fire guided rockets, making it highly effective for long-range strikes. Торнадо-С – модернизированная версия Смерч, с увеличенной дальностью и точностью.'),(37,'Kalibr','3M14 Kalibr','Kalibr',1400,0.1,900,'Russia',2500000,0.5,5,100,'Kalibr is a long-range sea-launched cruise missile used by the Russian Navy. It is highly accurate and has been used extensively in conflicts for striking high-value targets. Калибр – крылатая ракета большой дальности, используемая ВМФ России.'),(38,'Kinzhal','Kh-47M2','Kinzhal',4300,0.3,4800,'Russia',2000000,0.5,5,150,'Kinzhal is a Russian hypersonic missile launched from aircraft, capable of traveling at extreme speeds and evading missile defense systems. It is designed to strike high-value targets with precision. Кинжал – гиперзвуковая ракета России, способная преодолевать системы ПВО.'),(39,'Avangard','Avangard','Avangard HGV',2000,0.25,7000,'Russia',12000000,2000,20,4500,'Avangard is a Russian hypersonic glide vehicle that can carry nuclear warheads. It is designed to evade missile defenses while traveling at speeds greater than Mach 20. Авангард – гиперзвуковой планирующий блок России, способный нести ядерные боеголовки.'),(40,'Zircon','3M22','Zircon',4500,0.28,3100,'Russia',1000000,0.7,6,180,'Zircon is a sea-launched hypersonic missile capable of striking naval and land targets with extreme speed and precision. It is designed to counter enemy ships and missile defenses. Циркон – гиперзвуковая ракета России для поражения морских и наземных целей.'),(41,'Kh-555','Kh-555','Kh-555',1500,0.25,240,'Russia',2000000,0.4,5,150,'Kh-555 is a long-range air-launched cruise missile, designed for precision strikes on infrastructure and high-value targets. It carries a large explosive payload. Х-555 – крылатая ракета воздушного базирования для точечных ударов по инфраструктуре.'),(42,'S-300','S-300','9M83',1700,0.3,2000,'Russia',150000,0.5,6,100,'S-300, while traditionally used for air defense, has been modified to serve offensive roles by targeting ground installations and infrastructure. Using the 9M83 missile, it can strike ground targets with precision, making it a multi-purpose weapon system in the Russian arsenal. С-300, помимо противовоздушной обороны, эффективно используется для ударов по наземным целям, что делает его универсальной системой.'),(43,'RBK-500U (Winged)','RBK-500U','RBK-500U Glide Bomb',500,0.35,250,'Russia',30000,0.5,5,150,'The RBK-500U winged version is a glide bomb equipped with wings to extend its range significantly. It carries various submunitions, including anti-tank and anti-personnel bomblets, making it highly effective for long-range precision strikes. Air-dropped, its glide capabilities allow for extended reach. RBK-500U с крыльями – это планирующая бомба с увеличенным радиусом действия, оснащенная различными суббоеприпасами.'),(44,'RBK-500 SPBE-D','RBK-500 SPBE-D','RBK-500 SPBE-D Glide Bomb',500,0.35,250,'Russia',25000,0.5,5,150,'The RBK-500 SPBE-D is a guided, winged cluster bomb that deploys sensor-fused submunitions to target armored vehicles. Its wings allow it to glide over long distances, improving its effectiveness in modern combat scenarios. RBK-500 SPBE-D – это управляемая кассетная бомба с крыльями, оснащенная суббоеприпасами для поражения бронетехники.');
/*!40000 ALTER TABLE `launcher_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `launchers`
--

DROP TABLE IF EXISTS `launchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `launchers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `rocket_name` varchar(100) NOT NULL,
  `mass` float NOT NULL,
  `area` float NOT NULL,
  `speed` float NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `range` float NOT NULL,
  `explosive_yield` float DEFAULT NULL,
  `overpressure` float DEFAULT NULL,
  `blast_radius` float DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `launchers`
--

LOCK TABLES `launchers` WRITE;
/*!40000 ALTER TABLE `launchers` DISABLE KEYS */;
INSERT INTO `launchers` VALUES (1,'Iskander-M','9K720','9M723',3800,0.35,2100,50.7735,25.5322,500000,0.7,3,7.2,'Iskander-M is a tactical ballistic missile system with exceptional range and precision. Its 9M723 missile can deliver high-explosive warheads deep into enemy territory, making it highly effective against critical infrastructure. Искандер-М – это тактическая баллистическая ракетная система с исключительной дальностью.'),(2,'HIMARS','M142','M31 GMLRS',307,0.05,860,47.3214,28.5718,70000,0.09,3,3.4,'The HIMARS is a lighter, more mobile variant of the M270 MLRS, designed for rapid deployment and precision targeting. It uses the same M31 GMLRS rockets, offering highly accurate and devastating firepower in smaller, faster packages. HIMARS – это более мобильная версия M270, предназначенная для быстрого развертывания и точных ударов.');
/*!40000 ALTER TABLE `launchers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-21 12:09:55
