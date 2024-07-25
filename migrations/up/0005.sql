ALTER TABLE `reading`
  ADD COLUMN `pulse` int(10) NOT NULL AFTER `diastolic`;

UPDATE `reading` SET `pulse` = 60;