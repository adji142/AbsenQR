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

 Date: 16/11/2020 19:44:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Procedure structure for AIS_Validation
-- ----------------------------
DROP PROCEDURE IF EXISTS `AIS_Validation`;
delimiter ;;
CREATE PROCEDURE `AIS_Validation`(id VARCHAR(55),source INT)
BEGIN
	/*
		SOURCE :
			1 -> Item Master Data
			2 -> Customer
			3 -> Mutasi masuk
			4 -> Mutasi Keluar
			5 -> POS
			6 -> Retur
			7 -> Cash Flow
	*/
	DECLARE errno SMALLINT UNSIGNED DEFAULT 31001;
   SET @errmsg = '';
	 SET @count = 0 ;
	 
	 IF source = 1 AND @count = 0 THEN
		-- VALIDASI ITEM MASTER DATA
			SELECT COUNT(*) INTO @count FROM itemmasterdata WHERE KodeItemLama = id;
			
			IF @count > 0 THEN
				SET @errmsg = CONCAT('Kode Item Sudah di pakai',id,@count);
				SIGNAL SQLSTATE '45000' SET
				MYSQL_ERRNO = errno,
				MESSAGE_TEXT = @errmsg;
		 END IF;
			
	 END IF;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for rpt_bookingstok
-- ----------------------------
DROP PROCEDURE IF EXISTS `rpt_bookingstok`;
delimiter ;;
CREATE PROCEDURE `rpt_bookingstok`(IN `TglAwal` date,IN `TglAkhir` date)
BEGIN
	SELECT 
		b.NoTransaksi,b.TglTransaksi,c.NamaSales,
		SUM(a.Qty) Qty, SUM(COALESCE(d.Relasi,0)) Penyelesaian,
		SUM(a.Qty) - SUM(COALESCE(d.Relasi,0)) OutStanding ,SUM(a.Price) TotalHarga
	FROM bookdetail a
	LEFT JOIN bookheader b on a.NoTransaksi = b.NoTransaksi
	LEFT JOIN tsales c on b.KodeSales = c.KodeSales
	LEFT JOIN (
		SELECT
			x.BaseRef, SUM(x.Qty) Relasi
		FROM penjualandetail x
		GROUP BY x.BaseRef
	) d on b.NoTransaksi = d.BaseRef
	WHERE B.StatusTransaksi = 'O'
	AND b.TglTransaksi BETWEEN TglAwal AND TglAkhir
	GROUP BY B.NoTransaksi;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for rpt_labarugi
-- ----------------------------
DROP PROCEDURE IF EXISTS `rpt_labarugi`;
delimiter ;;
CREATE PROCEDURE `rpt_labarugi`(IN `TglAwal` date,IN `TglAkhir` date)
BEGIN
	SELECT 
	b.TglTransaksi, a.KodeItem,d.KodeItemLama,d.Article,d.Warna,
	d.Hpp, SUM(COALESCE(a.Qty,0)) Qty,SUM(COALESCE(c.QtyRetur,0)) QtyRetur,SUM(COALESCE(a.Qty,0) - COALESCE(c.QtyRetur,0)) QtyTotal,a.Harga,
	SUM(((COALESCE(a.Qty,0) - COALESCE(c.QtyRetur,0)) * a.Harga) - a.Disc) Total,
	SUM(((COALESCE(a.Qty,0) - COALESCE(c.QtyRetur,0)) * d.Hpp)) TotalHPP,
	SUM(((COALESCE(a.Qty,0) - COALESCE(c.QtyRetur,0)) * a.Harga) - a.Disc) - SUM(((COALESCE(a.Qty,0) - COALESCE(c.QtyRetur,0)) * d.Hpp)) 'L/R'
FROM penjualandetail a
LEFT JOIN penjualanheader b on a.NoTransaksi = b.NoTransaksi
LEFT JOIN (
	SELECT 
		a.BaseRef, 
		b.KodeItemBaru,
		SUM(b.QtyRetur) QtyRetur
	FROM returheader a
	LEFT JOIN returdetail b on a.NoTransaksi = b.NoTransaksi
	WHERE a.JenisTransaksi = 2
	GROUP BY a.BaseRef,b.KodeItemBaru
) c on c.BaseRef = a.NoTransaksi AND c.KodeItemBaru = a.KodeItem
LEFT JOIN vw_stok d on a.KodeItem = d.ItemCode
WHERE b.TglTransaksi BETWEEN TglAwal AND Tglakhir
GROUP BY b.TglTransaksi,d.KodeItemLama;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for rpt_mutasi
-- ----------------------------
DROP PROCEDURE IF EXISTS `rpt_mutasi`;
delimiter ;;
CREATE PROCEDURE `rpt_mutasi`(IN `TglAwal` date,IN `TglAkhir` date,IN `Mutasi` integer)
BEGIN
	SELECT 
		a.KodeItem,c.KodeItemLama,c.Article,c.Warna,SUM(Qty) Qty,a.Price
	FROM detailmutasi a
	LEFT JOIN headermutasi b on a.NoTransaksi = b.NoTransaksi
	LEFT JOIN vw_stok c on a.KodeItem = c.ItemCode
	WHERE b.TglTransaksi BETWEEN TglAwal AND TglAkhir
	AND b.Mutasi = Mutasi
	GROUP BY a.KodeItem,c.KodeItemLama;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for rpt_penjualanshopee
