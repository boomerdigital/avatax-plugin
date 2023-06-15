<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Dml{
    public function __construct(){
        
    }
    
    public static function insertAtCompany($response){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM  {$wpdb->prefix}avatax_company WHERE companyId=".$response['value'][0]['id']);
            if(count($checkValueExist) == 0){
                if (isset($response['value'][0]['settings']) && is_array($response['value'][0]['settings'])) {
                    $settings = $response['value'][0]['settings'];
                }else{
                    $settings = array();
                }
                if(!empty($response['value'][0]['id']) && !empty($response['value'][0]['accountId'])){
                $wpdb->insert($wpdb->prefix.'avatax_company',array( 
                    'companyId' => $response['value'][0]['id'], 
                    'accountId' => $response['value'][0]['accountId'], 
                    'companyCode' => $response['value'][0]['companyCode'], 
                    'name' => $response['value'][0]['name'], 
                    'isDefault' => $response['value'][0]['isDefault'], 
                    'isActive' => $response['value'][0]['isActive'], 
                    'taxpayerIdNumber' => $response['value'][0]['taxpayerIdNumber'], 
                    'IsFein' => $response['value'][0]['IsFein'], 
                    'hasProfile' => $response['value'][0]['hasProfile'], 
                    'isReportingEntity' => $response['value'][0]['isReportingEntity'], 
                    'defaultCountry' => $response['value'][0]['defaultCountry'], 
                    'roundingLevelId' => $response['value'][0]['roundingLevelId'], 
                    'warningsEnabled' => $response['value'][0]['warningsEnabled'], 
                    'isTest' => $response['value'][0]['isTest'], 
                    'inProgress' => $response['value'][0]['inProgress'], 
                    'createdDate' => $response['value'][0]['createdDate'], 
                    'createdUserId' => $response['value'][0]['createdUserId'],
                    'modifiedDate' => $response['value'][0]['modifiedDate'],
                    'modifiedUserId' => $response['value'][0]['modifiedUserId'],
                    'settings' => serialize($settings)
                ), 
                array('%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
                );
               } 
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    } 
    
    public static function insertAtTransactions($response,$order_id,$companycode){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}avatax_transactions WHERE order_id =".$order_id);
            if(count($checkValueExist) == 0){
                $wpdb->insert($wpdb->prefix.'avatax_transactions',array( 
                    'transactions_id' => $response->id,
                    'order_id' => $order_id,
                    'code' => $response->code,
                    'companyId' => $response->companyId,
                    'companyCode' => $companycode,
                    'date' => $response->date,
                    'status' => $response->status,
                    'type' => $response->type,
                    'batchCode' => $response->batchCode,
                    'currencyCode' => $response->currencyCode,
                    'exchangeRateCurrencyCode' => $response->exchangeRateCurrencyCode,
                    'customerUsageType' => $response->customerUsageType,
                    'entityUseCode' => $response->entityUseCode,
                    'customerVendorCode' => $response->customerVendorCode,
                    'customerCode' => $response->customerCode,
                    'exemptNo' => $response->exemptNo,
                    'reconciled' => $response->reconciled,
                    'locationCode' => $response->locationCode,
                    'reportingLocationCode' => $response->reportingLocationCode,
                    'purchaseOrderNo' => $response->purchaseOrderNo,
                    'referenceCode' => $response->referenceCode,
                    'salespersonCode' => $response->salespersonCode,
                    'taxOverrideType' => $response->taxOverrideType,
                    'taxOverrideAmount' => $response->taxOverrideAmount,
                    'taxOverrideReason' => $response->taxOverrideReason,
                    'totalAmount' => $response->totalAmount,
                    'totalExempt' => $response->totalExempt,
                    'totalDiscount' => $response->totalDiscount,
                    'totalTax' => $response->totalTax,
                    'totalTaxable' => $response->totalTaxable,
                    'totalTaxCalculated' => $response->totalTaxCalculated,
                    'adjustmentReason' => $response->adjustmentReason,
                    'adjustmentDescription' => $response->adjustmentDescription,
                    'locked' => $response->locked,
                    'region' => $response->region,
                    'country' => $response->country,
                    'version' => $response->version,
                    'softwareVersion' => $response->softwareVersion,
                    'originAddressId' => $response->originAddressId,
                    'destinationAddressId' => $response->destinationAddressId,
                    'exchangeRateEffectiveDate' => $response->exchangeRateEffectiveDate,
                    'exchangeRate' => $response->exchangeRate,
                    'description' => $response->description,
                    'email' => $response->email,
                    'businessIdentificationNo' => $response->businessIdentificationNo,
                    'modifiedDate' => $response->modifiedDate,
                    'modifiedUserId' => $response->modifiedUserId,
                    'taxDate' => $response->taxDate, 
                    'addresses' => serialize($response->addresses), 
                    'locationTypes' => serialize($response->locationTypes)
                ), 
                array('%d','%d','%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
                );
                foreach ($response->lines as $key => $line) {
                    Dml::insertAtTransactionsLines($line,$order_id);
                }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    } 

     public static function insertAtTransactionsLines($response,$order_id){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}avatax_transactions_lines WHERE product_id =".$productId." AND order_id =".$order_id);
            if(count($checkValueExist) == 0){
                $wpdb->insert($wpdb->prefix.'avatax_transactions_lines',array( 
                    'lines_id' => $response->id,
                    'order_id' => $order_id,
                    'transactionId' => $response->transactionId,
                    'lineNumber' => $response->lineNumber,
                    'boundaryOverrideId' => $response->boundaryOverrideId,
                    'customerUsageType' => $response->customerUsageType,
                    'entityUseCode' => $response->entityUseCode,
                    'description' => $response->description,
                    'destinationAddressId' => $response->destinationAddressId,
                    'originAddressId' => $response->originAddressId,
                    'discountAmount' => $response->discountAmount,
                    'discountTypeId' => $response->discountTypeId,
                    'exemptAmount' => $response->exemptAmount,
                    'exemptCertId' => $response->exemptCertId,
                    'exemptNo' => $response->exemptNo,
                    'isItemTaxable' => $response->isItemTaxable,
                    'isSSTP' => $response->isSSTP,
                    'itemCode' => $response->itemCode,
                    'lineAmount' => $response->lineAmount,
                    'quantity' => $response->quantity,
                    'ref1' => $response->ref1,
                    'ref2' => $response->ref2,
                    'reportingDate' => $response->reportingDate,
                    'revAccount' => $response->revAccount,
                    'sourcing' => $response->sourcing,
                    'tax' => $response->tax,
                    'taxableAmount' => $response->taxableAmount,
                    'taxCalculated' => $response->taxCalculated,
                    'taxCode' => $response->taxCode,
                    'taxCodeId' => $response->taxCodeId,
                    'taxDate' => $response->taxDate,
                    'taxEngine' => $response->taxEngine,
                    'taxOverrideType' => $response->taxOverrideType,
                    'businessIdentificationNo' => $response->businessIdentificationNo,
                    'taxOverrideAmount' => $response->taxOverrideAmount,
                    'taxOverrideReason' => $response->taxOverrideReason,
                    'nonPassthroughDetails' =>  serialize($response->nonPassthroughDetails),
                    'lineLocationTypes' =>  serialize($response->lineLocationTypes),
                    'hsCode' =>  $response->hsCode,
                    'costInsuranceFreight' =>  $response->costInsuranceFreight,
                    'vatCode' =>  $response->vatCode,
                    'vatNumberTypeId' =>  $response->vatNumberTypeId
                ), 
                array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
                );
                foreach($response->details as $details){
                    Dml::insertAtTransactionsLinesDetails($details,$order_id);
                }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

     public static function insertAtTransactionsLinesDetails($response,$order_id){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}avatax_transactions_lines_details WHERE  order_id =".$order_id);
            if(count($checkValueExist) == 0){
                $wpdb->insert($wpdb->prefix.'avatax_transactions_lines_details',array( 
                    'transactions_lines_details_id' => $response->id,
                    'order_id' => $order_id,
                    'transactionLineId' => $response->transactionLineId,
                    'transactionId' => $response->transactionId,
                    'addressId' => $response->addressId,
                    'country' => $response->country,
                    'region' => $response->region,
                    'countyFIPS' => $response->countyFIPS,
                    'stateFIPS' => $response->stateFIPS,
                    'exemptAmount' => $response->exemptAmount,
                    'exemptReasonId' => $response->exemptReasonId,
                    'inState' => $response->inState,
                    'jurisCode' => $response->jurisCode,
                    'jurisName' => $response->jurisName,
                    'jurisdictionId' => $response->jurisdictionId,
                    'signatureCode' => $response->signatureCode,
                    'stateAssignedNo' => $response->stateAssignedNo,
                    'jurisType' => $response->jurisType,
                    'jurisdictionType' => $response->jurisdictionType,
                    'nonTaxableAmount' => $response->nonTaxableAmount,
                    'nonTaxableRuleId' => $response->nonTaxableRuleId,
                    'nonTaxableType' => $response->nonTaxableType,
                    'rate' => $response->rate,
                    'rateRuleId' => $response->rateRuleId,
                    'rateSourceId' => $response->rateSourceId,
                    'serCode' => $response->serCode,
                    'sourcing' => $response->sourcing,
                    'tax' => $response->tax,
                    'taxableAmount' => $response->taxableAmount,
                    'taxType' => $response->taxType,
                    'taxSubTypeId' => $response->taxSubTypeId,
                    'taxTypeGroupId' => $response->taxTypeGroupId,
                    'taxName' => $response->taxName,
                    'taxAuthorityTypeId' => $response->taxAuthorityTypeId,
                    'taxRegionId' => $response->taxRegionId,
                    'taxCalculated' => $response->taxCalculated,
                    'taxOverride' => $response->taxOverride,
                    'rateType' => $response->rateType,
                    'rateTypeCode' => $response->rateTypeCode,
                    'taxableUnits' => $response->taxableUnits,
                    'nonTaxableUnits' => $response->nonTaxableUnits,
                    'exemptUnits' => $response->exemptUnits,
                    'unitOfBasis' => $response->unitOfBasis,
                    'isNonPassThru' => $response->isNonPassThru,
                    'isFee' => $response->isFee,
                    'reportingTaxableUnits' => $response->reportingTaxableUnits,
                    'reportingNonTaxableUnits' => $response->reportingNonTaxableUnits,
                    'reportingExemptUnits' => $response->reportingExemptUnits,
                    'reportingTax' => $response->reportingTax,
                    'reportingTaxCalculated' => $response->reportingTaxCalculated,
                    'liabilityType' => $response->liabilityType
                ), 
                array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
                );
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }  

    public static function insertAtTransactionsSummary($response,$transactionsId,$order_id){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}avatax_transactions_summary WHERE  order_id =".$order_id);
            if(count($checkValueExist) == 0){
                foreach($response as $response){
                $wpdb->insert($wpdb->prefix.'avatax_transactions_summary',array( 
                        'transaction_id' => $transactionsId,
                        'order_id' => $order_id,
                        'country' => $response->country,
                        'region' => $response->region,
                        'jurisType' => $response->jurisType,
                        'jurisCode' => $response->jurisCode,
                        'jurisName' => $response->jurisName,
                        'taxAuthorityType' => $response->taxAuthorityType,
                        'stateAssignedNo' => $response->stateAssignedNo,
                        'taxType' => $response->taxType,
                        'taxSubType' => $response->taxSubType,
                        'taxName' => $response->taxName,
                        'rateType' => $response->rateType,
                        'taxable' => $response->taxable,
                        'rate' => $response->rate,
                        'tax' => $response->tax,
                        'taxCalculated' => $response->taxCalculated,
                        'nonTaxable' => $response->nonTaxable,
                        'exemption' => $response->exemption
                ), 
                array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s') 
                );
            }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public static function companyAdminDetail($response){
        global $wpdb;
        
        update_option( 'origin', $response['origin']);
        update_option( 'Street', $response['street']);
        update_option( 'City', $response['city']);
        update_option( 'State', $response['state']);
        update_option( 'Zip', $response['zip']);
        update_option( 'Country', $response['country']);
    }     
      
    public static function updateAtTransactionsCommitStatus($commit,$transactionsId,$transactionsCode,$order_id){
        global $wpdb;
        try{
            $checkValueExist=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}avatax_transactions WHERE  order_id =".$order_id);
                if(count($checkValueExist) != 0){
                  $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}avatax_transactions SET status='Committed' WHERE  order_id = ".$order_id." AND transactions_id = ".$transactionsId));
                }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }           
    }
}

