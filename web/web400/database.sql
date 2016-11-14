SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `flag` (
  `value` varchar(255) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `flag` (`value`, `id`) VALUES
('DCTF{0a074628dbb7588bb5fd60350bc1ad9e}', 1);

CREATE TABLE IF NOT EXISTS `knowledge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=297 ;

INSERT INTO `knowledge` (`id`, `request`, `name`, `value`, `type`, `ip`) VALUES
(293, 'vuln.php', 'html', '{"totallen":774.5,"alpha":546.7,"alphanums":588.5,"nums":41.8,"special":186.1,"ascii":771.9,"nonascii":2.7}', 'RESPONSE', '127.0.0.1'),
(294, 'vuln.php', 'text', '{"totallen":774.5,"alpha":546.7,"alphanums":588.5,"nums":41.8,"special":186.1,"ascii":771.9,"nonascii":2.7}', 'RESPONSE', '127.0.0.1'),
(295, 'vuln.php', 'id', '{"totallen":1,"alpha":0,"alphanums":1,"nums":1,"special":0,"ascii":1,"nonascii":0}', 'GET', '127.0.0.1'),
(296, 'vuln.php', 'phpsessid', '{"totallen":26,"alpha":19,"alphanums":26,"nums":7,"special":0,"ascii":26,"nonascii":0}', 'COOKIE', '127.0.0.1');

CREATE TABLE IF NOT EXISTS `rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

INSERT INTO `rows` (`id`, `content`) VALUES
(1, 'Domestic cats are similar in size to the other members of the genus Felis, typically weighing between 4 and 5 kg (9 and 10 lb).[27] Some breeds, however, such as the Maine Coon, can occasionally exceed 11 kg (24 lb). Conversely, very small cats, less than 2 kg (4 lb), have been reported.[47] The world record for the largest cat is 21 kg (50 lb).[48] The smallest adult cat ever officially recorded weighed around 1 kg (2 lb).[48] Feral cats tend to be lighter as they have more limited access to food than house cats. In the Boston area, the average feral adult male will weigh 4 kg (9 lb) and average feral female 3 kg (7 lb).[49] Cats average about 23–25 cm (9–10 in) in height and 46 cm (18 in) in head/body length (males being larger than females), with tails averaging 30 cm (12 in) in length.[50]'),
(2, 'Cats are familiar and easily kept animals, and their physiology has been particularly well studied; it generally resembles those of other carnivorous mammals, but displays several unusual features probably attributable to cats'' descent from desert-dwelling species.[22] For instance, cats are able to tolerate quite high temperatures: Humans generally start to feel uncomfortable when their skin temperature passes about 38 °C (100 °F), but cats show no discomfort until their skin reaches around 52 °C (126 °F),[53]:46 and can tolerate temperatures of up to 56 °C (133 °F) if they have access to water.[63]'),
(3, 'Cats have excellent night vision and can see at only one-sixth the light level required for human vision.[53]:43 This is partly the result of cat eyes having a tapetum lucidum, which reflects any light that passes through the retina back into the eye, thereby increasing the eye''s sensitivity to dim light.[75] Another adaptation to dim light is the large pupils of cats'' eyes. Unlike some big cats, such as tigers, domestic cats have slit pupils.[76] These slit pupils can focus bright light without chromatic aberration, and are needed since the domestic cat''s pupils are much larger, relative to their eyes, than the pupils of the big cats.[76] At low light levels a cat''s pupils will expand to cover most of the exposed surface of its eyes.[77] However, domestic cats have rather poor color vision and (like most nonprimate mammals) have only two types of cones, optimized for sensitivity to blue and yellowish green; they have limited ability to distinguish between red and green.[78]'),
(4, 'The average lifespan of pet cats has risen in recent years. In the early 1980s it was about seven years,[97]:33[98] rising to 9.4 years in 1995[97]:33 and 12-15 years in 2014.[99] However, cats have been reported as surviving into their 30s,[100] with the oldest known cat, Creme Puff, dying at a verified age of 38.[101]Spaying or neutering increases life expectancy: one study found neutered male cats live twice as long as intact males, while spayed female cats live 62% longer than intact females.[97]:35 Having a cat neutered confers health benefits, because castrated males cannot develop testicular cancer, spayed females cannot develop uterine or ovarian cancer, and both have a reduced risk of mammary cancer.[102]');