-- ----------------------------
DROP PROCEDURE IF EXISTS `rpt_penjualanshopee`;
delimiter ;;
CREATE PROCEDURE `rpt_penjualanshopee`(IN `TglAwal` date,IN `TglAkhir` date)
BEGIN
	SELECT 
		c.NoTransaksi,
		c.TglTransaksi,
		ROW_NUMBER() OVER (ORDER BY TglTransaksi) AS num,
		c.NamaCustomer,
		c.RefNumberTrx,
		c.NamaCustomer NmAkun,
		c.NoResi,
		b.TglTransaksi TglCair,
		c.Qty,
		b.NominalCair,
		SUM(a.Debet) Debet,
		SUM(a.Debet) - b.NominalCair as selisih,
		CASE WHEN SUM(a.Debet) - b.NominalCair > 0 THEN 'Selisih Pencairan Shopee' else '' END Keterangan
	FROM cashflow a
	LEFT JOIN pencairanecomerce b on a.NoTransaksi = b.BaseRef
	LEFT JOIN (
		SELECT 
			b.NoTransaksi,b.TglTransaksi,b.RefNumberTrx,b.RefNumberPayment,b.NoResi,
			c.NamaCustomer,b.T_GrandTotal,b.TransactionType,SUM(a.Qty) Qty
		FROM penjualandetail a
		LEFT JOIN penjualanheader b on a.NoTransaksi = b.NoTransaksi
		LEFT JOIN tcustomer c on b.KodeCustomer = c.KodeCustomer
		GROUP BY b.NoTransaksi
	) c on a.BaseRef = c.NoTransaksi
	WHERE c.TransactionType = 1 AND c.TglTransaksi BETWEEN TglAwal AND TglAkhir
	GROUP BY c.NoTransaksi ORDER BY c.TglTransaksi;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for rpt_stok
