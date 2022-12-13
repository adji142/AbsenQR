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

 Date: 16/11/2020 17:33:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for articlewarna
-- ----------------------------
DROP TABLE IF EXISTS `articlewarna`;
CREATE TABLE `articlewarna`  (
  `ArticleCode` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ArticleName` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ArticleCode`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of articlewarna
-- ----------------------------
INSERT INTO `articlewarna` VALUES ('1001', 'ABU', 1);
INSERT INTO `articlewarna` VALUES ('1002', 'ARMY', 1);
INSERT INTO `articlewarna` VALUES ('1003', 'BIRU', 1);
INSERT INTO `articlewarna` VALUES ('1004', 'BLACK', 1);
INSERT INTO `articlewarna` VALUES ('1005', 'BLACK PINK', 1);
INSERT INTO `articlewarna` VALUES ('1006', 'BLUE', 1);
INSERT INTO `articlewarna` VALUES ('1007', 'CREAM', 1);
INSERT INTO `articlewarna` VALUES ('1008', 'HIJAU', 1);
INSERT INTO `articlewarna` VALUES ('1009', 'HITAM', 1);
INSERT INTO `articlewarna` VALUES ('1010', 'KUNING', 1);
INSERT INTO `articlewarna` VALUES ('1011', 'MAROON', 1);
INSERT INTO `articlewarna` VALUES ('1012', 'MERAH', 1);
INSERT INTO `articlewarna` VALUES ('1013', 'NAVY', 1);
INSERT INTO `articlewarna` VALUES ('1014', 'ORANYE', 1);
INSERT INTO `articlewarna` VALUES ('1015', 'PINK', 1);
INSERT INTO `articlewarna` VALUES ('1016', 'PUTIH', 1);
INSERT INTO `articlewarna` VALUES ('1017', 'RED', 1);
INSERT INTO `articlewarna` VALUES ('1018', 'TOSCA', 1);
INSERT INTO `articlewarna` VALUES ('1019', 'UNGU', 1);
INSERT INTO `articlewarna` VALUES ('1020', 'WHITE', 1);
INSERT INTO `articlewarna` VALUES ('1021', 'YELLOW', 1);

SET FOREIGN_KEY_CHECKS = 1;
