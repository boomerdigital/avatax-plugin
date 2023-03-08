<?php
class DB{
    public static function activate() {
        self::createTables();
        update_option( 'vendor', 0 );
        update_option( 'woocommerce_calc_taxes', 'yes' );
    }

    public static function createTables(){
        self::createAtCompany();
        self::createAtTransactions();
        self::createAtTransactionsLines();
        self::createAtTransactionsLinesDetails();
        self::createAtTransactionsSummary();
        self::createAtAddress();
        self::createUserData();
    }

    public static function createAtCompany(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'avatax_company';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                 `id` int(11) NOT NULL AUTO_INCREMENT,
                 `accountId` int(11) DEFAULT NULL,
                 `companyId` int(11) DEFAULT NULL,
                 `` varchar(255) DEFAULT NULL,
                 `name` varchar(255) DEFAULT NULL,
                 `isDefault` enum('0','1') DEFAULT NULL,
                 `isActive` enum('0','1') DEFAULT NULL,
                 `taxpayerIdNumber` varchar(255) DEFAULT NULL,
                 `IsFein` enum('0','1') DEFAULT NULL,
                 `hasProfile` enum('0','1') DEFAULT NULL,
                 `isReportingEntity` enum('0','1') DEFAULT NULL,
                 `defaultCountry` varchar(255) DEFAULT NULL,
                 `roundingLevelId` varchar(255) DEFAULT NULL,
                 `warningsEnabled` varchar(255) DEFAULT NULL,
                 `isTest` enum('0','1') DEFAULT NULL,
                 `inProgress` enum('0','1') DEFAULT NULL,
                 `createdDate` datetime DEFAULT NULL,
                 `createdUserId` int(11) DEFAULT NULL,
                 `modifiedDate` datetime DEFAULT NULL,
                 `modifiedUserId` int(11) DEFAULT NULL,
                 `settings` varchar(255) DEFAULT NULL,
                 `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 `deleted_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }  

    }  

    public static function createAtTransactions(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'avatax_transactions';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `transactions_id` varchar(255) DEFAULT NULL,
                  `product_id` int(11) DEFAULT NULL,
                  `order_id` varchar(255) DEFAULT NULL,
                  `code` varchar(255) DEFAULT NULL,
                  `companyId` int(11) DEFAULT NULL,
                  `date` text,
                  `status` varchar(255) DEFAULT NULL,
                  `type` varchar(255) DEFAULT NULL,
                  `batchCode` varchar(255) DEFAULT NULL,
                  `currencyCode` varchar(255) DEFAULT NULL,
                  `exchangeRateCurrencyCode` varchar(255) DEFAULT NULL,
                  `customerUsageType` varchar(255) DEFAULT NULL,
                  `entityUseCode` varchar(255) DEFAULT NULL,
                  `customerVendorCode` varchar(255) DEFAULT NULL,
                  `customerCode` varchar(255) DEFAULT NULL,
                  `exemptNo` varchar(255) DEFAULT NULL,
                  `reconciled` varchar(255) DEFAULT NULL,
                  `locationCode` varchar(255) DEFAULT NULL,
                  `reportingLocationCode` varchar(255) DEFAULT NULL,
                  `purchaseOrderNo` varchar(255) DEFAULT NULL,
                  `referenceCode` varchar(255) DEFAULT NULL,
                  `salespersonCode` varchar(255) DEFAULT NULL,
                  `taxOverrideType` varchar(255) DEFAULT NULL,
                  `taxOverrideAmount` varchar(255) DEFAULT NULL,
                  `taxOverrideReason` varchar(255) DEFAULT NULL,
                  `totalAmount` varchar(255) DEFAULT NULL,
                  `totalExempt` varchar(255) DEFAULT NULL,
                  `totalDiscount` varchar(255) DEFAULT NULL,
                  `totalTax` varchar(255) DEFAULT NULL,
                  `totalTaxable` varchar(255) DEFAULT NULL,
                  `totalTaxCalculated` varchar(255) DEFAULT NULL,
                  `adjustmentReason` varchar(255) DEFAULT NULL,
                  `adjustmentDescription` varchar(255) DEFAULT NULL,
                  `locked` varchar(255) DEFAULT NULL,
                  `region` varchar(255) DEFAULT NULL,
                  `country` varchar(255) DEFAULT NULL,
                  `version` varchar(255) DEFAULT NULL,
                  `softwareVersion` varchar(255) DEFAULT NULL,
                  `originAddressId` varchar(255) DEFAULT NULL,
                  `destinationAddressId` varchar(255) DEFAULT NULL,
                  `exchangeRateEffectiveDate` varchar(255) DEFAULT NULL,
                  `exchangeRate` varchar(255) DEFAULT NULL,
                  `description` varchar(255) DEFAULT NULL,
                  `email` varchar(255) DEFAULT NULL,
                  `businessIdentificationNo` varchar(255) DEFAULT NULL,
                  `modifiedDate` datetime DEFAULT NULL,
                  `modifiedUserId` varchar(255) DEFAULT NULL,
                  `taxDate` varchar(255) DEFAULT NULL,
                  `addresses` longtext,
                  `locationTypes` longtext,
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `deleted_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
          }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }  

    public static function createAtTransactionsLines(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'avatax_transactions_lines';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `lines_id` varchar(255) DEFAULT NULL,
                  `product_id` varchar(255) DEFAULT NULL,
                  `order_id` varchar(255) DEFAULT NULL,
                  `transactionId` varchar(255) DEFAULT NULL,
                  `lineNumber` varchar(255) DEFAULT NULL,
                  `boundaryOverrideId` varchar(255) DEFAULT NULL,
                  `customerUsageType` varchar(255) DEFAULT NULL,
                  `entityUseCode` varchar(255) DEFAULT NULL,
                  `description` longtext,
                  `destinationAddressId` varchar(255) DEFAULT NULL,
                  `originAddressId` varchar(255) DEFAULT NULL,
                  `discountAmount` varchar(255) DEFAULT NULL,
                  `discountTypeId` varchar(255) DEFAULT NULL,
                  `exemptAmount` varchar(255) DEFAULT NULL,
                  `exemptCertId` varchar(255) DEFAULT NULL,
                  `exemptNo` varchar(255) DEFAULT NULL,
                  `isItemTaxable` varchar(255) DEFAULT NULL,
                  `isSSTP` varchar(255) DEFAULT NULL,
                  `itemCode` varchar(255) DEFAULT NULL,
                  `lineAmount` varchar(255) DEFAULT NULL,
                  `quantity` varchar(255) DEFAULT NULL,
                  `ref1` varchar(255) DEFAULT NULL,
                  `ref2` varchar(255) DEFAULT NULL,
                  `reportingDate` varchar(255) DEFAULT NULL,
                  `revAccount` varchar(255) DEFAULT NULL,
                  `sourcing` varchar(255) DEFAULT NULL,
                  `tax` varchar(255) DEFAULT NULL,
                  `taxableAmount` varchar(255) DEFAULT NULL,
                  `taxCalculated` varchar(255) DEFAULT NULL,
                  `taxCode` varchar(255) DEFAULT NULL,
                  `taxCodeId` varchar(255) DEFAULT NULL,
                  `taxDate` varchar(255) DEFAULT NULL,
                  `taxEngine` varchar(255) DEFAULT NULL,
                  `taxOverrideType` varchar(255) DEFAULT NULL,
                  `businessIdentificationNo` varchar(255) DEFAULT NULL,
                  `taxOverrideAmount` varchar(255) DEFAULT NULL,
                  `taxOverrideReason` varchar(255) DEFAULT NULL,
                  `taxIncluded` varchar(255) DEFAULT NULL,
                  `nonPassthroughDetails` longtext,
                  `lineLocationTypes` longtext,
                  `hsCode` varchar(255) DEFAULT NULL,
                  `costInsuranceFreight` varchar(255) DEFAULT NULL,
                  `vatCode` varchar(255) DEFAULT NULL,
                  `vatNumberTypeId` varchar(255) DEFAULT NULL,
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `deleted_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
          }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    } 

    public static function createAtTransactionsLinesDetails(){
        global $wpdb; 
        try{
            $table = $wpdb->prefix.'avatax_transactions_lines_details';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `transactions_lines_details_id` varchar(255) DEFAULT NULL,
                  `product_id` varchar(255) DEFAULT NULL,
                  `order_id` varchar(255) DEFAULT NULL,
                  `transactionLineId` varchar(255) DEFAULT NULL,
                  `transactionId` varchar(255) DEFAULT NULL,
                  `addressId` varchar(255) DEFAULT NULL,
                  `country` varchar(255) DEFAULT NULL,
                  `region` varchar(255) DEFAULT NULL,
                  `countyFIPS` varchar(255) DEFAULT NULL,
                  `stateFIPS` varchar(255) DEFAULT NULL,
                  `exemptAmount` varchar(255) DEFAULT NULL,
                  `exemptReasonId` varchar(255) DEFAULT NULL,
                  `inState` varchar(255) DEFAULT NULL,
                  `jurisCode` varchar(255) DEFAULT NULL,
                  `jurisName` varchar(255) DEFAULT NULL,
                  `jurisdictionId` varchar(255) DEFAULT NULL,
                  `signatureCode` varchar(255) DEFAULT NULL,
                  `stateAssignedNo` varchar(255) DEFAULT NULL,
                  `jurisType` varchar(255) DEFAULT NULL,
                  `jurisdictionType` varchar(255) DEFAULT NULL,
                  `nonTaxableAmount` varchar(255) DEFAULT NULL,
                  `nonTaxableRuleId` varchar(255) DEFAULT NULL,
                  `nonTaxableType` varchar(255) DEFAULT NULL,
                  `rate` varchar(255) DEFAULT NULL,
                  `rateRuleId` varchar(255) DEFAULT NULL,
                  `rateSourceId` varchar(255) DEFAULT NULL,
                  `serCode` varchar(255) DEFAULT NULL,
                  `sourcing` varchar(255) DEFAULT NULL,
                  `tax` varchar(255) DEFAULT NULL,
                  `taxableAmount` varchar(255) DEFAULT NULL,
                  `taxType` varchar(255) DEFAULT NULL,
                  `taxSubTypeId` varchar(255) DEFAULT NULL,
                  `taxTypeGroupId` varchar(255) DEFAULT NULL,
                  `taxName` varchar(255) DEFAULT NULL,
                  `taxAuthorityTypeId` varchar(255) DEFAULT NULL,
                  `taxRegionId` varchar(255) DEFAULT NULL,
                  `taxCalculated` varchar(255) DEFAULT NULL,
                  `taxOverride` varchar(255) DEFAULT NULL,
                  `rateType` varchar(255) DEFAULT NULL,
                  `rateTypeCode` varchar(255) DEFAULT NULL,
                  `taxableUnits` varchar(255) DEFAULT NULL,
                  `nonTaxableUnits` varchar(255) DEFAULT NULL,
                  `exemptUnits` varchar(255) DEFAULT NULL,
                  `unitOfBasis` varchar(255) DEFAULT NULL,
                  `isNonPassThru` varchar(255) DEFAULT NULL,
                  `isFee` varchar(255) DEFAULT NULL,
                  `reportingTaxableUnits` varchar(255) DEFAULT NULL,
                  `reportingNonTaxableUnits` varchar(255) DEFAULT NULL,
                  `reportingExemptUnits` varchar(255) DEFAULT NULL,
                  `reportingTax` varchar(255) DEFAULT NULL,
                  `reportingTaxCalculated` varchar(255) DEFAULT NULL,
                  `liabilityType` varchar(255) DEFAULT NULL,
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `deleted_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
          }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    } 

    public static function createAtTransactionsSummary(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'avatax_transactions_summary';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` varchar(255) DEFAULT NULL,
                  `order_id` varchar(255) DEFAULT NULL,
                  `transaction_id` varchar(255) DEFAULT NULL,
                  `country` varchar(255) DEFAULT NULL,
                  `region` varchar(255) DEFAULT NULL,
                  `jurisType` varchar(255) DEFAULT NULL,
                  `jurisCode` varchar(255) DEFAULT NULL,
                  `jurisName` varchar(255) DEFAULT NULL,
                  `taxAuthorityType` varchar(255) DEFAULT NULL,
                  `stateAssignedNo` varchar(255) DEFAULT NULL,
                  `taxType` varchar(255) DEFAULT NULL,
                  `taxSubType` varchar(255) DEFAULT NULL,
                  `taxName` varchar(255) DEFAULT NULL,
                  `rateType` varchar(255) DEFAULT NULL,
                  `taxable` varchar(255) DEFAULT NULL,
                  `rate` varchar(255) DEFAULT NULL,
                  `tax` varchar(255) DEFAULT NULL,
                  `taxCalculated` varchar(255) DEFAULT NULL,
                  `nonTaxable` varchar(255) DEFAULT NULL,
                  `exemption` varchar(255) DEFAULT NULL,
                  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
                  `deleted_at` datetime DEFAULT CURRENT_TIMESTAMP,
                 PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public static function createAtAddress(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'avatax_address';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `address` varchar(500) NOT NULL,
             `validatedAddresses` varchar(500) NOT NULL,
             `coordinates` varchar(500) NOT NULL,
             `resolutionQuality` varchar(500) NOT NULL,
             `taxAuthorities` text NOT NULL,
             `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
             `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
             `deleted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
          }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public static function createUserData(){
        global $wpdb;
        try{
            $table = $wpdb->prefix.'user_data';
            $charset = $wpdb->get_charset_collate();
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `user_id` int(11) NOT NULL,
             `status` enum('1','0') NOT NULL DEFAULT '1',
             PRIMARY KEY (`id`)
              ) $charset_collate;";
              require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
              dbDelta( $sql );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }  
    }

     public static function insertAtAddress($response){
        global $wpdb;
        try{
            $wpdb->insert($wpdb->prefix.'avatax_address',array( 
            'address' => serialize(array($response->address)), 
            'validatedAddresses' => serialize($response->validatedAddresses), 
            'coordinates' => serialize(array($response->coordinates)), 
            'resolutionQuality' => $response->resolutionQuality,
            'taxAuthorities' => serialize(array($response->taxAuthorities))      
            ), 
            array('%s','%s','%s','%s','%s') 
            );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }

    } 
    
    public static function deactivate() {
        global $wpdb;
         $tableArray = [$wpdb->prefix."avatax_address",$wpdb->prefix."avatax_company",$wpdb->prefix."avatax_transactions",$wpdb->prefix."avatax_transactions_lines",$wpdb->prefix."avatax_transactions_lines_details",$wpdb->prefix."avatax_transactions_summary",$wpdb->prefix . "user_data"];

      foreach ($tableArray as $tablename) {
         $wpdb->query("DROP TABLE IF EXISTS $tablename");
      }
        delete_option("jal_db_version");
    } 
}