-- ----------------------------
DROP PROCEDURE IF EXISTS `rpt_stok`;
delimiter ;;
CREATE PROCEDURE `rpt_stok`(IN `TglAwal` date,IN `TglAkhir` date)
BEGIN
	SELECT 
	KodeItem,
	KodeItemLama,
	Article,
	Warna,
	SUM(COALESCE(SaldoAwal,0)) SaldoAwal,
	SUM(COALESCE(IN1,0)) IN1,
	SUM(COALESCE(OUT1,0)) OUT1,
	SUM(COALESCE(BOO1,0)) BOO1,
	SUM(COALESCE(PJL1,0)) PJL1,
	SUM(COALESCE(RET1,0)) RET1,
	SUM(COALESCE(EXC1,0)) EXC1,
	SUM(COALESCE(IN2,0)) IN2,
	SUM(COALESCE(OUT2,0)) OUT2,
	SUM(COALESCE(BOO2,0)) BOO2,
	SUM(COALESCE(PJL2,0)) PJL2,
	SUM(COALESCE(RET2,0)) RET2,
	SUM(COALESCE(EXC2,0)) EXC2,
	SUM(COALESCE(IN3,0)) IN3,
	SUM(COALESCE(OUT3,0)) OUT3,
	SUM(COALESCE(BOO3,0)) BOO3,
	SUM(COALESCE(PJL3,0)) PJL3,
	SUM(COALESCE(RET3,0)) RET3,
	SUM(COALESCE(EXC3,0)) EXC3,
	SUM(COALESCE(IN4,0)) IN4,
	SUM(COALESCE(OUT4,0)) OUT4,
	SUM(COALESCE(BOO4,0)) BOO4,
	SUM(COALESCE(PJL4,0)) PJL4,
	SUM(COALESCE(RET4,0)) RET4,
	SUM(COALESCE(EXC4,0)) EXC4,
	SUM(COALESCE(IN5,0)) IN5,
	SUM(COALESCE(OUT5,0)) OUT5,
	SUM(COALESCE(BOO5,0)) BOO5,
	SUM(COALESCE(PJL5,0)) PJL5,
	SUM(COALESCE(RET5,0)) RET5,
	SUM(COALESCE(EXC5,0)) EXC5,
	SUM(COALESCE(IN6,0)) IN6,
	SUM(COALESCE(OUT6,0)) OUT6,
	SUM(COALESCE(BOO6,0)) BOO6,
	SUM(COALESCE(PJL6,0)) PJL6,
	SUM(COALESCE(RET6,0)) RET6,
	SUM(COALESCE(EXC6,0)) EXC6,
	SUM(COALESCE(IN7,0)) IN7,
	SUM(COALESCE(OUT7,0)) OUT7,
	SUM(COALESCE(BOO7,0)) BOO7,
	SUM(COALESCE(PJL7,0)) PJL7,
	SUM(COALESCE(RET7,0)) RET7,
	SUM(COALESCE(EXC7,0)) EXC7,
	SUM(COALESCE(IN8,0)) IN8,
	SUM(COALESCE(OUT8,0)) OUT8,
	SUM(COALESCE(BOO8,0)) BOO8,
	SUM(COALESCE(PJL8,0)) PJL8,
	SUM(COALESCE(RET8,0)) RET8,
	SUM(COALESCE(EXC8,0)) EXC8,
	SUM(COALESCE(IN9,0)) IN9,
	SUM(COALESCE(OUT9,0)) OUT9,
	SUM(COALESCE(BOO9,0)) BOO9,
	SUM(COALESCE(PJL9,0)) PJL9,
	SUM(COALESCE(RET9,0)) RET9,
	SUM(COALESCE(EXC9,0)) EXC9,
	SUM(COALESCE(IN10,0)) IN10,
	SUM(COALESCE(OUT10,0)) OUT10,
	SUM(COALESCE(BOO10,0)) BOO10,
	SUM(COALESCE(PJL10,0)) PJL10,
	SUM(COALESCE(RET10,0)) RET10,
	SUM(COALESCE(EXC10,0)) EXC10,
	SUM(COALESCE(IN11,0)) IN11,
	SUM(COALESCE(OUT11,0)) OUT11,
	SUM(COALESCE(BOO11,0)) BOO11,
	SUM(COALESCE(PJL11,0)) PJL11,
	SUM(COALESCE(RET11,0)) RET11,
	SUM(COALESCE(EXC11,0)) EXC11,
	SUM(COALESCE(IN12,0)) IN12,
	SUM(COALESCE(OUT12,0)) OUT12,
	SUM(COALESCE(BOO12,0)) BOO12,
	SUM(COALESCE(PJL12,0)) PJL12,
	SUM(COALESCE(RET12,0)) RET12,
	SUM(COALESCE(EXC12,0)) EXC12,
	SUM(COALESCE(IN13,0)) IN13,
	SUM(COALESCE(OUT13,0)) OUT13,
	SUM(COALESCE(BOO13,0)) BOO13,
	SUM(COALESCE(PJL13,0)) PJL13,
	SUM(COALESCE(RET13,0)) RET13,
	SUM(COALESCE(EXC13,0)) EXC13,
	SUM(COALESCE(IN14,0)) IN14,
	SUM(COALESCE(OUT14,0)) OUT14,
	SUM(COALESCE(BOO14,0)) BOO14,
	SUM(COALESCE(PJL14,0)) PJL14,
	SUM(COALESCE(RET14,0)) RET14,
	SUM(COALESCE(EXC14,0)) EXC14,
	SUM(COALESCE(IN15,0)) IN15,
	SUM(COALESCE(OUT15,0)) OUT15,
	SUM(COALESCE(BOO15,0)) BOO15,
	SUM(COALESCE(PJL15,0)) PJL15,
	SUM(COALESCE(RET15,0)) RET15,
	SUM(COALESCE(EXC15,0)) EXC15,
	SUM(COALESCE(IN16,0)) IN16,
	SUM(COALESCE(OUT16,0)) OUT16,
	SUM(COALESCE(BOO16,0)) BOO16,
	SUM(COALESCE(PJL16,0)) PJL16,
	SUM(COALESCE(RET16,0)) RET16,
	SUM(COALESCE(EXC16,0)) EXC16,
	SUM(COALESCE(IN17,0)) IN17,
	SUM(COALESCE(OUT17,0)) OUT17,
	SUM(COALESCE(BOO17,0)) BOO17,
	SUM(COALESCE(PJL17,0)) PJL17,
	SUM(COALESCE(RET17,0)) RET17,
	SUM(COALESCE(EXC17,0)) EXC17,
	SUM(COALESCE(IN18,0)) IN18,
	SUM(COALESCE(OUT18,0)) OUT18,
	SUM(COALESCE(BOO18,0)) BOO18,
	SUM(COALESCE(PJL18,0)) PJL18,
	SUM(COALESCE(RET18,0)) RET18,
	SUM(COALESCE(EXC18,0)) EXC18,
	SUM(COALESCE(IN19,0)) IN19,
	SUM(COALESCE(OUT19,0)) OUT19,
	SUM(COALESCE(BOO19,0)) BOO19,
	SUM(COALESCE(PJL19,0)) PJL19,
	SUM(COALESCE(RET19,0)) RET19,
	SUM(COALESCE(EXC19,0)) EXC19,
	SUM(COALESCE(IN20,0)) IN20,
	SUM(COALESCE(OUT20,0)) OUT20,
	SUM(COALESCE(BOO20,0)) BOO20,
	SUM(COALESCE(PJL20,0)) PJL20,
	SUM(COALESCE(RET20,0)) RET20,
	SUM(COALESCE(EXC20,0)) EXC20,
	SUM(COALESCE(IN21,0)) IN21,
	SUM(COALESCE(OUT21,0)) OUT21,
	SUM(COALESCE(BOO21,0)) BOO21,
	SUM(COALESCE(PJL21,0)) PJL21,
	SUM(COALESCE(RET21,0)) RET21,
	SUM(COALESCE(EXC21,0)) EXC21,
	SUM(COALESCE(IN22,0)) IN22,
	SUM(COALESCE(OUT22,0)) OUT22,
	SUM(COALESCE(BOO22,0)) BOO22,
	SUM(COALESCE(PJL22,0)) PJL22,
	SUM(COALESCE(RET22,0)) RET22,
	SUM(COALESCE(EXC22,0)) EXC22,
	SUM(COALESCE(IN23,0)) IN23,
	SUM(COALESCE(OUT23,0)) OUT23,
	SUM(COALESCE(BOO23,0)) BOO23,
	SUM(COALESCE(PJL23,0)) PJL23,
	SUM(COALESCE(RET23,0)) RET23,
	SUM(COALESCE(EXC23,0)) EXC23,
	SUM(COALESCE(IN24,0)) IN24,
	SUM(COALESCE(OUT24,0)) OUT24,
	SUM(COALESCE(BOO24,0)) BOO24,
	SUM(COALESCE(PJL24,0)) PJL24,
	SUM(COALESCE(RET24,0)) RET24,
	SUM(COALESCE(EXC24,0)) EXC24,
	SUM(COALESCE(IN25,0)) IN25,
	SUM(COALESCE(OUT25,0)) OUT25,
	SUM(COALESCE(BOO25,0)) BOO25,
	SUM(COALESCE(PJL25,0)) PJL25,
	SUM(COALESCE(RET25,0)) RET25,
	SUM(COALESCE(EXC25,0)) EXC25,
	SUM(COALESCE(IN26,0)) IN26,
	SUM(COALESCE(OUT26,0)) OUT26,
	SUM(COALESCE(BOO26,0)) BOO26,
	SUM(COALESCE(PJL26,0)) PJL26,
	SUM(COALESCE(RET26,0)) RET26,
	SUM(COALESCE(EXC26,0)) EXC26,
	SUM(COALESCE(IN27,0)) IN27,
	SUM(COALESCE(OUT27,0)) OUT27,
	SUM(COALESCE(BOO27,0)) BOO27,
	SUM(COALESCE(PJL27,0)) PJL27,
	SUM(COALESCE(RET27,0)) RET27,
	SUM(COALESCE(EXC27,0)) EXC27,
	SUM(COALESCE(IN28,0)) IN28,
	SUM(COALESCE(OUT28,0)) OUT28,
	SUM(COALESCE(BOO28,0)) BOO28,
	SUM(COALESCE(PJL28,0)) PJL28,
	SUM(COALESCE(RET28,0)) RET28,
	SUM(COALESCE(EXC28,0)) EXC28,
	SUM(COALESCE(IN29,0)) IN29,
	SUM(COALESCE(OUT29,0)) OUT29,
	SUM(COALESCE(BOO29,0)) BOO29,
	SUM(COALESCE(PJL29,0)) PJL29,
	SUM(COALESCE(RET29,0)) RET29,
	SUM(COALESCE(EXC29,0)) EXC29,
	SUM(COALESCE(IN30,0)) IN30,
	SUM(COALESCE(OUT30,0)) OUT30,
	SUM(COALESCE(BOO30,0)) BOO30,
	SUM(COALESCE(PJL30,0)) PJL30,
	SUM(COALESCE(RET30,0)) RET30,
	SUM(COALESCE(EXC30,0)) EXC30,
	SUM(COALESCE(IN31,0)) IN31,
	SUM(COALESCE(OUT31,0)) OUT31,
	SUM(COALESCE(BOO31,0)) BOO31,
	SUM(COALESCE(PJL31,0)) PJL31,
	SUM(COALESCE(RET31,0)) RET31,
	SUM(COALESCE(EXC31,0)) EXC31,
	SUM(COALESCE(SaldoAwal,0)) + (SUM(COALESCE(IN1,0)) -	SUM(COALESCE(OUT1,0)) -	SUM(COALESCE(BOO1,0)) -	SUM(COALESCE(PJL1,0)) +	SUM(COALESCE(RET1,0)) +	SUM(COALESCE(EXC1,0))) +	(SUM(COALESCE(IN2,0)) -	SUM(COALESCE(OUT2,0)) -	SUM(COALESCE(BOO2,0)) -	SUM(COALESCE(PJL2,0)) +	SUM(COALESCE(RET2,0)) +	SUM(COALESCE(EXC2,0))) +	(SUM(COALESCE(IN3,0)) -	SUM(COALESCE(OUT3,0)) -	SUM(COALESCE(BOO3,0)) -	SUM(COALESCE(PJL3,0)) +	SUM(COALESCE(RET3,0)) +	SUM(COALESCE(EXC3,0))) +	(SUM(COALESCE(IN4,0)) -	SUM(COALESCE(OUT4,0)) -	SUM(COALESCE(BOO4,0)) -	SUM(COALESCE(PJL4,0)) +	SUM(COALESCE(RET4,0)) +	SUM(COALESCE(EXC4,0))) +	(SUM(COALESCE(IN5,0)) -	SUM(COALESCE(OUT5,0)) -	SUM(COALESCE(BOO5,0)) -	SUM(COALESCE(PJL5,0)) +	SUM(COALESCE(RET5,0)) +	SUM(COALESCE(EXC5,0))) +	(SUM(COALESCE(IN6,0)) -	SUM(COALESCE(OUT6,0)) -	SUM(COALESCE(BOO6,0)) -	SUM(COALESCE(PJL6,0)) +	SUM(COALESCE(RET6,0)) +	SUM(COALESCE(EXC6,0))) +	(SUM(COALESCE(IN7,0)) -	SUM(COALESCE(OUT7,0)) -	SUM(COALESCE(BOO7,0)) -	SUM(COALESCE(PJL7,0)) +	SUM(COALESCE(RET7,0)) +	SUM(COALESCE(EXC7,0))) +	(SUM(COALESCE(IN8,0)) -	SUM(COALESCE(OUT8,0)) -	SUM(COALESCE(BOO8,0)) -	SUM(COALESCE(PJL8,0)) +	SUM(COALESCE(RET8,0)) +	SUM(COALESCE(EXC8,0))) +	(SUM(COALESCE(IN9,0)) -	SUM(COALESCE(OUT9,0)) -	SUM(COALESCE(BOO9,0)) -	SUM(COALESCE(PJL9,0)) +	SUM(COALESCE(RET9,0)) +	SUM(COALESCE(EXC9,0))) +	(SUM(COALESCE(IN10,0)) -	SUM(COALESCE(OUT10,0)) -	SUM(COALESCE(BOO10,0)) -	SUM(COALESCE(PJL10,0)) +	SUM(COALESCE(RET10,0)) +	SUM(COALESCE(EXC10,0))) +	(SUM(COALESCE(IN11,0)) -	SUM(COALESCE(OUT11,0)) -	SUM(COALESCE(BOO11,0)) -	SUM(COALESCE(PJL11,0)) +	SUM(COALESCE(RET11,0)) +	SUM(COALESCE(EXC11,0))) +	(SUM(COALESCE(IN12,0)) -	SUM(COALESCE(OUT12,0)) -	SUM(COALESCE(BOO12,0)) -	SUM(COALESCE(PJL12,0)) +	SUM(COALESCE(RET12,0)) +	SUM(COALESCE(EXC12,0))) +	(SUM(COALESCE(IN13,0)) -	SUM(COALESCE(OUT13,0)) -	SUM(COALESCE(BOO13,0)) -	SUM(COALESCE(PJL13,0)) +	SUM(COALESCE(RET13,0)) +	SUM(COALESCE(EXC13,0))) +	(SUM(COALESCE(IN14,0)) -	SUM(COALESCE(OUT14,0)) -	SUM(COALESCE(BOO14,0)) -	SUM(COALESCE(PJL14,0)) +	SUM(COALESCE(RET14,0)) +	SUM(COALESCE(EXC14,0))) +	(SUM(COALESCE(IN15,0)) -	SUM(COALESCE(OUT15,0)) -	SUM(COALESCE(BOO15,0)) -	SUM(COALESCE(PJL15,0)) +	SUM(COALESCE(RET15,0)) +	SUM(COALESCE(EXC15,0))) +	(SUM(COALESCE(IN16,0)) -	SUM(COALESCE(OUT16,0)) -	SUM(COALESCE(BOO16,0)) -	SUM(COALESCE(PJL16,0)) +	SUM(COALESCE(RET16,0)) +	SUM(COALESCE(EXC16,0))) +	(SUM(COALESCE(IN17,0)) -	SUM(COALESCE(OUT17,0)) -	SUM(COALESCE(BOO17,0)) -	SUM(COALESCE(PJL17,0)) +	SUM(COALESCE(RET17,0)) +	SUM(COALESCE(EXC17,0))) +	(SUM(COALESCE(IN18,0)) -	SUM(COALESCE(OUT18,0)) -	SUM(COALESCE(BOO18,0)) -	SUM(COALESCE(PJL18,0)) +	SUM(COALESCE(RET18,0)) +	SUM(COALESCE(EXC18,0))) +	(SUM(COALESCE(IN19,0)) -	SUM(COALESCE(OUT19,0)) -	SUM(COALESCE(BOO19,0)) -	SUM(COALESCE(PJL19,0)) +	SUM(COALESCE(RET19,0)) +	SUM(COALESCE(EXC19,0))) +	(SUM(COALESCE(IN20,0)) -	SUM(COALESCE(OUT20,0)) -	SUM(COALESCE(BOO20,0)) -	SUM(COALESCE(PJL20,0)) +	SUM(COALESCE(RET20,0)) +	SUM(COALESCE(EXC20,0))) +	(SUM(COALESCE(IN21,0)) -	SUM(COALESCE(OUT21,0)) -	SUM(COALESCE(BOO21,0)) -	SUM(COALESCE(PJL21,0)) +	SUM(COALESCE(RET21,0)) +	SUM(COALESCE(EXC21,0))) +	(SUM(COALESCE(IN22,0)) -	SUM(COALESCE(OUT22,0)) -	SUM(COALESCE(BOO22,0)) -	SUM(COALESCE(PJL22,0)) +	SUM(COALESCE(RET22,0)) +	SUM(COALESCE(EXC22,0))) +	(SUM(COALESCE(IN23,0)) -	SUM(COALESCE(OUT23,0)) -	SUM(COALESCE(BOO23,0)) -	SUM(COALESCE(PJL23,0)) +	SUM(COALESCE(RET23,0)) +	SUM(COALESCE(EXC23,0))) +	(SUM(COALESCE(IN24,0)) -	SUM(COALESCE(OUT24,0)) -	SUM(COALESCE(BOO24,0)) -	SUM(COALESCE(PJL24,0)) +	SUM(COALESCE(RET24,0)) +	SUM(COALESCE(EXC24,0))) +	(SUM(COALESCE(IN25,0)) -	SUM(COALESCE(OUT25,0)) -	SUM(COALESCE(BOO25,0)) -	SUM(COALESCE(PJL25,0)) +	SUM(COALESCE(RET25,0)) +	SUM(COALESCE(EXC25,0))) +	(SUM(COALESCE(IN26,0)) -	SUM(COALESCE(OUT26,0)) -	SUM(COALESCE(BOO26,0)) -	SUM(COALESCE(PJL26,0)) +	SUM(COALESCE(RET26,0)) +	SUM(COALESCE(EXC26,0))) +	(SUM(COALESCE(IN27,0)) -	SUM(COALESCE(OUT27,0)) -	SUM(COALESCE(BOO27,0)) -	SUM(COALESCE(PJL27,0)) +	SUM(COALESCE(RET27,0)) +	SUM(COALESCE(EXC27,0))) +	(SUM(COALESCE(IN28,0)) -	SUM(COALESCE(OUT28,0)) -	SUM(COALESCE(BOO28,0)) -	SUM(COALESCE(PJL28,0)) +	SUM(COALESCE(RET28,0)) +	SUM(COALESCE(EXC28,0))) +	(SUM(COALESCE(IN29,0)) -	SUM(COALESCE(OUT29,0)) -	SUM(COALESCE(BOO29,0)) -	SUM(COALESCE(PJL29,0)) +	SUM(COALESCE(RET29,0)) +	SUM(COALESCE(EXC29,0))) +	(SUM(COALESCE(IN30,0)) -	SUM(COALESCE(OUT30,0)) -	SUM(COALESCE(BOO30,0)) -	SUM(COALESCE(PJL30,0)) +	SUM(COALESCE(RET30,0)) +	SUM(COALESCE(EXC30,0))) +	(SUM(COALESCE(IN31,0)) -	SUM(COALESCE(OUT31,0)) -	SUM(COALESCE(BOO31,0)) -	SUM(COALESCE(PJL31,0)) +	SUM(COALESCE(RET31,0)) +	SUM(COALESCE(EXC31,0)))  SaldoAkhir

