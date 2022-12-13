/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100406
 Source Host           : localhost:3306
 Source Schema         : dbpos

 Target Server Type    : MySQL
 Target Server Version : 100406
 File Encoding         : 65001

 Date: 16/11/2020 17:31:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for articlemotif
-- ----------------------------
DROP TABLE IF EXISTS `articlemotif`;
CREATE TABLE `articlemotif`  (
  `ArticleCode` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ArticleName` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ArticleCode`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of articlemotif
-- ----------------------------
INSERT INTO `articlemotif` VALUES ('2001', 'AMALFI HIJAU', 1);
INSERT INTO `articlemotif` VALUES ('2002', 'ANIMAL FACE', 1);
INSERT INTO `articlemotif` VALUES ('2003', 'BABY PANDA', 1);
INSERT INTO `articlemotif` VALUES ('2004', 'BANANA LEAVES NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2005', 'BATMAN BOOM', 1);
INSERT INTO `articlemotif` VALUES ('2006', 'BEAR CARTOON', 1);
INSERT INTO `articlemotif` VALUES ('2007', 'BEAR FACE', 1);
INSERT INTO `articlemotif` VALUES ('2008', 'BOLA BASKET', 1);
INSERT INTO `articlemotif` VALUES ('2009', 'BULAN BINTANG HIJAU', 1);
INSERT INTO `articlemotif` VALUES ('2010', 'BULAN SABIT', 1);
INSERT INTO `articlemotif` VALUES ('2011', 'CARS CHAMPION', 1);
INSERT INTO `articlemotif` VALUES ('2012', 'DAUN DANAR BIRU', 1);
INSERT INTO `articlemotif` VALUES ('2013', 'DAUN DANAR HITAM', 1);
INSERT INTO `articlemotif` VALUES ('2014', 'DAUN DANAR MAROON', 1);
INSERT INTO `articlemotif` VALUES ('2015', 'DAUN SHERRY HIJAU', 1);
INSERT INTO `articlemotif` VALUES ('2016', 'DORAEMON CATUR', 1);
INSERT INTO `articlemotif` VALUES ('2017', 'DORAEMON EARTH', 1);
INSERT INTO `articlemotif` VALUES ('2018', 'DORAEMON WHITE', 1);
INSERT INTO `articlemotif` VALUES ('2019', 'DUO JASS ORANYE', 1);
INSERT INTO `articlemotif` VALUES ('2020', 'ELMO FACE', 1);
INSERT INTO `articlemotif` VALUES ('2021', 'FLAMINGGO SUMMER', 1);
INSERT INTO `articlemotif` VALUES ('2022', 'FLAMINGGO TOSCA', 1);
INSERT INTO `articlemotif` VALUES ('2023', 'FLAMINGGO TROPICAL', 1);
INSERT INTO `articlemotif` VALUES ('2024', 'HELIKOPTER', 1);
INSERT INTO `articlemotif` VALUES ('2025', 'HELLO KITTY', 1);
INSERT INTO `articlemotif` VALUES ('2026', 'KANAYA HITAM', 1);
INSERT INTO `articlemotif` VALUES ('2027', 'KEITARO ABU', 1);
INSERT INTO `articlemotif` VALUES ('2028', 'KEPALA BEAR NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2029', 'KITTY', 1);
INSERT INTO `articlemotif` VALUES ('2030', 'KOTAK MERAH', 1);
INSERT INTO `articlemotif` VALUES ('2031', 'LEAF', 1);
INSERT INTO `articlemotif` VALUES ('2032', 'LEOPARD', 1);
INSERT INTO `articlemotif` VALUES ('2033', 'MICKEY FLASH', 1);
INSERT INTO `articlemotif` VALUES ('2034', 'MICKEY MOUSE', 1);
INSERT INTO `articlemotif` VALUES ('2035', 'MINION', 1);
INSERT INTO `articlemotif` VALUES ('2036', 'MONSTERA NEW HITAM', 1);
INSERT INTO `articlemotif` VALUES ('2037', 'OWL', 1);
INSERT INTO `articlemotif` VALUES ('2038', 'OZAKA NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2039', 'OZAKA PUTIH', 1);
INSERT INTO `articlemotif` VALUES ('2040', 'PALMA HIJAU', 1);
INSERT INTO `articlemotif` VALUES ('2041', 'PALMA PUTIH', 1);
INSERT INTO `articlemotif` VALUES ('2042', 'PANDA', 1);
INSERT INTO `articlemotif` VALUES ('2043', 'PAULINI PUTIH', 1);
INSERT INTO `articlemotif` VALUES ('2044', 'PIKACHU', 1);
INSERT INTO `articlemotif` VALUES ('2045', 'POOH AND FRIENDS', 1);
INSERT INTO `articlemotif` VALUES ('2046', 'POOH NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2047', 'POWER PUFF GIRL', 1);
INSERT INTO `articlemotif` VALUES ('2048', 'RABBIT MINI', 1);
INSERT INTO `articlemotif` VALUES ('2049', 'ROCKET CRAYON ABU', 1);
INSERT INTO `articlemotif` VALUES ('2050', 'ROCKET CRAYON HITAM', 1);
INSERT INTO `articlemotif` VALUES ('2051', 'ROCKET CRAYON NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2052', 'ROSE STRIPE', 1);
INSERT INTO `articlemotif` VALUES ('2053', 'SHEEP', 1);
INSERT INTO `articlemotif` VALUES ('2054', 'SPONGEBOB', 1);
INSERT INTO `articlemotif` VALUES ('2055', 'SPONGEBOB KUNING', 1);
INSERT INTO `articlemotif` VALUES ('2056', 'STITCH', 1);
INSERT INTO `articlemotif` VALUES ('2057', 'STRIPE HITAM', 1);
INSERT INTO `articlemotif` VALUES ('2058', 'STRIPE LITTLE', 1);
INSERT INTO `articlemotif` VALUES ('2059', 'STRIPE MAROON', 1);
INSERT INTO `articlemotif` VALUES ('2060', 'STRIPE NAVY', 1);
INSERT INTO `articlemotif` VALUES ('2061', 'TRIBAL PINK', 1);
INSERT INTO `articlemotif` VALUES ('2062', 'TROPICAL PALM BIRU', 1);
INSERT INTO `articlemotif` VALUES ('2063', 'TROPICAL PALM PUTIH', 1);

SET FOREIGN_KEY_CHECKS = 1;
