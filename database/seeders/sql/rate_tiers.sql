-- Inserting into rate_tiers table
INSERT INTO `rate_tiers` (`id`, `tier`, `minimumstay`, `useflatrate`, `flatrate`, `usedynamic`, `dynamicincrease`, `dynamicincreasepercent`, `dynamicdecrease`, `dynamicdecreasepercent`, `lastminuteincrease`, `lastminutedays`, `earlybookingincrease`, `earlybookingdays`, `weeklyrate`, `monthlyrate`, `seasonalrate`, `sundayrate`, `mondayrate`, `tuesdayrate`, `wednesdayrate`, `thursdayrate`, `fridayrate`, `saturdayrate`, `orderby`, `lastmodified`) VALUES
('1', 'WE30A', '1', '1', '57', '1', '10', '0', '2', '0', '0', '0', '0', '0', '300', '500', '0', '57', '57', '57', '55', '57', '63', '63', '0', '2023-12-19 15:49:32'),
('2', 'WSE30A', '1', '1', '62', '1', '10', '0', '2', '0', '0', '0', '0', '0', '300', '500', '0', '62', '62', '62', '58', '59', '65', '65', '0', '2023-12-19 15:49:42'),
('3', 'WE50A', '1', '1', '62', '1', '10', '0', '2', '0', '0', '0', '0', '0', '0', '0', '0', '62', '62', '62', '58', '59', '65', '65', '0', '2023-03-18 09:06:15'),
('4', 'WSE50A', '1', '1', '67', '1', '10', '0', '2', '0', '0', '0', '0', '0', '0', '0', '0', '62', '62', '62', '58', '59', '65', '65', '0', '2023-03-18 09:09:32'),
('5', 'CABIN', '2', '1', '150', '0', '10', '0', '8', '0.1', '10', '30', '15', '100', '0', '500', '0', '150', '150', '150', '150', '150', '150', '150', '0', '2023-12-19 15:49:20'),
('6', 'BOAT', '1', '1', '15', '1', '10', '0', '2', '0', '0', '0', '0', '0', '0', '0', '0', '10', '20', '20', '15', '20', '30', '30', '0', '2023-03-12 12:39:12'),
('7', 'JETSKI', '1', '1', '15', '1', '10', '0', '2', '0', '0', '0', '0', '0', '0', '0', '0', '10', '20', '20', '15', '20', '30', '30', '0', '2023-03-18 09:08:31'),
('8', 'RETRO', '1', '1', '100', '1', '10', '0', '2', '0', '0', '0', '0', '0', '500', '1500', '0', '105', '95', '95', '90', '90', '105', '110', '0', '2023-03-18 09:11:46'),
('9', 'NOHU', '1', '1', '50', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2023-03-18 09:25:42'),
('11', 'CABIN2', '2', '1', '185', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1125', '0', '0', '185', '175', '175', '175', '185', '185', '185', '6', '2023-11-06 16:45:58');