FROM (
	SELECT
		b.ItemCode KodeItem,
		b.KodeItemLama,
		b.Article,
		b.Warna,
		SUM(CASE WHEN a.idx = 'AdjIN' THEN a.Qty ELSE 0 END) -
		SUM(CASE WHEN a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END) -
		SUM(CASE WHEN a.idx = 'Booking' THEN a.Qty ELSE 0 END) - 
		SUM(CASE WHEN a.idx = 'Penjualan' THEN a.Qty ELSE 0 END) + 
		SUM(CASE WHEN a.idx = 'Retur' THEN a.Qty ELSE 0 END) + 
		SUM(CASE WHEN a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END) SaldoAwal,
		0 IN1,
		0 OUT1,
		0 BOO1,
		0 PJL1,
		0 RET1,
		0 EXC1,
		
		0 IN2,
		0 OUT2,
		0 BOO2,
		0 PJL2,
		0 RET2,
		0 EXC2,

		0 IN3,
		0 OUT3,
		0 BOO3,
		0 PJL3,
		0 RET3,
		0 EXC3,

		0 IN4,
		0 OUT4,
		0 BOO4,
		0 PJL4,
		0 RET4,
		0 EXC4,

		0 IN5,
		0 OUT5,
		0 BOO5,
		0 PJL5,
		0 RET5,
		0 EXC5,

		0 IN6,
		0 OUT6,
		0 BOO6,
		0 PJL6,
		0 RET6,
		0 EXC6,

		0 IN7,
		0 OUT7,
		0 BOO7,
		0 PJL7,
		0 RET7,
		0 EXC7,

		0 IN8,
		0 OUT8,
		0 BOO8,
		0 PJL8,
		0 RET8,
		0 EXC8,

		0 IN9,
		0 OUT9,
		0 BOO9,
		0 PJL9,
		0 RET9,
		0 EXC9,
		
		0 IN10,
		0 OUT10,
		0 BOO10,
		0 PJL10,
		0 RET10,
		0 EXC10,

		0 IN11,
		0 OUT11,
		0 BOO11,
		0 PJL11,
		0 RET11,
		0 EXC11,

		0 IN12,
		0 OUT12,
		0 BOO12,
		0 PJL12,
		0 RET12,
		0 EXC12,

		0 IN13,
		0 OUT13,
		0 BOO13,
		0 PJL13,
		0 RET13,
		0 EXC13,

		0 IN14,
		0 OUT14,
		0 BOO14,
		0 PJL14,
		0 RET14,
		0 EXC14,

		0 IN15,
		0 OUT15,
		0 BOO15,
		0 PJL15,
		0 RET15,
		0 EXC15,

		0 IN16,
		0 OUT16,
		0 BOO16,
		0 PJL16,
		0 RET16,
		0 EXC16,

		0 IN17,
		0 OUT17,
		0 BOO17,
		0 PJL17,
		0 RET17,
		0 EXC17,

		0 IN18,
		0 OUT18,
		0 BOO18,
		0 PJL18,
		0 RET18,
		0 EXC18,

		0 IN19,
		0 OUT19,
		0 BOO19,
		0 PJL19,
		0 RET19,
		0 EXC19,

		0 IN20,
		0 OUT20,
		0 BOO20,
		0 PJL20,
		0 RET20,
		0 EXC20,

		0 IN21,
		0 OUT21,
		0 BOO21,
		0 PJL21,
		0 RET21,
		0 EXC21,

		0 IN22,
		0 OUT22,
		0 BOO22,
		0 PJL22,
		0 RET22,
		0 EXC22,

		0 IN23,
		0 OUT23,
		0 BOO23,
		0 PJL23,
		0 RET23,
		0 EXC23,

		0 IN24,
		0 OUT24,
		0 BOO24,
		0 PJL24,
		0 RET24,
		0 EXC24,

		0 IN25,
		0 OUT25,
		0 BOO25,
		0 PJL25,
		0 RET25,
		0 EXC25,

		0 IN26,
		0 OUT26,
		0 BOO26,
		0 PJL26,
		0 RET26,
		0 EXC26,

		0 IN27,
		0 OUT27,
		0 BOO27,
		0 PJL27,
		0 RET27,
		0 EXC27,

		0 IN28,
		0 OUT28,
		0 BOO28,
		0 PJL28,
		0 RET28,
		0 EXC28,

		0 IN29,
		0 OUT29,
		0 BOO29,
		0 PJL29,
		0 RET29,
		0 EXC29,

		0 IN30,
		0 OUT30,
		0 BOO30,
		0 PJL30,
		0 RET30,
		0 EXC30,

		0 IN31,
		0 OUT31,
		0 BOO31,
		0 PJL31,
		0 RET31,
		0 EXC31
	FROM vw_stok b
	LEFT JOIN vw_trx a on a.KodeItem = b.ItemCode AND a.TglTransaksi < TglAwal -- a.TglTransaksi < DATE_SUB(a.TglTransaksi,INTERVAL DAYOFMONTH(a.TglTransaksi)-1 DAY)
	GROUP BY b.ItemCode,b.KodeItemLama

	UNION ALL

	SELECT 
		a.KodeItem,
		b.KodeItemLama,
		b.Article,
		b.Warna,
		0 SaldoAwal,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN1,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT1,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO1,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL1,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET1,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 1 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC1,
		
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN2,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT2,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO2,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL2,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET2,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 2 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC2,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN3,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT3,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO3,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL3,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET3,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 3 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC3,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN4,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT4,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO4,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL4,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET4,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 4 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC4,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN5,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT5,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO5,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL5,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET5,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 5 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC5,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN6,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT6,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO6,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL6,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET6,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 6 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC6,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN7,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT7,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO7,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL7,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET7,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 7 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC7,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN8,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT8,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO8,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL8,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET8,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 8 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC8,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN9,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT9,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO9,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL9,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET9,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 9 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC9,
		
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN10,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT10,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO10,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL10,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET10,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 10 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC10,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN11,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT11,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO11,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL11,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET11,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 11 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC11,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN12,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT12,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO12,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL12,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET12,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 12 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC12,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN13,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT13,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO13,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL13,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET13,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 13 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC13,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN14,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT14,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO14,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL14,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET14,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 14 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC14,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN15,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT15,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO15,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL15,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET15,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 15 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC15,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN16,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT16,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO16,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL16,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET16,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 16 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC16,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN17,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT17,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO17,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL17,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET17,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 17 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC17,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN18,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT18,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO18,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL18,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET18,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 18 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC18,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN19,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT19,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO19,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL19,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET19,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 19 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC19,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN20,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT20,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO20,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL20,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET20,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 20 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC20,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN21,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT21,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO21,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL21,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET21,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 21 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC21,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN22,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT22,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO22,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL22,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET22,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 22 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC22,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN23,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT23,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO23,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL23,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET23,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 23 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC23,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN24,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT24,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO24,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL24,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET24,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 24 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC24,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN25,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT25,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO25,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL25,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET25,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 25 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC25,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN26,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT26,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO26,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL26,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET26,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 26 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC26,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN27,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT27,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO27,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL27,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET27,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 27 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC27,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN28,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT28,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO28,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL28,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET28,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 28 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC28,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN29,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT29,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO29,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL29,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET29,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 29 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC29,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN30,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT30,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO30,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL30,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET30,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 30 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC30,

		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'AdjIN' THEN a.Qty ELSE 0 END ) IN31,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'AdjOUT' THEN a.Qty ELSE 0 END ) OUT31,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'Booking' THEN a.Qty ELSE 0 END ) BOO31,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'Penjualan' THEN a.Qty ELSE 0 END ) PJL31,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'Retur' THEN a.Qty ELSE 0 END ) RET31,
		SUM(CASE WHEN DAY(a.TglTransaksi) = 31 AND a.idx = 'Retur - Exchange' THEN a.Qty ELSE 0 END ) EXC31

	FROM vw_stok b
	LEFT JOIN vw_trx a on a.KodeItem = b.ItemCode
	WHERE a.TglTransaksi BETWEEN TglAwal AND TglAkhir	-- DATE_SUB(a.TglTransaksi,INTERVAL DAYOFMONTH(a.TglTransaksi)-1 DAY) AND LAST_DAY(a.TglTransaksi)
	GROUP BY a.KodeItem,b.KodeItemLama
) stk
GROUP BY KodeItem,KodeItemLama ;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for test_error
-- ----------------------------
DROP PROCEDURE IF EXISTS `test_error`;
delimiter ;;
CREATE PROCEDURE `test_error`(id INT)
BEGIN
   DECLARE errno SMALLINT UNSIGNED DEFAULT 31001;
   SET @errmsg = '';
	 SET @count = 0 ;
	 
	 SELECT COUNT(*) INTO @count FROM ttest WHERE id = id
	 AND nomor ='1001';
	 
	 
   IF @count > 0 THEN
			SET @errmsg = 'CATCH ERROR';
      SIGNAL SQLSTATE '45000' SET
      MYSQL_ERRNO = errno,
      MESSAGE_TEXT = @errmsg;
   END IF;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
