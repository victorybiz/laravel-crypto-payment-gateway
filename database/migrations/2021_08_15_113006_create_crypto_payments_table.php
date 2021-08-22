<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCryptoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Source: https://github.com/cryptoapi/Payment-Gateway

        DB::statement("
            CREATE TABLE IF NOT EXISTS `crypto_payments` (
                `paymentID` bigint unsigned NOT NULL AUTO_INCREMENT,
                `boxID` bigint unsigned NOT NULL DEFAULT '0',
                `boxType` enum('paymentbox','captchabox') NOT NULL,
                `orderID` varchar(50) NOT NULL DEFAULT '',
                `userID` varchar(50) NOT NULL DEFAULT '',
                `countryID` varchar(3) NOT NULL DEFAULT '',
                `coinLabel` varchar(6) NOT NULL DEFAULT '',
                `amount` double(20,8) NOT NULL DEFAULT '0.00000000',
                `amountUSD` double(20,8) NOT NULL DEFAULT '0.00000000',
                `unrecognised` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `addr` varchar(34) NOT NULL DEFAULT '',
                `txID` char(64) NOT NULL DEFAULT '',
                `txDate` datetime DEFAULT NULL,
                `txConfirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `txCheckDate` datetime DEFAULT NULL,
                `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `processedDate` datetime DEFAULT NULL,
                `recordCreated` datetime DEFAULT NULL,
                PRIMARY KEY (`paymentID`),
                KEY `boxID` (`boxID`),
                KEY `boxType` (`boxType`),
                KEY `userID` (`userID`),
                KEY `countryID` (`countryID`),
                KEY `orderID` (`orderID`),
                KEY `amount` (`amount`),
                KEY `amountUSD` (`amountUSD`),
                KEY `coinLabel` (`coinLabel`),
                KEY `unrecognised` (`unrecognised`),
                KEY `addr` (`addr`),
                KEY `txID` (`txID`),
                KEY `txDate` (`txDate`),
                KEY `txConfirmed` (`txConfirmed`),
                KEY `txCheckDate` (`txCheckDate`),
                KEY `processed` (`processed`),
                KEY `processedDate` (`processedDate`),
                KEY `recordCreated` (`recordCreated`),
                KEY `key1` (`boxID`,`orderID`),
                KEY `key2` (`boxID`,`orderID`,`userID`),
                UNIQUE KEY `key3` (`boxID`, `orderID`, `userID`, `txID`, `amount`, `addr`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_payments');
    }
}
