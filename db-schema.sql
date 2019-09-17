# create an example table
CREATE TABLE IF NOT EXISTS `example_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `value` VARCHAR(255) NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `udx_key` (`key`));

# clear any previously added values
TRUNCATE TABLE `example_data`;

# generate some random values to add
INSERT INTO `example_data` (`key`, `value`) VALUES (LEFT(UUID(), 8), LEFT(UUID(), 8));
INSERT INTO `example_data` (`key`, `value`) VALUES (LEFT(UUID(), 8), LEFT(UUID(), 8));
INSERT INTO `example_data` (`key`, `value`) VALUES (LEFT(UUID(), 8), LEFT(UUID(), 8));
INSERT INTO `example_data` (`key`, `value`) VALUES (LEFT(UUID(), 8), LEFT(UUID(), 8));
INSERT INTO `example_data` (`key`, `value`) VALUES (LEFT(UUID(), 8), LEFT(UUID(), 8));
